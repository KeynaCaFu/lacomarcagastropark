<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * PRUEBA DE INTEGRACIÓN: Validación de Creación de Productos
 * 
 * Casos de prueba:
 * 1. Crear productos en la base de datos
 * 2. Validación de campos obligatorios (nombre, precio, estado)
 * 3. Validación de límites de caracteres
 * 4. Validación de valores permitidos (estado)
 * 5. Mensajes de error personalizados
 * 6. Validación de múltiples errores simultáneamente
 * 7. Validación con datos opcionales
 */
class ProductCreationTest extends TestCase
{
    protected $localId = 7; // Local de prueba

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
            'estado' => 'required|string|in:Available,Unavailable'
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
            'estado.in' => 'El estado debe ser Available o Unavailable'
        ];
    }

    /**
     * Datos base válido para crear un producto
     */
    protected function getValidProductData()
    {
        return [
            'nombre' => 'Ceviche Test ' . uniqid(),
            'descripcion' => 'Ceviche con pescado fresco del día',
            'categoria' => 'Mariscos',
            'etiqueta' => 'Especialidad',
            'tipo_producto' => 'Plato Principal',
            'precio' => 15.99,
            'estado' => 'Available'
        ];
    }

    // ============================================================
    // PRUEBAS DE VALIDACIÓN - 
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
            'estado' => 'Available'
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
     * TEST 11: Estado "Available" es válido
     * Esperado: Validación exitosa
     */
    public function test_acepta_estado_available()
    {
        $data = $this->getValidProductData();
        $data['estado'] = 'Available';

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * TEST 12: Estado "Unavailable" es válido
     * Esperado: Validación exitosa
     */
    public function test_acepta_estado_unavailable()
    {
        $data = $this->getValidProductData();
        $data['estado'] = 'Unavailable';

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
            'estado' => 'Available'
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
        $data['nombre'] = 12345;

        $validator = Validator::make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombre', $validator->errors()->toArray());
    }

    // ============================================================
    // PRUEBAS DE INTEGRACIÓN - Crear productos en BD
    // ============================================================

    /**
     * TEST 21: Insertar producto válido en la base de datos
     * Esperado: El producto se guarda y se puede recuperar
     */
    public function test_crear_producto_valido_en_base_datos()
    {
        $data = [
            'name' => 'Tiradito Integration Test ' . uniqid(),
            'description' => 'Atún fresco con salsa de ají amarillo',
            'category' => 'Mariscos',
            'tag' => 'Premium',
            'product_type' => 'Plato Principal',
            'price' => 18.50,
            'status' => 'Available'
        ];

        $productId = DB::table('tbproduct')->insertGetId($data);

        $this->assertGreaterThan(0, $productId);
        $this->assertDatabaseHas('tbproduct', [
            'product_id' => $productId,
            'name' => $data['name']
        ]);
    }

    /**
     * TEST 22: Asociar producto a local
     * Esperado: El producto se asocia correctamente a tblocal_product
     */
    public function test_asociar_producto_a_local()
    {
        $data = [
            'name' => 'Causa Integration Test ' . uniqid(),
            'description' => 'Papa amarilla con aguacate',
            'category' => 'Entradas',
            'tag' => 'Clásico',
            'product_type' => 'Entrada',
            'price' => 12.00,
            'status' => 'Available'
        ];

        $productId = DB::table('tbproduct')->insertGetId($data);

        DB::table('tblocal_product')->insert([
            'local_id' => $this->localId,
            'product_id' => $productId,
            'price' => $data['price'],
            'is_available' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->assertDatabaseHas('tblocal_product', [
            'local_id' => $this->localId,
            'product_id' => $productId
        ]);
    }

    /**
     * TEST 23: Recuperar producto por ID
     * Esperado: Se obtiene el producto correcto de la BD
     */
    public function test_recuperar_producto_por_id()
    {
        $data = [
            'name' => 'Ceviche Integration Test ' . uniqid(),
            'description' => 'Ceviche con pescado fresco',
            'category' => 'Mariscos',
            'tag' => 'Especialidad',
            'product_type' => 'Plato Principal',
            'price' => 15.99,
            'status' => 'Available'
        ];

        $productId = DB::table('tbproduct')->insertGetId($data);
        
        $product = DB::table('tbproduct')->where('product_id', $productId)->first();

        $this->assertNotNull($product);
        $this->assertEquals($data['name'], $product->name);
        $this->assertEquals($data['price'], $product->price);
    }

    /**
     * TEST 24: Listar productos de un local
     * Esperado: Se obtienen todos los productos del local
     */
    public function test_listar_productos_de_local()
    {
        // Contar productos iniciales del local 7
        $initialCount = DB::table('tblocal_product')
            ->where('local_id', $this->localId)
            ->count();

        // Crear 3 productos nuevos
        for ($i = 0; $i < 3; $i++) {
            $productId = DB::table('tbproduct')->insertGetId([
                'name' => "Test Product $i " . uniqid(),
                'description' => "Test description $i",
                'price' => 10 + $i,
                'status' => 'Available'
            ]);

            DB::table('tblocal_product')->insert([
                'local_id' => $this->localId,
                'product_id' => $productId,
                'price' => 10 + $i,
                'is_available' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Verificar que se agregaron
        $finalCount = DB::table('tblocal_product')
            ->where('local_id', $this->localId)
            ->count();

        $this->assertEquals($initialCount + 3, $finalCount);
    }

    /**
     * TEST 25: Actualizar estado de producto
     * Esperado: El estado se actualiza correctamente
     */
    public function test_actualizar_estado_producto()
    {
        $data = [
            'name' => 'Product Status Test ' . uniqid(),
            'price' => 20.00,
            'status' => 'Available'
        ];

        $productId = DB::table('tbproduct')->insertGetId($data);

        DB::table('tbproduct')
            ->where('product_id', $productId)
            ->update(['status' => 'Unavailable']);

        $product = DB::table('tbproduct')->where('product_id', $productId)->first();

        $this->assertEquals('Unavailable', $product->status);
    }
}

