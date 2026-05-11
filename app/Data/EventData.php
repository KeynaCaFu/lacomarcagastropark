<?php

namespace App\Data;

use App\Models\Event;
use Carbon\Carbon;

class EventData
{
    protected $table = 'tbevents';

   
    public function all(array $filters = [])
    {
        $query = Event::query();

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['date'])) {
            $query->whereDate('start_at', $filters['date']);
        }

        if (!empty($filters['status'])) {
            $isActive = $filters['status'] === 'activo';
            $query->where('is_active', $isActive);
        }

        // Orden por fecha de inicio (más recientes primero)
        return $query->orderBy('start_at', 'desc')
            ->paginate(6)
            ->withQueryString();
    }

    public function find($id)
    {
        return Event::find($id);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update($id, array $data)
    {
        $ev = Event::findOrFail($id);
        $ev->update($data);
        return $ev;
    }

    public function delete($id)
    {
        $ev = Event::findOrFail($id);
        return $ev->delete();
    }
}
