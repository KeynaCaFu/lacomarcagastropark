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
     * Relación: Reseñas del producto
     */
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'product_id');
    }

    /**
     * Accessor: Obtener rating promedio del producto
     * Uso en Blade: {{ $product->average_rating }}
     * Optimizado para usar eager-loaded relations si están disponibles
     */
    public function getAverageRatingAttribute()
    {
        // Si ya tenemos las relaciones cargadas, úsalas
        if ($this->relationLoaded('productReviews')) {
            $reviews = $this->productReviews;
            if ($reviews->isEmpty()) {
                return 0;
            }

            $totalRating = $reviews->sum(function ($productReview) {
                return $productReview->review->rating ?? 0;
            });

            return round($totalRating / $reviews->count(), 1);
        }

        // Si no están cargadas, haz la query (fallback)
        $reviews = $this->productReviews()
            ->whereHas('review')
            ->with('review')
            ->get();

        if ($reviews->isEmpty()) {
            return 0;
        }

        $totalRating = $reviews->sum(function ($productReview) {
            return $productReview->review->rating ?? 0;
        });

        return round($totalRating / $reviews->count(), 1);
    }

    /**
     * Accessor: Obtener URL de la foto
     * Uso en Blade: {{ $product->photo_url }}
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }

        // Si ya es URL absoluta, devolverla
        if (str_starts_with($this->photo, 'http')) {
            return $this->photo;
        }

        // Si empieza con /storage/, usar Storage (compatibilidad atrás)
        if (str_starts_with($this->photo, '/storage/')) {
            $path = str_replace('/storage/', '', $this->photo);
            return \Illuminate\Support\Facades\Storage::url($path);
        }

        // Si comienza con public/images/, remover el prefijo public/
        if (str_starts_with($this->photo, 'public/')) {
            $photo = str_replace('public/', '', $this->photo);
            return asset($photo);
        }

        // Si empieza con images/, usar asset()
        if (str_starts_with($this->photo, 'images/')) {
            return asset($this->photo);
        }

        // Por defecto, usar asset
        return asset($this->photo);
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

    /**
     * Scope: Incluir rating promedio calculado en BD
     * Mucho más eficiente que calcular en PHP
     * Uso: Product::withAverageRating()->get()
     * Resultado: $product->average_rating_db
     */
    public function scopeWithAverageRating($query)
    {
        return $query->selectRaw(
            'tbproduct.*,
            ROUND(COALESCE(AVG(tbreview.rating), 0), 1) as average_rating_db'
        )
        ->leftJoin('tbproduct_review', 'tbproduct.product_id', '=', 'tbproduct_review.product_id')
        ->leftJoin('tbreview', 'tbproduct_review.review_id', '=', 'tbreview.review_id')
        ->groupBy('tbproduct.product_id');
    }

    /**
     * Scope: Optimización para vistas de Plaza
     * Selects específicos + eager loading eficiente
     * Uso: Product::forPlaza()->get()
     */
    public function scopeForPlaza($query)
    {
        return $query->select('tbproduct.product_id', 'tbproduct.name', 'tbproduct.price', 
                             'tbproduct.photo', 'tbproduct.category', 'tbproduct.status',
                             'tbproduct.created_at', 'tbproduct.updated_at')
            ->with([
                'locals' => function ($q) {
                    $q->select('tblocal.local_id', 'tblocal.name');
                }
            ]);
    }

    /**
     * Scope: Obtener solo categorías disponibles
     * Uso: Product::availableCategories()
     */
    public function scopeAvailableCategories($query)
    {
        return $query->where('status', 'Available')
            ->select('category')
            ->distinct()
            ->orderBy('category');
    }

    /**
     * Scope: Filtrar por rango de precio
     * Uso: Product::priceRange(10, 50)->get()
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }
}
