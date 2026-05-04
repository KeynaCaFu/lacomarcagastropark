<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Local;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * PRUEBA DE INTEGRACIÓN: Registro de Local
 * 
 * Objetivo: Validar que se puede registrar un Local en la BD
 * Casos de prueba: 3
 * 
 * Requisitos:
 * - Base de datos de testing debe existir
 * - Tabla tblocal debe estar disponible
 * - Tabla tbuser_local (pivot) debe estar disponible
 */
class LocalRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test 1: Crear Local en BD
     * 
     * Pasos:
     * 1. Crear Local con datos válidos
     * 2. Verificar que fue creado en BD
     * 3. Verificar que tiene ID asignado
     */
    public function test_can_create_local_in_database()
    {
        $local = Local::create([
            'name' => 'Local Test',
            'description' => 'Un local de prueba',
            'contact' => '2765-3456',
            'status' => 'Active',
            'image_logo' => 'images/logo.png'
        ]);

        // VALIDACIÓN 1: Debe existir en BD
        $this->assertDatabaseHas('tblocal', [
            'name' => 'Local Test',
            'status' => 'Active'
        ]);

        // VALIDACIÓN 2: Debe tener ID asignado
        $this->assertNotNull($local->local_id);

        // VALIDACIÓN 3: Datos deben coincidir
        $this->assertEquals('Local Test', $local->name);
        $this->assertEquals('Un local de prueba', $local->description);
    }

    /**
     * Test 2: Local puede tener múltiples gerentes (relación)
     * 
     * Pasos:
     * 1. Crear Local
     * 2. Crear 2 gerentes
     * 3. Asociar gerentes al Local
     * 4. Verificar relación en BD (tabla pivot)
     */
    public function test_local_can_have_multiple_managers()
    {
        // Crear rol Gerente
        $role = Role::firstOrCreate(
            ['role_type' => 'Gerente'],
            ['description' => 'Gerente de Local']
        );

        // Crear Local
        $local = Local::create([
            'name' => 'Local Multiple Managers',
            'description' => 'Test múltiples gerentes',
            'contact' => '1234-5678',
            'status' => 'Active'
        ]);

        // Crear 2 gerentes
        $manager1 = User::create([
            'full_name' => 'Manager 1',
            'email' => 'manager1@test.com',
            'password' => bcrypt('pass1'),
            'role_id' => $role->role_id,
            'status' => 'Active'
        ]);

        $manager2 = User::create([
            'full_name' => 'Manager 2',
            'email' => 'manager2@test.com',
            'password' => bcrypt('pass2'),
            'role_id' => $role->role_id,
            'status' => 'Active'
        ]);

        // Asociar gerentes al local usando belongsToMany
        $local->users()->attach([$manager1->user_id, $manager2->user_id]);

        // VALIDACIÓN 1: Debe haber 2 gerentes en el local
        $this->assertEquals(2, $local->users->count());

        // VALIDACIÓN 2: Relación en tabla pivot debe existir
        $this->assertDatabaseHas('tbuser_local', [
            'local_id' => $local->local_id,
            'user_id' => $manager1->user_id
        ]);

        $this->assertDatabaseHas('tbuser_local', [
            'local_id' => $local->local_id,
            'user_id' => $manager2->user_id
        ]);

        // VALIDACIÓN 3: Local debe contener ambos managers
        $this->assertTrue($local->users->contains($manager1));
        $this->assertTrue($local->users->contains($manager2));
    }

    /**
     * Test 3: Local debe existir en BD después de creación
     * 
     * Pasos:
     * 1. Crear Local
     * 2. Buscar Local por nombre
     * 3. Verificar que fue encontrado
     */
    public function test_created_local_exists_in_database()
    {
        Local::create([
            'name' => 'Existing Local',
            'description' => 'Test existencia',
            'contact' => '9999-8888',
            'status' => 'Active'
        ]);

        // Buscar Local
        $local = Local::where('name', 'Existing Local')->first();
        
        // VALIDACIÓN: Debe existir
        $this->assertNotNull($local);
        $this->assertEquals('Existing Local', $local->name);
        $this->assertEquals('Test existencia', $local->description);
    }
}
