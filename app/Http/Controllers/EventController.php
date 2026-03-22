<?php

namespace App\Http\Controllers;

use App\Data\EventData;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EventController extends Controller
{
    protected $eventData;

    public function __construct(EventData $eventData)
    {
        $this->eventData = $eventData;
    }

    // Lista de eventos (filtros compatibles con Data/EventData.php)
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('q'),
            'date' => $request->get('fecha'),
            'status' => $request->get('estado')
        ];

        $events = $this->eventData->all(array_filter($filters));

        // Si es una petición AJAX, devolver solo el HTML de las tarjetas
        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->view('events.partials.cards', compact('events'), 200, ['Content-Type' => 'text/html']);
        }

        return view('events.index', compact('events'));
    }

    // Guardar nuevo evento 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required|date_format:H:i',
            'status'      => 'required|in:activo,inactivo',
            'description' => 'required|string',
            'location'    => 'nullable|string|max:255',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $startAt = Carbon::parse($validated['date'].' '.$validated['time']);
        $isActive = $validated['status'] === 'activo';

        $imageUrl = null;
        if ($request->hasFile('photo')) {
            // Crear directorio si no existe
            $eventDir = public_path('images/events');
            if (!File::isDirectory($eventDir)) {
                File::makeDirectory($eventDir, 0755, true, true);
            }

            // Generar nombre único para la imagen
            $filename = 'event_' . time() . '_' . rand(1000, 9999) . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move($eventDir, $filename);
            $imageUrl = 'images/events/' . $filename;
        }

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_at' => $startAt,
            'location' => $validated['location'] ?? null,
            'is_active' => $isActive,
            'image_url' => $imageUrl
        ];

        try {
            $this->eventData->create($data);
        } catch (\Exception $e) {
            return redirect()->route('eventos.index')->with('error', 'Error al crear el evento: ' . $e->getMessage());
        }

        return redirect()->route('eventos.index')->with('success', 'Evento guardado exitosamente');
    }

    // Editar (form)
    public function edit(Event $evento)
    {
        return view('events.edit', ['event' => $evento]);
    }

    // Actualizar evento
    public function update(Request $request, Event $evento)
    {
        // Si solo se está actualizando el estado (desde el toggler)
        if ($request->has('is_active') && !$request->has('title')) {
            $validated = $request->validate([
                'is_active' => 'required|boolean',
            ]);
            
            $this->eventData->update($evento->event_id, [
                'is_active' => $validated['is_active']
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Estado actualizado exitosamente', 'is_active' => $validated['is_active']]);
            }

            return redirect()->route('eventos.index')->with('success', 'Estado actualizado exitosamente');
        }

        // Actualización completa del evento (desde el formulario de edición)
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required|date_format:H:i',
            'status'      => 'required|in:activo,inactivo',
            'description' => 'required|string',
            'location'    => 'nullable|string|max:255',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $startAt = Carbon::parse($validated['date'].' '.$validated['time']);
        $isActive = $validated['status'] === 'activo';

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_at' => $startAt,
            'location' => $validated['location'] ?? null,
            'is_active' => $isActive,
        ];

        if ($request->hasFile('photo')) {
            // Eliminar imagen antigua si existe
            if ($evento->image_url) {
                $oldPath = public_path(str_replace('public/', '', $evento->image_url));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            
            // Crear directorio si no existe
            $eventDir = public_path('images/events');
            if (!File::isDirectory($eventDir)) {
                File::makeDirectory($eventDir, 0755, true, true);
            }

            // Generar nombre único para la imagen
            $filename = 'event_' . $evento->event_id . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move($eventDir, $filename);
            $data['image_url'] = 'images/events/' . $filename;
        }

        try {
            $this->eventData->update($evento->event_id, $data);
        } catch (\Exception $e) {
            return redirect()->route('eventos.index')->with('error', 'Error al actualizar el evento: ' . $e->getMessage());
        }

        return redirect()->route('eventos.index')->with('success', 'Evento actualizado exitosamente');
    }

    // Mostrar un evento individual
    public function show(Event $evento)
    {
        return view('events.show', ['event' => $evento]);
    }

    public function destroy(Event $evento)
    {
        if ($evento->image_url) {
            $oldPath = public_path(str_replace('public/', '', $evento->image_url));
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $this->eventData->delete($evento->event_id);

        return redirect()->route('eventos.index')->with('success', 'Evento eliminado exitosamente');
    }

    // Cargar partial con detalles (AJAX)
    public function showModal($event_id)
    {
        $event = Event::findOrFail($event_id);
        return response()->view('events.partials.show-modal', ['event' => $event], 200, ['Content-Type' => 'text/html']);
    }

    // Cargar partial con formulario de edición (AJAX)
    public function editModal($event_id)
    {
        $event = Event::findOrFail($event_id);
        return response()->view('events.partials.edit-modal', ['event' => $event], 200, ['Content-Type' => 'text/html']);
    }
}
