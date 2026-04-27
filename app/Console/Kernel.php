<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Limpiar tokens expirados cada noche a las 3:00 AM
        // CA2 + CA5: Gestión de tokens y limpieza automática
        $schedule->command('clean:expired-tokens --days=7')
            ->dailyAt('03:00')
            ->timezone('America/Costa_Rica')
            ->onSuccess(function () {
                Log::info(' Limpieza de tokens completada exitosamente');
            })
            ->onFailure(function () {
                Log::error(' Error en la limpieza de tokens');
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
