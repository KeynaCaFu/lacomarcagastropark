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
        return $query->where('local_id', $localId)
            ->orderByRaw("FIELD(day_of_week, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')");
    }

    /**
     * Scope: Solo horarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Obtener horario de hoy para un local
     * Optimizado: una sola query, select específico
     * Uso: Schedule::todayForLocal($localId)->first()
     */
    public function scopeTodayForLocal($query, $localId)
    {
        $dayOfWeek = \App\Helpers\PlazaHelper::translateDayToSpanish(now()->format('l'));
        
        return $query->select('schedule_id', 'local_id', 'day_of_week', 'opening_time', 'closing_time', 'status')
            ->where('local_id', $localId)
            ->where('day_of_week', $dayOfWeek)
            ->active();
    }

    /**
     * Scope: Obtener todos los horarios de hoy para múltiples locales
     * Optimizado: una sola query
     * Uso: Schedule::todayForLocals($localIds)->get()
     */
    public function scopeTodayForLocals($query, $localIds)
    {
        $dayOfWeek = \App\Helpers\PlazaHelper::translateDayToSpanish(now()->format('l'));
        
        return $query->select('schedule_id', 'local_id', 'day_of_week', 'opening_time', 'closing_time', 'status')
            ->whereIn('local_id', $localIds)
            ->where('day_of_week', $dayOfWeek)
            ->active();
    }

    /**
     * Scope: Obtener horarios de un día específico
     */
    public function scopeByDay($query, $day)
    {
        return $query->where('day_of_week', $day)->active();
    }

    /**
     * Verificar si el local está abierto en una hora específica
     * Optimizado: usa scopes y PlazaHelper
     */
    public static function isCurrentlyOpen($localId)
    {
        $now = now();
        $schedule = self::todayForLocal($localId)->first();

        if (!$schedule) {
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