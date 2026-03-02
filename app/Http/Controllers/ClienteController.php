<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

        return redirect()->route('client.welcome')->with('status', 'Perfil actualizado exitosamente.');
    }
}
