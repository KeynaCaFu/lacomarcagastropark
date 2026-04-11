<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDOException;

class TestConnectionErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:connection-errors {type=all}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Simula errores de conexión para probar las vistas. Tipo: db, internet, all';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case 'db':
                $this->testDbError();
                break;
            case 'internet':
                $this->testInternetError();
                break;
            case 'all':
                $this->testDbError();
                $this->testInternetError();
                break;
            default:
                $this->error("Tipo no válido. Usa: db, internet, all");
        }
    }

    /**
     * Simular error de base de datos
     */
    protected function testDbError()
    {
        $this->info("Iniciando prueba de error de base de datos...");
        $this->warn("Para ver la vista, visita: http://localhost:8000/test-db-error");
    }

    /**
     * Simular error de internet
     */
    protected function testInternetError()
    {
        $this->info("Iniciando prueba de error de internet...");
        $this->warn("Para ver la vista, visita: http://localhost:8000/test-internet-error");
    }
}
