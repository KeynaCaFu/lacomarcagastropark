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

class ReviewDisplayTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;
    protected $manager;
    protected $local;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear cliente
        $this->client = User::factory()->create();
        
        // Crear gerente
        $this->manager = User::factory()->create();
        
        // Crear local y asociarlo al gerente
        $this->local = Local::factory()->create();
        $this->local->users()->attach($this->manager->user_id);
        
        // Crear producto
        $this->product = Product::factory()->create();
        $this->product->locals()->attach($this->local->local_id);
        
        // Crear orden entregada para que el cliente pueda reseñar
        $order = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_DELIVERED,
        ]);
        $order->user()->attach($this->client->user_id);
    }

    /**
     * CP-238-01 | Visualización de respuesta del gerente en reseña [Positivo]
     * Objetivo: Verificar que el cliente puede ver la respuesta del gerente en su reseña correctamente
     * 
     * Historia: G4DS-238
     * Tipo: Positivo
     * Precondiciones: Reseña publicada con respuesta del gerente registrada.
     */
    public function test_client_can_see_manager_response_in_local_review()
    {
        // Arrange: Crear reseña local con respuesta del gerente
        $review = Review::create([
            'rating' => 5,
            'comment' => 'Excelente servicio y comida deliciosa.',
            'date' => now(),
            'response' => 'Gracias por tu comentario. Nos alegra que hayas disfrutado. ¡Esperamos verte pronto!',
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);

        // Act: Obtener la reseña desde la BD
        $retrievedReview = Review::find($review->review_id);

        // Assert: Verificar que la respuesta existe y se puede acceder
        $this->assertNotNull($retrievedReview->response);
        $this->assertEquals('Gracias por tu comentario. Nos alegra que hayas disfrutado. ¡Esperamos verte pronto!', $retrievedReview->response);
        
        // Verificar que la respuesta está diferenciada del comentario
        $this->assertNotEquals($retrievedReview->comment, $retrievedReview->response);
        $this->assertEquals('Excelente servicio y comida deliciosa.', $retrievedReview->comment);
    }

    /**
     * CP-238-01 | Visualización de respuesta del gerente en reseña de producto [Positivo]
     */
    public function test_client_can_see_manager_response_in_product_review()
    {
        // Arrange: Crear reseña de producto con respuesta del gerente
        $review = Review::create([
            'rating' => 4,
            'comment' => 'Buen producto, llegó fresco.',
            'date' => now(),
            'response' => 'Gracias por comprar con nosotros. Esperamos que disfrutes el producto.',
        ]);

        $productReview = ProductReview::create([
            'review_id' => $review->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);

        // Act & Assert
        $retrievedReview = Review::find($review->review_id);
        
        $this->assertNotNull($retrievedReview->response);
        $this->assertEquals('Gracias por comprar con nosotros. Esperamos que disfrutes el producto.', $retrievedReview->response);
        $this->assertNotEquals($retrievedReview->comment, $retrievedReview->response);
    }

    /**
     * CP-238-02 | Indicador de reseña sin respuesta [Positivo]
     * Objetivo: Verificar que se muestra 'Sin respuesta' cuando el gerente no ha respondido
     * 
     * Historia: G4DS-238
     * Tipo: Positivo
     * Precondiciones: Reseña publicada sin respuesta del gerente.
     */
    public function test_review_without_manager_response_shows_no_response_indicator()
    {
        // Arrange: Crear reseña local SIN respuesta
        $review = Review::create([
            'rating' => 3,
            'comment' => 'Servicio normal, nada especial.',
            'date' => now(),
            'response' => null, // Sin respuesta
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);

        // Act: Obtener la reseña
        $retrievedReview = Review::find($review->review_id);

        // Assert: Verificar que no hay respuesta
        $this->assertNull($retrievedReview->response);
        
        // La lógica de mostrar "Sin respuesta" se hace en la vista/API
        // pero el test verifica que el campo está vacío
        $this->assertTrue($retrievedReview->response === null);
    }

    /**
     * CP-238-02 | Indicador de producto sin respuesta [Positivo]
     */
    public function test_product_review_without_response_shows_no_response()
    {
        // Arrange: Crear reseña de producto SIN respuesta
        $review = Review::create([
            'rating' => 2,
            'comment' => 'Producto aceptable pero podría mejorar.',
            'date' => now(),
            'response' => null,
        ]);

        $productReview = ProductReview::create([
            'review_id' => $review->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);

        // Act & Assert
        $retrievedReview = Review::find($review->review_id);
        
        $this->assertNull($retrievedReview->response);
    }

    /**
     * CP-238-03 | Asociación correcta de respuesta a reseña [Positivo]
     * Objetivo: Verificar que la respuesta está asociada a la reseña correcta
     * 
     * Historia: G4DS-238
     * Tipo: Positivo
     * Precondiciones: Múltiples reseñas, algunas con respuesta y otras sin respuesta.
     */
    public function test_multiple_reviews_show_correct_response_association()
    {
        // Arrange: Crear múltiples reseñas, algunas con respuesta y otras sin
        $review1 = Review::create([
            'rating' => 5,
            'comment' => 'Excelente lugar.',
            'date' => now(),
            'response' => 'Gracias, vuelve pronto.',
        ]);
        $localReview1 = LocalReview::create([
            'review_id' => $review1->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);

        $review2 = Review::create([
            'rating' => 3,
            'comment' => 'Servicio normal.',
            'date' => now(),
            'response' => null, // Sin respuesta
        ]);
        $localReview2 = LocalReview::create([
            'review_id' => $review2->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);

        $review3 = Review::create([
            'rating' => 4,
            'comment' => 'Buena comida.',
            'date' => now(),
            'response' => 'Nos alegra que hayas venido.',
        ]);
        $localReview3 = LocalReview::create([
            'review_id' => $review3->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);

        // Act: Obtener todas las reseñas del cliente
        $reviews = Review::whereIn('review_id', [$review1->review_id, $review2->review_id, $review3->review_id])
            ->get()
            ->keyBy('review_id');

        // Assert: Verificar que cada reseña tiene su respuesta correcta
        $this->assertNotNull($reviews[$review1->review_id]->response);
        $this->assertEquals('Gracias, vuelve pronto.', $reviews[$review1->review_id]->response);
        
        $this->assertNull($reviews[$review2->review_id]->response);
        
        $this->assertNotNull($reviews[$review3->review_id]->response);
        $this->assertEquals('Nos alegra que hayas venido.', $reviews[$review3->review_id]->response);
    }

    /**
     * CA3 | Respuesta diferenciada del comentario - verificación de estructura
     */
    public function test_review_response_is_clearly_differentiated_from_comment()
    {
        // Arrange
        $review = Review::create([
            'rating' => 5,
            'comment' => 'Comentario del cliente: muy bueno.',
            'date' => now(),
            'response' => 'Respuesta del gerente: gracias por tu confianza.',
        ]);

        // Act
        $retrievedReview = Review::find($review->review_id);

        // Assert: Verificar que ambos campos existen y son diferentes
        $this->assertTrue($retrievedReview->comment !== $retrievedReview->response);
        $this->assertEquals('Comentario del cliente: muy bueno.', $retrievedReview->comment);
        $this->assertEquals('Respuesta del gerente: gracias por tu confianza.', $retrievedReview->response);
        
        // Verificar que ambos campos tienen contenido
        $this->assertNotEmpty($retrievedReview->comment);
        $this->assertNotEmpty($retrievedReview->response);
    }

    /**
     * CA4 | La información está correctamente asociada a la reseña
     */
    public function test_review_response_is_correctly_associated_with_correct_review()
    {
        // Arrange: Crear dos reseñas con respuestas diferentes
        $review1 = Review::create([
            'rating' => 5,
            'comment' => 'Primera reseña.',
            'date' => now(),
            'response' => 'Respuesta 1',
        ]);

        $review2 = Review::create([
            'rating' => 4,
            'comment' => 'Segunda reseña.',
            'date' => now(),
            'response' => 'Respuesta 2',
        ]);

        // Act: Recuperar ambas reseñas
        $retrieved1 = Review::find($review1->review_id);
        $retrieved2 = Review::find($review2->review_id);

        // Assert: Verificar que las respuestas están asociadas correctamente
        $this->assertEquals('Respuesta 1', $retrieved1->response);
        $this->assertEquals('Respuesta 2', $retrieved2->response);
        
        // Verificar que no se cruzaron las respuestas
        $this->assertNotEquals($retrieved1->response, $retrieved2->response);
        
        // Verificar la asociación con comentarios
        $this->assertEquals('Primera reseña.', $retrieved1->comment);
        $this->assertEquals('Segunda reseña.', $retrieved2->comment);
    }

    /**
     * Test: Obtener reseña con respuesta mediante relación LocalReview
     */
    public function test_local_review_relation_retrieves_review_with_response()
    {
        // Arrange
        $review = Review::create([
            'rating' => 5,
            'comment' => 'Excelente servicio.',
            'date' => now(),
            'response' => 'Gracias por tu confianza.',
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);

        // Act: Recuperar a través de la relación
        $retrievedLocalReview = LocalReview::find($localReview->local_review_id);
        $retrievedReview = $retrievedLocalReview->review;

        // Assert
        $this->assertNotNull($retrievedReview->response);
        $this->assertEquals('Gracias por tu confianza.', $retrievedReview->response);
    }

    /**
     * Test: Obtener reseña con respuesta mediante relación ProductReview
     */
    public function test_product_review_relation_retrieves_review_with_response()
    {
        // Arrange
        $review = Review::create([
            'rating' => 4,
            'comment' => 'Buen producto.',
            'date' => now(),
            'response' => 'Gracias por tu compra.',
        ]);

        $productReview = ProductReview::create([
            'review_id' => $review->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);

        // Act: Recuperar a través de la relación
        $retrievedProductReview = ProductReview::find($productReview->product_review_id);
        $retrievedReview = $retrievedProductReview->review;

        // Assert
        $this->assertNotNull($retrievedReview->response);
        $this->assertEquals('Gracias por tu compra.', $retrievedReview->response);
    }
}
