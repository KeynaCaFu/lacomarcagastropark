<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on user role
        $user = auth()->user();
        
        // Asegurar que la relación role esté cargada
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        // Solo admins globales y locales se redirigen a sus dashboards
        if ($user->isAdminGlobal()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAdminLocal()) {
            return redirect()->route('dashboard');
        }
        
        // Por defecto, clientes y cualquier otro usuario ve la plaza
        return redirect()->route('plaza.index');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('logged_out', 'Sesión cerrada correctamente');
    }
}
