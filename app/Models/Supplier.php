<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'tbsuppliers';
    protected $primaryKey = 'supplier_id';
    public $timestamps = true; // Usando created_at y updated_at

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'status', 'total_purchases'
    ];

    protected $casts = [
        'total_purchases' => 'decimal:2',
    ];

    public function supplies()
    {
       
        return $this->belongsToMany(Supply::class, 'tbsupplier_supply', 'supplier_id', 'supply_id')
            ->withPivot('created_at');
    }

    /**
     * Accessor: Obtener estado en español para las vistas
     * Uso en Blade: {{ $supplier->status_in_spanish }}
     */
    public function getStatusInSpanishAttribute()
    {
        $statusMap = [
            'Active' => 'Activo',
            'Inactive' => 'Inactivo'
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * Scope: Filtrar por nombre
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        }
        return $query;
    }

    /**
     * Scope: Solo activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope: Solo inactivos
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    /**
     * Scope: Filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Accessor: Verificar si está activo
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'Active';
    }

    /**
     * Accessor: Obtener número de insumos asociados
     */
    public function getSuppliesCountAttribute()
    {
        return $this->supplies()->count();
    }

    /**
     * Accessor: Verificar si tiene insumos asociados
     */
    public function getHasSuppliesAttribute()
    {
        return $this->supplies()->exists();
    }
}
