<?php

namespace App\Data;

use App\Models\ProductGallery;

class ProductGalleryData
{
    protected $table = 'tbproduct_gallery';

    /**
     * Obtener todas las imágenes de un producto
     */
    public function getByProductId($productId)
    {
        return ProductGallery::where('product_id', $productId)
            ->orderBy('product_gallery_id')
            ->get();
    }

    /**
     * Agregar una imagen a la galería
     */
    public function add($productId, $imageUrl)
    {
        return ProductGallery::create([
            'product_id' => $productId,
            'image_url' => $imageUrl
        ]);
    }

    /**
     * Eliminar una imagen de la galería
     */
    public function delete($galleryId)
    {
        return ProductGallery::where('product_gallery_id', $galleryId)->delete();
    }

    /**
     * Eliminar todas las imágenes de un producto
     */
    public function deleteByProductId($productId)
    {
        return ProductGallery::where('product_id', $productId)->delete();
    }

    /**
     * Obtener conteo de imágenes por producto
     */
    public function countByProductId($productId)
    {
        return ProductGallery::where('product_id', $productId)->count();
    }

    /**
     * Verificar si existe una imagen específica
     */
    public function exists($productId, $imageUrl)
    {
        return ProductGallery::where('product_id', $productId)
            ->where('image_url', $imageUrl)
            ->exists();
    }

    /**
     * Obtener la primera imagen de un producto (thumbnail)
     */
    public function getFirstImage($productId)
    {
        return ProductGallery::where('product_id', $productId)
            ->orderBy('product_gallery_id')
            ->first();
    }
}
