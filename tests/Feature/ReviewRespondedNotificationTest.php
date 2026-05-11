<?php

namespace Tests\Feature;

use App\Events\ReviewResponded;
use App\Models\Local;
use App\Models\LocalReview;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Review;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * QA-276 | Pruebas de notificación de respuesta a reseñas (ReviewResponded)
 *
 * Historia de usuario: Como cliente, quiero recibir una notificación cuando el gerente
 * responda mi reseña, para poder ver la respuesta y continuar la conversación.
 *
 * Criterios de aceptación:
 * - CA1: Cuando el gerente responde una reseña, se genera una notificación (evento)
 * - CA2: La notificación llega únicamente al cliente que hizo la reseña
 * - CA3: La notificación indica que su reseña fue respondida
 * - CA4: El cliente puede acceder al detalle de la reseña desde la notificación
 * - CA5: La notificación se marca como leída después de abrirla
 */
class ReviewRespondedNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $gerente;
    protected User $cliente;
    protected Local $local;
    protected Product $producto;

    public function setUp(): void
    {
        parent::setUp();

        $this->gerente  = User::factory()->create(['role_id' => 2]); // Asegurar rol de Gerente para el middleware
        $this->cliente  = User::factory()->create();
        $this->local    = Local::factory()->create();
        $this->producto = Product::factory()->create();
        $this->producto->locals()->attach($this->local->local_id);
        $this->local->users()->attach($this->gerente->user_id);
    }

    // ================================================================
    // CA1 — Cuando el gerente responde una reseña, se genera un evento
    // ================================================================

    /**
     * CA1-001 | El evento ReviewResponded se instancia correctamente [Positivo]
     *
     * Objetivo: Verificar que el evento puede crearse con los datos requeridos
     * Precondiciones: Datos de cliente, reseña y local disponibles.
     */
    public function test_review_responded_event_can_be_instantiated_with_required_data()
    {
        $evento = new ReviewResponded(
            $this->cliente->user_id,
            1,
            'La Comarca',
            'Ceviche Especial'
        );

        $this->assertInstanceOf(ReviewResponded::class, $evento);
        $this->assertEquals($this->cliente->user_id, $evento->userId);
        $this->assertEquals(1, $evento->reviewId);
        $this->assertEquals('La Comarca', $evento->localName);
        $this->assertEquals('Ceviche Especial', $evento->productName);
    }

    /**
     * CA1-002 | El evento se despacha al responder una reseña de local [Positivo]
     *
     * Objetivo: Verificar que responder una reseña local genera el evento de notificación
     * Precondiciones: Reseña de local creada por el cliente, gerente autenticado.
     */
    public function test_review_responded_event_is_dispatched_when_manager_responds_local_review()
    {
        Event::fake();

        $review = Review::factory()->create(['rating' => 4, 'comment' => 'Buen local']);
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $this->actingAs($this->gerente)
            ->post("/resenas/{$localReview->local_review_id}/responder", [
                'response' => '¡Muchas gracias por tu visita!',
                'review_type' => 'local',
            ]);

        Event::assertDispatched(ReviewResponded::class);
    }

    /**
     * CA1-003 | El evento se despacha al responder una reseña de producto [Positivo]
     *
     * Objetivo: Verificar que responder una reseña de producto también genera el evento
     * Precondiciones: Reseña de producto creada por el cliente, gerente autenticado.
     */
    public function test_review_responded_event_is_dispatched_when_manager_responds_product_review()
    {
        Event::fake();

        $review = Review::factory()->create(['rating' => 5, 'comment' => 'Producto excelente']);
        $productReview = ProductReview::create([
            'review_id'  => $review->review_id,
            'product_id' => $this->producto->product_id,
            'user_id'    => $this->cliente->user_id,
        ]);

        $this->actingAs($this->gerente)
            ->post("/resenas/{$productReview->product_review_id}/responder", [
                'response' => '¡Nos alegra que te gustara el producto!',
                'review_type' => 'product',
            ]);

        Event::assertDispatched(ReviewResponded::class);
    }

    /**
     * CA1-004 | El evento NO se despacha si la respuesta está vacía [Negativo]
     *
     * Objetivo: Verificar que sin una respuesta válida no se genera la notificación
     * Precondiciones: Reseña existente, gerente autenticado, respuesta vacía.
     */
    public function test_event_is_not_dispatched_when_response_is_empty()
    {
        Event::fake();

        $review = Review::factory()->create(['rating' => 3, 'comment' => 'Regular']);
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $this->actingAs($this->gerente)
            ->post("/resenas/{$localReview->local_review_id}/responder", [
                'response' => '',
                'review_type' => 'local',
            ]);

        Event::assertNotDispatched(ReviewResponded::class);
    }

    /**
     * CA1-005 | El evento NO se despacha si la respuesta supera los 1000 caracteres [Negativo]
     *
     * Objetivo: Verificar que una respuesta demasiado larga es rechazada y no genera evento
     * Precondiciones: Reseña existente, gerente autenticado, respuesta de más de 1000 caracteres.
     */
    public function test_event_is_not_dispatched_when_response_exceeds_max_length()
    {
        Event::fake();

        $review = Review::factory()->create(['rating' => 2, 'comment' => 'No me gustó']);
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $this->actingAs($this->gerente)
            ->post("/resenas/{$localReview->local_review_id}/responder", [
                'response' => str_repeat('A', 1001),
                'review_type' => 'local',
            ]);

        Event::assertNotDispatched(ReviewResponded::class);
    }

    // ================================================================
    // CA2 — La notificación llega únicamente al cliente que hizo la reseña
    // ================================================================

    /**
     * CA2-001 | El evento se emite en el canal del cliente que hizo la reseña [Positivo]
     *
     * Objetivo: Verificar que el canal broadcast corresponde exactamente al user_id del cliente
     * Precondiciones: Evento instanciado con datos del cliente.
     */
    public function test_event_broadcasts_on_the_correct_client_channel()
    {
        $evento = new ReviewResponded(
            $this->cliente->user_id,
            1,
            'La Comarca',
            'Producto Test'
        );

        $channel = $evento->broadcastOn();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('user.' . $this->cliente->user_id, $channel->name);
    }

    /**
     * CA2-002 | El canal del evento NO corresponde al gerente ni a otros usuarios [Negativo]
     *
     * Objetivo: Verificar que la notificación no es enviada al gerente ni a terceros
     * Precondiciones: Evento instanciado con user_id del cliente.
     */
    public function test_event_channel_does_not_target_manager_or_other_users()
    {
        $otroUsuario = User::factory()->create();

        $evento = new ReviewResponded(
            $this->cliente->user_id,
            1,
            'La Comarca',
            'Producto Test'
        );

        $channelName = $evento->broadcastOn()->name;

        $this->assertNotEquals('user.' . $this->gerente->user_id, $channelName);
        $this->assertNotEquals('user.' . $otroUsuario->user_id, $channelName);
    }

    /**
     * CA2-003 | Dos clientes distintos reciben notificaciones en canales distintos [Positivo]
     *
     * Objetivo: Verificar el aislamiento de canales entre clientes diferentes
     * Precondiciones: Dos clientes con reseñas distintas.
     */
    public function test_each_client_receives_notification_on_their_own_isolated_channel()
    {
        $otroCliente = User::factory()->create();

        $evento1 = new ReviewResponded($this->cliente->user_id, 1, 'Local A', 'Producto A');
        $evento2 = new ReviewResponded($otroCliente->user_id, 2, 'Local B', 'Producto B');

        $channel1 = $evento1->broadcastOn()->name;
        $channel2 = $evento2->broadcastOn()->name;

        $this->assertNotEquals($channel1, $channel2);
        $this->assertEquals('user.' . $this->cliente->user_id, $channel1);
        $this->assertEquals('user.' . $otroCliente->user_id, $channel2);
    }

    /**
     * CA2-004 | El evento despachado pertenece al canal del cliente correcto [Positivo]
     *
     * Objetivo: Verificar que el evento despachado tiene el user_id del cliente que hizo la reseña
     * Precondiciones: Reseña local del cliente, gerente que responde.
     */
    public function test_dispatched_event_targets_the_client_who_wrote_the_review()
    {
        Event::fake();

        $review = Review::factory()->create(['rating' => 5, 'comment' => 'Excelente']);
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $this->actingAs($this->gerente)
            ->post("/resenas/{$localReview->local_review_id}/responder", [
                'response' => 'Gracias por visitarnos',
                'review_type' => 'local',
            ]);

        Event::assertDispatched(ReviewResponded::class, function ($event) {
            return $event->userId === $this->cliente->user_id;
        });
    }

    // ================================================================
    // CA3 — La notificación indica que la reseña fue respondida
    // ================================================================

    /**
     * CA3-001 | El payload del evento incluye un mensaje de respuesta [Positivo]
     *
     * Objetivo: Verificar que la notificación contiene un mensaje descriptivo
     * Precondiciones: Evento instanciado con datos completos.
     */
    public function test_broadcast_payload_includes_a_response_message()
    {
        $evento = new ReviewResponded(
            $this->cliente->user_id,
            1,
            'La Comarca',
            'Producto Test'
        );

        $payload = $evento->broadcastWith();

        $this->assertArrayHasKey('message', $payload);
        $this->assertNotEmpty($payload['message']);
    }

    /**
     * CA3-002 | El mensaje de la notificación indica que el gerente respondió [Positivo]
     *
     * Objetivo: Verificar que el mensaje informa explícitamente que la reseña fue respondida
     * Precondiciones: Evento instanciado.
     */
    public function test_notification_message_indicates_manager_responded()
    {
        $evento = new ReviewResponded(
            $this->cliente->user_id,
            1,
            'La Comarca',
            'Producto Test'
        );

        $payload = $evento->broadcastWith();

        $this->assertStringContainsStringIgnoringCase('respondió', $payload['message']);
    }

    /**
     * CA3-003 | El mensaje de la notificación incluye el nombre del local [Positivo]
     *
     * Objetivo: Verificar que el cliente sabe de qué local proviene la respuesta
     * Precondiciones: Evento instanciado con nombre de local específico.
     */
    public function test_notification_message_includes_the_local_name()
    {
        $localName = 'Tacos El Patron';

        $evento = new ReviewResponded(
            $this->cliente->user_id,
            1,
            $localName,
            'Producto Test'
        );

        $payload = $evento->broadcastWith();

        $this->assertStringContainsString($localName, $payload['message']);
    }

    /**
     * CA3-004 | El payload contiene todos los campos requeridos para la notificación [Positivo]
     *
     * Objetivo: Verificar que el evento transmite user_id, review_id, local_name, product_name y message
     * Precondiciones: Evento instanciado con datos completos.
     */
    public function test_broadcast_payload_contains_all_required_fields()
    {
        $evento = new ReviewResponded(
            $this->cliente->user_id,
            99,
            'Local Test',
            'Producto Test'
        );

        $payload = $evento->broadcastWith();

        $this->assertArrayHasKey('user_id', $payload);
        $this->assertArrayHasKey('review_id', $payload);
        $this->assertArrayHasKey('local_name', $payload);
        $this->assertArrayHasKey('product_name', $payload);
        $this->assertArrayHasKey('message', $payload);
    }

    /**
     * CA3-005 | Los valores del payload coinciden con los datos del evento [Positivo]
     *
     * Objetivo: Verificar la integridad de los datos transmitidos en el payload
     * Precondiciones: Evento con valores conocidos.
     */
    public function test_broadcast_payload_values_match_event_constructor_data()
    {
        $userId      = $this->cliente->user_id;
        $reviewId    = 77;
        $localName   = 'Restaurante Test';
        $productName = 'Plato Test';

        $evento = new ReviewResponded($userId, $reviewId, $localName, $productName);

        $payload = $evento->broadcastWith();

        $this->assertEquals($userId, $payload['user_id']);
        $this->assertEquals($reviewId, $payload['review_id']);
        $this->assertEquals($localName, $payload['local_name']);
        $this->assertEquals($productName, $payload['product_name']);
    }

    // ================================================================
    // CA4 — El cliente puede acceder al detalle de la reseña desde la notificación
    // ================================================================

    /**
     * CA4-001 | El payload incluye el review_id para navegar al detalle [Positivo]
     *
     * Objetivo: Verificar que la notificación contiene el ID necesario para ir al detalle de la reseña
     * Precondiciones: Evento instanciado con un review_id específico.
     */
    public function test_notification_payload_includes_review_id_for_detail_navigation()
    {
        $reviewId = 42;

        $evento = new ReviewResponded(
            $this->cliente->user_id,
            $reviewId,
            'Local Test',
            'Producto Test'
        );

        $payload = $evento->broadcastWith();

        $this->assertArrayHasKey('review_id', $payload);
        $this->assertEquals($reviewId, $payload['review_id']);
    }

    /**
     * CA4-002 | El review_id del payload apunta a una reseña existente en BD [Positivo]
     *
     * Objetivo: Verificar que el review_id transmitido corresponde a una reseña real y accesible
     * Precondiciones: Reseña creada y respondida, evento generado.
     */
    public function test_review_id_in_payload_points_to_existing_review_in_database()
    {
        $review = Review::factory()->create([
            'rating'   => 5,
            'comment'  => 'Excelente local',
            'response' => 'Gracias por tu visita',
        ]);

        LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $evento = new ReviewResponded(
            $this->cliente->user_id,
            $review->review_id,
            'Local Test',
            'Producto Test'
        );

        $payload = $evento->broadcastWith();

        $reviewEncontrada = Review::find($payload['review_id']);
        $this->assertNotNull($reviewEncontrada);
        $this->assertEquals($review->review_id, $reviewEncontrada->review_id);
    }

    /**
     * CA4-003 | La reseña apuntada por el payload contiene la respuesta del gerente [Positivo]
     *
     * Objetivo: Verificar que al navegar al detalle, la respuesta del gerente es visible
     * Precondiciones: Reseña con respuesta guardada en BD.
     */
    public function test_review_pointed_by_payload_contains_manager_response()
    {
        $respuesta = 'Gracias por tu comentario, esperamos verte pronto.';

        $review = Review::factory()->create([
            'rating'   => 4,
            'comment'  => 'Muy buena comida',
            'response' => $respuesta,
        ]);

        LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $evento  = new ReviewResponded($this->cliente->user_id, $review->review_id, 'Local Test', 'Test');
        $payload = $evento->broadcastWith();

        $reviewDesdePayload = Review::find($payload['review_id']);

        $this->assertNotNull($reviewDesdePayload->response);
        $this->assertEquals($respuesta, $reviewDesdePayload->response);
    }

    /**
     * CA4-004 | El payload identifica al cliente receptor para permitir acceso al detalle [Positivo]
     *
     * Objetivo: Verificar que el user_id del payload permite identificar al cliente
     * Precondiciones: Evento instanciado con user_id del cliente.
     */
    public function test_payload_user_id_identifies_the_correct_client()
    {
        $evento = new ReviewResponded(
            $this->cliente->user_id,
            1,
            'Local Test',
            'Producto Test'
        );

        $payload = $evento->broadcastWith();

        $clienteEncontrado = User::find($payload['user_id']);

        $this->assertNotNull($clienteEncontrado);
        $this->assertEquals($this->cliente->user_id, $clienteEncontrado->user_id);
        $this->assertNotEquals($this->gerente->user_id, $clienteEncontrado->user_id);
    }

    // ================================================================
    // CA5 — La notificación se marca como leída (reseña tiene respuesta)
    // ================================================================

    /**
     * CA5-001 | La reseña queda con respuesta guardada tras que el gerente responde [Positivo]
     *
     * Objetivo: Verificar que el campo response de la reseña se actualiza al responder
     * Precondiciones: Reseña sin respuesta, gerente autenticado que responde.
     */
    public function test_review_response_field_is_saved_after_manager_replies()
    {
        Event::fake();

        $review = Review::factory()->create(['rating' => 4, 'comment' => 'Muy bueno', 'response' => null]);
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $this->actingAs($this->gerente)
            ->post("/resenas/{$localReview->local_review_id}/responder", [
                'response' => 'Gracias por tu comentario',
                'review_type' => 'local',
            ]);

        $reviewActualizado = Review::find($review->review_id);

        $this->assertNotNull($reviewActualizado->response);
        $this->assertEquals('Gracias por tu comentario', $reviewActualizado->response);
    }

    /**
     * CA5-002 | Una reseña sin responder tiene el campo response en null [Positivo]
     *
     * Objetivo: Verificar el estado inicial de una reseña (no respondida = response null)
     * Precondiciones: Reseña recién creada sin respuesta.
     */
    public function test_review_without_response_has_null_response_field()
    {
        $review = Review::factory()->create([
            'rating'  => 3,
            'comment' => 'Regular experiencia',
        ]);

        LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $reviewSinRespuesta = Review::find($review->review_id);

        $this->assertNull($reviewSinRespuesta->response);
    }

    /**
     * CA5-003 | El payload del evento tiene datos suficientes para construir la notificación [Positivo]
     *
     * Objetivo: Verificar que el evento contiene toda la información para renderizar la notificación
     * Precondiciones: Evento instanciado con todos sus campos.
     */
    public function test_event_payload_has_all_data_needed_to_build_the_notification()
    {
        $evento = new ReviewResponded(
            $this->cliente->user_id,
            10,
            'Local Comarca',
            'Ceviche Especial'
        );

        $payload = $evento->broadcastWith();

        $this->assertNotEmpty($payload['message']);
        $this->assertIsInt($payload['user_id']);
        $this->assertIsInt($payload['review_id']);
        $this->assertIsString($payload['local_name']);
        $this->assertIsString($payload['product_name']);
        $this->assertNotEmpty($payload['local_name']);
        $this->assertNotEmpty($payload['product_name']);
    }

    /**
     * CA5-004 | Una reseña respondida tiene response distinto de null [Positivo]
     *
     * Objetivo: Contrastar el estado respondida vs. no respondida de una reseña
     * Precondiciones: Dos reseñas: una con respuesta y otra sin respuesta.
     */
    public function test_responded_and_unresponded_reviews_differ_in_response_field()
    {
        $reviewRespondida = Review::factory()->create([
            'rating'   => 5,
            'comment'  => 'Muy bueno',
            'response' => 'Gracias por su visita',
        ]);

        $reviewSinRespuesta = Review::factory()->create([
            'rating'  => 4,
            'comment' => 'Estuvo bien',
        ]);

        LocalReview::create([
            'review_id' => $reviewRespondida->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        LocalReview::create([
            'review_id' => $reviewSinRespuesta->review_id,
            'local_id'  => $this->local->local_id,
            'user_id'   => $this->cliente->user_id,
        ]);

        $conRespuesta    = Review::find($reviewRespondida->review_id);
        $sinRespuesta    = Review::find($reviewSinRespuesta->review_id);

        $this->assertNotNull($conRespuesta->response);
        $this->assertNull($sinRespuesta->response);
        $this->assertNotEquals($conRespuesta->response, $sinRespuesta->response);
    }
}
