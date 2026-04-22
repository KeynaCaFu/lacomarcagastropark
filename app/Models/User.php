<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbuser';

    protected $primaryKey = 'user_id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role_id',
        'status',
        'provider',
        'provider_id',
        'remember_token',
        'avatar',
        'temporary_password',
        'temporary_password_expires_at',
        'google_token_expires_at',
        'google_token_last_refreshed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relationship with Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Relationship with Local (for managers)
     */
    public function locals()
    {
        return $this->belongsToMany(Local::class, 'tbuser_local', 'user_id', 'local_id');
    }

    /**
     * Check if user is admin (global admin)
     */
    public function isAdminGlobal()
    {
        // Cargar la relación si no está cargada
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        
        // Verificar por role_type en lugar de role_id hardcodeado
        return $this->role && $this->role->role_type === 'Administrador';
    }

    /**
     * Accessor: generar URL correcta para avatar
     */
    public function getAvatarUrlAttribute()
    {
        if (empty($this->attributes['avatar'])) {
            return null;
        }

        $avatar = $this->attributes['avatar'];

        // Si es una URL absoluta, retornar como está
        if (str_starts_with($avatar, 'http')) {
            return $avatar;
        }

        // Si comienza con /storage/, usar Storage::url() para backward compatibility
        if (str_starts_with($avatar, '/storage/')) {
            $path = str_replace('/storage/', '', $avatar);
            return \Illuminate\Support\Facades\Storage::url($path);
        }

        // Si comienza con public/images/, remover el prefijo public/
        if (str_starts_with($avatar, 'public/')) {
            $avatar = str_replace('public/', '', $avatar);
        }

        // Si comienza con images/, usar asset()
        if (str_starts_with($avatar, 'images/')) {
            return asset($avatar);
        }

        // Por defecto, asumir que es una ruta pública
        return asset($avatar);
    }

    /**
     * Check if user is manager (local admin)
     */
    public function isAdminLocal()
    {
        // Cargar la relación si no está cargada
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        
        // Verificar por role_type en lugar de role_id hardcodeado
        return $this->role && $this->role->role_type === 'Gerente';
    }

    /**
     * Check if user is client
     */
    public function isClient()
    {
        // Cargar la relación si no está cargada
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        
        // Verificar por role_type
        return $this->role && $this->role->role_type === 'Cliente';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }
}

