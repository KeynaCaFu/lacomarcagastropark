<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class DeactivateExpiredEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:deactivate-expired';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Desactivar automáticamente eventos que pasaron hace más de 1 día';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Eventos que sean de hace más de 1 día desde su fecha se consideran expirados
        // Por ejemplo, si hoy es 13 de abril, los eventos del 11 de abril ya están expirados
        $yesterday = now()->subDay()->toDateString();

        // Obtener eventos activos que tengan start_at antes de ayer
        $expiredEvents = Event::where('is_active', true)
            ->whereDate('start_at', '<', $yesterday)
            ->get();

        if ($expiredEvents->isEmpty()) {
            $this->info('✓ No hay eventos expirados para desactivar.');
            return 0;
        }

        $count = $expiredEvents->count();

        // Actualizar eventos
        Event::where('is_active', true)
            ->whereDate('start_at', '<', $yesterday)
            ->update(['is_active' => false]);

        $this->info("✓ Se desactivaron {$count} evento(s) que son más de 1 día antiguo.");

        return 0;
    }
}
