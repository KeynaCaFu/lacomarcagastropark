<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreserveAdminSessionForPlaza
{
    /**
     * Handle an incoming request.
     * Permite a admins/gerentes ver plaza sin afectar su sesión activa.
     * Si son admin, se guarda su sesión en una variable temporal pero no se cierra.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario está autenticado
        if (auth()->check()) {
            $user = auth()->user();
            
            // Cargar la relación role si no está cargada
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
            
            // Si es admin global o admin local, permitir acceso a plaza pero marcar sesión como "admin_preview"
            if ($user->isAdminGlobal() || $user->isAdminLocal()) {
                // Marcar en la sesión que esta es una vista previa de admin
                session(['plaza_admin_preview' => true, 'admin_user_id' => $user->user_id]);
            }
        }
        
        return $next($request);
    }
}
