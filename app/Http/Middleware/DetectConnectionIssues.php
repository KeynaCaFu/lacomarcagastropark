<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectConnectionIssues
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Intentar verificar la conectividad de internet
            if (!$this->hasInternetConnection()) {
                return response()->view('errors.no-internet', [], 503);
            }

            return $next($request);
        } catch (\Exception $e) {
            // Si hay algún error inesperado, dejar que continúe
            return $next($request);
        }
    }

    /**
     * Verificar si hay conexión a internet.
     * Intenta hacer una solicitud DNS rápida.
     *
     * @return bool
     */
    protected function hasInternetConnection(): bool
    {
        // En desarrollo, siempre asumir que hay conexión
        if (app()->environment('local')) {
            return true;
        }

        // Intentar resolver un dominio confiable
        $host = 'google.com';
        $timeout = 2;

        // Usar fsockopen o gethostbyname para verificar
        if (function_exists('gethostbyname')) {
            $ip = @gethostbyname($host);
            if ($ip !== $host) {
                return true; // Se resolvió correctamente
            }
        }

        // Intentar conexión TCP
        $fp = @fsockopen($host, 80, $errno, $errstr, $timeout);
        if ($fp) {
            fclose($fp);
            return true;
        }

        return false;
    }
}
