<?php

namespace App\Data;

use App\Models\Product;

class ProductData
{
    protected $table = 'tbproduct';

    /**
     * Obtener todos los Productos con filtros opcionales
     */
    public function all(array $filters = [])
    {
        $query = Product::select('tbproduct.*')
            ->selectRaw('(select count(*) from `tbproduct_gallery` where `tbproduct`.`product_id` = `tbproduct_gallery`.`product_id`) as `gallery_count`');

        // Filtro de búsqueda
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filtro por categoría
        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        // Filtro por local (para gerentes)
        if (!empty($filters['local_id'])) {
            $query->byLocal($filters['local_id']);
        }

        // Paginación de 6 por página
        return $query->orderBy('product_id', 'desc')->paginate(6)->withQueryString();
    }

    /**
     * Obtener solo Productos disponibles
     */
    public function allActive()
    {
        return Product::active()->with('gallery')->get();
    }

    /**
     * Obtener solo Productos disponibles con datos mínimos
     */
    public function allActiveMinimal()
    {
        return Product::active()
            ->select('product_id', 'name', 'price', 'photo')
            ->orderBy('name')
            ->get();
    }

    /**
     * Buscar un Producto por ID
     */
    public function find($id)
    {
        return Product::with('gallery')->find($id);
    }

    /**
     * Crear un nuevo Producto
     */
    public function create(array $data)
    {
        return Product::create($data);
    }

    /**
     * Actualizar un Producto
     */
    public function update($id, array $data)
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($data);
            return $product;
        }
        return null;
    }

    /**
     * Eliminar un Producto
     */
    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return true;
        }
        return false;
    }

    /**
     * Obtener estadísticas de productos
     */
    public function countTotals()
    {
        return [
            'total' => Product::count(),
            'available' => Product::active()->count(),
            'unavailable' => Product::inactive()->count(),
        ];
    }

    /**
     * Obtener estadísticas de productos por local
     */
    public function countTotalsByLocal($localId)
    {
        return [
            'total' => Product::byLocal($localId)->count(),
            'available' => Product::byLocal($localId)->active()->count(),
            'unavailable' => Product::byLocal($localId)->inactive()->count(),
        ];
    }

    /**
     * Obtener todas las categorías únicas
     */
    public function getAllCategories()
    {
        return Product::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    /**
     * Obtener todas las categorías únicas de un local
     */
    public function getCategoriesByLocal($localId)
    {
        return Product::byLocal($localId)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    /**
     * Obtener productos por categoría
     */
    public function getByCategory($category)
    {
        return Product::byCategory($category)
            ->with('gallery')
            ->orderBy('name')
            ->get();
    }
}
