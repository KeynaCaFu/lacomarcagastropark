<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrGenerationLog extends Model
{
    use HasFactory;

    protected $table = 'qr_generation_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'qr_setting_id',
        'action',
        'old_key',
        'new_key',
        'admin_id',
        'admin_ip',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relación con el QR Setting
     */
    public function qrSetting()
    {
        return $this->belongsTo(QrSetting::class, 'qr_setting_id');
    }

    /**
     * Relación con el administrador que realizó la acción
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
}
