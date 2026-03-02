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
     * Accessor: Obtener URL de imagen
     * Convierte rutas relativas en URLs accesibles
     */
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // Si ya es URL absoluta, devolverla
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Si empieza con /storage/, usar Storage (compatibilidad atrás)
        if (str_starts_with($value, '/storage/')) {
            $path = str_replace('/storage/', '', $value);
            return \Illuminate\Support\Facades\Storage::url($path);
        }

        // Si comienza con public/images/, remover el prefijo public/
        if (str_starts_with($value, 'public/')) {
            $value = str_replace('public/', '', $value);
        }

        // Si empieza con images/, usar asset()
        if (str_starts_with($value, 'images/')) {
            return asset($value);
        }

        // Por defecto, usar asset
        return asset($value);
    }

    /**
     * Relación: Producto al que pertenece esta imagen
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
