<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\LocalGallery;
use Illuminate\Support\Str;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
   

    /**
     * Mostrar horario del local ver solo horarios
     */
    public function schedule(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        // Obtener los horarios del local ordenados por día de la semana
        $schedules = Schedule::byLocal($local->local_id)->get();

        // Obtener el estado actual del local
        $isOpen = Schedule::isCurrentlyOpen($local->local_id);
        $currentStatus = Schedule::getCurrentStatus($local->local_id);

        // Preparar breadcrumbs
        $crumbs = [
            ['label' => 'Horario', 'url' => null]
        ];

        return view('local.schedule', compact('local', 'schedules', 'isOpen', 'currentStatus', 'crumbs'));
    }

    /**
     * Actualizar horario de un día del local del gerente
     */
    public function updateSchedule(Request $request, $scheduleId)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        $schedule = Schedule::where('schedule_id', $scheduleId)
            ->where('local_id', $local->local_id)
            ->first();

        if (!$schedule) {
            return redirect()->route('local.schedule')
                ->with('error', 'Horario no encontrado.');
        }

        $validated = $request->validate([
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'status' => 'required|boolean',
        ], [
            'opening_time.date_format' => 'La hora de apertura debe tener el formato HH:MM.',
            'closing_time.date_format' => 'La hora de cierre debe tener el formato HH:MM.',
            'status.required' => 'El estado del horario es obligatorio.',
        ]);

        $isOpenDay = (bool) $validated['status'];
        $openingTime = $validated['opening_time'] ?? null;
        $closingTime = $validated['closing_time'] ?? null;

        if ($isOpenDay) {
            if (!$openingTime || !$closingTime) {
                return redirect()->route('local.schedule')
                    ->with('error', 'Debes indicar hora de apertura y cierre para un día abierto.');
            }

            if ($openingTime >= $closingTime) {
                return redirect()->route('local.schedule')
                    ->with('error', 'La hora de apertura debe ser menor a la hora de cierre.');
            }
        } else {
            $openingTime = null;
            $closingTime = null;
        }

        $schedule->update([
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
            'status' => $isOpenDay,
        ]);

        return redirect()->route('local.schedule')
            ->with('success', '✓ Horario actualizado correctamente.');
    }

    /**
     * Guardar nuevo horario para un día del local del gerente
     */
    public function storeSchedule(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes un local asignado.'], 403);
            }
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        $validated = $request->validate([
            'day_of_week' => 'required|string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'status' => 'required|boolean',
        ], [
            'day_of_week.required' => 'El día de la semana es obligatorio.',
            'day_of_week.in' => 'Debes seleccionar un día válido.',
            'opening_time.date_format' => 'La hora de apertura debe tener el formato HH:MM.',
            'closing_time.date_format' => 'La hora de cierre debe tener el formato HH:MM.',
            'status.required' => 'El estado del horario es obligatorio.',
        ]);

        // Verificar si el día ya existe
        $existingSchedule = Schedule::where('local_id', $local->local_id)
            ->where('day_of_week', $validated['day_of_week'])
            ->first();

        if ($existingSchedule) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Este día ya tiene un horario configurado.'], 409);
            }
            return redirect()->route('local.schedule')
                ->with('error', 'Este día ya tiene un horario configurado.');
        }

        $isOpenDay = (bool) $validated['status'];
        $openingTime = $validated['opening_time'] ?? null;
        $closingTime = $validated['closing_time'] ?? null;

        if ($isOpenDay) {
            if (!$openingTime || !$closingTime) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Debes indicar hora de apertura y cierre para un día abierto.'], 422);
                }
                return redirect()->route('local.schedule')
                    ->with('error', 'Debes indicar hora de apertura y cierre para un día abierto.');
            }

            if ($openingTime >= $closingTime) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'La hora de apertura debe ser menor a la hora de cierre.'], 422);
                }
                return redirect()->route('local.schedule')
                    ->with('error', 'La hora de apertura debe ser menor a la hora de cierre.');
            }
        } else {
            $openingTime = null;
            $closingTime = null;
        }

        Schedule::create([
            'local_id' => $local->local_id,
            'day_of_week' => $validated['day_of_week'],
            'opening_time' => $openingTime,
            'closing_time' => $closingTime,
            'status' => $isOpenDay,
        ]);

        $message = '✓ Horario agregado correctamente para ' . $validated['day_of_week'] . '.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 200);
        }

        return redirect()->route('local.schedule')
            ->with('success', $message);
    }

    

    /**
     * Eliminar horario de un día del local del gerente
     */
    public function destroySchedule(Request $request, $scheduleId)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        $schedule = Schedule::where('schedule_id', $scheduleId)
            ->where('local_id', $local->local_id)
            ->first();

        if (!$schedule) {
            return redirect()->route('local.schedule')
                ->with('error', 'Horario no encontrado.');
        }

        $schedule->delete();

        return redirect()->route('local.schedule')
            ->with('success', '✓ Horario eliminado correctamente.');
    }
}