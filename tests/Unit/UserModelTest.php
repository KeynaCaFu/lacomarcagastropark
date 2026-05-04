<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * PRUEBA UNITARIA: Modelo User
 * 
 * Objetivo: Validar el modelo User sin base de datos
 * Casos de prueba: 3
 */
class UserModelTest extends TestCase
{
    /**
     * Test 1: Usuario debe poder ser instanciado
     * 
     * Requisito: El modelo User debe poder crear instancias
     */
    public function test_user_can_be_instantiated()
    {
        $user = new User([
            'full_name' => 'Juan Gerente',
            'email' => 'juan@test.com',
            'password' => 'password123', // Sin encriptar para unitarias
            'role_id' => 2, // Gerente
            'status' => 'Active'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('juan@test.com', $user->email);
        $this->assertEquals('Juan Gerente', $user->full_name);
    }

    /**
     * Test 2: Validar atributos fillable del User
     * 
     * Requisito: Los campos correctos deben ser mass-assignable
     */
    public function test_user_has_correct_fillable_attributes()
    {
        $user = new User();
        
        $expected = [
            'full_name', 'email', 'phone', 'password', 'role_id',
            'status', 'provider', 'provider_id', 'remember_token',
            'avatar', 'temporary_password', 'temporary_password_expires_at',
            'google_token_expires_at', 'google_token_last_refreshed_at'
        ];

        $this->assertEquals($expected, $user->getFillable());
    }

    /**
     * Test 3: Validar tabla y primary key correctos
     * 
     * Requisito: El modelo debe usar la tabla correcta (tbuser)
     */
    public function test_user_uses_correct_table_and_primary_key()
    {
        $user = new User();
        
        $this->assertEquals('tbuser', $user->getTable());
        $this->assertEquals('user_id', $user->getKeyName());
    }
}
