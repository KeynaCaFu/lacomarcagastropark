<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'tbreview';
    protected $primaryKey = 'review_id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'rating',
        'comment',
        'date',
        'response'
    ];

    public function localReview()
    {
        return $this->hasOne(LocalReview::class, 'review_id', 'review_id');
    }
}