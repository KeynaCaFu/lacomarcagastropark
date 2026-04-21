<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirigir a Google para autenticación
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
            Log::error('Google OAuth credentials not configured');
            return redirect()->route('login')->withErrors(['error' => 'Google no está configurado.']);
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            Log::error('Google redirect error', ['message' => $e->getMessage()]);
            return redirect()->route('login')->withErrors(['error' => 'Error al redirigir a Google.']);
        }
    }

    /**
     * Obtener información del usuario desde Google y registrar/iniciar sesión
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            // Obtener usuario desde Google (valida state automáticamente)
            $googleUser = Socialite::driver('google')->user();

            // Validar que Google devolvió un ID válido
            if (!$googleUser->getId()) {
                Log::warning('Google returned user without ID');
                return redirect()->route('login')->withErrors(['error' => 'No se pudo obtener tu ID de Google. Por favor, intenta nuevamente.']);
            }

            // Validar que Google devolvió un email válido
            if (!$googleUser->getEmail()) {
                Log::warning('Google returned user without email');
                return redirect()->route('login')->withErrors(['error' => 'No se pudo obtener tu email de Google. Por favor, intenta nuevamente.']);
            }

            // Buscar usuario existente por provider_id o email
            $user = User::where('provider_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Verificar si el usuario existe pero con otro proveedor
                if ($user->provider && $user->provider !== 'google' && $user->provider_id !== $googleUser->getId()) {
                    Log::info('User already registered with different provider', [
                        'email' => $googleUser->getEmail(),
                        'existing_provider' => $user->provider,
                    ]);

                    return redirect()->route('login')->withErrors(['error' => 'Este email ya está registrado con otro método. Por favor, inicia sesión con ese método.']);
                }

                // Actualizar información de Google si es necesario
                if (!$user->provider || $user->provider !== 'google') {
                    $user->update([
                        'provider' => 'google',
                        'provider_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);

                    Log::info('User provider updated to Google', ['user_id' => $user->id]);
                }
            } else {
                // Crear nuevo usuario desde Google
                $clientRole = Role::where('role_type', 'Cliente')->first();
                
                if (!$clientRole) {
                    Log::error('Client role not found in database');
                    return redirect()->route('login')->withErrors(['error' => 'No se encontró el rol Cliente. Por favor, contacta al administrador.']);
                }

                $user = User::create([
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'phone' => null,
                    'password' => bcrypt(uniqid()),
                    'role_id' => $clientRole->role_id,
                    'status' => 'Active',
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);

                Log::info('New user created via Google', ['user_id' => $user->id, 'email' => $user->email]);
            }

            // Verificar que el usuario esté activo
            if ($user->status !== 'Active') {
                Log::warning('Inactive user attempted login via Google', ['user_id' => $user->id]);
                return redirect()->route('login')->withErrors(['error' => 'Tu cuenta ha sido desactivada. Contacta al administrador.']);
            }

            // Iniciar sesión
            Auth::login($user, true); // true para "recordarme"
            
            // Registrar timestamps de sesión de Google para validación posterior
            session(['google_session_start_time' => now()]);
            session(['google_session_last_activity' => now()]);
            
            // Recargar usuario para asegurar relaciones
            $user = Auth::user();
            $user->load('role');

            Log::info('User logged in via Google', ['user_id' => $user->id, 'role' => $user->role->role_type ?? 'unknown']);

            // Redirigir según el rol
            if ($user->role && $user->role->role_type === 'Administrador') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role && $user->role->role_type === 'Gerente') {
                return redirect()->route('dashboard');
            }
            
            return redirect()->route('plaza.index');

        } catch (InvalidStateException $e) {
            // Error de validación de estado (ataque CSRF potencial)
            Log::warning('Invalid state exception in Google callback', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors(['error' => 'La sesión de autenticación expiró o es inválida. Por favor, intenta nuevamente.']);

        } catch (Exception $e) {
            Log::error('Google authentication error', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // No exponer detalles técnicos al usuario
            $errorMessage = 'Error al autenticarse con Google. Por favor, intenta nuevamente.';
            
            // En desarrollo, mostrar más detalles
            if (config('app.debug')) {
                $errorMessage .= ' (' . $e->getMessage() . ')';
            }

            return redirect()->route('login')->withErrors(['error' => $errorMessage]);
        }
    }
}
