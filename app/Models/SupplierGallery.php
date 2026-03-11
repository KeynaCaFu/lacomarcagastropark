<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierGallery extends Model
{
    use HasFactory;

    protected $table = 'tb_supplier_gallery';
    protected $primaryKey = 'gallery_id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'supplier_id',
        'image_path',
        'description'
    ];

    /**
     * Relación: Proveedor propietario de esta galería
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Accessor: Obtener URL de la imagen
     * Convierte rutas relativas en URLs accesibles
     * Ruta base: /public/proveedor/
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

        // Si empieza con /storage/, usar Storage
        if (str_starts_with($value, '/storage/')) {
            $path = str_replace('/storage/', '', $value);
            return \Illuminate\Support\Facades\Storage::url($path);
        }

        // Si comienza con public/, remover el prefijo public/
        if (str_starts_with($value, 'public/')) {
            $value = str_replace('public/', '', $value);
        }

        // Si empieza con proveedor/, usar asset()
        if (str_starts_with($value, 'proveedor/')) {
            return asset($value);
        }

        // Por defecto, usar asset con la ruta
        return asset($value);
    }
}
