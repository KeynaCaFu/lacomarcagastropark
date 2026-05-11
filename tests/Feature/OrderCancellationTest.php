<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Local;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * QA-199 | Pruebas de cancelación de pedidos del cliente
 * 
 * Historia de usuario: Como cliente, quiero cancelar un pedido mientras esté en estado 'Pendiente', 
 * para corregir errores sin costo adicional.
 * 
 * Requisitos funcionales:
 * - En Mis órdenes el cliente ve una opción de cancelar una orden siempre y cuando esté en estado Pendiente
 * - Al cancelar, podrá volver a cargar el carrito con la orden que está cancelando
 * - La opción de cancelar no está disponible si el pedido ya cambió a "En preparación"
 * - El panel del local refleja el pedido como cancelado
 */
class OrderCancellationTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;
    protected $localManager;
    protected $local;
    protected $product;
    protected $order;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear cliente autenticado
        $this->client = User::factory()->create();
        
        // Crear gerente del local
        $this->localManager = User::factory()->create();
        
        // Crear local
        $this->local = Local::factory()->create();
        $this->local->users()->attach($this->localManager->user_id);
        
        // Crear producto
        $this->product = Product::factory()->create([
            'price' => 50.00,
            'name' => 'Producto Test',
        ]);
        $this->product->locals()->attach($this->local->local_id);
    }

    /**
     * CP-01-01 | Cancelación exitosa - Estado de orden actualizado [Positivo]
     * 
     * Objetivo: Verificar que una orden en estado "Pendiente" puede ser cancelada exitosamente
     * Precondiciones: Cliente autenticado con una orden en estado Pending
     */
    public function test_client_can_cancel_pending_order_successfully()
    {
        // Arrange: Crear orden en estado Pending
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        // Crear items en la orden
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 2,
            'price' => $this->product->price,
        ]);
        
        // Verificar estado inicial
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        
        // Act: Cliente auténticado cancela la orden
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Cambié de opinión'
            ]);
        
        // Assert: Verificar respuesta exitosa
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 1
        ]);
        
        // Verificar que el estado de la orden cambió a Cancelled
        $cancelledOrder = Order::find($order->order_id);
        $this->assertEquals(Order::STATUS_CANCELLED, $cancelledOrder->status);
        $this->assertNotNull($cancelledOrder->cancellation_reason);
    }

    /**
     * CP-01-02 | Cancelación exitosa - Items devueltos al carrito [Positivo]
     * 
     * Objetivo: Verificar que al cancelar una orden, los items se devuelven al carrito de sesión
     * Precondiciones: Cliente autenticado con una orden Pending con múltiples items
     */
    public function test_cancelled_order_items_are_restored_to_cart()
    {
        // Arrange: Crear orden con múltiples items
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 200.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        // Crear múltiples items
        for ($i = 0; $i < 3; $i++) {
            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $this->product->product_id,
                'quantity' => $i + 1,
                'price' => $this->product->price,
                'customization' => 'Sin cebolla' . ($i > 0 ? ' - Variante ' . $i : '')
            ]);
        }
        
        // Act: Cancelar la orden
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'No necesito'
            ]);
        
        // Assert: Respuesta exitosa
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Verificar que los datos en la respuesta indican los items restaurados
        $data = $response->json();
        $this->assertTrue($data['success']);
        
        // Verificar que la orden está cancelada
        $this->assertEquals(
            Order::STATUS_CANCELLED,
            Order::find($order->order_id)->status
        );
    }

    /**
     * CP-01-03 | Cancelación exitosa - Confirmación con motivo [Positivo]
     * 
     * Objetivo: Verificar que se puede incluir un motivo al cancelar
     * Precondiciones: Cliente con orden Pending
     */
    public function test_client_can_cancel_order_with_reason()
    {
        // Arrange
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 50.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        $cancellationReason = 'Cambié de opinión sobre el producto';
        
        // Act: Cancelar con motivo
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => $cancellationReason
            ]);
        
        // Assert
        $response->assertStatus(200);
        
        $cancelledOrder = Order::find($order->order_id);
        $this->assertEquals(Order::STATUS_CANCELLED, $cancelledOrder->status);
        $this->assertEquals($cancellationReason, $cancelledOrder->cancellation_reason);
    }

    /**
     * CP-01-04 | Cancelación exitosa - Sin motivo (usa default) [Positivo]
     * 
     * Objetivo: Verificar que se puede cancelar sin proporcionar un motivo
     * Precondiciones: Cliente con orden Pending
     */
    public function test_client_can_cancel_order_without_reason()
    {
        // Arrange
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 75.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act: Cancelar sin motivo
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", []);
        
        // Assert
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $cancelledOrder = Order::find($order->order_id);
        $this->assertEquals(Order::STATUS_CANCELLED, $cancelledOrder->status);
        $this->assertNotNull($cancelledOrder->cancellation_reason); // Debe tener un motivo default
    }

    /**
     * CP-02-01 | Intento de cancelación con estado "En preparación" [Negativo]
     * 
     * Objetivo: Verificar que NO se puede cancelar una orden en estado "En preparación"
     * Precondiciones: Orden en estado Preparing
     */
    public function test_cannot_cancel_order_in_preparing_state()
    {
        // Arrange: Crear orden en estado Preparing
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PREPARATION,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act: Intentar cancelar la orden
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Quiero cancelar'
            ]);
        
        // Assert: Verificar que falla
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $response->assertJsonFragment([
            'message' => 'No se puede cancelar una orden que ya está en En Preparación'
        ]);
        
        // Verificar que el estado NO cambió
        $this->assertEquals(
            Order::STATUS_PREPARATION,
            Order::find($order->order_id)->status
        );
    }

    /**
     * CP-02-02 | Intento de cancelación con estado "Listo" [Negativo]
     * 
     * Objetivo: Verificar que NO se puede cancelar una orden en estado "Listo"
     * Precondiciones: Orden en estado Ready
     */
    public function test_cannot_cancel_order_in_ready_state()
    {
        // Arrange: Crear orden en estado Ready
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_READY,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act: Intentar cancelar
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Quiero cancelar'
            ]);
        
        // Assert
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        
        // Verificar que el estado NO cambió
        $this->assertEquals(Order::STATUS_READY, Order::find($order->order_id)->status);
    }

    /**
     * CP-02-03 | Intento de cancelación con estado "Entregado" [Negativo]
     * 
     * Objetivo: Verificar que NO se puede cancelar una orden en estado "Entregado"
     * Precondiciones: Orden en estado Delivered
     */
    public function test_cannot_cancel_order_in_delivered_state()
    {
        // Arrange: Crear orden en estado Delivered
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_DELIVERED,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act: Intentar cancelar
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Quiero cancelar'
            ]);
        
        // Assert
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        
        // Verificar que el estado NO cambió
        $this->assertEquals(Order::STATUS_DELIVERED, Order::find($order->order_id)->status);
    }

    /**
     * CP-02-04 | Intento de cancelación de orden ya cancelada [Negativo]
     * 
     * Objetivo: Verificar que NO se puede cancelar nuevamente una orden ya cancelada
     * Precondiciones: Orden en estado Cancelled
     */
    public function test_cannot_cancel_already_cancelled_order()
    {
        // Arrange: Crear orden ya cancelada
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_CANCELLED,
            'total_amount' => 100.00,
            'cancellation_reason' => 'Cancelada previamente',
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act: Intentar cancelar nuevamente
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Quiero cancelar'
            ]);
        
        // Assert
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /**
     * CP-02-05 | Validación de permisos - Usuario no autenticado [Negativo]
     * 
     * Objetivo: Verificar que un usuario no autenticado NO puede cancelar una orden
     * Precondiciones: Orden existente, usuario no autenticado
     */
    public function test_unauthenticated_user_cannot_cancel_order()
    {
        // Arrange: Crear orden
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        // Act: Intentar cancelar sin autenticación
        $response = $this->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
            'reason' => 'Quiero cancelar'
        ]);
        
        // Assert
        $response->assertStatus(401);
    }

    /**
     * CP-02-06 | Validación de permisos - Usuario diferente [Negativo]
     * 
     * Objetivo: Verificar que un usuario diferente NO puede cancelar la orden de otro
     * Precondiciones: Dos usuarios, uno con orden Pending
     */
    public function test_different_user_cannot_cancel_another_user_order()
    {
        // Arrange: Crear otro usuario
        $otherUser = User::factory()->create();
        
        // Crear orden del cliente original
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act: Otro usuario intenta cancelar la orden
        $response = $this->actingAs($otherUser)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Intento de cancelación no autorizado'
            ]);
        
        // Assert
        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonFragment(['message' => 'No tienes permiso para cancelar esta orden']);
        
        // Verificar que el estado NO cambió
        $this->assertEquals(Order::STATUS_PENDING, Order::find($order->order_id)->status);
    }

    /**
     * CP-03-01 | Notificación al local - Orden aparece como cancelada [Positivo]
     * 
     * Objetivo: Verificar que después de cancelar, la orden aparece con status Cancelled en BD
     * Precondiciones: Cliente cancela orden Pending
     */
    public function test_cancelled_order_appears_in_local_dashboard_as_cancelled()
    {
        // Arrange: Crear orden Pending
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 2,
            'price' => $this->product->price,
        ]);
        
        // Act: Cliente cancela la orden
        $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => 'Error en el pedido'
            ]);
        
        // Assert: Verificar que el local puede verla como cancelada
        $cancelledOrders = Order::where('local_id', $this->local->local_id)
            ->where('status', Order::STATUS_CANCELLED)
            ->get();
        
        $this->assertGreaterThanOrEqual(1, $cancelledOrders->count());
        
        $foundOrder = $cancelledOrders->firstWhere('order_id', $order->order_id);
        $this->assertNotNull($foundOrder);
        $this->assertEquals(Order::STATUS_CANCELLED, $foundOrder->status);
        $this->assertNotNull($foundOrder->cancellation_reason);
    }

    /**
     * CP-03-02 | Notificación al local - Contiene motivo de cancelación [Positivo]
     * 
     * Objetivo: Verificar que el motivo de cancelación se registra para que el local lo vea
     * Precondiciones: Cliente cancela con motivo
     */
    public function test_cancellation_reason_is_recorded_for_local_view()
    {
        // Arrange
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        $reason = 'Producto no disponible en la zona';
        
        // Act: Cancelar con motivo
        $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => $reason
            ]);
        
        // Assert: El local puede ver el motivo
        $cancelledOrder = Order::find($order->order_id);
        $this->assertEquals($reason, $cancelledOrder->cancellation_reason);
        $this->assertNotNull($cancelledOrder->cancellation_reason);
    }

    /**
     * CP-03-03 | Notificación al local - Orden se elimina de pedidos activos [Positivo]
     * 
     * Objetivo: Verificar que la orden cancelada NO aparece en los pedidos activos del local
     * Precondiciones: Local con órdenes canceladas y pendientes
     */
    public function test_cancelled_order_not_in_active_orders()
    {
        // Arrange: Crear dos órdenes para el local
        $orderPending = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $orderPending->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $orderPending->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Crear otro usuario y orden para que quede pendiente
        $otherUser = User::factory()->create();
        $orderStaysPending = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 75.00,
            'origin' => 'web'
        ]);
        $orderStaysPending->user()->attach($otherUser->user_id);
        
        OrderItem::create([
            'order_id' => $orderStaysPending->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act: Cancelar la primera orden
        $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$orderPending->order_id}", [
                'reason' => 'No necesito'
            ]);
        
        // Assert: Verificar órdenes activas (Pending) del local
        $activeOrders = Order::where('local_id', $this->local->local_id)
            ->where('status', Order::STATUS_PENDING)
            ->get();
        
        // Solo la segunda orden debe estar en Pending
        $this->assertEquals(1, $activeOrders->count());
        $this->assertEquals($orderStaysPending->order_id, $activeOrders->first()->order_id);
        $this->assertFalse($activeOrders->contains($orderPending->order_id));
        
        // Verificar que la cancelada aparece en canceladas
        $cancelledOrders = Order::where('local_id', $this->local->local_id)
            ->where('status', Order::STATUS_CANCELLED)
            ->get();
        
        $this->assertTrue($cancelledOrders->contains($orderPending->order_id));
    }

    /**
     * CP-04 | Validación de permisos - Orden no existe [Negativo]
     * 
     * Objetivo: Verificar manejo de intento de cancelación de orden inexistente
     * Precondiciones: Cliente intenta cancelar orden con ID inválido
     */
    public function test_cannot_cancel_nonexistent_order()
    {
        // Act: Intentar cancelar orden que no existe
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/99999", [
                'reason' => 'Orden fantasma'
            ]);
        
        // Assert: Debe fallar con 404
        $response->assertStatus(404);
    }

    /**
     * CP-05 | Validación de razón de cancelación - Límite de caracteres [Positivo]
     * 
     * Objetivo: Verificar que la razón de cancelación respeta el límite máximo
     * Precondiciones: Cliente intenta cancelar con razón muy larga
     */
    public function test_cancellation_reason_respects_max_length()
    {
        // Arrange
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $order->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Crear razón dentro del límite (500 caracteres)
        $validReason = str_repeat('a', 500);
        
        // Act: Cancelar con razón válida
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/carrito/api/cancelar/{$order->order_id}", [
                'reason' => $validReason
            ]);
        
        // Assert
        $response->assertStatus(200);
        
        $cancelledOrder = Order::find($order->order_id);
        $this->assertEquals(Order::STATUS_CANCELLED, $cancelledOrder->status);
    }
}
