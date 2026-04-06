<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'tblocal';

    protected $primaryKey = 'local_id';

    protected $casts = [
        'local_id' => 'int',
    ];

    protected $fillable = [
        'name',
        'description',
        'contact',
        'status',
        'image_logo',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Relationship with Users (Managers)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'tbuser_local', 'local_id', 'user_id');
    }

    /**
     * Relación: Proveedores de este local
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'tb_local_supplier', 'local_id', 'supplier_id')
            ->withTimestamps();
    }

    /**
     * Relación: Galería de imágenes del local
     */
    public function gallery()
    {
        return $this->hasMany(LocalGallery::class, 'local_id', 'local_id');
    }

    /**
     * Relación: Horarios del local 
     */
    public function schedules(){
        return $this->hasMany(Schedule::class, 'local_id', 'local_id');
    }

    /**
     * Relación: Productos disponibles en este local
     * Relación: Reseñas del local
     */
    public function localReviews()
    {
        return $this->hasMany(LocalReview::class, 'local_id', 'local_id');
    }

    /**
     * Relación: Productos de este local
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'tblocal_product', 'local_id', 'product_id')
            ->withPivot('price', 'is_available')
            ->withTimestamps();
    }

    /**
     * Accessor: Obtener el promedio de calificación del local
     * Usa el cálculo real de las reseñas
     */
    public function getAverageRatingAttribute()
    {
        $average = $this->localReviews()
            ->join('tbreview', 'tblocal_review.review_id', '=', 'tbreview.review_id')
            ->avg('tbreview.rating');

        return $average ? round($average, 1) : 0;
    }

    /**
     * Accessor: Obtener URL del logo del local
     * Uso en Blade: {{ $local->logo_url }}
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->image_logo) {
            return null;
        }

        // Si ya es URL absoluta, devolverla
        if (str_starts_with($this->image_logo, 'http')) {
            return $this->image_logo;
        }

        // Si empieza con /storage/, usar Storage (compatibilidad atrás)
        if (str_starts_with($this->image_logo, '/storage/')) {
            $path = str_replace('/storage/', '', $this->image_logo);
            return \Illuminate\Support\Facades\Storage::url($path);
        }

        // Si comienza con public/, remover el prefijo public/
        if (str_starts_with($this->image_logo, 'public/')) {
            $logo = str_replace('public/', '', $this->image_logo);
            return asset($logo);
        }

        // Si empieza con images/, usar asset()
        if (str_starts_with($this->image_logo, 'images/')) {
            return asset($this->image_logo);
        }

        // Por defecto, usar asset
        return asset($this->image_logo);
    }
}
