<?php

namespace Tests\Feature;

use App\Events\OrderStatusUpdated;
use App\Models\Local;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * QA G4DS-261 | Broadcast de cambios de estado de orden en tiempo real (cliente)
 *
 * Historia de usuario: Como cliente, quiero ver el estado de mi orden actualizado
 * en tiempo real desde la vista "Mis Órdenes", sin necesitar recargar la página.
 *
 * Criterios de aceptación:
 * - CA1: El evento implementa ShouldBroadcastNow (entrega sincrónica al cliente)
 * - CA2: El evento usa un canal privado order.{orderId}, exclusivo de esa orden
 * - CA3: El payload contiene order_id, status y updated_at
 * - CA4: El estado sigue el flujo válido: Pendiente → En Preparación → Listo → Entregado
 * - CA5: El cliente recibe notificación cuando el estado cambia a "Listo" (Ready)
 */
class OrderStatusUpdatedBroadcastTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;
    protected $manager;
    protected $local;
    protected $product;
    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->client  = User::factory()->create();
        $this->manager = User::factory()->manager()->create();
        $this->local   = Local::factory()->create();
        $this->local->users()->attach($this->manager->user_id);

        $this->product = Product::factory()->create([
            'price'  => 50.00,
            'name'   => 'Producto Estado Test',
            'status' => 'Available',
        ]);
        $this->product->locals()->attach($this->local->local_id, ['is_available' => 1]);

        $this->order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'quantity'     => 2,
            'origin'       => 'web',
        ]);
        $this->order->user()->attach($this->client->user_id);

        OrderItem::create([
            'order_id'   => $this->order->order_id,
            'product_id' => $this->product->product_id,
            'quantity'   => 2,
            'price'      => $this->product->price,
        ]);
    }

    // -------------------------------------------------------------------------
    // CA1 | ShouldBroadcastNow — entrega sincrónica sin cola
    // -------------------------------------------------------------------------

    /**
     * CA1-01 | OrderStatusUpdated implementa ShouldBroadcastNow [Positivo]
     *
     * Objetivo: Verificar que el cambio de estado llega al cliente inmediatamente,
     * sin pasar por una cola de trabajo que podría retrasarlo.
     */
    public function test_order_status_updated_implements_should_broadcast_now()
    {
        $event = new OrderStatusUpdated(
            $this->order->order_id,
            Order::STATUS_PREPARATION,
            now()->toIso8601String()
        );

        $this->assertInstanceOf(ShouldBroadcastNow::class, $event);
    }

    // -------------------------------------------------------------------------
    // CA2 | Canal privado order.{orderId}
    // -------------------------------------------------------------------------

    /**
     * CA2-01 | OrderStatusUpdated usa un PrivateChannel con el ID de la orden [Positivo]
     *
     * Objetivo: Verificar que el canal es privado (requiere autenticación Pusher)
     * y está ligado al order_id, no a datos de otro usuario u orden.
     */
    public function test_event_broadcasts_on_private_channel_for_order()
    {
        $event   = new OrderStatusUpdated(
            $this->order->order_id,
            Order::STATUS_PREPARATION,
            now()->toIso8601String()
        );
        $channel = $event->broadcastOn();

        // PrivateChannel antepone 'private-' al nombre internamente (comportamiento de Laravel).
        // El cliente JS usa Echo.private('order.X') que resuelve al mismo canal.
        $this->assertInstanceOf(PrivateChannel::class, $channel);
        $this->assertEquals('private-order.' . $this->order->order_id, $channel->name);
    }

    /**
     * CA2-02 | Órdenes distintas usan canales privados distintos [Positivo]
     *
     * Objetivo: Verificar que el cliente A no puede escuchar el estado de la
     * orden del cliente B (cada orden tiene su propio canal privado).
     */
    public function test_different_orders_use_different_private_channels()
    {
        $otherOrder = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 60.00,
            'quantity'     => 1,
            'origin'       => 'web',
        ]);

        $eventA = new OrderStatusUpdated(
            $this->order->order_id,
            Order::STATUS_PREPARATION,
            now()->toIso8601String()
        );
        $eventB = new OrderStatusUpdated(
            $otherOrder->order_id,
            Order::STATUS_PREPARATION,
            now()->toIso8601String()
        );

        $this->assertNotEquals($eventA->broadcastOn()->name, $eventB->broadcastOn()->name);
        $this->assertEquals('private-order.' . $this->order->order_id, $eventA->broadcastOn()->name);
        $this->assertEquals('private-order.' . $otherOrder->order_id, $eventB->broadcastOn()->name);
    }

    // -------------------------------------------------------------------------
    // CA3 | Payload correcto
    // -------------------------------------------------------------------------

    /**
     * CA3-01 | El payload contiene order_id, status y updated_at [Positivo]
     *
     * Objetivo: Verificar que el JS del cliente recibe todos los campos necesarios
     * para actualizar la UI sin hacer un request adicional al servidor.
     */
    public function test_event_has_correct_broadcast_payload()
    {
        $timestamp = now()->toIso8601String();

        $event   = new OrderStatusUpdated(
            $this->order->order_id,
            Order::STATUS_PREPARATION,
            $timestamp
        );
        $payload = $event->broadcastWith();

        $this->assertArrayHasKey('order_id', $payload);
        $this->assertArrayHasKey('status', $payload);
        $this->assertArrayHasKey('updated_at', $payload);
        $this->assertEquals($this->order->order_id, $payload['order_id']);
        $this->assertEquals(Order::STATUS_PREPARATION, $payload['status']);
        $this->assertEquals($timestamp, $payload['updated_at']);
    }

    /**
     * CA3-02 | El payload refleja el nuevo estado correcto en cada transición [Positivo]
     *
     * Objetivo: Verificar que el status en el payload corresponde exactamente
     * al estado al que cambió la orden, para todos los estados del flujo.
     */
    public function test_payload_status_matches_each_valid_state()
    {
        $transitions = [
            Order::STATUS_PREPARATION,
            Order::STATUS_READY,
            Order::STATUS_DELIVERED,
        ];

        foreach ($transitions as $status) {
            $event   = new OrderStatusUpdated($this->order->order_id, $status, now()->toIso8601String());
            $payload = $event->broadcastWith();

            $this->assertEquals(
                $status,
                $payload['status'],
                "El payload debe reflejar el estado: {$status}"
            );
        }
    }

    // -------------------------------------------------------------------------
    // CA4 | Flujo de estados Pendiente → En Preparación → Listo → Entregado
    // -------------------------------------------------------------------------

    /**
     * CA4-01 | Gerente puede cambiar Pendiente → En Preparación [Positivo]
     *
     * Objetivo: Verificar el primer paso del flujo: el gerente acepta la orden.
     */
    public function test_manager_can_change_status_from_pending_to_preparing()
    {
        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_PREPARATION,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(
            Order::STATUS_PREPARATION,
            Order::find($this->order->order_id)->status
        );
    }

    /**
     * CA4-02 | Gerente puede cambiar En Preparación → Listo [Positivo]
     *
     * Objetivo: Verificar el segundo paso del flujo: la orden está lista para retirar.
     */
    public function test_manager_can_change_status_from_preparing_to_ready()
    {
        $this->order->update(['status' => Order::STATUS_PREPARATION]);

        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_READY,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(
            Order::STATUS_READY,
            Order::find($this->order->order_id)->status
        );
    }

    /**
     * CA4-03 | No se puede saltar de Pendiente a Listo [Negativo]
     *
     * Objetivo: Verificar que el sistema impide saltar estados intermedios,
     * manteniendo la integridad del flujo de la orden.
     */
    public function test_cannot_skip_status_from_pending_to_ready()
    {
        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_READY,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);

        $this->assertEquals(
            Order::STATUS_PENDING,
            Order::find($this->order->order_id)->status
        );
    }

    /**
     * CA4-04 | No se puede cambiar el estado de una orden Entregada [Negativo]
     *
     * Objetivo: Verificar que el estado Entregado es terminal — no admite
     * ningún cambio posterior.
     */
    public function test_cannot_change_status_of_delivered_order()
    {
        $this->order->update(['status' => Order::STATUS_DELIVERED]);

        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_CANCELLED,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /**
     * CA4-05 | No se puede cambiar el estado de una orden Cancelada [Negativo]
     *
     * Objetivo: Verificar que el estado Cancelado es terminal — no admite
     * ningún reactivación posterior.
     */
    public function test_cannot_change_status_of_cancelled_order()
    {
        $this->order->update([
            'status'              => Order::STATUS_CANCELLED,
            'cancellation_reason' => 'Cancelada por el cliente',
        ]);

        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_PREPARATION,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /**
     * CA4-06 | Estado inválido es rechazado [Negativo]
     *
     * Objetivo: Verificar que el endpoint no acepta valores arbitrarios como
     * estado, solo los estados del sistema.
     */
    public function test_invalid_status_value_is_rejected()
    {
        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => 'EnVuelo',
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    // -------------------------------------------------------------------------
    // CA5 | Notificación al cliente cuando el estado cambia a "Listo"
    // -------------------------------------------------------------------------

    /**
     * CA5-01 | El broadcast se emite cuando el estado cambia a "Listo" [Positivo]
     *
     * Objetivo: Verificar que el cliente recibe la notificación en tiempo real
     * cuando el gerente marca la orden como lista para retirar.
     * Este es el cambio de estado más crítico para la experiencia del cliente.
     */
    public function test_broadcast_dispatched_when_status_changes_to_ready()
    {
        $broadcastSpy = $this->spy(BroadcastFactory::class);

        $this->order->update(['status' => Order::STATUS_PREPARATION]);

        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_READY,
            ]);

        $response->assertStatus(200);

        $broadcastSpy->shouldHaveReceived('event')
            ->once()
            ->with(\Mockery::on(function ($event) {
                return $event instanceof OrderStatusUpdated
                    && $event->orderId === $this->order->order_id
                    && $event->status  === Order::STATUS_READY;
            }));
    }

    /**
     * CA5-02 | El evento emitido en "Listo" contiene el canal privado correcto [Positivo]
     *
     * Objetivo: Verificar que la notificación de "Listo" llega exclusivamente
     * al canal privado del cliente dueño de la orden, no a otros clientes.
     */
    public function test_ready_notification_targets_correct_private_channel()
    {
        $timestamp = now()->toIso8601String();

        $event   = new OrderStatusUpdated(
            $this->order->order_id,
            Order::STATUS_READY,
            $timestamp
        );
        $channel = $event->broadcastOn();

        $this->assertInstanceOf(PrivateChannel::class, $channel);
        $this->assertEquals('private-order.' . $this->order->order_id, $channel->name);

        $payload = $event->broadcastWith();
        $this->assertEquals(Order::STATUS_READY, $payload['status']);
    }

    /**
     * CA5-03 | El broadcast se emite en el cambio Pendiente → En Preparación [Positivo]
     *
     * Objetivo: Verificar que el cliente también recibe la actualización cuando
     * el gerente acepta y comienza a preparar la orden.
     */
    public function test_broadcast_dispatched_on_pending_to_preparing_change()
    {
        $broadcastSpy = $this->spy(BroadcastFactory::class);

        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_PREPARATION,
            ]);

        $response->assertStatus(200);

        $broadcastSpy->shouldHaveReceived('event')
            ->once()
            ->with(\Mockery::on(function ($event) {
                return $event instanceof OrderStatusUpdated
                    && $event->orderId === $this->order->order_id
                    && $event->status  === Order::STATUS_PREPARATION;
            }));
    }

    /**
     * CA5-04 | El broadcast NO se emite cuando la transición es inválida [Negativo]
     *
     * Objetivo: Verificar que el cliente no recibe notificaciones falsas
     * cuando el gerente intenta un cambio de estado inválido.
     */
    public function test_broadcast_not_dispatched_on_invalid_status_transition()
    {
        $broadcastSpy = $this->spy(BroadcastFactory::class);

        // Pending → Ready es inválido (debe pasar por Preparing)
        $response = $this->actingAs($this->manager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_READY,
            ]);

        $response->assertStatus(422);

        $broadcastSpy->shouldNotHaveReceived('event');
    }

    // -------------------------------------------------------------------------
    // Seguridad | Solo el gerente del local puede cambiar el estado
    // -------------------------------------------------------------------------

    /**
     * SEG-01 | El cliente no puede cambiar el estado de su propia orden [Negativo]
     *
     * Objetivo: Verificar que la ruta de cambio de estado está protegida por
     * el middleware admin.local — los clientes reciben 403.
     */
    public function test_client_cannot_change_order_status()
    {
        $response = $this->actingAs($this->client)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_PREPARATION,
            ]);

        $response->assertStatus(403);

        $this->assertEquals(
            Order::STATUS_PENDING,
            Order::find($this->order->order_id)->status
        );
    }

    /**
     * SEG-02 | Un gerente no puede cambiar el estado de la orden de otro local [Negativo]
     *
     * Objetivo: Verificar que el gerente de Local A no puede manipular las
     * órdenes del Local B.
     */
    public function test_manager_cannot_change_status_of_another_locals_order()
    {
        $otherLocal   = Local::factory()->create();
        $otherManager = User::factory()->manager()->create();
        $otherLocal->users()->attach($otherManager->user_id);

        $response = $this->actingAs($otherManager)
            ->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
                'status' => Order::STATUS_PREPARATION,
            ]);

        $response->assertStatus(403);

        $this->assertEquals(
            Order::STATUS_PENDING,
            Order::find($this->order->order_id)->status
        );
    }

    /**
     * SEG-03 | Usuario no autenticado no puede cambiar el estado [Negativo]
     *
     * Objetivo: Verificar que el endpoint rechaza peticiones sin sesión activa.
     */
    public function test_unauthenticated_user_cannot_change_order_status()
    {
        $response = $this->postJson("/ordenes/{$this->order->order_id}/cambiar-estado", [
            'status' => Order::STATUS_PREPARATION,
        ]);

        $response->assertStatus(401);
    }
}
