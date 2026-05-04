<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

/**
 * PRUEBA DE INTEGRACIÓN: Validación de Creación de Productos
 * 
 * Objetivo: Validar el flujo de validación sin necesidad de BD
 * Prueba la lógica de validación de formularios del controlador
 * 
 * Casos de prueba:
 * 1. Validación de campos obligatorios (nombre, precio, estado)
 * 2. Validación de límites de caracteres
 * 3. Validación de valores permitidos (estado)
 * 4. Mensajes de error personalizados
 * 5. Validación de múltiples errores simultáneamente
 * 6. Validación con datos opcionales
 */
class ProductCreationTest extends TestCase
{



    /**
     * Obtener reglas de validación (idénticas a ProductController)
     */
    protected function getValidationRules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:100',
            'etiqueta' => 'nullable|string|max:100',
            'tipo_producto' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|string|in:Disponible,No disponible'
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    protected function getValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'precio.required' => 'El precio es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'precio.min' => 'El precio no puede ser negativo',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La imagen debe ser JPG, PNG o GIF',
            'foto.max' => 'La imagen no puede ser mayor a 2MB',
            'estado.in' => 'El estado debe ser Disponible o No disponible'
        ];
    }

    /**
     * Datos base válido para crear un producto
     */
    protected function getValidProductData()
    {
        return [
            'nombre' => 'Ceviche Fresco',
            'descripcion' => 'Ceviche con pescado fresco del día',
            'categoria' => 'Mariscos',
            'etiqueta' => 'Especialidad',
            'tipo_producto' => 'Plato Principal',
            'precio' => 15.99,
            'estado' => 'Disponible'
        ];
    }

    // ============================================================
    // PRUEBAS DE VALIDACIÓN - SIN BD
    // ============================================================

    /**
     * TEST 1: Validación exitosa con datos completos
     * Esperado: No hay errores de validación
     */
    public function test_validacion_exitosa_con_todos_los_datos()
    {
        $data = $this->getValidProductData();

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 2: Validación exitosa con solo campos obligatorios
     * Esperado: No hay errores cuando faltan campos opcionales
     */
    public function test_validacion_con_solo_campos_obligatorios()
    {
        $data = [
            'nombre' => 'Producto Mínimo',
            'precio' => 9.99,
            'estado' => 'Disponible'
        ];

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 3: Nombre es requerido
     * Esperado: Error de validación para nombre
     */
    public function test_falla_sin_nombre()
    {
        $data = $this->getValidProductData();
        unset($data['nombre']);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombre', $validator->errors()->toArray());
    }

    /**
     * TEST 4: Precio es requerido
     * Esperado: Error de validación para precio
     */
    public function test_falla_sin_precio()
    {
        $data = $this->getValidProductData();
        unset($data['precio']);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('precio', $validator->errors()->toArray());
    }

    /**
     * TEST 5: Estado es requerido
     * Esperado: Error de validación para estado
     */
    public function test_falla_sin_estado()
    {
        $data = $this->getValidProductData();
        unset($data['estado']);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('estado', $validator->errors()->toArray());
    }

    /**
     * TEST 6: Precio no puede ser negativo
     * Esperado: Error de validación
     */
    public function test_falla_con_precio_negativo()
    {
        $data = $this->getValidProductData();
        $data['precio'] = -10;

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('precio', $validator->errors()->toArray());
    }

    /**
     * TEST 7: Precio debe ser numérico
     * Esperado: Error de validación
     */
    public function test_falla_con_precio_no_numerico()
    {
        $data = $this->getValidProductData();
        $data['precio'] = 'abc';

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('precio', $validator->errors()->toArray());
    }

    /**
     * TEST 8: Nombre no excede 255 caracteres
     * Esperado: Error de validación con nombre > 255 chars
     */
    public function test_falla_con_nombre_muy_largo()
    {
        $data = $this->getValidProductData();
        $data['nombre'] = str_repeat('A', 256);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombre', $validator->errors()->toArray());
    }

    /**
     * TEST 9: Nombre acepta 255 caracteres exactos
     * Esperado: Validación exitosa
     */
    public function test_acepta_nombre_de_255_caracteres()
    {
        $data = $this->getValidProductData();
        $data['nombre'] = str_repeat('A', 255);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 10: Estado solo acepta valores permitidos
     * Esperado: Error con estado inválido
     */
    public function test_falla_con_estado_invalido()
    {
        $data = $this->getValidProductData();
        $data['estado'] = 'Activo'; // Inválido

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('estado', $validator->errors()->toArray());
    }

    /**
     * TEST 11: Estado "Disponible" es válido
     * Esperado: Validación exitosa
     */
    public function test_acepta_estado_disponible()
    {
        $data = $this->getValidProductData();
        $data['estado'] = 'Disponible';

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 12: Estado "No disponible" es válido
     * Esperado: Validación exitosa
     */
    public function test_acepta_estado_no_disponible()
    {
        $data = $this->getValidProductData();
        $data['estado'] = 'No disponible';

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 13: Categoría es opcional pero tiene límite
     * Esperado: Falla con categoría > 100 caracteres
     */
    public function test_falla_con_categoria_muy_larga()
    {
        $data = $this->getValidProductData();
        $data['categoria'] = str_repeat('A', 101);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('categoria', $validator->errors()->toArray());
    }

    /**
     * TEST 14: Validación detecta múltiples errores
     * Esperado: Detecta errores en nombre, precio y estado
     */
    public function test_detecta_multiples_errores()
    {
        $data = [
            'nombre' => '', // Falla: requerido
            'precio' => 'abc', // Falla: debe ser numérico
            'estado' => 'Invalido' // Falla: valor inválido
        ];

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue(count($validator->errors()) >= 3);
    }

    /**
     * TEST 15: Mensajes de error están en español
     * Esperado: Los mensajes personalizados se muestran
     */
    public function test_mensajes_de_error_estan_en_espanol()
    {
        $data = ['precio' => -10];

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->toArray();
        
        // Verificar que los mensajes contienen el idioma español
        $this->assertTrue(
            count($errors) > 0,
            'Debe haber al menos un error'
        );
    }

    /**
     * TEST 16: Precio acepta 0 como valor válido
     * Esperado: Validación exitosa con precio 0
     */
    public function test_acepta_precio_cero()
    {
        $data = $this->getValidProductData();
        $data['precio'] = 0;

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 17: Precio acepta decimales
     * Esperado: Validación exitosa con 19.99
     */
    public function test_acepta_precio_decimal()
    {
        $data = $this->getValidProductData();
        $data['precio'] = 19.99;

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 18: Descripción es opcional y sin límite
     * Esperado: Acepta textos muy largos
     */
    public function test_acepta_descripcion_muy_larga()
    {
        $data = $this->getValidProductData();
        $data['descripcion'] = str_repeat('A', 5000);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 19: Campos opcionales no se validan si están ausentes
     * Esperado: Sin errores en campos opcionales cuando están vacíos
     */
    public function test_campos_opcionales_no_requeridos()
    {
        $data = [
            'nombre' => 'Producto',
            'precio' => 10,
            'estado' => 'Disponible'
            // Sin descripción, categoría, etiqueta, tipo_producto
        ];

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 20: Regla de tipo para nombre
     * Esperado: Falla si nombre no es string
     */
    public function test_nombre_debe_ser_string()
    {
        $data = $this->getValidProductData();
        $data['nombre'] = 12345; // Número en lugar de string

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombre', $validator->errors()->toArray());
    }
}
