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
     * Relación: Reseñas del local
     */
    public function localReviews()
    {
        return $this->hasMany(LocalReview::class, 'local_id', 'local_id');
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
}
