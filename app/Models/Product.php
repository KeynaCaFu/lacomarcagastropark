<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'tbproduct';
    protected $primaryKey = 'product_id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'description',
        'category',
        'tag',
        'product_type',
        'price',
        'photo',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relación: Galería de imágenes del producto
     */
    public function gallery()
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'product_id');
    }

    /**
     * Relación: Locales que tienen este producto
     */
    public function locals()
    {
        return $this->belongsToMany(Local::class, 'tblocal_product', 'product_id', 'local_id')
            ->withPivot('price', 'is_available')
            ->withTimestamps();
    }

    /**
     * Accessor: Obtener URL de la foto usando Storage
     * Uso en Blade: {{ $product->photo_url }}
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }

        // Si la ruta ya tiene /storage/, quitarlo
        $path = $this->photo;
        if (str_starts_with($path, '/storage/')) {
            $path = str_replace('/storage/', '', $path);
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }

    /**
     * Accessor: Obtener estado en español para las vistas
     * Uso en Blade: {{ $product->status_in_spanish }}
     */
    public function getStatusInSpanishAttribute()
    {
        $statusMap = [
            'Available' => 'Disponible',
            'Unavailable' => 'No disponible'
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * Scope: Filtrar por nombre
     */
    public function scopeSearch($query, $term)
    {
        if (!empty($term)) {
            return $query->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%");
        }
        return $query;
    }

    /**
     * Scope: Filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        if (!empty($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope: Solo productos disponibles
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Available');
    }

    /**
     * Scope: Solo productos no disponibles
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Unavailable');
    }

    /**
     * Scope: Filtrar por categoría
     */
    public function scopeByCategory($query, $category)
    {
        if (!empty($category)) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope: Filtrar productos de un local específico
     */
    public function scopeByLocal($query, $localId)
    {
        if (!empty($localId)) {
            return $query->whereHas('locals', function ($q) use ($localId) {
                $q->where('tblocal_product.local_id', $localId);
            });
        }
        return $query;
    }
}
