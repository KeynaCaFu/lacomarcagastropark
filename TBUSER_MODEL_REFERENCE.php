<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tbuser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Especificar el nombre de la tabla
     * 
     * @var string
     */
    protected $table = 'tbuser';

    /**
     * Especificar la clave primaria
     * 
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Desactivar timestamps si no están en la tabla
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * Campos que pueden ser asignados masivamente
     * 
     * @var array
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role_id',
        'status',
        'provider',      // Nuevo: para Google Auth
        'provider_id',   // Nuevo: para Google Auth
        'avatar',        // Nuevo: para Google Auth
    ];

    /**
     * Campos que deben estar ocultos en serialización
     * 
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting de atributos
     * 
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación con la tabla tbrole
     * Un usuario pertenece a un rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Verificar si el usuario es admin global
     */
    public function isAdminGlobal()
    {
        return $this->role && in_array($this->role->role_type, ['SuperAdmin', 'Admin']);
    }

    /**
     * Verificar si el usuario es admin local (gerente)
     */
    public function isAdminLocal()
    {
        return $this->role && $this->role->role_type === 'Gerente';
    }

    /**
     * Verificar si el usuario es cliente
     */
    public function isClient()
    {
        return $this->role && $this->role->role_type === 'Cliente';
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Obtener el nombre que se muestra en la aplicación
     */
    public function getDisplayNameAttribute()
    {
        return $this->full_name ?? $this->email;
    }

    /**
     * Verificar si está autenticado con proveedor (Google)
     */
    public function isAuthenticatedWithProvider()
    {
        return !is_null($this->provider) && !is_null($this->provider_id);
    }

    /**
     * Obtener el proveedor de autenticación
     */
    public function getAuthProviderAttribute()
    {
        return $this->provider ?? 'default';
    }
}
