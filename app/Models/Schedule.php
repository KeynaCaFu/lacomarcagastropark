<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'tbschedule';

    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'day_of_week',
        'opening_time',
        'closing_time',
        'status',
        'local_id',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $casts = [
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',
        'status' => 'boolean',
    ];

    /**
     * Relación: Local al que pertenece este horario
     */
    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id', 'local_id');
    }

    /**
     * Scope: Obtener horarios de un local específico
     */
    public function scopeByLocal($query, $localId)
    {
        return $query->where('local_id', $localId)->orderByRaw("FIELD(day_of_week, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')");
    }

    /**
     * Verificar si el local está abierto en una hora específica
     */
    public static function isCurrentlyOpen($localId)
    {
        $now = now();
        $dayOfWeek = $now->translatedFormat('l'); // Lunes, Martes, etc.
        
        // Mapear día en inglés a español
        $dayTranslation = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        $dayInSpanish = $dayTranslation[$dayOfWeek] ?? null;

        if (!$dayInSpanish) {
            return false;
        }

        $schedule = self::where('local_id', $localId)
            ->where('day_of_week', $dayInSpanish)
            ->first();

        if (!$schedule || !$schedule->status) {
            return false;
        }

        $currentTime = $now->format('H:i:s');
        $openingTime = $schedule->opening_time ? $schedule->opening_time->format('H:i:s') : null;
        $closingTime = $schedule->closing_time ? $schedule->closing_time->format('H:i:s') : null;

        if (!$openingTime || !$closingTime) {
            return false;
        }

        return $currentTime >= $openingTime && $currentTime < $closingTime;
    }

    /**
     * Obtener el estado actual del local
     */
    public static function getCurrentStatus($localId)
    {
        return self::isCurrentlyOpen($localId) ? 'Abierto' : 'Cerrado';
    }
}