<?php

namespace Tests\Unit;

use App\Models\Local;
use PHPUnit\Framework\TestCase;

/**
 * PRUEBA UNITARIA: Modelo Local
 * 
 * Objetivo: Validar el modelo Local sin base de datos
 * Casos de prueba: 4
 */
class LocalModelTest extends TestCase
{
    /**
     * Test 1: Local debe poder ser instanciado
     * 
     * Requisito: El modelo Local debe poder crear instancias
     */
    public function test_local_can_be_instantiated()
    {
        $local = new Local([
            'name' => 'Local Test',
            'description' => 'Descripción del local',
            'contact' => '1234-5678',
            'status' => 'Active',
            'image_logo' => 'images/logo.png'
        ]);

        $this->assertInstanceOf(Local::class, $local);
        $this->assertEquals('Local Test', $local->name);
        $this->assertEquals('Active', $local->status);
    }

    /**
     * Test 2: Validar tabla y primary key correctos
     * 
     * Requisito: El modelo debe usar la tabla correcta (tblocal)
     */
    public function test_local_uses_correct_table_and_primary_key()
    {
        $local = new Local();
        
        $this->assertEquals('tblocal', $local->getTable());
        $this->assertEquals('local_id', $local->getKeyName());
    }

    /**
     * Test 3: Validar atributos fillable del Local
     * 
     * Requisito: Los campos correctos deben ser mass-assignable
     */
    public function test_local_has_correct_fillable_attributes()
    {
        $local = new Local();
        
        $expected = [
            'name', 'description', 'contact', 'status', 'image_logo'
        ];

        $this->assertEquals($expected, $local->getFillable());
    }

    /**
     * Test 4: Validar timestamps
     * 
     * Requisito: El modelo debe usar timestamps (created_at, updated_at)
     */
    public function test_local_has_timestamps()
    {
        $local = new Local();
        
        $this->assertTrue($local->usesTimestamps());
        $this->assertEquals('created_at', $local->getCreatedAtColumn());
        $this->assertEquals('updated_at', $local->getUpdatedAtColumn());
    }
}
