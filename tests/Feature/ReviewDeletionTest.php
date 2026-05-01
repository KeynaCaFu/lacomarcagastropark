<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Local;
use App\Models\Review;
use App\Models\LocalReview;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewDeletionTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $otherUser;
    protected $local;
    protected $product;
    protected $order;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear usuarios
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        
        // Crear local
        $this->local = Local::factory()->create();
        
        // Crear producto y asociarlo al local
        $this->product = Product::factory()->create();
        $this->product->locals()->attach($this->local->local_id);
        
        // Crear una orden entregada para que el usuario pueda reseñar
        $this->order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_DELIVERED,
        ]);
        $this->order->user()->attach($this->user->user_id);
    }

    /**
     * CP-236-01 | Eliminación exitosa de reseña propia [Positivo]
     * Objetivo: Verificar que el cliente puede eliminar su propia reseña con confirmación
     * 
     * Historia: G4DS-236
     * Tipo: Positivo
     * Precondiciones: Cliente autenticado con al menos una reseña publicada.
     */
    public function test_user_can_delete_own_local_review()
    {
        // Arrange: Cliente autenticado con reseña publicada
        $review = Review::create([
            'rating' => 5,
            'comment' => 'Excelente servicio, la comida estaba deliciosa y el personal muy atento.',
            'date' => now(),
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->user->user_id,
        ]);

        // Act: El usuario intenta eliminar su propia reseña
        $response = $this->actingAs($this->user)
            ->deleteJson("/plaza/{$this->local->local_id}/review/{$localReview->local_review_id}");

        // Assert: La reseña debería desaparecer del sistema
        $response->assertStatus(200)
                 ->assertJsonStructure(['success'])
                 ->assertJsonPath('success', true);

        // Verificar que la reseña y la localReview fueron eliminadas
        $this->assertDatabaseMissing('tblocal_review', [
            'local_review_id' => $localReview->local_review_id
        ]);

        $this->assertDatabaseMissing('tbreview', [
            'review_id' => $review->review_id
        ]);
    }

    /**
     * CP-236-01 | Eliminación exitosa de reseña propia (Producto) [Positivo]
     * Objetivo: Verificar que el cliente puede eliminar su propia reseña de producto
     * 
     * Historia: G4DS-236
     * Tipo: Positivo
     * Precondiciones: Cliente autenticado con al menos una reseña de producto publicada.
     */
    public function test_user_can_delete_own_product_review()
    {
        // Arrange: Crear una orden con el producto y usuario
        $orderItem = $this->order->items()->create([
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        $review = Review::create([
            'rating' => 4,
            'comment' => 'Buen producto, llegó fresco y bien empacado. Lo recomiendo.',
            'date' => now(),
        ]);

        $productReview = ProductReview::create([
            'review_id' => $review->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->user->user_id,
        ]);

        // Act: El usuario intenta eliminar su propia reseña de producto
        $response = $this->actingAs($this->user)
            ->deleteJson("/plaza/producto/{$productReview->product_review_id}/resena");

        // Assert: La reseña debería desaparecer del sistema
        $response->assertStatus(200)
                 ->assertJsonStructure(['success'])
                 ->assertJsonPath('success', true);

        // Verificar que la reseña y la productReview fueron eliminadas
        $this->assertDatabaseMissing('tbproduct_review', [
            'product_review_id' => $productReview->product_review_id
        ]);

        $this->assertDatabaseMissing('tbreview', [
            'review_id' => $review->review_id
        ]);
    }

    /**
     * CP-236-02 | Confirmación antes de eliminar [Positivo]
     * Objetivo: Verificar que el sistema solicita confirmación antes de proceder con la eliminación
     * 
     * Historia: G4DS-236
     * Tipo: Positivo
     * Precondiciones: Cliente autenticado con reseña publicada.
     */
    public function test_delete_review_endpoint_requires_authentication()
    {
        // Arrange
        $review = Review::create([
            'rating' => 5,
            'comment' => 'Excelente servicio y comida deliciosa.',
            'date' => now(),
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->user->user_id,
        ]);

        // Act & Assert: Usuario sin autenticar no debería poder eliminar
        $response = $this->deleteJson("/plaza/{$this->local->local_id}/review/{$localReview->local_review_id}");

        // No autenticado debería ser redirigido (status 302) o recibir 401
        $this->assertTrue(
            in_array($response->status(), [302, 401, 405, 404]),
            "Expected redirect or auth error, got {$response->status()}"
        );

        // Verificar que la reseña NO fue eliminada
        $this->assertDatabaseHas('tblocal_review', [
            'local_review_id' => $localReview->local_review_id
        ]);
    }

    /**
     * CP-236-03 | Cliente no puede eliminar reseña de otro usuario [Positivo]
     * Objetivo: Verificar que un cliente solo puede eliminar sus propias reseñas
     * 
     * Historia: G4DS-236
     * Tipo: Positivo
     * Precondiciones: Cliente A autenticado. Existe reseña del cliente B.
     */
    public function test_user_cannot_delete_other_user_local_review()
    {
        // Arrange: Crear reseña del otro usuario
        $review = Review::create([
            'rating' => 4,
            'comment' => 'Muy buena experiencia en este local.',
            'date' => now(),
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->otherUser->user_id, // Reseña del OTRO usuario
        ]);

        // Act: Usuario intenta eliminar la reseña del otro usuario
        $response = $this->actingAs($this->user)
            ->deleteJson("/plaza/{$this->local->local_id}/review/{$localReview->local_review_id}");

        // Assert: Debería obtener un error 404 (Not Found) porque no encontrará la reseña con su user_id
        $response->assertStatus(404);

        // Verificar que la reseña NO fue eliminada
        $this->assertDatabaseHas('tblocal_review', [
            'local_review_id' => $localReview->local_review_id,
            'user_id' => $this->otherUser->user_id
        ]);
    }

    /**
     * CP-236-03 | Cliente no puede eliminar reseña de producto de otro usuario [Positivo]
     * Objetivo: Verificar que un cliente solo puede eliminar sus propias reseñas de producto
     * 
     * Historia: G4DS-236
     * Tipo: Positivo
     * Precondiciones: Cliente A autenticado. Existe reseña de producto del cliente B.
     */
    public function test_user_cannot_delete_other_user_product_review()
    {
        // Arrange: Crear orden y producto review para el otro usuario
        $otherUserOrder = Order::factory()->create([
            'status' => Order::STATUS_DELIVERED,
        ]);
        $otherUserOrder->user()->attach($this->otherUser->user_id);

        $review = Review::create([
            'rating' => 3,
            'comment' => 'Producto aceptable, pero podría mejorar.',
            'date' => now(),
        ]);

        $productReview = ProductReview::create([
            'review_id' => $review->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->otherUser->user_id, // Reseña del OTRO usuario
        ]);

        // Act: Usuario intenta eliminar la reseña del otro usuario
        $response = $this->actingAs($this->user)
            ->deleteJson("/plaza/producto/{$productReview->product_review_id}/resena");

        // Assert: Debería obtener un error 404 (Not Found)
        $response->assertStatus(404);

        // Verificar que la reseña NO fue eliminada
        $this->assertDatabaseHas('tbproduct_review', [
            'product_review_id' => $productReview->product_review_id,
            'user_id' => $this->otherUser->user_id
        ]);
    }

    /**
     * CP-236-04 | Eliminación no afecta otras reseñas del sistema [Positivo]
     * Objetivo: Verificar que eliminar una reseña no altera otras reseñas ni datos del sistema
     * 
     * Historia: G4DS-236
     * Tipo: Positivo
     * Precondiciones: Múltiples reseñas en el sistema.
     */
    public function test_deleting_review_does_not_affect_other_reviews()
    {
        // Arrange: Crear múltiples reseñas para verificar que solo se elimina la correcta
        $review1 = Review::create([
            'rating' => 5,
            'comment' => 'Excelente lugar para comer con familia.',
            'date' => now(),
        ]);
        $localReview1 = LocalReview::create([
            'review_id' => $review1->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->user->user_id,
        ]);

        $review2 = Review::create([
            'rating' => 4,
            'comment' => 'Buena experiencia, volveré pronto.',
            'date' => now(),
        ]);
        $localReview2 = LocalReview::create([
            'review_id' => $review2->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->otherUser->user_id,
        ]);

        $review3 = Review::create([
            'rating' => 3,
            'comment' => 'Servicio normal, nada especial.',
            'date' => now(),
        ]);
        $localReview3 = LocalReview::create([
            'review_id' => $review3->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->otherUser->user_id,
        ]);

        // Act: Usuario intenta eliminar solo su propia reseña (la primera)
        $response = $this->actingAs($this->user)
            ->deleteJson("/plaza/{$this->local->local_id}/review/{$localReview1->local_review_id}");

        // Assert: La eliminación fue exitosa
        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        // Verificar que SOLO la primera reseña fue eliminada
        $this->assertDatabaseMissing('tblocal_review', [
            'local_review_id' => $localReview1->local_review_id
        ]);

        // Verificar que las otras reseñas siguen intactas
        $this->assertDatabaseHas('tblocal_review', [
            'local_review_id' => $localReview2->local_review_id,
            'user_id' => $this->otherUser->user_id
        ]);

        $this->assertDatabaseHas('tblocal_review', [
            'local_review_id' => $localReview3->local_review_id,
            'user_id' => $this->otherUser->user_id
        ]);

        // Verificar que las reviews associadas siguen existiendo
        $this->assertDatabaseHas('tbreview', [
            'review_id' => $review2->review_id
        ]);

        $this->assertDatabaseHas('tbreview', [
            'review_id' => $review3->review_id
        ]);
    }

    /**
     * CP-236-04 | Eliminación no afecta reseñas de productos [Positivo]
     * Objetivo: Verificar que eliminar una reseña local no afecta reseñas de productos
     * 
     * Historia: G4DS-236
     * Tipo: Positivo
     * Precondiciones: Múltiples reseñas de productos en el sistema.
     */
    public function test_deleting_local_review_does_not_affect_product_reviews()
    {
        // Arrange: Crear reseña local y varias reseñas de producto
        $localReview = LocalReview::create([
            'review_id' => Review::create([
                'rating' => 5,
                'comment' => 'Excelente local.',
                'date' => now(),
            ])->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->user->user_id,
        ]);

        $productReview1 = ProductReview::create([
            'review_id' => Review::create([
                'rating' => 4,
                'comment' => 'Buen producto.',
                'date' => now(),
            ])->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->user->user_id,
        ]);

        $productReview2 = ProductReview::create([
            'review_id' => Review::create([
                'rating' => 5,
                'comment' => 'Excelente producto.',
                'date' => now(),
            ])->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->otherUser->user_id,
        ]);

        $initialProductReviewCount = ProductReview::count();

        // Act: Eliminar la reseña local
        $this->actingAs($this->user)
            ->deleteJson("/plaza/{$this->local->local_id}/review/{$localReview->local_review_id}");

        // Assert: Las reseñas de producto no fueron afectadas
        $this->assertEquals($initialProductReviewCount, ProductReview::count());

        $this->assertDatabaseHas('tbproduct_review', [
            'product_review_id' => $productReview1->product_review_id
        ]);

        $this->assertDatabaseHas('tbproduct_review', [
            'product_review_id' => $productReview2->product_review_id
        ]);
    }

    /**
     * Test de validación: Intento de eliminar con ID inválido
     */
    public function test_delete_review_with_invalid_id_returns_404()
    {
        // Act: Intentar eliminar con un ID que no existe
        $response = $this->actingAs($this->user)
            ->deleteJson("/plaza/{$this->local->local_id}/review/999999");

        // Assert: Debería recibir un error 404
        $response->assertStatus(404);
    }

    /**
     * Test de validación: Intento de eliminar con local_id inválido
     */
    public function test_delete_review_with_invalid_local_id_returns_404()
    {
        // Arrange: Crear una reseña
        $review = Review::create([
            'rating' => 5,
            'comment' => 'Excelente servicio.',
            'date' => now(),
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->user->user_id,
        ]);

        // Act: Intentar eliminar con un local_id diferente
        $response = $this->actingAs($this->user)
            ->deleteJson("/plaza/999999/review/{$localReview->local_review_id}");

        // Assert: Debería recibir un error 404
        $response->assertStatus(404);

        // Verificar que la reseña NO fue eliminada
        $this->assertDatabaseHas('tblocal_review', [
            'local_review_id' => $localReview->local_review_id
        ]);
    }
}
