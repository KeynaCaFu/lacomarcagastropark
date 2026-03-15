<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\LocalGallery;
use Illuminate\Support\Str;
use App\Models\Schedule;
use Illuminate\Http\Request;

class LocalController extends Controller
{
     //Mostrar el índice de opciones del local del gerente
    public function index(Request $request){
        $user = $request->user();
        $local = $user->locals()->first(); 

        if(!$local){
            return redirect()->route('dashboard')
            ->with('error', 'No tienes un local asignado.');
        }

        return view('local.index', compact('local'));
    }

    //Mostrar el formulario de edicion del local del gerente
    public function edit(Request $request){
        $user = $request->user();
        $local = $user->locals()->first(); 

        if(!$local){
            return redirect()->route('dashboard')
            ->with('error', 'No tienes un local asignado.');
        }

        // Preparar breadcrumbs
        $crumbs = [
            ['label' => 'Editar Local', 'url' => null]
        ];

        return view('local.edit', compact('local', 'crumbs'));
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

        return redirect()->route('local.index')
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

        // Obtener todas las imágenes de la galería del local
        $images = $local->gallery()->get();
        
        // Preparar breadcrumbs
        $crumbs = [
            ['label' => 'Galería', 'url' => null]
        ];

        return view('local.gallery', compact('local', 'images', 'crumbs'));
    }

    /**
     * Subir nueva imagen a la galería
     */
    public function galleryUpload(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        // Validar la imagen
        $validated = $request->validate([
            'gallery_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gallery_image')) {
            $file = $request->file('gallery_image');
            // Generar nombre único
            $extension = $file->getClientOriginalExtension();
            $filename = 'gallery_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . Str::random(16) . '.' . $extension;
            
            // Asegurar que el directorio existe
            $destinationPath = public_path('images/locals/gallery');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Mover el archivo a la ruta correcta
            $file->move($destinationPath, $filename);
            
            // Guardar la referencia en la BD
            LocalGallery::create([
                'local_id' => $local->local_id,
                'image_url' => 'images/locals/gallery/' . $filename,
            ]);
        }

        return redirect()->route('local.gallery')
            ->with('success', '✓ Imagen agregada a la galería correctamente.');
    }

    /**
     * Eliminar imagen de la galería
     */
    public function galleryDelete(Request $request, $id)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        // Buscar la imagen
        $image = LocalGallery::where('local_gallery_id', $id)
            ->where('local_id', $local->local_id)
            ->firstOrFail();

        // Eliminar archivo fisico
        $filePath = public_path($image->image_url);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Eliminar del registro en la BD
        $image->delete();

        return redirect()->route('local.gallery')
            ->with('success', '✓ Imagen eliminada correctamente.');
    }
    /**
     * Mostrar índice de todos los locales (para admin global)
     */
    public function indexAdmin(Request $request)
    {
        // Obtener todos los locales con sus gerentes asociados
        $locales = Local::with(['users' => function($q){ $q->where('role_id', 2); }])
            ->orderByDesc('created_at')
            ->get();

        // Obtener todos los usuarios con rol Gerente para el modal de creación
        $gerentes = \App\Models\User::where('role_id', 2)
            ->orderBy('full_name')
            ->get(['user_id', 'full_name', 'email']);

        return view('admin.locales.index', compact('locales', 'gerentes'));
    }

    /**
     * Crear un nuevo local con nombre y gerente asignado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'manager_id' => 'required|exists:tbuser,user_id',
        ]);

        $local = Local::create([
            'name'   => $validated['name'],
            'status' => 'Inactive',
        ]);

        $local->users()->attach($validated['manager_id']);

        return redirect()->route('locales.index')
            ->with('success', '✓ Local creado y gerente asignado correctamente.');
    }

    /**
     * Editar nombre y gerente de un local (para admin global)
     */
    public function updateAdmin(Request $request, $localId)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'manager_id' => 'required|exists:tbuser,user_id',
        ]);

        $local = Local::findOrFail($localId);
        $local->update([
            'name' => $validated['name'],
        ]);

        // Se mantiene un único gerente asignado por local en este flujo.
        $local->users()->sync([$validated['manager_id']]);

        return redirect()->route('locales.index')
            ->with('success', '✓ Local actualizado correctamente.');
    }

    /**
     * Actualizar solo el estado de un local (para admin global)
     */
    public function updateStatus(Request $request, $localId)
    {
        $validated = $request->validate([
            'status' => 'required|in:Active,Inactive',
        ]);

        $local = Local::findOrFail($localId);
        $local->update(['status' => $validated['status']]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Estado del local actualizado correctamente.',
                'status' => $local->status,
            ]);
        }

        return redirect()->route('locales.index')
            ->with('success', 'Estado del local actualizado correctamente.');
    }

    /**
     * Eliminar un local (para admin global)
     */
    public function destroy($localId)
    {
        $local = Local::findOrFail($localId);

        // Eliminar imágenes físicas de la galería del local
        foreach ($local->gallery as $image) {
            $filePath = public_path($image->image_url);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Eliminar logo físico del local si existe
        if (!empty($local->image_logo)) {
            $logoPath = public_path($local->image_logo);
            if (file_exists($logoPath)) {
                @unlink($logoPath);
            }
        }

        // Limpiar relaciones y registros asociados
        $local->users()->detach();
        $local->gallery()->delete();
        $local->delete();

        return redirect()->route('locales.index')
            ->with('success', '✓ Local eliminado correctamente.');
    }

    /**
     * Mostrar horario del local ver solo horarios
     */
    public function schedule(Request $request)
    {
        $user = $request->user();
        $local = $user->locals()->first();

        if (!$local) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un local asignado.');
        }

        // Obtener los horarios del local ordenados por día de la semana
        $schedules = Schedule::byLocal($local->local_id)->get();

        // Obtener el estado actual del local
        $isOpen = Schedule::isCurrentlyOpen($local->local_id);
        $currentStatus = Schedule::getCurrentStatus($local->local_id);

        // Preparar breadcrumbs
        $crumbs = [
            ['label' => 'Horario', 'url' => null]
        ];

        return view('local.schedule', compact('local', 'schedules', 'isOpen', 'currentStatus', 'crumbs'));
    }
}