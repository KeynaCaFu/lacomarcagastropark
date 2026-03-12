<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalGallery extends Model
{
    use HasFactory;

    protected $table = 'tblocal_gallery';
    protected $primaryKey = 'local_gallery_id';
    public $timestamps = false;

    protected $fillable = [
        'local_id',
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

        // Si empieza con /storage/, usar Storage
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
     * Relación: Local al que pertenece esta imagen
     */
    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id', 'local_id');
    }
}
