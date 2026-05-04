<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Local;
use App\Models\Review;
use App\Models\LocalReview;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewDeletionTest extends TestCase
{
    
    // use DatabaseTransactions;
    // ./vendor/bin/pest tests/Feature/ReviewDeletionTest.php
    // /** tests/Feature/ — para pruebas que prueban flujos completos,
    // con peticiones HTTP, base de datos, autenticación. O sea, varias capas juntas.*/



    /** @test */
    public function usuario_puede_eliminar_su_propia_resena()
    {
        // Crear usuario y loguearlo
        $user = User::factory()->create();

        // Crear un local
        $local = Local::factory()->create();

        //  Crear la reseña
        $review = Review::create([
            'rating'  => 5,
            'comment' => 'Excelente servicio, muy recomendado.',
            'date'    => now(),
        ]);

        // Conectar la reseña al local y al usuario
        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $local->local_id,
            'user_id'   => $user->user_id,
        ]);

        // Hacer  petición DELETE como ese usuario
        $response = $this->actingAs($user)
            ->deleteJson("/plaza/{$local->local_id}/review/{$localReview->local_review_id}");

        //  Verificar que respondió bien
        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        // Verificar que ya no esta en la base de datos
        $this->assertDatabaseMissing('tblocal_review', [
            'local_review_id' => $localReview->local_review_id
        ]);
    } 
    
    
    /** @test */
    public function usuario_no_puede_eliminar_resena_ajena()
    {
        //  Crear dos usuarios
        $propietario = User::factory()->create();
        $atacante    = User::factory()->create();

        // Crear un local
        $local = Local::factory()->create();

        // Crear reseña del usuario logueado
        $review = Review::create([
            'rating'  => 4,
            'comment' => 'Muy buena experiencia en este local.',
            'date'    => now(),
        ]);

        $localReview = LocalReview::create([
            'review_id' => $review->review_id,
            'local_id'  => $local->local_id,
            'user_id'   => $propietario->user_id,
        ]);

        // el otro usuario intenta eliminar la resenia del logueado 
        $response = $this->actingAs($atacante)
            ->deleteJson("/plaza/{$local->local_id}/review/{$localReview->local_review_id}");

        // Debe dar error 404
        $response->assertStatus(404);

        // 6. La reseña debe seguir existiendo
        $this->assertDatabaseHas('tblocal_review', [
            'local_review_id' => $localReview->local_review_id
        ]);
    }
     
}