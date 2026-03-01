<?php

namespace App\Http\Controllers;

use App\Data\EventData;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'date' => $request->get('fecha')
        ];

        $events = $this->eventData->all(array_filter($filters));

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
            $path = $request->file('photo')->store('events', 'public');
            $imageUrl = 'storage/' . $path;
        }

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_at' => $startAt,
            'location' => $validated['location'] ?? null,
            'is_active' => $isActive,
            'image_url' => $imageUrl
        ];

        $this->eventData->create($data);

        return redirect()->route('eventos.index')->with('ok', 'saved');
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

            return redirect()->route('eventos.index')->with('ok', 'saved');
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
            if ($evento->image_url && str_starts_with($evento->image_url, 'storage/')) {
                $rel = str_replace('storage/', '', $evento->image_url);
                if (Storage::disk('public')->exists($rel)) {
                    Storage::disk('public')->delete($rel);
                }
            }
            
            // Guardar nueva imagen en carpeta events
            $path = $request->file('photo')->store('events', 'public');
            $data['image_url'] = 'storage/' . $path;
        }

        $this->eventData->update($evento->event_id, $data);

        return redirect()->route('eventos.index')->with('ok', 'saved');
    }

    // Mostrar un evento individual
    public function show(Event $evento)
    {
        return view('events.show', ['event' => $evento]);
    }

    public function destroy(Event $evento)
    {
        if ($evento->image_url && str_starts_with($evento->image_url, 'storage/')) {
            $rel = str_replace('storage/', '', $evento->image_url);
            if (Storage::disk('public')->exists($rel)) {
                Storage::disk('public')->delete($rel);
            }
        }

        $this->eventData->delete($evento->event_id);

        return redirect()->route('eventos.index')->with('ok', 'deleted');
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
