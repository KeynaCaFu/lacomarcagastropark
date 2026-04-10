<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * DEPRECATED: Use PreserveAdminSessionForPlaza instead
 * Este archivo se mantiene por compatibilidad pero no se utiliza
 */
class BlockNonClients
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}

