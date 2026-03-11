<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'tblocal';

    protected $primaryKey = 'local_id';

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
}
