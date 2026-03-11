<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Http\Request;

class LocalController extends Controller
{
    //Mostrar el formulario de edicion del local del gerente
    public function edit(Request $request){
        $user = $request->user();
        $local = $user->locals()->first(); 

        if(!$local){
            return redirect()->route('dashboard')
            ->with('error', 'No tienes un local asignado.');
        }

        return view('local.edit', compact('local'));
    }

    //Actualizar la informacion del local del gerente
    public function update(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        // Validar los datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contact' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'image_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Si hay una nueva imagen, guardarla en public/images/locals
        if ($request->hasFile('image_logo')) {
            $file = $request->file('image_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/locals'), $filename);
            $validated['image_logo'] = 'images/locals/' . $filename;
        }

        // Actualizar el local
        $local->update($validated);

        return redirect()->route('local.edit')
            ->with('success', '✓ Datos del local actualizados correctamente.');
    }

    //Mostrar la galeria de imagenes del local del gerente
    /**
     * Ver la galería del local
     */
    public function gallery(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        // Esta funcionalidad se implementará más adelante
        return view('local.gallery', compact('local'));
    }


}