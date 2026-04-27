<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrSetting extends Model
{
    use HasFactory;

    protected $table = 'qr_settings';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'qr_key',
        'qr_url',
        'is_active',
        'generated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el usuario que generó el QR
     */
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by', 'user_id');
    }

    /**
     * Relación con los logs de generación
     */
    public function logs()
    {
        return $this->hasMany(QrGenerationLog::class, 'qr_setting_id');
    }

    /**
     * Obtener el QR activo (normalmente solo hay uno)
     */
    public static function getActiveQr()
    {
        return self::where('is_active', true)->latest('updated_at')->first();
    }

    /**
     * Generar una nueva clave secreta
     */
    public static function generateNewKey()
    {
        return strtoupper(\Illuminate\Support\Str::random(20));
    }

    /**
     * Generar la URL del QR
     */
    public function generateQrUrl($appUrl = null)
    {
        $baseUrl = $appUrl ?? env('APP_URL');
        return "{$baseUrl}/api/orders/validate?key={$this->qr_key}";
    }
}
