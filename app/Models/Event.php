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
     * Scope: activos y cuya fecha no superó las 24 horas de antigüedad
     */
    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where('start_at', '>=', now()->subHours(24));
    }

    /**
     * Scope: excluir eventos que pasaron hace más de 24 horas
     * Solo muestra eventos que aún no han terminado o terminaron hace menos de 24 horas
     */
    public function scopeNotExpired($query)
    {
        // Obtener la hora actual con zona horaria correcta
        $now = \Carbon\Carbon::now();
        $twentyFourHoursAgo = $now->copy()->subHours(24);
        
        return $query->where('start_at', '>=', $twentyFourHoursAgo);
    }

    /**
     * Accessor: estado en español para vistas
     */
    public function getStatusInSpanishAttribute()
    {
        return $this->is_active ? 'Activo' : 'Inactivo';
    }

    /**
     * Accessor: generar URL correcta para image_url
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->attributes['image_url'])) {
            return null;
        }

        $url = $this->attributes['image_url'];

        // Si es una URL absoluta, limpiarla si tiene /public/ en la ruta
        if (str_starts_with($url, 'http')) {
            // Remover /public/ de la URL si está allí (http://.../ public/images -> http://.../images)
            $url = str_replace('/public/', '/', $url);
            return $url;
        }

        // Si comienza con /storage/, usar Storage::url() para backward compatibility
        if (str_starts_with($url, '/storage/')) {
            $path = str_replace('/storage/', '', $url);
            return \Illuminate\Support\Facades\Storage::url($path);
        }

        // Si comienza con public/images/, remover el prefijo public/
        if (str_starts_with($url, 'public/')) {
            $url = str_replace('public/', '', $url);
        }

        // Si comienza con images/, usar asset()
        if (str_starts_with($url, 'images/')) {
            return asset($url);
        }

        // Por defecto, asumir que es una ruta pública
        return asset($url);
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
