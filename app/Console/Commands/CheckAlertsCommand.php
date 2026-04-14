<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class CheckAlertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simp:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa lotes proximos a vencer y productos con stock bajo para generar alertas automaticas.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando verificacion de alertas...');

        $alertService = new AlertService();

        try {
            $this->info('Verificando alertas de expiracion...');
            $alertService->checkExpiryAlerts();

            $this->info('Verificando alertas de stock bajo...');
            $alertService->checkLowStockAlerts();

            $this->info('Verificacion completada exitosamente.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error durante la verificacion: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
