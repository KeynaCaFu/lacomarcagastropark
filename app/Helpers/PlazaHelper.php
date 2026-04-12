<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PlazaHelper
{
    // Cache durations (en minutos)
    const CACHE_CATEGORIES = 60;      // 1 hora
    const CACHE_STATS = 120;          // 2 horas
    const CACHE_CATEGORY_ICONS = 1440; // 24 horas

    /**
     * Traducir día de la semana en inglés a español
     * 
     * @param string $dayOfWeek Día en formato 'l' (ej: 'Monday')
     * @return string|null Día en español o null
     */
    public static function translateDayToSpanish($dayOfWeek)
    {
        // Este dato se cachea porque nunca cambia
        $translations = Cache::remember('plaza:day_translations', self::CACHE_CATEGORY_ICONS, function () {
            return [
                'Monday'    => 'Lunes',
                'Tuesday'   => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday'  => 'Jueves',
                'Friday'    => 'Viernes',
                'Saturday'  => 'Sábado',
                'Sunday'    => 'Domingo',
            ];
        });

        return $translations[$dayOfWeek] ?? null;
    }

    /**
     * Obtener icono de Font Awesome según categoría de producto
     * Usa lookup directo en lugar de iteración
     * 
     * @param string $category Categoría del producto
     * @return string Icono de Font Awesome
     */
    public static function getCategoryIcon($category)
    {
        // Este mapa se cachea porque rara vez cambia
        $categoryMap = Cache::remember('plaza:category_icons_map', self::CACHE_CATEGORY_ICONS, function () {
            return [
                'hamburguesería' => 'fa-burger',
                'pizza'          => 'fa-pizza-slice',
                'sushi'          => 'fa-fish',
                'postres'        => 'fa-cake-candles',
                'bebidas'        => 'fa-glass-water',
                'comida rápida'  => 'fa-fire',
                'ensaladas'      => 'fa-leaf',
                'sandwich'       => 'fa-sandwich',
            ];
        });

        $categorySlug = Str::slug($category);
        
        // Búsqueda directa primero (caso insensible)
        foreach ($categoryMap as $key => $icon) {
            if ($categorySlug === Str::slug($key)) {
                return $icon;
            }
        }

        // Si no coincide exactamente, buscar por coincidencia parcial
        foreach ($categoryMap as $key => $icon) {
            if (str_contains($categorySlug, Str::slug($key))) {
                return $icon;
            }
        }

        return 'fa-utensils'; // Icono por defecto
    }

    /**
     * Formatear arreglo de categorías con slug e icono
     * 
     * @param array $categories Array de nombres de categorías
     * @return array Array formateado con nombre, slug e icono
     */
    public static function formatCategories($categories)
    {
        return $categories->map(function ($category) {
            return [
                'nombre' => $category,
                'slug'   => Str::slug($category),
                'icono'  => self::getCategoryIcon($category),
            ];
        })->toArray();
    }

    /**
     * Limpiar cache de Plaza (útil cuando cambian categorías o datos estáticos)
     */
    public static function clearCache()
    {
        Cache::forget('plaza:day_translations');
        Cache::forget('plaza:category_icons_map');
        Cache::forget('plaza:stats');
        Cache::forget('plaza:all_categories');
    }
}
