<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'tbproduct_review';
    protected $primaryKey = 'product_review_id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'review_id',
        'product_id',
        'user_id',
        'responded_by'
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}