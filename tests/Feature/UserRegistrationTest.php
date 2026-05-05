<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * PRUEBA DE INTEGRACIÓN: Registro de Usuario (Gerente)
 *
 * Objetivo: Validar que se puede registrar un usuario Gerente en la BD
 * Casos de prueba: 3
 *
 * Requisitos:
 * - Base de datos de testing debe existir
 * - Tabla tbuser debe estar disponible
 * - Tabla tbrole debe estar disponible
 */
class UserRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test 1: Crear usuario Gerente en BD
     *
     * Pasos:
     * 1. Crear/obtener rol Gerente
     * 2. Crear usuario con ese rol
     * 3. Verificar que fue creado en BD
     * 4. Verificar que es realmente Gerente
     */
    public function test_can_create_manager_user_in_database()
    {
        // PASO 1: Asegurar que el rol Gerente existe
        $role = Role::firstOrCreate(['role_type' => 'Gerente']);

        // PASO 2: Crear usuario
        $user = User::create([
            'full_name' => 'Carlos Gerente',
            'email'     => 'carlos@test.com',
            'phone'     => '8765-4321',
            'password'  => bcrypt('password123'),
            'role_id'   => $role->role_id,
            'status'    => 'Active',
        ]);

        // VALIDACIÓN 1: Usuario debe existir en BD
        $this->assertDatabaseHas('tbuser', [
            'email'     => 'carlos@test.com',
            'full_name' => 'Carlos Gerente',
            'status'    => 'Active',
        ]);

        // VALIDACIÓN 2: ID debe estar asignado
        $this->assertNotNull($user->user_id);

        // VALIDACIÓN 3: Debe ser Gerente
        $user->load('role');
        $this->assertTrue($user->isAdminLocal());
    }

    /**
     * Test 2: Usuario registrado debe existir en BD
     *
     * Pasos:
     * 1. Crear usuario
     * 2. Buscar usuario por email
     * 3. Verificar datos intactos
     */
    public function test_registered_user_exists_in_database()
    {
        $role = Role::firstOrCreate(['role_type' => 'Gerente']);

        User::create([
            'full_name' => 'Maria Manager',
            'email'     => 'maria@test.com',
            'password'  => bcrypt('secret123'),
            'role_id'   => $role->role_id,
            'status'    => 'Active',
        ]);

        // VALIDACIÓN: Verificar en BD
        $user = User::where('email', 'maria@test.com')->first();

        $this->assertNotNull($user);
        $this->assertEquals('Maria Manager', $user->full_name);
        $this->assertEquals('Active', $user->status);
        $this->assertEquals($role->role_id, $user->role_id);
    }

    /**
     * Test 3: Múltiples usuarios pueden ser creados
     *
     * Pasos:
     * 1. Crear 3 usuarios diferentes
     * 2. Verificar que los 3 existen en BD
     */
    public function test_multiple_managers_can_be_created()
    {
        $role = Role::firstOrCreate(['role_type' => 'Gerente']);

        // Crear 3 usuarios
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'full_name' => "Manager $i",
                'email'     => "manager$i@test.com",
                'password'  => bcrypt("pass$i"),
                'role_id'   => $role->role_id,
                'status'    => 'Active',
            ]);
        }

        // VALIDACIÓN: Cada uno debe existir
        for ($i = 1; $i <= 3; $i++) {
            $this->assertDatabaseHas('tbuser', [
                'email'     => "manager$i@test.com",
                'full_name' => "Manager $i",
                'status'    => 'Active',
            ]);
        }
    }
}
