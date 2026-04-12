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
                ->withErrors(['email' => 'No encontramos una cuenta con ese email.']);
        }

        // Generate a temporary password (12 characters)
        $tempPassword = Str::random(12);

        // Update user password
        $user->password = Hash::make($tempPassword);
        $user->save();

        // Send email with temporary password
        try {
            Mail::to($user->email)->send(new RecoveryPasswordMail($user->full_name, $tempPassword));
            
            return back()->with('status', 'Se ha enviado una contraseña temporal a tu correo. Revisa tu bandeja de entrada.');
        } catch (\Exception $e) {
            \Log::error('Error sending recovery password email: ' . $e->getMessage());
            
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Hubo un error al enviar el correo. Intenta más tarde.']);
        }
    }
}
