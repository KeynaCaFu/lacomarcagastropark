<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    use HasFactory;

    protected $table = 'tbproduct_gallery';
    protected $primaryKey = 'product_gallery_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_url'
    ];

    /**
     * Accessor: Obtener URL de imagen usando Storage
     * Usa image_url si existe, sino devuelve null
     */
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // Si la ruta ya tiene /storage/, quitarlo
        $path = $value;
        if (str_starts_with($path, '/storage/')) {
            $path = str_replace('/storage/', '', $path);
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }

    /**
     * Relación: Producto al que pertenece esta imagen
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
