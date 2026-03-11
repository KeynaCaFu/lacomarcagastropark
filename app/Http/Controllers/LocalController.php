<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Support\Str;
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
            // Generar nombre único: nombrearchivo_codigoaleatorio.extensión
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . Str::random(20) . '.' . $extension;
            
            // Asegurar que el directorio existe
            $destinationPath = public_path('images/locals');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Mover el archivo a la ruta correcta
            $file->move($destinationPath, $filename);
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