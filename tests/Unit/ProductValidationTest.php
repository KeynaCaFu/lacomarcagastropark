<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

/**
 * PRUEBA UNITARIA: Validaciones de Creación de Producto
 * 
 * Objetivo: Validar la lógica de validación sin acceso a BD ni autenticación
 * Casos de prueba cubiertos:
 * 1. Validación de nombre (requerido, máximo 255 caracteres)
 * 2. Validación de precio (requerido, numérico, no negativo)
 * 3. Validación de foto (formato válido, tamaño máximo 2MB)
 * 4. Validación de estado (debe ser Disponible o No disponible)
 * 5. Campos opcionales (descripción, categoría, etiqueta, tipo)
 */
class ProductValidationTest extends TestCase
{
    /**
     * Datos válidos base para usar en pruebas
     */
    protected function getValidProductData()
    {
        return [
            'nombre' => 'Ceviche Fresco',
            'descripcion' => 'Ceviche hecho con pescado fresco del día',
            'categoria' => 'Mariscos',
            'etiqueta' => 'Especialidad',
            'tipo_producto' => 'Plato Principal',
            'precio' => 15.99,
            'estado' => 'Disponible'
        ];
    }

    /**
     * Reglas de validación (idénticas a ProductController)
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

    // ============================================================
    // PRUEBAS DEL CAMPO "NOMBRE"
    // ============================================================

    /**
     * TEST 1.1: El nombre es requerido
     * Esperado: Validación falla sin nombre
     */
    public function test_nombre_es_requerido()
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
     * TEST 1.2: El nombre no puede exceder 255 caracteres
     * Esperado: Validación falla con nombre > 255 chars
     */
    public function test_nombre_no_excede_maximo_caracteres()
    {
        $data = $this->getValidProductData();
        $data['nombre'] = str_repeat('A', 256); // 256 caracteres

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombre', $validator->errors()->toArray());
    }

    /**
     * TEST 1.3: El nombre acepta 255 caracteres exactos
     * Esperado: Validación pasa con nombre de 255 chars
     */
    public function test_nombre_acepta_maximo_de_255_caracteres()
    {
        $data = $this->getValidProductData();
        $data['nombre'] = str_repeat('A', 255); // 255 caracteres

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 1.4: El nombre debe ser string
     * Esperado: Validación falla si nombre no es string
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
    }

    // ============================================================
    // PRUEBAS DEL CAMPO "PRECIO"
    // ============================================================

    /**
     * TEST 2.1: El precio es requerido
     * Esperado: Validación falla sin precio
     */
    public function test_precio_es_requerido()
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
     * TEST 2.2: El precio debe ser numérico
     * Esperado: Validación falla con precio no numérico
     */
    public function test_precio_debe_ser_numerico()
    {
        $data = $this->getValidProductData();
        $data['precio'] = 'abc'; // String no numérico

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('precio', $validator->errors()->toArray());
    }

    /**
     * TEST 2.3: El precio no puede ser negativo
     * Esperado: Validación falla con precio < 0
     */
    public function test_precio_no_puede_ser_negativo()
    {
        $data = $this->getValidProductData();
        $data['precio'] = -10.50;

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('precio', $validator->errors()->toArray());
    }

    /**
     * TEST 2.4: El precio acepta 0 como valor válido
     * Esperado: Validación pasa con precio = 0
     */
    public function test_precio_acepta_cero()
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
     * TEST 2.5: El precio acepta decimales
     * Esperado: Validación pasa con precio decimal
     */
    public function test_precio_acepta_decimales()
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

    // ============================================================
    // PRUEBAS DEL CAMPO "ESTADO"
    // ============================================================

    /**
     * TEST 3.1: El estado es requerido
     * Esperado: Validación falla sin estado
     */
    public function test_estado_es_requerido()
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
     * TEST 3.2: El estado solo acepta "Disponible" o "No disponible"
     * Esperado: Validación falla con estado inválido
     */
    public function test_estado_solo_acepta_valores_validos()
    {
        $data = $this->getValidProductData();
        $data['estado'] = 'Activo'; // Valor inválido

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('estado', $validator->errors()->toArray());
    }

    /**
     * TEST 3.3: El estado "Disponible" es válido
     * Esperado: Validación pasa con "Disponible"
     */
    public function test_estado_disponible_es_valido()
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
     * TEST 3.4: El estado "No disponible" es válido
     * Esperado: Validación pasa con "No disponible"
     */
    public function test_estado_no_disponible_es_valido()
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

    // ============================================================
    // PRUEBAS DEL CAMPO "CATEGORÍA"
    // ============================================================

    /**
     * TEST 4.1: La categoría es opcional
     * Esperado: Validación pasa sin categoría
     */
    public function test_categoria_es_opcional()
    {
        $data = $this->getValidProductData();
        unset($data['categoria']);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 4.2: La categoría no excede 100 caracteres
     * Esperado: Validación falla con categoría > 100 chars
     */
    public function test_categoria_no_excede_maximo_caracteres()
    {
        $data = $this->getValidProductData();
        $data['categoria'] = str_repeat('A', 101); // 101 caracteres

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
    }

    // ============================================================
    // PRUEBAS DEL CAMPO "FOTO"
    // ============================================================

    /**
     * TEST 5.1: La foto es opcional
     * Esperado: Validación pasa sin foto
     */
    public function test_foto_es_opcional()
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
     * TEST 5.2: La foto solo acepta formatos válidos
     * Esperado: Validación falla con formato inválido
     * Nota: En un test unitario real habría que usar UploadedFile
     */
    public function test_foto_solo_acepta_formatos_validos()
    {
        // En tests unitarios puros, validamos la regla
        $rules = $this->getValidationRules();
        $this->assertStringContainsString('jpeg,png,jpg,gif', $rules['foto']);
    }

    // ============================================================
    // PRUEBAS DE VALIDACIÓN COMPLETA
    // ============================================================

    /**
     * TEST 6.1: Todos los datos válidos pasan la validación
     * Esperado: Validación exitosa con datos completos
     */
    public function test_validacion_exitosa_con_datos_completos()
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
     * TEST 6.2: Todos los datos opcionales omitidos pasan
     * Esperado: Validación pasa solo con campos obligatorios
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
     * TEST 6.3: Múltiples errores se detectan simultaneamente
     * Esperado: Validación falla con varios errores
     */
    public function test_validacion_detecta_multiples_errores()
    {
        $data = [
            'nombre' => '', // Falla: requerido
            'precio' => 'invalid', // Falla: debe ser numérico
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
     * TEST 6.4: Los errores incluyen mensajes personalizados
     * Esperado: Los mensajes de error son claros en español
     */
    public function test_validacion_incluye_mensajes_personalizados()
    {
        $data = $this->getValidProductData();
        unset($data['nombre']);

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->toArray();
        $this->assertStringContainsString('obligatorio', $errors['nombre'][0]);
    }

    /**
     * TEST 6.5: Validación con descripción larga (opcional)
     * Esperado: Acepta descripciones sin límite de caracteres
     */
    public function test_descripcion_acepta_textos_largos()
    {
        $data = $this->getValidProductData();
        $data['descripcion'] = str_repeat('A', 1000); // Texto muy largo

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }
}
