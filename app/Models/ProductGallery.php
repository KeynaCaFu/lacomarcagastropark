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
     * Relación: Producto al que pertenece esta imagen
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
