<?php

namespace Tests\Unit;

use App\Models\LocalReview;
use PHPUnit\Framework\TestCase;

class LocalReviewTest extends TestCase
{   //./vendor/bin/pest tests/Unit/LocalReviewTest.php
    /*/Unit/ — para pruebas que prueban una sola cosa aislada, sin base de datos, sin HTTP, 
    sin nada externo. 
    Solo PHP puro. */
    
    /** @test */
    public function resena_pertenece_al_usuario_correcto()
    {
        $localReview = new LocalReview();
        $localReview->user_id = 5;

        $this->assertTrue($localReview->perteneceAlUsuario(5));
    }

    /** @test */
    public function resena_no_pertenece_a_usuario_ajeno()
    {
        $localReview = new LocalReview();
        $localReview->user_id = 5;

        $this->assertFalse($localReview->perteneceAlUsuario(99));
    }
}