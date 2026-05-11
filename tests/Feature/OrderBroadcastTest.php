<?php

namespace Tests\Feature;

use App\Events\NewOrderPlaced;
use App\Events\OrderCancelled;
use App\Models\Local;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * QA G4DS-261 | Broadcast de eventos de órdenes en tiempo real
 *
 * Historia de usuario: Como gerente, quiero recibir notificaciones y ver los pedidos
 * nuevos o cancelados en tiempo real, sin necesitar recargar la página.
 *
 * Criterios de aceptación:
 * - CA1: Al confirmar una orden, se emite el evento NewOrderPlaced hacia el gerente
 * - CA2: Los eventos contienen el payload correcto (order_id, order_number, local_id, etc.)
 * - CA3: Al cancelar una orden, se emite el evento OrderCancelled hacia el gerente
 * - CA4: Los eventos implementan ShouldBroadcastNow (entrega sincrónica, sin cola)
 * - CA5: Los eventos usan el canal orders.{localId}; la vista del gerente recibe localId
 */
class OrderBroadcastTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;
    protected $manager;
    protected $local;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->client  = User::factory()->create();
        $this->manager = User::factory()->manager()->create();
        $this->local   = Local::factory()->create();
        $this->local->users()->attach($this->manager->user_id);

        $this->product = Product::factory()->create([
            'price'  => 50.00,
            'name'   => 'Producto Broadcast Test',
            'status' => 'Available',
        ]);
        $this->product->locals()->attach($this->local->local_id, ['is_available' => 1]);
    }

    // -------------------------------------------------------------------------
    // CA4 | Implementación de ShouldBroadcastNow
    // -------------------------------------------------------------------------

    /**
     * CA4-01 | NewOrderPlaced implementa ShouldBroadcastNow [Positivo]
     *
     * Objetivo: Verificar que la orden nueva se emite sincrónicamnte (sin encolar).
     */
    public function test_new_order_placed_implements_should_broadcast_now()
    {
        $order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 50.00,
            'quantity'     => 1,
            'time'         => now()->toTimeString(),
        ]);

        $event = new NewOrderPlaced($order, 'Cliente Test', [
            ['product_name' => 'Producto Broadcast Test', 'quantity' => 1],
        ]);

        $this->assertInstanceOf(ShouldBroadcastNow::class, $event);
    }

    /**
     * CA4-02 | OrderCancelled implementa ShouldBroadcastNow [Positivo]
     *
     * Objetivo: Verificar que la cancelación se emite sincrónicamnte (sin encolar).
     */
    public function test_order_cancelled_implements_should_broadcast_now()
    {
        $order = Order::factory()->create([
            'local_id'            => $this->local->local_id,
            'status'              => Order::STATUS_CANCELLED,
            'total_amount'        => 50.00,
            'cancellation_reason' => 'Cambié de opinión',
        ]);

        $event = new OrderCancelled($order, 'Cliente Test');

        $this->assertInstanceOf(ShouldBroadcastNow::class, $event);
    }

    // -------------------------------------------------------------------------
    // CA5 | Canal correcto orders.{localId} y localId en la vista del gerente
    // -------------------------------------------------------------------------

    /**
     * CA5-01 | NewOrderPlaced usa canal orders.{localId} [Positivo]
     *
     * Objetivo: Verificar que el canal de broadcast apunta al local correcto.
     */
    public function test_new_order_placed_broadcasts_on_correct_channel()
    {
        $order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 50.00,
            'quantity'     => 1,
            'time'         => now()->toTimeString(),
        ]);

        $event   = new NewOrderPlaced($order, 'Cliente Test', []);
        $channel = $event->broadcastOn();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('orders.' . $this->local->local_id, $channel->name);
    }

    /**
     * CA5-02 | OrderCancelled usa canal orders.{localId} [Positivo]
     *
     * Objetivo: Verificar que la cancelación se emite al canal correcto del local.
     */
    public function test_order_cancelled_broadcasts_on_correct_channel()
    {
        $order = Order::factory()->create([
            'local_id'            => $this->local->local_id,
            'status'              => Order::STATUS_CANCELLED,
            'total_amount'        => 50.00,
            'cancellation_reason' => 'No necesito',
        ]);

        $event   = new OrderCancelled($order, 'Cliente Test');
        $channel = $event->broadcastOn();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('orders.' . $this->local->local_id, $channel->name);
    }

    /**
     * CA5-03 | Locales distintos usan canales distintos [Positivo]
     *
     * Objetivo: Verificar que dos locales no comparten el mismo canal de broadcast.
     */
    public function test_different_locals_use_different_broadcast_channels()
    {
        $localA = Local::factory()->create();
        $localB = Local::factory()->create();

        $orderA = Order::factory()->create([
            'local_id'     => $localA->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 50.00,
            'quantity'     => 1,
            'time'         => now()->toTimeString(),
        ]);
        $orderB = Order::factory()->create([
            'local_id'     => $localB->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 80.00,
            'quantity'     => 2,
            'time'         => now()->toTimeString(),
        ]);

        $channelA = (new NewOrderPlaced($orderA, 'Cliente A', []))->broadcastOn();
        $channelB = (new NewOrderPlaced($orderB, 'Cliente B', []))->broadcastOn();

        $this->assertNotEquals($channelA->name, $channelB->name);
        $this->assertEquals('orders.' . $localA->local_id, $channelA->name);
        $this->assertEquals('orders.' . $localB->local_id, $channelB->name);
    }

    /**
     * CA5-04 | OrderController pasa localId correcto a la vista para el Gerente [Positivo]
     *
     * Objetivo: Verificar que la vista del gerente recibe el localId para inicializar
     * el listener de WebSocket en el canal correcto.
     */
    public function test_order_controller_passes_local_id_to_view_for_manager()
    {
        $response = $this->actingAs($this->manager)->get('/ordenes');

        $response->assertStatus(200);
        $response->assertViewHas('localId', $this->local->local_id);
    }

    /**
     * CA5-05 | OrderController protege la ruta: solo Gerente puede acceder [Negativo]
     *
     * Objetivo: Verificar que un usuario con rol de Admin Global no puede acceder
     * a la vista de órdenes del gerente (la ruta tiene middleware admin.local).
     * Esto garantiza que el localId nunca se filtre a usuarios no autorizados.
     */
    public function test_order_controller_blocks_non_manager_from_orders_view()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/ordenes');

        $response->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // CA2 | Payload correcto en los eventos
    // -------------------------------------------------------------------------

    /**
     * CA2-01 | NewOrderPlaced contiene el payload completo [Positivo]
     *
     * Objetivo: Verificar que broadcastWith() retorna todos los campos que el JS
     * del gerente necesita para renderizar la tarjeta de orden nueva.
     */
    public function test_new_order_placed_has_correct_broadcast_payload()
    {
        $order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 150.00,
            'quantity'     => 3,
            'time'         => '12:30:00',
        ]);

        $items = [
            ['product_name' => 'Empanada', 'quantity' => 2],
            ['product_name' => 'Refresco', 'quantity' => 1],
        ];

        $event   = new NewOrderPlaced($order, 'Juan Pérez', $items);
        $payload = $event->broadcastWith();

        $this->assertEquals($order->order_id, $payload['order_id']);
        $this->assertEquals($order->order_number, $payload['order_number']);
        $this->assertEquals($this->local->local_id, $payload['local_id']);
        $this->assertEquals('Juan Pérez', $payload['customer_name']);
        $this->assertEquals(150.00, $payload['total_amount']);
        $this->assertEquals(3, $payload['quantity']);
        $this->assertEquals($items, $payload['items']);
        $this->assertArrayHasKey('message', $payload);
        $this->assertStringContainsString($order->order_number, $payload['message']);
    }

    /**
     * CA2-02 | OrderCancelled contiene el payload completo [Positivo]
     *
     * Objetivo: Verificar que broadcastWith() retorna todos los campos que el JS
     * del gerente necesita para mover la tarjeta al tab de canceladas.
     */
    public function test_order_cancelled_has_correct_broadcast_payload()
    {
        $order = Order::factory()->create([
            'local_id'            => $this->local->local_id,
            'status'              => Order::STATUS_CANCELLED,
            'total_amount'        => 100.00,
            'cancellation_reason' => 'No me gustó la espera',
        ]);

        $event   = new OrderCancelled($order, 'María García');
        $payload = $event->broadcastWith();

        $this->assertEquals($order->order_id, $payload['order_id']);
        $this->assertEquals($order->order_number, $payload['order_number']);
        $this->assertEquals($this->local->local_id, $payload['local_id']);
        $this->assertEquals('María García', $payload['customer_name']);
        $this->assertEquals('No me gustó la espera', $payload['reason']);
        $this->assertArrayHasKey('message', $payload);
        $this->assertStringContainsString('María García', $payload['message']);
    }

    /**
     * CA2-03 | OrderCancelled usa razón default si no hay motivo [Positivo]
     *
     * Objetivo: Verificar que el evento no emite un reason vacío/nulo aunque
     * el cliente no haya ingresado un motivo.
     */
    public function test_order_cancelled_uses_default_reason_when_none_provided()
    {
        $order = Order::factory()->create([
            'local_id'            => $this->local->local_id,
            'status'              => Order::STATUS_CANCELLED,
            'total_amount'        => 50.00,
            'cancellation_reason' => null,
        ]);

        $event   = new OrderCancelled($order, 'Cliente Test');
        $payload = $event->broadcastWith();

        $this->assertNotEmpty($payload['reason']);
        $this->assertEquals('Cancelada por el cliente', $payload['reason']);
    }

    // -------------------------------------------------------------------------
    // CA1 | NewOrderPlaced se emite al crear una orden
    // -------------------------------------------------------------------------

    /**
     * CA1-01 | NewOrderPlaced se emite al hacer broadcast de una orden nueva [Positivo]
     *
     * Objetivo: Verificar que al llamar broadcast(new NewOrderPlaced(...)) la
     * infraestructura de broadcasting recibe el evento con los datos correctos.
     * El flujo HTTP completo (QR + GPS + horario) se valida en pruebas E2E de staging.
     */
    public function test_new_order_placed_event_is_sent_to_broadcaster()
    {
        $broadcastSpy = $this->spy(BroadcastFactory::class);

        $order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 50.00,
            'quantity'     => 1,
            'time'         => now()->toTimeString(),
        ]);

        $items = [['product_name' => 'Producto Test', 'quantity' => 1]];

        broadcast(new NewOrderPlaced(
            $order,
            $this->client->full_name ?? 'Cliente',
            $items
        ));

        $broadcastSpy->shouldHaveReceived('event')
            ->once()
            ->with(\Mockery::on(function ($event) use ($order) {
                return $event instanceof NewOrderPlaced
                    && $event->orderId  === $order->order_id
                    && $event->localId  === $this->local->local_id;
            }));
    }

    /**
     * CA1-02 | NewOrderPlaced lleva el localId del local donde se hizo la orden [Positivo]
     *
     * Objetivo: Verificar que el evento se emite hacia el canal del local correcto,
     * no al canal de otro local.
     */
    public function test_new_order_placed_event_targets_correct_local()
    {
        $otherLocal = Local::factory()->create();

        $orderForThisLocal = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 50.00,
            'quantity'     => 1,
            'time'         => now()->toTimeString(),
        ]);

        $event = new NewOrderPlaced($orderForThisLocal, 'Cliente', []);

        $this->assertEquals($this->local->local_id, $event->localId);
        $this->assertNotEquals($otherLocal->local_id, $event->localId);
        $this->assertEquals('orders.' . $this->local->local_id, $event->broadcastOn()->name);
    }

    // -------------------------------------------------------------------------
    // CA3 | OrderCancelled se emite al cancelar una orden
    // -------------------------------------------------------------------------

    /**
     * CA3-01 | OrderCancelled se emite al cancelar una orden Pending [Positivo]
     *
     * Objetivo: Verificar que cuando el cliente cancela, el evento OrderCancelled
     * llega al broadcaster con los datos de la orden correcta.
     */
    public function test_order_cancelled_event_is_dispatched_on_cancel()
    {
        $broadcastSpy = $this->spy(BroadcastFactory::class);

        $order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin'       => 'web',
        ]);
        $order->user()->attach($this->client->user_id);

        OrderItem::create([
            'order_id'   => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity'   => 2,
            'price'      => $this->product->price,
        ]);

        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Cambié de opinión',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $broadcastSpy->shouldHaveReceived('event')
            ->once()
            ->with(\Mockery::on(function ($event) use ($order) {
                return $event instanceof OrderCancelled
                    && $event->orderId === $order->order_id
                    && $event->localId === $this->local->local_id;
            }));
    }

    /**
     * CA3-02 | OrderCancelled NO se emite si la orden no es cancelable [Negativo]
     *
     * Objetivo: Verificar que el evento no se dispara cuando la cancelación falla
     * (orden ya en preparación), evitando notificaciones falsas al gerente.
     */
    public function test_order_cancelled_event_not_dispatched_when_order_not_cancellable()
    {
        $broadcastSpy = $this->spy(BroadcastFactory::class);

        $order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PREPARATION,
            'total_amount' => 100.00,
            'origin'       => 'web',
        ]);
        $order->user()->attach($this->client->user_id);

        OrderItem::create([
            'order_id'   => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity'   => 1,
            'price'      => $this->product->price,
        ]);

        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Intento inválido',
            ]);

        $response->assertStatus(422);

        $broadcastSpy->shouldNotHaveReceived('event');
    }

    /**
     * CA3-03 | El evento OrderCancelled lleva el motivo real de cancelación [Positivo]
     *
     * Objetivo: Verificar que el gerente recibe en el payload el motivo exacto
     * que el cliente ingresó, para entender la razón de la cancelación.
     */
    public function test_order_cancelled_event_payload_includes_cancellation_reason()
    {
        $broadcastSpy = $this->spy(BroadcastFactory::class);

        $order = Order::factory()->create([
            'local_id'     => $this->local->local_id,
            'status'       => Order::STATUS_PENDING,
            'total_amount' => 75.00,
            'origin'       => 'web',
        ]);
        $order->user()->attach($this->client->user_id);

        OrderItem::create([
            'order_id'   => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity'   => 1,
            'price'      => $this->product->price,
        ]);

        $reason = 'El local tardaba demasiado';

        $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => $reason,
            ]);

        $broadcastSpy->shouldHaveReceived('event')
            ->once()
            ->with(\Mockery::on(function ($event) use ($reason) {
                return $event instanceof OrderCancelled
                    && $event->reason === $reason;
            }));
    }

    /**
     * CA3-04 | OrderCancelled lleva el nombre del cliente que canceló [Positivo]
     *
     * Objetivo: Verificar que el gerente puede identificar en la notificación
     * qué cliente realizó la cancelación.
     */
    public function test_order_cancelled_event_includes_customer_name()
    {
        $order = Order::factory()->create([
            'local_id'            => $this->local->local_id,
            'status'              => Order::STATUS_CANCELLED,
            'total_amount'        => 80.00,
            'cancellation_reason' => 'Error en el pedido',
        ]);

        $customerName = 'Ana Rodríguez';
        $event        = new OrderCancelled($order, $customerName);
        $payload      = $event->broadcastWith();

        $this->assertEquals($customerName, $payload['customer_name']);
        $this->assertStringContainsString($customerName, $payload['message']);
    }
}
