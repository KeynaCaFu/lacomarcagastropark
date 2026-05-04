<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $localId = 7; // Local de gerente.chinita@gmail.com

        // Productos a crear
        $products = [
            [
                'name' => 'Ceviche Fresco',
                'description' => 'Ceviche con pescado fresco del día, limón, cebolla roja y cilantro',
                'category' => 'Mariscos',
                'tag' => 'Especialidad',
                'product_type' => 'Plato Principal',
                'price' => 15.99,
                'status' => 'Available'
            ],
            [
                'name' => 'Tiradito de Atún',
                'description' => 'Atún fresco en láminas delgadas con salsa de ají amarillo y aceite de oliva',
                'category' => 'Mariscos',
                'tag' => 'Premium',
                'product_type' => 'Plato Principal',
                'price' => 18.50,
                'status' => 'Available'
            ],
            [
                'name' => 'Causa Limeña',
                'description' => 'Papa amarilla, aguacate, pollo desmenuzado y salsa de limón',
                'category' => 'Entradas',
                'tag' => 'Clásico',
                'product_type' => 'Entrada',
                'price' => 12.00,
                'status' => 'Available'
            ],
            [
                'name' => 'Anticuchos de Corazón',
                'description' => 'Corazón de res marinado en vinagre, ají panca y especias, a la parrilla',
                'category' => 'Carnes',
                'tag' => 'Tradicional',
                'product_type' => 'Plato Principal',
                'price' => 14.50,
                'status' => 'Available'
            ],
            [
                'name' => 'Ají de Gallina',
                'description' => 'Pechuga de gallina en crema de ají amarillo con papas y aceitunas',
                'category' => 'Carnes',
                'tag' => 'Clásico',
                'product_type' => 'Plato Principal',
                'price' => 13.99,
                'status' => 'Available'
            ],
            [
                'name' => 'Lomo a lo Pobre',
                'description' => 'Corte de lomo a la parrilla acompañado de papas, cebolla y huevo',
                'category' => 'Carnes',
                'tag' => 'Premium',
                'product_type' => 'Plato Principal',
                'price' => 22.00,
                'status' => 'Available'
            ],
            [
                'name' => 'Chicha Morada',
                'description' => 'Bebida refrescante de maíz morado con piña, canela y clavo',
                'category' => 'Bebidas',
                'tag' => 'Bebida',
                'product_type' => 'Bebida',
                'price' => 3.50,
                'status' => 'Available'
            ],
            [
                'name' => 'Pisco Sour',
                'description' => 'Cóctel tradicional peruano con pisco, limón, clara de huevo y angostura',
                'category' => 'Bebidas',
                'tag' => 'Alcohólico',
                'product_type' => 'Bebida',
                'price' => 8.00,
                'status' => 'Available'
            ],
            [
                'name' => 'Ensalada de Quinua',
                'description' => 'Ensalada con quinua, tomate, pepino, queso fresco y aderezo de limón',
                'category' => 'Ensaladas',
                'tag' => 'Saludable',
                'product_type' => 'Acompañamiento',
                'price' => 9.50,
                'status' => 'Available'
            ],
            [
                'name' => 'Arroz con Mariscos',
                'description' => 'Arroz con camarones, calamares, almejas y caldo de pescado',
                'category' => 'Arroces',
                'tag' => 'Especialidad',
                'product_type' => 'Plato Principal',
                'price' => 19.99,
                'status' => 'Available'
            ],
        ];

        // Insertar productos
        foreach ($products as $product) {
            $product['created_at'] = now();
            $product['updated_at'] = now();
            
            $productId = DB::table('tbproduct')->insertGetId($product);

            // Asociar el producto al local
            DB::table('tblocal_product')->insert([
                'local_id' => $localId,
                'product_id' => $productId,
                'price' => $product['price'],
                'is_available' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ 10 productos agregados al local 7 (gerente.chinita@gmail.com)\n";
    }
}
