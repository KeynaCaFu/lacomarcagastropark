<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Mail\TemporaryPasswordMail;

class ClienteController extends Controller
{
    /**
     * Display the client welcome page
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('client.client-welcome', [
            'user' => $user,
        ]);
    }

    /**
     * Show the client profile edit form
     */
    public function editProfile()
    {
        $user = auth()->user();
        
        return view('client.cliente-perfil', [
            'user' => $user,
        ]);
    }

    /**
     * Update the client profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar) {
                $oldPath = public_path(str_replace(url('/'), '', $user->avatar));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Crear directorio si no existe
            $avatarDir = public_path('images/avatars');
            if (!File::isDirectory($avatarDir)) {
                File::makeDirectory($avatarDir, 0755, true, true);
            }

            // Guardar nuevo avatar
            $filename = 'avatar_' . $user->user_id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move($avatarDir, $filename);
            $validated['avatar'] = 'images/avatars/' . $filename;
        }

        $user->update($validated);

        return redirect()->route('plaza.index')->with('status', 'Perfil actualizado exitosamente.');
    }

    /**
     * Update the client password
     */
    public function updatePassword(Request $request)
    {
        // No permitir cambio de contraseña si es una cuenta de terceros (Google, etc)
        $user = auth()->user();
        if ($user->provider) {
            return redirect()->route('client.profile.edit')->withErrors([
                'password' => 'No puedes cambiar la contraseña de una cuenta vinculada a ' . ucfirst($user->provider) . '.'
            ]);
        }

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('client.profile.edit')->with('status', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Request a temporary password via email
     */
    public function requestTemporaryPassword(Request $request)
    {
        $user = auth()->user();

        // No permitir si es una cuenta de terceros
        if ($user->provider) {
            return redirect()->route('client.profile.edit')->withErrors([
                'temporary' => 'No puedes recuperar contraseña en una cuenta vinculada a ' . ucfirst($user->provider) . '.'
            ]);
        }

        // Generar contraseña temporal (8 caracteres: mayúsculas, minúsculas, números)
        $temporaryPassword = Str::random(4) . rand(1000, 9999) . Str::random(2);
        
        // Guardar la contraseña temporal en la base de datos (con expiración de 15 minutos)
        $user->update([
            'temporary_password' => Hash::make($temporaryPassword),
            'temporary_password_expires_at' => now()->addMinutes(15),
        ]);

        // Enviar el correo con la contraseña temporal
        Mail::to($user->email)->send(new TemporaryPasswordMail($user, $temporaryPassword));

        return back()->with('status', 'Se ha enviado una contraseña temporal a tu correo. Válida por 15 minutos.');
    }

    /**
     * Update password using temporary password
     */
    public function updatePasswordWithTemporary(Request $request)
    {
        $user = auth()->user();

        if ($user->provider) {
            return redirect()->route('client.profile.edit')->withErrors([
                'password' => 'No puedes cambiar la contraseña de una cuenta vinculada a ' . ucfirst($user->provider) . '.'
            ]);
        }

        // Validar que tenga una contraseña temporal activa
        if (!$user->temporary_password || !$user->temporary_password_expires_at || $user->temporary_password_expires_at < now()) {
            return back()->withErrors([
                'temporary_password' => 'La contraseña temporal ha expirado. Solicita una nueva.'
            ], 'temporaryPassword');
        }

        $validated = $request->validateWithBag('temporaryPassword', [
            'temporary_password' => ['required', 'string'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Verificar la contraseña temporal
        if (!Hash::check($validated['temporary_password'], $user->temporary_password)) {
            return back()->withErrors([
                'temporary_password' => 'La contraseña temporal es incorrecta.'
            ], 'temporaryPassword');
        }

        // Actualizar contraseña y limpiar la temporal
        $user->update([
            'password' => Hash::make($validated['password']),
            'temporary_password' => null,
            'temporary_password_expires_at' => null,
        ]);

        return redirect()->route('client.profile.edit')->with('status', 'Contraseña actualizada exitosamente usando contraseña temporal.');
    }
}
