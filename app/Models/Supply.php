<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Supply extends Model
{
    protected $table = 'tbsupplies';
    protected $primaryKey = 'supply_id';
    public $timestamps = true; // Usando created_at y updated_at
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'current_stock',
        'minimum_stock',
        'expiration_date',
        'unit_of_measure',
        'quantity',
        'price',
        'status'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'price' => 'decimal:2',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'quantity' => 'integer'
    ];

    /**
     * Relación muchos a muchos con Proveedores
     */
    public function suppliers()
    {
        return $this->belongsToMany(
            Supplier::class,
            'tbsupplier_supply',
            'supply_id',
            'supplier_id'
        )
            
            ->withPivot('created_at');
    }

    /**
     * Accessor: Obtener estado en español para las vistas
     * Uso en Blade: {{ $supply->status_in_spanish }}
     */
    public function getStatusInSpanishAttribute()
    {
        $statusMap = [
            'Available' => 'Disponible',
            'Out of Stock' => 'Agotado',
            'Expired' => 'Vencido'
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
     * Scope: Stock bajo (current_stock <= minimum_stock)
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    /**
     * Scope: Por vencer (próximos 30 días)
     */
    public function scopeExpiringSoon($query)
    {
        $now = Carbon::now();
        $future = Carbon::now()->addDays(30);
        
        return $query->whereBetween('expiration_date', [$now, $future]);
    }

    /**
     * Scope: Vencidos
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', Carbon::now());
    }

    /**
     * Scope: En buen estado (sin vencer o sin fecha de vencimiento)
     */
    public function scopeGoodCondition($query)
    {
        $now = Carbon::now();
        
        return $query->where(function($q) use ($now) {
            $q->whereNull('expiration_date')
              ->orWhere('expiration_date', '>', $now->addDays(30));
        });
    }

    /**
     * Accessor: Verificar si el stock está bajo
     */
    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Accessor: Verificar si está vencido
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiration_date) {
            return false;
        }
        
        // Ensure Carbon instance to avoid analyzer warnings
        return \Carbon\Carbon::parse($this->expiration_date)->isPast();
    }

    /**
     * Accessor: Verificar si está por vencer (próximos 30 días)
     */
    public function getIsExpiringSoonAttribute()
    {
        if (!$this->expiration_date) {
            return false;
        }
        
        $now = Carbon::now();
        $future = Carbon::now()->addDays(30);
        
        $date = \Carbon\Carbon::parse($this->expiration_date);
        return $date->between($now, $future);
    }

    /**
     * Accessor: Días hasta vencimiento
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->expiration_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->expiration_date, false);
    }

    /**
     * Accessor: Diferencia de stock (current - minimum)
     */
    public function getStockDifferenceAttribute()
    {
        return $this->current_stock - $this->minimum_stock;
    }
}
