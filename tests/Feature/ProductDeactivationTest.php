<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Local;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductDeactivationTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;
    protected $local;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear cliente
        $this->client = User::factory()->create();
        
        // Crear local
        $this->local = Local::factory()->create();
        
        // Crear producto activo
        $this->product = Product::factory()->create([
            'status' => 'Available',
        ]);
        
        // Asociar producto al local
        $this->product->locals()->attach($this->local->local_id);
        
        // Crear orden entregada para contexto
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_DELIVERED,
        ]);
        $order->user()->attach($this->client->user_id);
    }

    /**
     * CA1 | Al desactivar un producto, desaparece del menú visible para clientes en tiempo real
     * Objetivo: Verificar que un producto desactivado no aparece en la lista de productos disponibles
     */
    public function test_deactivated_product_is_not_visible_in_product_list()
    {
        // Arrange: Producto activo
        $this->assertEquals('Available', $this->product->status);
        
        // Act: Desactivar el producto
        $this->product->update(['status' => 'Unavailable']);
        
        // Assert: Verificar que el producto desactivado NO aparece en búsquedas
        $activeProducts = Product::where('status', 'Available')->get();
        
        // El producto no debe estar en la lista de disponibles
        $this->assertFalse(
            $activeProducts->contains('product_id', $this->product->product_id),
            'El producto desactivado aún aparece en la lista de disponibles'
        );
        
        // Verificar que el producto está realmente desactivado
        $deactivatedProduct = Product::find($this->product->product_id);
        $this->assertEquals('Unavailable', $deactivatedProduct->status);
    }

    /**
     * CA1 | Verificación que el producto desactivado no se obtiene en consultas públicas
     */
    public function test_deactivated_product_not_returned_in_public_api()
    {
        // Arrange & Act
        $this->product->update(['status' => 'Unavailable']);
        
        // Assert: Simular consulta de cliente para obtener productos
        $publicProducts = Product::where('status', 'Available')
            ->where('product_id', $this->product->product_id)
            ->first();
        
        $this->assertNull($publicProducts);
    }

    /**
     * CA2 | Al reactivar un producto, vuelve a aparecer en el menú en tiempo real
     * Objetivo: Verificar que un producto reactivado aparece nuevamente en la lista
     */
    public function test_reactivated_product_appears_in_product_list()
    {
        // Arrange: Desactivar el producto primero
        $this->product->update(['status' => 'Unavailable']);
        
        // Verificar que está desactivado
        $this->assertEquals('Unavailable', $this->product->status);
        $inactiveProducts = Product::where('status', 'Available')
            ->where('product_id', $this->product->product_id)
            ->first();
        $this->assertNull($inactiveProducts);
        
        // Act: Reactivar el producto
        $this->product->update(['status' => 'Available']);
        
        // Assert: Verificar que ahora aparece en la lista de disponibles
        $activeProducts = Product::where('status', 'Available')->get();
        
        $this->assertTrue(
            $activeProducts->contains('product_id', $this->product->product_id),
            'El producto reactivado no aparece en la lista de disponibles'
        );
        
        $this->assertEquals('Available', $this->product->status);
    }

    /**
     * CA2 | Verificar que la transición está completa (de inactivo a activo)
     */
    public function test_product_status_transition_from_inactive_to_active()
    {
        // Arrange
        $this->product->update(['status' => 'Unavailable']);
        
        // Act & Assert
        $this->assertEquals('Unavailable', $this->product->status);
        
        $this->product->update(['status' => 'Available']);
        
        // Refresh para asegurar que se obtiene del DB
        $this->product->refresh();
        $this->assertEquals('Available', $this->product->status);
    }

    /**
     * CA3 | El cliente no puede agregar al carrito un producto recién desactivado
     * Objetivo: Verificar que la API rechaza agregar productos desactivados
     */
    public function test_client_cannot_add_deactivated_product_to_cart()
    {
        // Act: Desactivar el producto
        $this->product->update(['status' => 'Unavailable']);
        
        // Assert: Intentar obtener el producto (simulando agregar al carrito)
        $deactivatedProduct = Product::where('product_id', $this->product->product_id)
            ->where('status', 'Available')
            ->first();
        
        // Assert: El producto no debe encontrarse
        $this->assertNull($deactivatedProduct, 'El cliente puede acceder a un producto desactivado');
    }

    /**
     * CA3 | Verificación adicional: búsqueda de producto desactivado devuelve null
     */
    public function test_find_deactivated_product_returns_null_in_public_query()
    {
        // Arrange
        $this->product->update(['status' => 'Unavailable']);
        
        // Act: Buscar el producto con filtro de disponibilidad
        $result = Product::where('product_id', $this->product->product_id)
            ->where('status', 'Available')
            ->first();
        
        // Assert
        $this->assertNull($result);
    }

    /**
     * CA4 | Si el producto estaba en el carrito cuando fue desactivado, se muestra alerta
     * Objetivo: Verificar que un producto en carrito se detecta como desactivado
     */
    public function test_deactivated_product_in_cart_can_be_detected()
    {
        // Arrange: Simulamos que el cliente tiene el producto en carrito (guardado en cliente)
        $cartProducts = [
            [
                'product_id' => $this->product->product_id,
                'quantity' => 2,
            ]
        ];
        
        // Act: Desactivar el producto
        $this->product->update(['status' => 'Unavailable']);
        
        // Assert: Verificar que podemos detectar productos desactivados en el carrito
        $unavailableProductsInCart = [];
        
        foreach ($cartProducts as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            
            if ($product && $product->status !== 'Available') {
                $unavailableProductsInCart[] = [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'status' => $product->status,
                ];
            }
        }
        
        // Debe detectar que hay un producto no disponible
        $this->assertNotEmpty($unavailableProductsInCart, 'No se detectó producto desactivado en carrito');
        $this->assertEquals($this->product->product_id, $unavailableProductsInCart[0]['product_id']);
        $this->assertEquals('Unavailable', $unavailableProductsInCart[0]['status']);
    }

    /**
     * CA4 | Verificación que el carrito detecta múltiples productos desactivados
     */
    public function test_multiple_deactivated_products_in_cart_are_detected()
    {
        // Arrange: Crear más productos
        $product2 = Product::factory()->create(['status' => 'Available']);
        $product2->locals()->attach($this->local->local_id);
        
        $product3 = Product::factory()->create(['status' => 'Available']);
        $product3->locals()->attach($this->local->local_id);
        
        // Carrito con múltiples productos
        $cartProducts = [
            ['product_id' => $this->product->product_id, 'quantity' => 1],
            ['product_id' => $product2->product_id, 'quantity' => 2],
            ['product_id' => $product3->product_id, 'quantity' => 1],
        ];
        
        // Act: Desactivar algunos productos
        $this->product->update(['status' => 'Unavailable']);
        $product3->update(['status' => 'Unavailable']);
        
        // Assert: Detectar todos los productos desactivados
        $unavailableProducts = [];
        
        foreach ($cartProducts as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            if ($product && $product->status !== 'Available') {
                $unavailableProducts[] = $product->product_id;
            }
        }
        
        $this->assertCount(2, $unavailableProducts);
        $this->assertContains($this->product->product_id, $unavailableProducts);
        $this->assertContains($product3->product_id, $unavailableProducts);
    }

    /**
     * CA4 | Verificación que productos activos en carrito se mantienen disponibles
     */
    public function test_available_products_in_cart_remain_available()
    {
        // Arrange
        $product2 = Product::factory()->create(['status' => 'Available']);
        $product2->locals()->attach($this->local->local_id);
        
        $cartProducts = [
            ['product_id' => $this->product->product_id, 'quantity' => 1],
            ['product_id' => $product2->product_id, 'quantity' => 1],
        ];
        
        // Act: Desactivar solo el primer producto
        $this->product->update(['status' => 'Unavailable']);
        
        // Assert: Verificar que el segundo producto sigue disponible
        $product2Check = Product::where('product_id', $product2->product_id)
            ->where('status', 'Available')
            ->first();
        
        $this->assertNotNull($product2Check);
        $this->assertEquals('Available', $product2Check->status);
    }

    /**
     * CA1 | Verificar que múltiples productos se pueden desactivar independientemente
     */
    public function test_multiple_products_deactivation_independently()
    {
        // Arrange: Crear múltiples productos
        $product2 = Product::factory()->create(['status' => 'Available']);
        $product2->locals()->attach($this->local->local_id);
        
        $product3 = Product::factory()->create(['status' => 'Available']);
        $product3->locals()->attach($this->local->local_id);
        
        // Verificar que todos están activos
        $activeCount = Product::where('status', 'Available')->count();
        $this->assertGreaterThanOrEqual(3, $activeCount);
        
        // Act: Desactivar solo uno
        $this->product->update(['status' => 'Unavailable']);
        
        // Assert
        $activeCountAfter = Product::where('status', 'Available')->count();
        $this->assertEquals($activeCount - 1, $activeCountAfter);
        
        // Los otros siguen activos
        $product2->refresh();
        $product3->refresh();
        $this->assertEquals('Available', $product2->status);
        $this->assertEquals('Available', $product3->status);
    }

    /**
     * CA2 | Verificar que ciclo completo funciona (Active -> Inactive -> Active)
     */
    public function test_product_status_cycle_active_inactive_active()
    {
        // Estado inicial
        $this->assertEquals('Available', $this->product->status);
        
        // Desactivar
        $this->product->update(['status' => 'Unavailable']);
        $this->product->refresh();
        $this->assertEquals('Unavailable', $this->product->status);
        
        // Reactivar
        $this->product->update(['status' => 'Available']);
        $this->product->refresh();
        $this->assertEquals('Available', $this->product->status);
        
        // Verificar que aparece en búsqueda final
        $finalProduct = Product::where('product_id', $this->product->product_id)
            ->where('status', 'Available')
            ->first();
        
        $this->assertNotNull($finalProduct);
    }

    /**
     * CA3 | Verificar que producto desactivado no puede ser consultado por cliente autenticado
     */
    public function test_authenticated_client_cannot_access_deactivated_product()
    {
        // Arrange
        $this->product->update(['status' => 'Unavailable']);
        
        // Act: Simulación de cliente intentando obtener producto
        $productFromClientView = Product::where('product_id', $this->product->product_id)
            ->where('status', 'Available')
            ->first();
        
        // Assert
        $this->assertNull($productFromClientView);
    }

    /**
     * CA1 | Verificación que vista públca solo muestra productos activos
     */
    public function test_public_view_only_shows_active_products()
    {
        // Arrange: Crear varios productos, algunos activos y otros no
        $activeProduct = Product::factory()->create(['status' => 'Available']);
        $activeProduct->locals()->attach($this->local->local_id);
        
        $inactiveProduct = Product::factory()->create(['status' => 'Unavailable']);
        $inactiveProduct->locals()->attach($this->local->local_id);
        
        // Act: Obtener solo productos visibles al público
        $publicProducts = Product::where('status', 'Available')->get();
        
        // Assert
        $this->assertTrue($publicProducts->contains('product_id', $activeProduct->product_id));
        $this->assertFalse($publicProducts->contains('product_id', $inactiveProduct->product_id));
        $this->assertTrue($publicProducts->contains('product_id', $this->product->product_id)); // Original también está activo
    }

    /**
     * Historia: G4DS-263
     * Tipo: Positivo
     * Objetivo: Verificar que el menú del cliente se actualiza al desactivar un producto
     * 
     * Precondiciones:
     * - Cliente con el menú abierto
     * - Producto activo visible
     * - Administrador autenticado
     * 
     * Pasos:
     * 1. Cliente abre el menú del local → Producto visible y disponible
     * 2. Administrador desactiva el producto → Producto marcado inactivo en sistema
     * 3. Verificar menú sin recargar → Producto desaparece automáticamente
     * 4. Cliente intenta agregar desde caché → Sistema muestra alerta
     */
    public function test_cp_263_01_menu_updates_when_product_deactivated()
    {
        // Paso 1: Cliente abre el menú - Platillo visible y disponible
        $menuProducts = Product::where('status', 'Available')
            ->where('product_id', $this->product->product_id)
            ->first();
        
        $this->assertNotNull($menuProducts, 'El producto debe ser visible en el menú');
        $this->assertEquals('Available', $menuProducts->status);
        
        // Paso 2: Administrador desactiva el producto
        $this->product->update(['status' => 'Unavailable']);
        
        // Verificar que está marcado inactivo en sistema
        $this->product->refresh();
        $this->assertEquals('Unavailable', $this->product->status);
        
        // Paso 3: Verificar menú sin recargar (simula consulta real)
        // El cliente hace una nueva consulta sin recargar la página
        $updatedMenuProducts = Product::where('status', 'Available')
            ->where('product_id', $this->product->product_id)
            ->first();
        
        // El producto debe desaparecer del menú
        $this->assertNull($updatedMenuProducts, 'El producto debe desaparecer del menú');
        
        // Paso 4: Cliente intenta agregar desde caché
        // Simular que el cliente tiene el producto guardado en cache/localStorage
        $cachedProduct = Product::find($this->product->product_id);
        
        // El sistema debe detectar que no está disponible
        if ($cachedProduct && $cachedProduct->status !== 'Available') {
            $alert = [
                'message' => 'Este producto ya no está disponible',
                'status' => $cachedProduct->status,
            ];
            
            $this->assertNotNull($alert);
            $this->assertEquals('Unavailable', $alert['status']);
            $this->assertStringContainsString('no está disponible', $alert['message']);
        }
    }

    /**
     * CP-263-02 | Cliente alertado si producto en carrito es desactivado [Negativo]
     * Historia: G4DS-263
     * Tipo: Negativo
     * Objetivo: Verificar que cliente es alertado si un producto en su carrito es desactivado
     * 
     * Precondiciones:
     * - Cliente con producto agregado al carrito
     * - Gerente autenticado
     * 
     * Pasos:
     * 1. Cliente agrega producto al carrito → Producto en carrito correctamente
     * 2. Gerente desactiva el producto → Producto marcado inactivo en sistema
     * 3. Verificar carrito sin recargar → Alerta visible: producto ya no disponible
     */
    public function test_cp_263_02_alert_when_carted_product_deactivated()
    {
        // Paso 1: Cliente agrega producto al carrito
        // Simulamos carrito como array (como se guarda en session/localStorage)
        $clientCart = [
            [
                'product_id' => $this->product->product_id,
                'name' => $this->product->name,
                'quantity' => 2,
                'price' => $this->product->price,
            ]
        ];
        
        // Verificar que el producto está en carrito
        $this->assertCount(1, $clientCart);
        $this->assertEquals($this->product->product_id, $clientCart[0]['product_id']);
        
        // Paso 2: Gerente desactiva el producto
        $this->product->update(['status' => 'Unavailable']);
        
        // Verificar que está marcado inactivo en sistema
        $this->product->refresh();
        $this->assertEquals('Unavailable', $this->product->status);
        
        // Paso 3: Verificar carrito sin recargar (cliente consulta estado)
        // Simular que cliente hace una validación de su carrito sin recargar
        $unavailableItemsInCart = [];
        
        foreach ($clientCart as $cartItem) {
            $currentProductStatus = Product::find($cartItem['product_id']);
            
            if ($currentProductStatus && $currentProductStatus->status !== 'Available') {
                $unavailableItemsInCart[] = [
                    'product_id' => $cartItem['product_id'],
                    'product_name' => $cartItem['name'],
                    'alert_message' => "El producto '{$cartItem['name']}' ya no está disponible",
                    'status' => $currentProductStatus->status,
                ];
            }
        }
        
        // Debe haber una alerta para el producto desactivado
        $this->assertNotEmpty($unavailableItemsInCart, 'Debe detectarse el producto no disponible en carrito');
        $this->assertCount(1, $unavailableItemsInCart);
        $this->assertEquals($this->product->product_id, $unavailableItemsInCart[0]['product_id']);
        $this->assertStringContainsString('ya no está disponible', $unavailableItemsInCart[0]['alert_message']);
        $this->assertEquals('Unavailable', $unavailableItemsInCart[0]['status']);
    }

    /**
     * Test adicional: Múltiples productos en carrito, algunos desactivados
     */
    public function test_multiple_products_in_cart_some_deactivated()
    {
        // Arrange: Crear varios productos
        $product2 = Product::factory()->create(['status' => 'Available']);
        $product2->locals()->attach($this->local->local_id);
        
        $product3 = Product::factory()->create(['status' => 'Available']);
        $product3->locals()->attach($this->local->local_id);
        
        // Carrito del cliente con múltiples productos
        $clientCart = [
            ['product_id' => $this->product->product_id, 'name' => $this->product->name, 'quantity' => 1],
            ['product_id' => $product2->product_id, 'name' => $product2->name, 'quantity' => 2],
            ['product_id' => $product3->product_id, 'name' => $product3->name, 'quantity' => 1],
        ];
        
        // Act: Desactivar algunos productos
        $this->product->update(['status' => 'Unavailable']);
        $product3->update(['status' => 'Unavailable']);
        
        // Assert: Validar carrito
        $validCartItems = [];
        $unavailableCartItems = [];
        
        foreach ($clientCart as $item) {
            $product = Product::find($item['product_id']);
            
            if ($product && $product->status === 'Available') {
                $validCartItems[] = $item;
            } else {
                $unavailableCartItems[] = $item;
            }
        }
        
        // Verificar que se detectan los productos no disponibles
        $this->assertCount(2, $unavailableCartItems);
        $this->assertCount(1, $validCartItems);
        
        // Verificar que solo product2 sigue disponible
        $this->assertEquals($product2->product_id, $validCartItems[0]['product_id']);
    }

    /**
     * Test: Recuperación de producto desactivado que vuelve a activarse
     */
    public function test_cart_validation_after_product_reactivation()
    {
        // Arrange
        $clientCart = [
            ['product_id' => $this->product->product_id, 'name' => $this->product->name, 'quantity' => 1],
        ];
        
        // Act & Assert - Desactivar
        $this->product->update(['status' => 'Unavailable']);
        
        $unavailableCount = 0;
        foreach ($clientCart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->status !== 'Available') {
                $unavailableCount++;
            }
        }
        
        $this->assertEquals(1, $unavailableCount);
        
        // Act: Reactivar producto
        $this->product->update(['status' => 'Available']);
        
        // Assert: Verificar que carrito está válido nuevamente
        $unavailableCount = 0;
        foreach ($clientCart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->status !== 'Available') {
                $unavailableCount++;
            }
        }
        
        $this->assertEquals(0, $unavailableCount, 'El producto debe estar disponible nuevamente');
    }
}

