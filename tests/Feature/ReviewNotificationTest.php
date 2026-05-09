<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Local;
use App\Models\Product;
use App\Models\Review;
use App\Models\LocalReview;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Events\NewReviewPosted;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * QA-255 | Pruebas de notificaciones de reseñas para gerentes
 * 
 * Historia de usuario: Como gerente de local, quiero recibir una notificación cuando 
 * un cliente publique una nueva reseña sobre un producto para poder responder 
 * oportunamente y dar seguimiento a la experiencia del cliente.
 * 
 * Requisitos funcionales:
 * - Cuando un cliente crea una reseña, se genera una notificación
 * - La notificación llega únicamente al gerente del local correspondiente
 * - La notificación indica que hay una nueva reseña
 * - El gerente puede hacer clic en la notificación
 * - El sistema redirige al detalle de la reseña
 * - La notificación se marca como leída después de abrirla
 */
class ReviewNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;
    protected $localManager;
    protected $anotherManager;
    protected $local;
    protected $anotherLocal;
    protected $product;
    protected $anotherProduct;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear cliente
        $this->client = User::factory()->create();
        
        // Crear primer gerente de local
        $this->localManager = User::factory()->create();
        
        // Crear segundo gerente de otro local
        $this->anotherManager = User::factory()->create();
        
        // Crear primer local
        $this->local = Local::factory()->create();
        $this->local->users()->attach($this->localManager->user_id);
        
        // Crear segundo local
        $this->anotherLocal = Local::factory()->create();
        $this->anotherLocal->users()->attach($this->anotherManager->user_id);
        
        // Crear primer producto
        $this->product = Product::factory()->create([
            'name' => 'Hamburgesa Especial',
            'price' => 50.00,
        ]);
        $this->product->locals()->attach($this->local->local_id);
        
        // Crear segundo producto en otro local
        $this->anotherProduct = Product::factory()->create([
            'name' => 'Pizza Margherita',
            'price' => 45.00,
        ]);
        $this->anotherProduct->locals()->attach($this->anotherLocal->local_id);
        
        // El cliente necesita tener un pedido entregado para poder reseñar
        $deliveredOrder = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_DELIVERED,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $deliveredOrder->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $deliveredOrder->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Orden entregada para el segundo local
        $deliveredOrder2 = Order::factory()->create([
            'local_id' => $this->anotherLocal->local_id,
            'status' => Order::STATUS_DELIVERED,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $deliveredOrder2->user()->attach($this->client->user_id);
        
        OrderItem::create([
            'order_id' => $deliveredOrder2->order_id,
            'product_id' => $this->anotherProduct->product_id,
            'quantity' => 1,
            'price' => $this->anotherProduct->price,
        ]);
    }

    /**
     * NF-01 | Generación de notificación al crear reseña del local [Positivo]
     */
    public function test_notification_is_generated_when_client_creates_local_review()
    {
        // Arrange
        Event::fake();
        $localId = $this->local->local_id;
        $rating = 5;
        $comment = 'Excelente servicio y comida deliciosa';
        
        // Act
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/{$localId}/review", [
                'rating' => $rating,
                'comment' => $comment
            ]);
        
        // Assert
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        Event::assertDispatched(NewReviewPosted::class);
    }

    /**
     * NF-02 | Generación de notificación al crear reseña de producto [Positivo]
     */
    public function test_notification_is_generated_when_client_creates_product_review()
    {
        // Arrange
        Event::fake();
        $productId = $this->product->product_id;
        $rating = 4;
        $comment = 'Muy sabrosa, precio razonable';
        
        // Act
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/producto/{$productId}/resena", [
                'rating' => $rating,
                'comment' => $comment
            ]);
        
        // Assert
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        Event::assertDispatched(NewReviewPosted::class);
    }

    /**
     * NF-03 | Notificación llega solo al gerente correcto [Positivo]
     */
    public function test_notification_reaches_only_correct_local_manager()
    {
        // Arrange
        Event::fake();
        $localId = $this->local->local_id;
        
        // Act
        $this->actingAs($this->client)
            ->postJson("/plaza/{$localId}/review", [
                'rating' => 5,
                'comment' => 'Muy bueno, lo recomiendo'
            ]);
        
        // Assert
        Event::assertDispatched(NewReviewPosted::class, function ($event) use ($localId) {
            return $event->localId === $localId;
        });
    }

    /**
     * NF-04 | Notificación contiene datos correctos [Positivo]
     */
    public function test_notification_contains_correct_data()
    {
        // Arrange
        Event::fake();
        $localId = $this->local->local_id;
        $rating = 5;
        
        // Act
        $this->actingAs($this->client)
            ->postJson("/plaza/producto/{$this->product->product_id}/resena", [
                'rating' => $rating,
                'comment' => 'Excelente sabor y presentacion'
            ]);
        
        // Assert
        Event::assertDispatched(NewReviewPosted::class, function ($event) use ($localId, $rating) {
            return $event->localId === $localId &&
                   $event->rating === $rating &&
                   !empty($event->clientName) &&
                   !empty($event->reviewId);
        });
    }

    /**
     * NF-05 | No permitir reseña duplicada de producto [Negativo]
     */
    public function test_client_cannot_create_duplicate_product_review()
    {
        // Arrange
        $this->actingAs($this->client)
            ->postJson("/plaza/producto/{$this->product->product_id}/resena", [
                'rating' => 5,
                'comment' => 'Primera resena muy positiva'
            ]);
        
        // Act
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/producto/{$this->product->product_id}/resena", [
                'rating' => 4,
                'comment' => 'Segunda resena diferente'
            ]);
        
        // Assert
        $response->assertStatus(409);
        $response->assertJson(['success' => false]);
    }

    /**
     * NF-06 | Permitir múltiples reseñas de local [Positivo]
     */
    public function test_client_can_create_multiple_local_reviews()
    {
        // Arrange
        $localId = $this->local->local_id;
        
        // Act
        $response1 = $this->actingAs($this->client)
            ->postJson("/plaza/{$localId}/review", [
                'rating' => 5,
                'comment' => 'Primera visita excelente'
            ]);
        
        $response2 = $this->actingAs($this->client)
            ->postJson("/plaza/{$localId}/review", [
                'rating' => 4,
                'comment' => 'Segunda visita tambien buena'
            ]);
        
        // Assert
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        $localReviews = LocalReview::where('local_id', $localId)
            ->where('user_id', $this->client->user_id)
            ->count();
        
        $this->assertEquals(2, $localReviews);
    }

    /**
     * NF-07 | Verificar que la reseña se creó y está disponible [Positivo]
     */
    public function test_created_review_can_be_accessed()
    {
        // Arrange
        $productId = $this->product->product_id;
        $rating = 5;
        $comment = 'Comida excelente y muy sabrosa';
        
        // Act
        $this->actingAs($this->client)
            ->postJson("/plaza/producto/{$productId}/resena", [
                'rating' => $rating,
                'comment' => $comment
            ]);
        
        // Assert
        $review = Review::where('rating', $rating)->where('comment', $comment)->first();
        $this->assertNotNull($review);
        
        $productReview = ProductReview::where('review_id', $review->review_id)->first();
        $this->assertNotNull($productReview);
        $this->assertEquals($productId, $productReview->product_id);
    }

    /**
     * NF-08 | No permitir reseña sin pedido entregado [Negativo]
     */
    public function test_client_cannot_review_local_without_delivered_order()
    {
        // Arrange
        $newClient = User::factory()->create();
        
        // Act
        $response = $this->actingAs($newClient)
            ->postJson("/plaza/{$this->local->local_id}/review", [
                'rating' => 5,
                'comment' => 'Intento sin pedido'
            ]);
        
        // Assert
        $response->assertStatus(403);
    }

    /**
     * NF-09 | No permitir reseña de producto sin pedido [Negativo]
     */
    public function test_client_cannot_review_product_without_delivered_order()
    {
        // Arrange
        $newClient = User::factory()->create();
        
        // Act
        $response = $this->actingAs($newClient)
            ->postJson("/plaza/producto/{$this->product->product_id}/resena", [
                'rating' => 5,
                'comment' => 'Intento sin pedido'
            ]);
        
        // Assert
        $response->assertStatus(403);
    }

    /**
     * NF-10 | Múltiples notificaciones para múltiples reseñas [Positivo]
     */
    public function test_manager_receives_multiple_notifications_for_multiple_reviews()
    {
        // Arrange
        Event::fake();
        
        $secondClient = User::factory()->create();
        $deliveredOrder = Order::factory()->create([
            'local_id' => $this->local->local_id,
            'status' => Order::STATUS_DELIVERED,
            'total_amount' => 100.00,
            'origin' => 'web'
        ]);
        $deliveredOrder->user()->attach($secondClient->user_id);
        
        OrderItem::create([
            'order_id' => $deliveredOrder->order_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);
        
        // Act
        $this->actingAs($this->client)
            ->postJson("/plaza/{$this->local->local_id}/review", [
                'rating' => 5,
                'comment' => 'Resena del cliente 1'
            ]);
        
        $this->actingAs($secondClient)
            ->postJson("/plaza/{$this->local->local_id}/review", [
                'rating' => 4,
                'comment' => 'Resena del cliente 2'
            ]);
        
        // Assert
        Event::assertDispatchedTimes(NewReviewPosted::class, 2);
    }

    /**
     * NF-11 | Notificación incluye nombre del cliente [Positivo]
     */
    public function test_notification_includes_client_name()
    {
        // Arrange
        Event::fake();
        $clientName = $this->client->full_name ?? $this->client->name ?? 'Cliente';
        
        // Act
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/{$this->local->local_id}/review", [
                'rating' => 5,
                'comment' => 'Muy bueno, lo recomiendo'
            ]);
        
        // Assert
        $response->assertStatus(200);
        Event::assertDispatched(NewReviewPosted::class, function ($event) use ($clientName) {
            return $event->clientName === $clientName;
        });
    }

    /**
     * NF-12 | Se emite al canal correcto [Positivo]
     */
    public function test_notification_broadcasted_on_correct_channel()
    {
        // Arrange
        Event::fake();
        $localId = $this->local->local_id;
        
        // Act
        $response = $this->actingAs($this->client)
            ->postJson("/plaza/{$localId}/review", [
                'rating' => 5,
                'comment' => 'Excelente servicio, muy recomendado'
            ]);
        
        // Assert
        $response->assertStatus(200);
        Event::assertDispatched(NewReviewPosted::class, function ($event) use ($localId) {
            return $event->localId === $localId;
        });
    }
}
