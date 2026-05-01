<?php

namespace App\Http\Middleware;

use App\Models\Schedule;
use Closure;
use Illuminate\Http\Request;

class CheckBusinessHours
{
    public function handle(Request $request, Closure $next)
    {
        $localId = $request->input('local_id');

        if (!$localId) {
            return $next($request);
        }

        if (!Schedule::isCurrentlyOpen((int) $localId)) {
            $message = 'El local no se encuentra en horario de atención. No se pueden procesar pedidos en este momento.';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => $message], 422);
            }

            return back()->with('error', $message);
        }

        return $next($request);
    }
}
