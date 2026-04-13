<?php

namespace Tests\Unit;

use App\Helpers\CartHelper;
use PHPUnit\Framework\TestCase;

class CartHelperTest extends TestCase
{
    /**
     * Test normalización básica: mayúsculas → minúsculas
     */
    public function test_normalize_converts_to_lowercase()
    {
        $this->assertEquals(
            'sin alcohol',
            CartHelper::normalizeCustomization('Sin Alcohol')
        );

        $this->assertEquals(
            'sin alcohol',
            CartHelper::normalizeCustomization('SIN ALCOHOL')
        );
    }

    /**
     * Test limpieza de espacios extras
     */
    public function test_normalize_removes_extra_spaces()
    {
        $this->assertEquals(
            'sin alcohol',
            CartHelper::normalizeCustomization('Sin  Alcohol')
        );

        $this->assertEquals(
            'sin alcohol',
            CartHelper::normalizeCustomization('Sin   Alcohol')
        );

        $this->assertEquals(
            'sin alcohol',
            CartHelper::normalizeCustomization('  Sin Alcohol  ')
        );
    }

    /**
     * Test remoción de acentos
     */
    public function test_normalize_removes_accents()
    {
        $this->assertEquals(
            'sin alcohol',
            CartHelper::normalizeCustomization('Sín Alcohol')
        );

        $this->assertEquals(
            'extra queso con oregano',
            CartHelper::normalizeCustomization('EXTRA Queso con Orégano')
        );

        $this->assertEquals(
            'sin azucar',
            CartHelper::normalizeCustomization('Sin Azúcar')
        );

        $this->assertEquals(
            'bacon y champinones',
            CartHelper::normalizeCustomization('Bacon y Champiñones')
        );
    }

    /**
     * Test normalización de puntuación
     */
    public function test_normalize_punctuation()
    {
        $this->assertEquals(
            'sin alcohol, extra queso',
            CartHelper::normalizeCustomization('Sin Alcohol,Extra Queso')
        );

        $this->assertEquals(
            'sin alcohol, extra queso',
            CartHelper::normalizeCustomization('Sin Alcohol , Extra Queso')
        );

        $this->assertEquals(
            'sin alcohol - extra queso',
            CartHelper::normalizeCustomization('Sin Alcohol-Extra Queso')
        );
    }

    /**
     * Test cadenas vacías y nulas
     */
    public function test_normalize_empty_strings()
    {
        $this->assertEquals('', CartHelper::normalizeCustomization(''));
        $this->assertEquals('', CartHelper::normalizeCustomization(null));
        $this->assertEquals('', CartHelper::normalizeCustomization('   '));
    }

    /**
     * Test generación de item_key
     */
    public function test_generate_item_key_same_for_equivalent_customizations()
    {
        $productId = 5;

        $key1 = CartHelper::generateItemKey($productId, 'Sin Alcohol');
        $key2 = CartHelper::generateItemKey($productId, 'sin alcohol');
        $key3 = CartHelper::generateItemKey($productId, 'SIN ALCOHOL');

        $this->assertEquals($key1, $key2);
        $this->assertEquals($key2, $key3);
    }

    /**
     * Test generación de item_key diferente para producto diferente
     */
    public function test_generate_item_key_different_for_different_products()
    {
        $key1 = CartHelper::generateItemKey(5, 'sin alcohol');
        $key2 = CartHelper::generateItemKey(6, 'sin alcohol');

        $this->assertNotEquals($key1, $key2);
    }

    /**
     * Test generación de item_key diferente para customizaciones diferentes
     */
    public function test_generate_item_key_different_for_different_customizations()
    {
        $key1 = CartHelper::generateItemKey(5, 'sin alcohol');
        $key2 = CartHelper::generateItemKey(5, 'extra queso');

        $this->assertNotEquals($key1, $key2);
    }

    /**
     * Test equivalencia de customizaciones
     */
    public function test_are_customizations_equivalent_with_case_variations()
    {
        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent('Sin Alcohol', 'sin alcohol')
        );

        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent('SIN ALCOHOL', 'sin alcohol')
        );

        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent('Sin  Alcohol', 'sin alcohol')
        );
    }

    /**
     * Test no equivalencia de customizaciones diferentes
     */
    public function test_are_customizations_not_equivalent_for_different_notes()
    {
        $this->assertFalse(
            CartHelper::areCustomizationsEquivalent('Sin Alcohol', 'Con Alcohol')
        );

        $this->assertFalse(
            CartHelper::areCustomizationsEquivalent('Pizza', 'Hamburguesa')
        );
    }

    /**
     * Test equivalencia con customizaciones vacías
     */
    public function test_are_customizations_equivalent_with_empty_strings()
    {
        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent('', null)
        );

        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent(null, '')
        );

        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent('', '')
        );
    }

    /**
     * Test caso complejo: acentos + espacios + mayúsculas
     */
    public function test_normalize_complex_case()
    {
        $input = '  EXTRA Queso,  Sín Cebolla  -  Champiñones  ';
        $expected = 'extra queso, sin cebolla - champinones';

        $this->assertEquals($expected, CartHelper::normalizeCustomization($input));
    }

    /**
     * Test casos de uso reales
     */
    public function test_real_world_scenarios()
    {
        // Scenario 1: Usuario copia-pega con espacios irregulares
        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent(
                'Sin Alcohol,  Sin Picante,  Extra Queso',
                'sin alcohol, sin picante, extra queso'
            )
        );

        // Scenario 2: Usuario escribe con acentos incorrectos
        $this->assertTrue(
            CartHelper::areCustomizationsEquivalent(
                'Champiñones, Más Oregano',
                'Champinones, Mas oregano'
            )
        );

        // Scenario 3: Mismo producto, mismas notas, diferente escrita
        $key1 = CartHelper::generateItemKey(
            10,
            'Sin Alcohol, Extra Queso'
        );
        $key2 = CartHelper::generateItemKey(
            10,
            'sin alcohol, extra queso'
        );
        $this->assertEquals($key1, $key2);
    }
}
