<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * CleanExpiredTokens
 * 
 * Comando para limpiar órdenes con tokens expirados.
 * Se ejecuta automáticamente cada noche a las 3:00 AM
 * 
 * CA2 + CA5: Gestión de tokens y timestamps
 * 
 * Ejecutar manualmente: php artisan clean:expired-tokens
 */
class CleanExpiredTokens extends Command
{
    /**
     * Nombre y descripción del comando
     */
    protected $signature = 'clean:expired-tokens {--days=7 : Días de antigüedad para considerar expirado}';
    protected $description = 'Limpiar órdenes con tokens expirados que no fueron confirmados';

    /**
     * Ejecutar el comando
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("🗑️  Limpiando tokens expirados (más de {$days} días)...");

        try {
            // Calcular fecha límite
            $expiryDate = Carbon::now()->subDays($days);

            // Encontrar órdenes a eliminar
            $ordersToDelete = Order::where('status', 'Pending')
                ->whereNotNull('verification_token')
                ->where('confirmed_at', '<', $expiryDate)
                ->get();

            $count = $ordersToDelete->count();

            if ($count === 0) {
                $this->info("✅ No hay tokens expirados para limpiar.");
                Log::info("Clean Expired Tokens: No orders found to delete.");
                return 0;
            }

            // Registrar órdenes antes de eliminar
            $deletedTokens = $ordersToDelete->pluck('verification_token')->toArray();
            $deletedIds = $ordersToDelete->pluck('order_id')->toArray();

            // Eliminar órdenes y sus items asociados
            foreach ($ordersToDelete as $order) {
                $order->items()->delete(); // Eliminar items primero
                $order->delete();
            }

            // Logging
            $message = "✅ Se eliminaron {$count} órdenes con tokens expirados.";
            $this->info($message);
            
            Log::info('Clean Expired Tokens Success', [
                'deleted_count' => $count,
                'deleted_order_ids' => $deletedIds,
                'deleted_tokens' => $deletedTokens,
                'expiry_date' => $expiryDate->toDateTimeString(),
                'executed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error durante la limpieza: {$e->getMessage()}");
            
            Log::error('Clean Expired Tokens Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }
}
