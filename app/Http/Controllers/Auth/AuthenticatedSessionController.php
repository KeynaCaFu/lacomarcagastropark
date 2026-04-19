<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        // 1. Check if user has temporary password (expired or not)
        if ($user && $user->temporary_password && $user->temporary_password_expires_at) {
            // Check if temporary password is still valid
            if (now()->isBefore($user->temporary_password_expires_at)) {
                // Temporary password is valid, check if it matches
                if (Hash::check($credentials['password'], $user->temporary_password)) {
                    // Log the user in
                    Auth::login($user);
                    $request->session()->regenerate();
                    // Redirect to change temporary password form
                    return redirect()->route('client.password.change-temporary-form');
                } else {
                    // Temporary password is active but incorrect
                    // Don't proceed to normal auth, show specific error
                    return back()
                        ->withInput($request->only('email'))
                        ->withErrors(['password' => 'La contraseña temporal es incorrecta.']);
                }
            } else {
                // Temporary password has expired
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['password' => 'Tu contraseña temporal ha expirado. Por favor, solicita una nueva']);
            }
        }

        // 2. If no temporary password, proceed with normal authentication
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
        // Obtener el usuario antes de logout
        $user = auth()->user();
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirigir según el rol del usuario
        if ($user && ($user->isAdminGlobal() || $user->isAdminLocal())) {
            // Administrador o Gerente -> al login
            return redirect()->route('login')->with('logged_out', 'Sesión cerrada correctamente');
        }
        
        // Cliente u otros -> a la plaza
        return redirect('/')->with('logged_out', 'Sesión cerrada correctamente');
    }
}
