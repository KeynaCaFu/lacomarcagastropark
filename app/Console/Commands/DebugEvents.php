<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DebugEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:debug';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Verificar la hora actual y el estado de los eventos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $yesterday = $now->copy()->subDay()->toDateString();
        $today = $now->toDateString();

        $this->info('=== INFORMACIÓN DE FECHAS ===');
        $this->line("Hoy: " . $today);
        $this->line("Ayer: " . $yesterday);
        $this->line("Hora actual: " . $now->format('Y-m-d H:i:s'));
        $this->line('');

        $this->info('=== EVENTOS ACTIVOS ===');
        $activeEvents = Event::where('is_active', true)
            ->orderBy('start_at', 'desc')
            ->get();

        foreach ($activeEvents as $event) {
            $startAt = Carbon::parse($event->start_at);
            $eventDate = $startAt->toDateString();
            $diffDays = $startAt->diffInDays($now);
            $diffHours = $startAt->diffInHours($now);
            $isPast = $startAt < $now;
            $status = $isPast ? "PASADO" : "FUTURO";
            
            // Verificar si está dentro del rango visible (hoy o ayer)
            $isVisible = $eventDate >= $yesterday;
            $visibilityStatus = $isVisible ? "✓ VISIBLE (1 día)" : "✗ EXPIRADO";

            $this->line(
                "ID: {$event->event_id} | {$event->title} | {$startAt->format('Y-m-d H:i:s')} | " .
                "{$status} ({$diffDays}d, {$diffHours}h) | {$visibilityStatus}"
            );
        }

        $this->line('');
        $this->info('=== EVENTOS QUE APARECERÍAN EN LA VISTA ===');
        $visibleEvents = Event::active()
            ->notExpired()
            ->orderBy('start_at', 'asc')
            ->get();

        if ($visibleEvents->isEmpty()) {
            $this->warn('No hay eventos visibles en la vista pública.');
        } else {
            foreach ($visibleEvents as $event) {
                $startAt = Carbon::parse($event->start_at);
                $this->line("✓ {$event->title} - {$startAt->format('Y-m-d H:i:s')}");
            }
        }

        return 0;
    }
}
