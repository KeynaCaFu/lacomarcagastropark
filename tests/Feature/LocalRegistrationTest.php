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
 * - Un gerente debe existir antes de crear el local (regla de negocio)
 */
class LocalRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test 1: Crear Local en BD con gerente asignado
     *
     * Pasos:
     * 1. Crear gerente (requisito previo obligatorio)
     * 2. Crear Local con solo nombre (como en el formulario real)
     * 3. Asociar el gerente al local
     * 4. Verificar que fue creado en BD con estado Inactive
     * 5. Verificar que tiene ID asignado y gerente asociado
     */
    public function test_can_create_local_in_database()
    {
        // Requisito previo: el gerente debe existir antes de crear el local
        $role = Role::firstOrCreate(['role_type' => 'Gerente']);
        $manager = User::create([
            'full_name' => 'Gerente Test',
            'email'     => 'gerente.test@test.com',
            'password'  => bcrypt('password123'),
            'role_id'   => $role->role_id,
            'status'    => 'Active',
        ]);

        // Crear local con solo nombre (únicos campos del formulario de creación)
        $local = Local::create([
            'name'   => 'Local Test',
            'status' => 'Inactive',
        ]);

        // Asociar gerente al local (obligatorio según regla de negocio)
        $local->users()->attach($manager->user_id);

        // VALIDACIÓN 1: Debe existir en BD con estado Inactive
        $this->assertDatabaseHas('tblocal', [
            'name'   => 'Local Test',
            'status' => 'Inactive',
        ]);

        // VALIDACIÓN 2: Debe tener ID asignado
        $this->assertNotNull($local->local_id);

        // VALIDACIÓN 3: Nombre debe coincidir
        $this->assertEquals('Local Test', $local->name);

        // VALIDACIÓN 4: Debe tener exactamente un gerente asignado
        $this->assertEquals(1, $local->users()->count());
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
        $role = Role::firstOrCreate(['role_type' => 'Gerente']);

        // Crear Local
        $local = Local::create([
            'name'   => 'Local Multiple Managers',
            'status' => 'Inactive',
        ]);

        // Crear 2 gerentes
        $manager1 = User::create([
            'full_name' => 'Manager 1',
            'email'     => 'manager1@test.com',
            'password'  => bcrypt('pass1'),
            'role_id'   => $role->role_id,
            'status'    => 'Active',
        ]);

        $manager2 = User::create([
            'full_name' => 'Manager 2',
            'email'     => 'manager2@test.com',
            'password'  => bcrypt('pass2'),
            'role_id'   => $role->role_id,
            'status'    => 'Active',
        ]);

        // Asociar gerentes al local usando belongsToMany
        $local->users()->attach([$manager1->user_id, $manager2->user_id]);

        // VALIDACIÓN 1: Debe haber 2 gerentes en el local
        $this->assertEquals(2, $local->users->count());

        // VALIDACIÓN 2: Relación en tabla pivot debe existir
        $this->assertDatabaseHas('tbuser_local', [
            'local_id' => $local->local_id,
            'user_id'  => $manager1->user_id,
        ]);

        $this->assertDatabaseHas('tbuser_local', [
            'local_id' => $local->local_id,
            'user_id'  => $manager2->user_id,
        ]);

        // VALIDACIÓN 3: Local debe contener ambos managers
        $this->assertTrue($local->users->contains($manager1));
        $this->assertTrue($local->users->contains($manager2));
    }

    /**
     * Test 3: Local debe existir en BD después de creación
     *
     * Pasos:
     * 1. Crear gerente (requisito previo obligatorio)
     * 2. Crear Local con solo nombre
     * 3. Asociar gerente
     * 4. Buscar Local por nombre
     * 5. Verificar que fue encontrado con estado correcto
     */
    public function test_created_local_exists_in_database()
    {
        // Requisito previo: el gerente debe existir antes de crear el local
        $role = Role::firstOrCreate(['role_type' => 'Gerente']);
        $manager = User::create([
            'full_name' => 'Gerente Existencia',
            'email'     => 'gerente.existencia@test.com',
            'password'  => bcrypt('password123'),
            'role_id'   => $role->role_id,
            'status'    => 'Active',
        ]);

        $local = Local::create([
            'name'   => 'Existing Local',
            'status' => 'Inactive',
        ]);
        $local->users()->attach($manager->user_id);

        // Buscar Local
        $found = Local::where('name', 'Existing Local')->first();

        // VALIDACIÓN: Debe existir con nombre y estado correctos
        $this->assertNotNull($found);
        $this->assertEquals('Existing Local', $found->name);
        $this->assertEquals('Inactive', $found->status);
    }
}
