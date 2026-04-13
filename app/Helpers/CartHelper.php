<?php

namespace App\Helpers;

class CartHelper
{
    /**
     * Normalizar customización para comparación consistente
     * 
     * - Convierte a minúsculas
     * - Remueve acentos
     * - Trimea espacios y reemplaza múltiples espacios con uno
     * - Normaliza puntuación
     * 
     * @param string|null $customization
     * @return string Customización normalizada
     * 
     * Ejemplos:
     * "Sin Alcohol" -> "sin alcohol"
     * "sin  alcohol" -> "sin alcohol"
     * "Sin Alcohol,  Extra Queso" -> "sin alcohol, extra queso"
     * "Sín Alcohol" -> "sin alcohol"
     */
    public static function normalizeCustomization($customization)
    {
        if (empty($customization)) {
            return '';
        }

        // Convertir a minúsculas
        $normalized = strtolower(trim($customization));

        // Remover acentos (normalización Unicode)
        $normalized = preg_replace('/[áàâäã]/u', 'a', $normalized);
        $normalized = preg_replace('/[éèêë]/u', 'e', $normalized);
        $normalized = preg_replace('/[íìîï]/u', 'i', $normalized);
        $normalized = preg_replace('/[óòôöõ]/u', 'o', $normalized);
        $normalized = preg_replace('/[úùûü]/u', 'u', $normalized);
        $normalized = preg_replace('/[ñ]/u', 'n', $normalized);

        // Reemplazar múltiples espacios con uno
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Normalizar espacios alrededor de comas y guiones
        $normalized = preg_replace('/\s*,\s*/', ', ', $normalized);
        $normalized = preg_replace('/\s*-\s*/', ' - ', $normalized);

        return trim($normalized);
    }

    /**
     * Generar item_key único para identificar items en el carrito
     * 
     * @param int $productId
     * @param string|null $customization
     * @return string
     */
    public static function generateItemKey($productId, $customization = null)
    {
        $normalizedCustomization = self::normalizeCustomization($customization);
        return $productId . '_' . md5($normalizedCustomization);
    }

    /**
     * Detectar si dos customizaciones son equivalentes
     * Útil para validaciones en formularios
     * 
     * @param string|null $customization1
     * @param string|null $customization2
     * @return bool
     */
    public static function areCustomizationsEquivalent($customization1, $customization2)
    {
        return self::normalizeCustomization($customization1) === self::normalizeCustomization($customization2);
    }
}
