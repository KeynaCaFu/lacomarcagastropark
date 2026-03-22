<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'tbsupplier';
    protected $primaryKey = 'supplier_id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'phone',
        'email'
    ];

    /**
     * Relación: Galería de imágenes del proveedor
     */
    public function gallery()
    {
        return $this->hasMany(SupplierGallery::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Relación: Locales que tienen este proveedor
     */
    public function locals()
    {
        return $this->belongsToMany(Local::class, 'tblocal_supplier', 'supplier_id', 'local_id')
            ->withTimestamps();
    }

    /**
     * Scope: Filtrar por búsqueda (nombre, teléfono, email)
     */
    public function scopeSearch($query, $term)
{
    if (!empty($term)) {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    return $query;
}

    /**
     * Scope: Filtrar proveedores de un local específico
     */
    public function scopeByLocal($query, $localId)
    {
        if (!empty($localId)) {
            return $query->whereHas('locals', function ($q) use ($localId) {
                $q->where('tblocal_supplier.local_id', $localId);
            });
        }
        return $query;
    }
}
