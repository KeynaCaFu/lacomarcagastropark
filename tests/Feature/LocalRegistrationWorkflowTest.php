<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Local;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * PRUEBA DE SISTEMA: Flujo Completo (End-to-End)
 * 
 * Objetivo: Validar el flujo COMPLETO del negocio:
 * 1. Registrar un Usuario con rol Gerente
 * 2. Registrar un Local
 * 3. Asociar el Gerente al Local
 * 4. Verificar que todo funciona correctamente
 * 
 * Casos de prueba: 4
 * 
 * Requisito de Negocio:
 * "Un Local DEBE TENER al menos un Gerente registrado"
 */
class LocalRegistrationWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Principal: Flujo Completo
     * 
     * Escenario: Registrar un Gerente, Registrar un Local, Asociarlos
     * 
     * Pasos:
     * 1. Crear rol Gerente
     * 2. Crear usuario con rol Gerente
     * 3. Verificar que usuario es Gerente
     * 4. Crear Local
     * 5. Asociar Gerente a Local
     * 6. Verificar que relación existe
     * 7. Verificar desde ambos lados (Local.users / User.locals)
     */
    public function test_complete_workflow_register_manager_and_local()
    {
        // ===== PASO 1: REGISTRAR USUARIO GERENTE =====
        
        // Prerequisito: Rol Gerente debe existir
        $role = Role::firstOrCreate(
            ['role_type' => 'Gerente'],
            ['description' => 'Gerente de Local']
        );

        // Crear usuario Gerente
        $manager = User::create([
            'full_name' => 'Juan Carlos Gerente',
            'email' => 'juan.gerente@lacomarca.com',
            'phone' => '8765-4321',
            'password' => bcrypt('SecurePass123!'),
            'role_id' => $role->role_id,
            'status' => 'Active'
        ]);

        // VALIDACIÓN 1.1: Usuario debe existir en BD
        $this->assertDatabaseHas('tbuser', [
            'email' => 'juan.gerente@lacomarca.com',
            'full_name' => 'Juan Carlos Gerente'
        ]);

        // VALIDACIÓN 1.2: Usuario debe tener ID asignado
        $this->assertNotNull($manager->user_id);

        // VALIDACIÓN 1.3: Usuario debe tener estado Active
        $this->assertEquals('Active', $manager->status);

        // VALIDACIÓN 1.4: Usuario debe ser Gerente (rol correcto)
        $manager->load('role');
        $this->assertTrue($manager->isAdminLocal(), 
            'El usuario debe tener rol de Gerente/Administrador Local');

        // ===== PASO 2: REGISTRAR LOCAL =====

        $local = Local::create([
            'name' => 'La Comarca - Sarapiquí',
            'description' => 'Primer local del proyecto',
            'contact' => '2765-1234',
            'status' => 'Active',
            'image_logo' => 'images/logo-comarca.png'
        ]);

        // VALIDACIÓN 2.1: Local debe existir en BD
        $this->assertDatabaseHas('tblocal', [
            'name' => 'La Comarca - Sarapiquí',
            'status' => 'Active'
        ]);

        // VALIDACIÓN 2.2: Local debe tener ID asignado
        $this->assertNotNull($local->local_id);

        // VALIDACIÓN 2.3: Datos deben ser correctos
        $this->assertEquals('La Comarca - Sarapiquí', $local->name);
        $this->assertEquals('Primer local del proyecto', $local->description);

        // ===== PASO 3: ASOCIAR GERENTE A LOCAL =====

        // Usar relación belongsToMany para asociar
        $local->users()->attach($manager->user_id);

        // VALIDACIÓN 3.1: Relación debe existir en tabla pivot
        $this->assertDatabaseHas('tbuser_local', [
            'local_id' => $local->local_id,
            'user_id' => $manager->user_id
        ]);

        // ===== PASO 4: VERIFICAR RELACIÓN COMPLETA =====

        // VALIDACIÓN 4.1: Local debe contener el gerente
        $this->assertTrue(
            $local->users->contains($manager),
            'El Local debe contener al gerente asociado'
        );

        // VALIDACIÓN 4.2: Gerente debe estar en el Local
        $this->assertTrue(
            $manager->locals->contains($local),
            'El gerente debe estar asociado al Local'
        );

        // VALIDACIÓN 4.3: Contar gerentes en el Local
        $this->assertEquals(1, $local->users->count(),
            'El Local debe tener exactamente 1 gerente');

        // VALIDACIÓN 4.4: Verificar datos del gerente desde Local
        $assignedManager = $local->users->first();
        $this->assertEquals($manager->user_id, $assignedManager->user_id);
        $this->assertEquals('Juan Carlos Gerente', $assignedManager->full_name);

        // VALIDACIÓN 4.5: Verificar datos del Local desde User
        $userLocal = $manager->locals->first();
        $this->assertEquals($local->local_id, $userLocal->local_id);
        $this->assertEquals('La Comarca - Sarapiquí', $userLocal->name);
    }

    /**
     * Test 2: Verificar que Local sin Gerente es detectado
     * 
     * Requisito de negocio: Un Local DEBE TENER al menos un Gerente
     * 
     * Pasos:
     * 1. Crear Local sin asociar Gerente
     * 2. Verificar que no tiene gerentes
     */
    public function test_local_without_managers_should_be_detected()
    {
        // Crear Local sin gerentes
        $local = Local::create([
            'name' => 'Local sin gerente',
            'description' => 'Test',
            'contact' => '2765-5555',
            'status' => 'Active'
        ]);

        // VALIDACIÓN: Local NO debe tener gerentes
        $this->assertEquals(0, $local->users->count(),
            'Un Local sin asociación no debe tener gerentes');

        // En tu controlador, deberías validar que no se puede guardar
        // un Local sin al menos 1 Gerente
    }

    /**
     * Test 3: Múltiples Gerentes en un Local
     * 
     * Escenario: Un Local puede tener varios gerentes
     * 
     * Pasos:
     * 1. Crear 3 gerentes
     * 2. Crear 1 Local
     * 3. Asociar los 3 gerentes al Local
     * 4. Verificar que todos están asociados
     */
    public function test_local_can_have_multiple_managers_workflow()
    {
        // Crear rol
        $role = Role::firstOrCreate(
            ['role_type' => 'Gerente'],
            ['description' => 'Gerente de Local']
        );

        // Crear 3 gerentes
        $managers = [];
        for ($i = 1; $i <= 3; $i++) {
            $managers[] = User::create([
                'full_name' => "Gerente $i",
                'email' => "gerente$i@test.com",
                'password' => bcrypt('password123'),
                'role_id' => $role->role_id,
                'status' => 'Active'
            ]);
        }

        // Crear Local
        $local = Local::create([
            'name' => 'Local Compartido',
            'description' => 'Con múltiples gerentes',
            'contact' => '2765-9999',
            'status' => 'Active'
        ]);

        // Asociar todos los gerentes
        foreach ($managers as $manager) {
            $local->users()->attach($manager->user_id);
        }

        // VALIDACIÓN 1: Debe haber 3 gerentes en el Local
        $this->assertEquals(3, $local->users->count(),
            'El Local debe tener 3 gerentes');

        // VALIDACIÓN 2: Cada gerente debe estar en el Local
        foreach ($managers as $manager) {
            $this->assertTrue(
                $manager->locals->contains($local),
                "El gerente {$manager->full_name} debe estar en el Local"
            );
        }

        // VALIDACIÓN 3: Verificar en tabla pivot
        for ($i = 1; $i <= 3; $i++) {
            $this->assertDatabaseHas('tbuser_local', [
                'local_id' => $local->local_id,
                'user_id' => $managers[$i - 1]->user_id
            ]);
        }
    }

    /**
     * Test 4: Validar integridad de datos después de asociación
     * 
     * Objetivo: Asegurar que los datos no se corrompen al asociar
     * 
     * Pasos:
     * 1. Crear usuario con datos
     * 2. Crear Local con datos
     * 3. Asociarlos
     * 4. Recargar desde BD
     * 5. Verificar que datos son idénticos
     */
    public function test_local_data_integrity_after_manager_assignment()
    {
        // Crear rol
        $role = Role::firstOrCreate(['role_type' => 'Gerente']);
        
        // Crear usuario con datos específicos
        $manager = User::create([
            'full_name' => 'Test Manager Integrity',
            'email' => 'test.integrity@test.com',
            'phone' => '9999-8888',
            'password' => bcrypt('TestPass123!'),
            'role_id' => $role->role_id,
            'status' => 'Active'
        ]);

        // Crear Local con datos específicos
        $local = Local::create([
            'name' => 'Test Local Integrity',
            'description' => 'Description with special chars: á é í ó ú ñ',
            'contact' => '2765-7777',
            'status' => 'Active',
            'image_logo' => 'images/test-integrity.png'
        ]);

        // Asociar
        $local->users()->attach($manager->user_id);

        // Recargar desde BD para asegurar coherencia
        $localFresh = Local::find($local->local_id);
        $managerFresh = User::find($manager->user_id);

        // VALIDACIÓN 1: Datos del Local intactos
        $this->assertEquals('Test Local Integrity', $localFresh->name);
        $this->assertEquals('Description with special chars: á é í ó ú ñ', $localFresh->description);
        $this->assertEquals('2765-7777', $localFresh->contact);
        $this->assertEquals('Active', $localFresh->status);

        // VALIDACIÓN 2: Datos del Manager intactos
        $this->assertEquals('Test Manager Integrity', $managerFresh->full_name);
        $this->assertEquals('test.integrity@test.com', $managerFresh->email);
        $this->assertEquals('9999-8888', $managerFresh->phone);
        $this->assertEquals('Active', $managerFresh->status);

        // VALIDACIÓN 3: Relación aún existe
        $this->assertTrue($localFresh->users->contains($managerFresh));
    }
}
