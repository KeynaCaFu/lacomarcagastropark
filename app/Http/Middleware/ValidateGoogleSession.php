<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ValidateGoogleSession
{
    /**
     * Valida que la sesión de Google sea aún válida
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si no hay usuario autenticado, continuar normalmente
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Solo validar para usuarios autenticados con Google
        if ($user->provider !== 'google') {
            return $next($request);
        }

        // Obtener la última actividad de la sesión de Google
        $googleSessionLastActivity = session('google_session_last_activity');
        $googleSessionStartTime = session('google_session_start_time');

        // Si es la primera vez que vemos al usuario de Google, inicializar timestamps
        if (!$googleSessionStartTime) {
            session(['google_session_start_time' => now()]);
            session(['google_session_last_activity' => now()]);
            
            return $next($request);
        }

        // Google tokens típicamente expiran en 1 hora
        $maxSessionAge = 3600; // 1 hora en segundos
        
        // Verificar si la sesión de Google ha expirado (basado en tiempo de inicio)
        $sessionAge = now()->diffInSeconds($googleSessionStartTime);
        
        if ($sessionAge > $maxSessionAge) {
            Log::warning('Google session expired', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_age_seconds' => $sessionAge,
            ]);

            // Logout del usuario
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'error' => 'Tu sesión de Google ha expirado. Por favor, inicia sesión nuevamente.'
            ]);
        }

        // Actualizar la última actividad
        session(['google_session_last_activity' => now()]);

        // Continuamos normalmente
        return $next($request);
    }
}
