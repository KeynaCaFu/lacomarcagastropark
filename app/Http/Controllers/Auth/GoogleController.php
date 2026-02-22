<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tbuser;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirigir a Google para autenticación
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtener información del usuario desde Google y registrar/iniciar sesión
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Buscar si el usuario ya existe por email o provider_id
            $user = Tbuser::where('email', $googleUser->getEmail())
                ->orWhere('provider_id', $googleUser->getId())
                ->first();

            if ($user) {
                // Actualizar información si el usuario existe
                // Pero solo si no tiene provider configurado
                if (!$user->provider || $user->provider !== 'google') {
                    $user->update([
                        'provider' => 'google',
                        'provider_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Crear nuevo usuario
                $clientRole = Role::where('role_type', 'Cliente')->first();
                
                if (!$clientRole) {
                    return redirect('/login')->with('error', 'No se encontró el rol Cliente en la base de datos.');
                }

                $user = Tbuser::create([
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'phone' => null,
                    'password' => bcrypt(uniqid()), // Contraseña random por si acaso
                    'role_id' => $clientRole->role_id,
                    'status' => 'Active',
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Iniciar sesión automáticamente
            Auth::login($user, true); // true para "recordarme"

            // Redirigir al dashboard o home
            return redirect()->intended(route('dashboard'));

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Error al autenticarse con Google: ' . $e->getMessage());
        }
    }

    /**
     * Desconectar del proveedor (opcional)
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('status', 'Sesión cerrada correctamente.');
    }
}
