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

/**
 * QA-237 | Pruebas de visualización de reseñas del cliente
 * 
 * Historia de usuario: Como cliente, quiero visualizar todas las reseñas que he realizado 
 * para llevar control de mis opiniones por producto y por local.
 * 
 * Requisitos funcionales:
 * - El cliente puede ver únicamente sus reseñas
 * - Cada reseña muestra comentario, estrellas y fecha
 * - La información se presenta en lista o tarjetas
 * - No se muestran reseñas de otros usuarios
 */
class ClientReviewsVisualizationTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;
    protected $otherClient;
    protected $local;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear cliente autenticado
        $this->client = User::factory()->create();
        
        // Crear otro cliente para pruebas de aislamiento
        $this->otherClient = User::factory()->create();
        
        // Crear local
        $this->local = Local::factory()->create();
        
        // Crear producto
        $this->product = Product::factory()->create();
        $this->product->locals()->attach($this->local->local_id);
    }

    /**
     * QA-001 | El cliente puede ver únicamente sus reseñas [Positivo]
     * 
     * Objetivo: Verificar que cada cliente solo puede ver sus propias reseñas
     * Precondiciones: Múltiples clientes con reseñas en el sistema.
     */
    public function test_client_can_only_see_their_own_local_reviews()
    {
        // Arrange: Crear reseña del cliente autenticado
        $myReview = Review::factory()->create([
            'rating' => 5,
            'comment' => 'Mi reseña del local',
            'date' => now()->subDays(5),
        ]);
        
        LocalReview::create([
            'review_id' => $myReview->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Crear reseña del otro cliente
        $otherClientReview = Review::factory()->create([
            'rating' => 3,
            'comment' => 'Reseña del otro cliente',
            'date' => now()->subDays(3),
        ]);
        
        LocalReview::create([
            'review_id' => $otherClientReview->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->otherClient->user_id,
        ]);
        
        // Act: Obtener reseñas del cliente autenticado
        $clientReviews = LocalReview::with(['review', 'local'])
            ->where('user_id', $this->client->user_id)
            ->whereHas('review')
            ->orderByDesc('created_at')
            ->get();
        
        // Assert: El cliente solo ve sus propias reseñas
        $this->assertEquals(1, $clientReviews->count());
        $this->assertEquals($this->client->user_id, $clientReviews->first()->user_id);
        $this->assertEquals('Mi reseña del local', $clientReviews->first()->review->comment);
        
        // Verificar que no se ve la reseña del otro cliente
        $this->assertFalse($clientReviews->contains(function ($review) use ($otherClientReview) {
            return $review->review_id === $otherClientReview->review_id;
        }));
    }

    /**
     * QA-002 | El cliente puede ver únicamente sus reseñas de productos [Positivo]
     * 
     * Objetivo: Verificar que cada cliente solo ve sus reseñas de productos
     * Precondiciones: Múltiples clientes con reseñas de productos.
     */
    public function test_client_can_only_see_their_own_product_reviews()
    {
        // Arrange: Crear reseña de producto del cliente autenticado
        $myProductReview = Review::factory()->create([
            'rating' => 4,
            'comment' => 'Mi reseña del producto',
            'date' => now()->subDays(2),
        ]);
        
        ProductReview::create([
            'review_id' => $myProductReview->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Crear reseña de producto del otro cliente
        $otherProductReview = Review::factory()->create([
            'rating' => 2,
            'comment' => 'Reseña del producto del otro cliente',
            'date' => now()->subDays(1),
        ]);
        
        ProductReview::create([
            'review_id' => $otherProductReview->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->otherClient->user_id,
        ]);
        
        // Act: Obtener reseñas de productos del cliente autenticado
        $clientProductReviews = ProductReview::with(['review', 'product.locals'])
            ->where('user_id', $this->client->user_id)
            ->whereHas('review')
            ->orderByDesc('created_at')
            ->get();
        
        // Assert: El cliente solo ve sus propias reseñas de productos
        $this->assertEquals(1, $clientProductReviews->count());
        $this->assertEquals($this->client->user_id, $clientProductReviews->first()->user_id);
        $this->assertEquals('Mi reseña del producto', $clientProductReviews->first()->review->comment);
        
        // Verificar que no se ve la reseña del otro cliente
        $this->assertFalse($clientProductReviews->contains(function ($review) use ($otherProductReview) {
            return $review->review_id === $otherProductReview->review_id;
        }));
    }

    /**
     * QA-003 | Cada reseña muestra comentario, estrellas y fecha [Positivo]
     * 
     * Objetivo: Verificar que cada reseña contiene todos los datos requeridos
     * Precondiciones: Reseña publicada en el sistema.
     */
    public function test_review_displays_all_required_fields()
    {
        // Arrange: Crear reseña con datos completos
        $testDate = now()->subDays(10);
        $testRating = 4;
        $testComment = 'Excelente comida, muy recomendado';
        
        $review = Review::factory()->create([
            'rating' => $testRating,
            'comment' => $testComment,
            'date' => $testDate,
        ]);
        
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Act: Obtener la reseña
        $retrievedReview = LocalReview::with('review')
            ->find($localReview->local_review_id);
        
        // Assert: Verificar que todos los campos están presentes
        $this->assertNotNull($retrievedReview->review);
        $this->assertEquals($testComment, $retrievedReview->review->comment);
        $this->assertEquals($testRating, $retrievedReview->review->rating);
        $this->assertNotNull($retrievedReview->review->date);
        
        // Verificar que el rating está en rango válido (1-5 estrellas)
        $this->assertGreaterThanOrEqual(1, $retrievedReview->review->rating);
        $this->assertLessThanOrEqual(5, $retrievedReview->review->rating);
    }

    /**
     * QA-004 | Reseña de producto muestra todos los campos requeridos [Positivo]
     * 
     * Objetivo: Verificar que las reseñas de productos contienen todos los datos
     * Precondiciones: Reseña de producto publicada.
     */
    public function test_product_review_displays_all_required_fields()
    {
        // Arrange
        $testDate = now()->subDays(5);
        $testRating = 5;
        $testComment = 'Producto excelente, muy fresco';
        
        $review = Review::factory()->create([
            'rating' => $testRating,
            'comment' => $testComment,
            'date' => $testDate,
        ]);
        
        $productReview = ProductReview::create([
            'review_id' => $review->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Act: Obtener la reseña del producto
        $retrievedReview = ProductReview::with('review')
            ->find($productReview->product_review_id);
        
        // Assert: Verificar que todos los campos están presentes
        $this->assertNotNull($retrievedReview->review);
        $this->assertEquals($testComment, $retrievedReview->review->comment);
        $this->assertEquals($testRating, $retrievedReview->review->rating);
        $this->assertNotNull($retrievedReview->review->date);
        $this->assertGreaterThanOrEqual(1, $retrievedReview->review->rating);
        $this->assertLessThanOrEqual(5, $retrievedReview->review->rating);
    }

    /**
     * QA-005 | Las reseñas se presentan en formato de lista/colección para locales [Positivo]
     * 
     * Objetivo: Verificar que las reseñas de locales se devuelven en formato iterable
     * Precondiciones: Cliente con múltiples reseñas de locales.
     */
    public function test_local_reviews_are_returned_as_iterable_collection()
    {
        // Arrange: Crear múltiples reseñas locales para el cliente
        $reviewsCount = 3;
        for ($i = 0; $i < $reviewsCount; $i++) {
            $review = Review::factory()->create([
                'rating' => rand(1, 5),
                'comment' => "Reseña local número " . ($i + 1),
                'date' => now()->subDays($i),
            ]);
            
            LocalReview::create([
                'review_id' => $review->review_id,
                'local_id' => $this->local->local_id,
                'user_id' => $this->client->user_id,
            ]);
        }
        
        // Act: Obtener reseñas locales
        $clientLocalReviews = LocalReview::with(['review', 'local'])
            ->where('user_id', $this->client->user_id)
            ->whereHas('review')
            ->orderByDesc('created_at')
            ->get();
        
        // Assert: Verificar que se devuelve una colección iterable
        $this->assertIsIterable($clientLocalReviews);
        $this->assertEquals($reviewsCount, $clientLocalReviews->count());
        
        // Verificar que cada elemento tiene los datos requeridos
        foreach ($clientLocalReviews as $localReview) {
            $this->assertNotNull($localReview->review);
            $this->assertNotNull($localReview->review->comment);
            $this->assertNotNull($localReview->review->rating);
            $this->assertNotNull($localReview->review->date);
            $this->assertNotNull($localReview->local);
        }
    }

    /**
     * QA-006 | Las reseñas se presentan en formato de lista/colección para productos [Positivo]
     * 
     * Objetivo: Verificar que las reseñas de productos se devuelven en formato iterable
     * Precondiciones: Cliente con múltiples reseñas de productos.
     */
    public function test_product_reviews_are_returned_as_iterable_collection()
    {
        // Arrange: Crear múltiples reseñas de productos para el cliente
        $reviewsCount = 3;
        for ($i = 0; $i < $reviewsCount; $i++) {
            $product = Product::factory()->create();
            $product->locals()->attach($this->local->local_id);
            
            $review = Review::factory()->create([
                'rating' => rand(2, 5),
                'comment' => "Reseña de producto número " . ($i + 1),
                'date' => now()->subDays($i * 2),
            ]);
            
            ProductReview::create([
                'review_id' => $review->review_id,
                'product_id' => $product->product_id,
                'user_id' => $this->client->user_id,
            ]);
        }
        
        // Act: Obtener reseñas de productos
        $clientProductReviews = ProductReview::with(['review', 'product.locals'])
            ->where('user_id', $this->client->user_id)
            ->whereHas('review')
            ->orderByDesc('created_at')
            ->get();
        
        // Assert: Verificar que se devuelve una colección iterable
        $this->assertIsIterable($clientProductReviews);
        $this->assertEquals($reviewsCount, $clientProductReviews->count());
        
        // Verificar que cada elemento tiene los datos requeridos
        foreach ($clientProductReviews as $productReview) {
            $this->assertNotNull($productReview->review);
            $this->assertNotNull($productReview->review->comment);
            $this->assertNotNull($productReview->review->rating);
            $this->assertNotNull($productReview->review->date);
            $this->assertNotNull($productReview->product);
        }
    }

    /**
     * QA-007 | No se muestran reseñas de otros usuarios en el listado [Positivo]
     * 
     * Objetivo: Verificar que un cliente NO ve reseñas de otros usuarios
     * Precondiciones: Múltiples usuarios con reseñas en el sistema.
     */
    public function test_other_users_local_reviews_are_not_shown()
    {
        // Arrange: Crear múltiples usuarios con reseñas
        $anotherUser = User::factory()->create();
        
        // Reseñas de otros usuarios para el mismo local
        for ($i = 0; $i < 2; $i++) {
            $review = Review::factory()->create();
            LocalReview::create([
                'review_id' => $review->review_id,
                'local_id' => $this->local->local_id,
                'user_id' => $this->otherClient->user_id,
            ]);
            
            $review2 = Review::factory()->create();
            LocalReview::create([
                'review_id' => $review2->review_id,
                'local_id' => $this->local->local_id,
                'user_id' => $anotherUser->user_id,
            ]);
        }
        
        // Reseña del cliente autenticado
        $myReview = Review::factory()->create();
        LocalReview::create([
            'review_id' => $myReview->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Act: Obtener reseñas del cliente autenticado
        $clientReviews = LocalReview::where('user_id', $this->client->user_id)
            ->get();
        
        // Assert: El cliente solo ve su propia reseña
        $this->assertEquals(1, $clientReviews->count());
        $this->assertEquals($this->client->user_id, $clientReviews->first()->user_id);
        
        // Verificar que no hay reseñas de otros usuarios
        $userIds = $clientReviews->pluck('user_id')->unique()->toArray();
        $this->assertCount(1, $userIds);
        $this->assertEquals($this->client->user_id, $userIds[0]);
    }

    /**
     * QA-008 | No se muestran reseñas de otros usuarios en reseñas de productos [Positivo]
     * 
     * Objetivo: Verificar que un cliente NO ve reseñas de productos de otros usuarios
     * Precondiciones: Múltiples usuarios con reseñas de productos.
     */
    public function test_other_users_product_reviews_are_not_shown()
    {
        // Arrange: Crear reseñas de productos de otros usuarios
        for ($i = 0; $i < 2; $i++) {
            $review = Review::factory()->create();
            ProductReview::create([
                'review_id' => $review->review_id,
                'product_id' => $this->product->product_id,
                'user_id' => $this->otherClient->user_id,
            ]);
        }
        
        // Reseña de producto del cliente autenticado
        $myProductReview = Review::factory()->create();
        ProductReview::create([
            'review_id' => $myProductReview->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Act: Obtener reseñas de productos del cliente autenticado
        $clientProductReviews = ProductReview::where('user_id', $this->client->user_id)
            ->get();
        
        // Assert: El cliente solo ve su propia reseña de producto
        $this->assertEquals(1, $clientProductReviews->count());
        $this->assertEquals($this->client->user_id, $clientProductReviews->first()->user_id);
        
        // Verificar que no hay reseñas de otros usuarios
        $userIds = $clientProductReviews->pluck('user_id')->unique()->toArray();
        $this->assertCount(1, $userIds);
        $this->assertEquals($this->client->user_id, $userIds[0]);
    }

    /**
     * QA-009 | Las reseñas están ordenadas por fecha descendente [Positivo]
     * 
     * Objetivo: Verificar que las reseñas se muestran ordenadas por fecha más reciente primero
     * Precondiciones: Cliente con múltiples reseñas en diferentes fechas.
     */
    public function test_reviews_are_ordered_by_recent_first()
    {
        // Arrange: Crear reseñas con diferentes fechas
        $dates = [
            now()->subDays(5),
            now()->subDays(2),
            now()->subDays(10),
            now()->subDays(1),
        ];
        
        foreach ($dates as $date) {
            $review = Review::factory()->create([
                'date' => $date,
                'comment' => "Reseña de " . $date->format('Y-m-d'),
            ]);
            
            LocalReview::create([
                'review_id' => $review->review_id,
                'local_id' => $this->local->local_id,
                'user_id' => $this->client->user_id,
            ]);
        }
        
        // Act: Obtener reseñas ordenadas por fecha descendente
        $orderedReviews = LocalReview::with('review')
            ->where('user_id', $this->client->user_id)
            ->orderByDesc('created_at')
            ->get();
        
        // Assert: Verificar que están ordenadas de más reciente a más antigua
        $this->assertEquals(4, $orderedReviews->count());
        
        // Extraer los timestamps de created_at para verificar orden
        $timestamps = $orderedReviews->map(function ($review) {
            return $review->created_at;
        })->toArray();
        
        // Verificar que están en orden descendente
        for ($i = 0; $i < count($timestamps) - 1; $i++) {
            $this->assertTrue(
                $timestamps[$i]->gte($timestamps[$i + 1]),
                "Las reseñas no están ordenadas correctamente"
            );
        }
    }

    /**
     * QA-010 | Cliente sin reseñas recibe lista vacía [Positivo]
     * 
     * Objetivo: Verificar que un cliente sin reseñas recibe una colección vacía
     * Precondiciones: Cliente sin reseñas en el sistema.
     */
    public function test_client_without_reviews_receives_empty_collection()
    {
        // Arrange: Crear un nuevo cliente sin reseñas
        $newClient = User::factory()->create();
        
        // Act: Obtener reseñas del cliente sin reseñas
        $clientLocalReviews = LocalReview::with(['review', 'local'])
            ->where('user_id', $newClient->user_id)
            ->whereHas('review')
            ->get();
        
        $clientProductReviews = ProductReview::with(['review', 'product.locals'])
            ->where('user_id', $newClient->user_id)
            ->whereHas('review')
            ->get();
        
        // Assert: Ambas colecciones deben estar vacías
        $this->assertEmpty($clientLocalReviews);
        $this->assertEmpty($clientProductReviews);
        $this->assertEquals(0, $clientLocalReviews->count());
        $this->assertEquals(0, $clientProductReviews->count());
    }

    /**
     * QA-011 | Reseña contiene información del local asociado [Positivo]
     * 
     * Objetivo: Verificar que la información del local está disponible en la reseña local
     * Precondiciones: Reseña local publicada.
     */
    public function test_local_review_contains_associated_local_information()
    {
        // Arrange: Crear reseña local
        $review = Review::factory()->create([
            'comment' => 'Reseña del local',
        ]);
        
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Act: Recuperar la reseña con información del local
        $retrievedLocalReview = LocalReview::with(['review', 'local'])
            ->find($localReview->local_review_id);
        
        // Assert: Verificar que la información del local está disponible
        $this->assertNotNull($retrievedLocalReview->local);
        $this->assertEquals($this->local->local_id, $retrievedLocalReview->local->local_id);
        $this->assertNotNull($retrievedLocalReview->review);
        $this->assertEquals($this->client->user_id, $retrievedLocalReview->user_id);
    }

    /**
     * QA-012 | Reseña contiene información del producto asociado [Positivo]
     * 
     * Objetivo: Verificar que la información del producto está disponible en la reseña de producto
     * Precondiciones: Reseña de producto publicada.
     */
    public function test_product_review_contains_associated_product_information()
    {
        // Arrange: Crear reseña de producto
        $productReview = Review::factory()->create([
            'comment' => 'Reseña del producto',
        ]);
        
        $productReviewEntry = ProductReview::create([
            'review_id' => $productReview->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Act: Recuperar la reseña con información del producto
        $retrievedProductReview = ProductReview::with(['review', 'product.locals'])
            ->find($productReviewEntry->product_review_id);
        
        // Assert: Verificar que la información del producto está disponible
        $this->assertNotNull($retrievedProductReview->product);
        $this->assertEquals($this->product->product_id, $retrievedProductReview->product->product_id);
        $this->assertNotNull($retrievedProductReview->review);
        $this->assertEquals($this->client->user_id, $retrievedProductReview->user_id);
    }

    /**
     * QA-013 | Mezcla de reseñas locales y de productos [Positivo]
     * 
     * Objetivo: Verificar que un cliente puede tener reseñas tanto de locales como de productos
     * Precondiciones: Cliente con reseñas de ambos tipos.
     */
    public function test_client_can_have_both_local_and_product_reviews()
    {
        // Arrange: Crear reseña local
        $localReviewData = Review::factory()->create([
            'rating' => 5,
            'comment' => 'Reseña local',
        ]);
        
        LocalReview::create([
            'review_id' => $localReviewData->review_id,
            'local_id' => $this->local->local_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Crear reseña de producto
        $productReviewData = Review::factory()->create([
            'rating' => 4,
            'comment' => 'Reseña de producto',
        ]);
        
        ProductReview::create([
            'review_id' => $productReviewData->review_id,
            'product_id' => $this->product->product_id,
            'user_id' => $this->client->user_id,
        ]);
        
        // Act: Obtener ambos tipos de reseñas
        $clientLocalReviews = LocalReview::where('user_id', $this->client->user_id)->get();
        $clientProductReviews = ProductReview::where('user_id', $this->client->user_id)->get();
        
        // Assert: Verificar que el cliente tiene ambos tipos de reseñas
        $this->assertEquals(1, $clientLocalReviews->count());
        $this->assertEquals(1, $clientProductReviews->count());
        
        // Verificar que ambas reseñas pertenecen al cliente
        $this->assertEquals($this->client->user_id, $clientLocalReviews->first()->user_id);
        $this->assertEquals($this->client->user_id, $clientProductReviews->first()->user_id);
    }
}
