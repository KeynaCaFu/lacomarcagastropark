<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'tbrole';

    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_type',
        'permissions_list',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Relationship with User
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
