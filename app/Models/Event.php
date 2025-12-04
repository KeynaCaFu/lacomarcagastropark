<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    
    protected $table = 'tbevents';
    protected $primaryKey = 'event_id';
    public $timestamps = true; // usa created_at y updated_at

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'location',
        'is_active',
        'image_url',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Scope: buscar por título
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('title', 'like', '%' . $search . '%');
        }
        return $query;
    }

    /**
     * Scope: solo activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessor: estado en español para vistas
     */
    public function getStatusInSpanishAttribute()
    {
        return $this->is_active ? 'Activo' : 'Inactivo';
    }

    /**
     * Mutator convenience: set start_at from date+time strings 
     */
    public function setStartAtAttribute($value)
    {
        if ($value instanceof Carbon) {
            $this->attributes['start_at'] = $value;
            return;
        }

        $this->attributes['start_at'] = Carbon::parse($value);
    }
}
