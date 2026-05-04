<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalReview extends Model
{
    protected $table = 'tblocal_review';
    protected $primaryKey = 'local_review_id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'review_id',
        'local_id',
        'user_id'
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id', 'local_id');
    }


/** Metodo para la prueba unitaria que me compara si el user_id de la reseña es igual al usuario que se le pasa. Devuelve true o false. */

    public function perteneceAlUsuario($userId)
{
    return $this->user_id === $userId;
}
}