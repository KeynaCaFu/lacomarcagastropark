<?php

namespace App\Console\Commands;

use App\Helpers\CartHelper;
use Illuminate\Console\Command;

class DebugCartNormalization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:debug-normalize {customization? : La customización a normalizar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug: Ver cómo se normaliza una customización en el carrito';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = $this->argument('customization');

        if (! $input) {
            $this->info('=== CART NORMALIZATION DEBUG ===');
            $this->newLine();
            $this->info('Ejemplos de normalización:');
            $this->newLine();

            $examples = [
                'Sin Alcohol',
                'sin alcohol',
                'SIN ALCOHOL',
                'Sin  Alcohol',
                '  Sin Alcohol  ',
                'Sín Alcohol',
                'Sin Alcohol, Extra Queso',
                'Sin Alcohol-Extra queso',
                'EXTRA Queso Con Orégano',
            ];

            foreach ($examples as $example) {
                $normalized = CartHelper::normalizeCustomization($example);
                $this->line("Input:      " . str_pad("'{$example}'", 35));
                $this->line("Normalized: '{$normalized}'");
                $this->newLine();
            }

            $this->info('✓ Para probar una customización específica:');
            $this->line('   php artisan cart:debug-normalize "Sin Alcohol"');
            $this->newLine();

            return;
        }

        $this->info('=== NORMALIZACIÓN DE CUSTOMIZACIÓN ===');
        $this->newLine();

        $normalized = CartHelper::normalizeCustomization($input);

        $this->line("Original:   <info>{$input}</info>");
        $this->line("Normalized: <comment>{$normalized}</comment>");
        $this->newLine();

        // Mostrar item_key para diferentes product_ids
        $this->info('Item Keys (para diferentes productos):');
        $this->newLine();

        foreach ([1, 5, 10] as $productId) {
            $itemKey = CartHelper::generateItemKey($productId, $input);
            $this->line("  Product #$productId: <fg=cyan>$itemKey</>");
        }

        $this->newLine();

        // Test de equivalencia
        $this->info('Pruebas de equivalencia:');
        $this->newLine();

        $testCases = [
            strtolower($input),
            strtoupper($input),
            '  ' . $input . '  ',
        ];

        foreach ($testCases as $testCase) {
            $isEquivalent = CartHelper::areCustomizationsEquivalent($input, $testCase);
            $status = $isEquivalent ? '<fg=green>✓ EQUAL</>' : '<fg=red>✗ DIFFERENT</>';
            $this->line("  '{$testCase}' → {$status}");
        }

        $this->newLine();
        $this->info('✓ Normalización completada');
    }
}
