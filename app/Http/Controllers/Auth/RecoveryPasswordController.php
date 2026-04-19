<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RecoveryPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecoveryPasswordController extends Controller
{
    /**
     * Display the password recovery request view.
     */
    public function create(): View
    {
        return view('auth.recovery-password');
    }

    /**
     * Handle an incoming password recovery request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No encontramos una cuenta con ese email.'], 'recovery');
        }

        // Validar si es una cuenta de un proveedor externo (como Google)
        if (!empty($user->provider_id)) {
            return redirect()->route('login')
                ->with('recovery-error', 'Esa cuenta está registrada con un proveedor externo (como Google). Debes iniciar sesión a través de ese servicio.');
        }

        // Generate a temporary password (12 characters)
        $tempPassword = Str::random(12);

        // Update user with temporary password and expiration
        $user->temporary_password = Hash::make($tempPassword);
        $user->temporary_password_expires_at = now()->addHours(24);
        $user->save();

        // Send email with temporary password
        try {
            Mail::to($user->email)->send(new RecoveryPasswordMail($user->full_name, $tempPassword));
            
            return redirect()->route('login')->with('recovery-status', 'Se ha enviado una contraseña temporal a tu correo. Revisa tu bandeja de entrada.');
        } catch (\Exception $e) {
           
            
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Hubo un error al enviar el correo. Intenta más tarde.'], 'recovery');
        }
    }
}
