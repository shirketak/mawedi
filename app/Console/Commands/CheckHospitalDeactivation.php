<?php

namespace App\Console\Commands;

use App\Services\HospitalDeactivationService;
use Illuminate\Console\Command;

class CheckHospitalDeactivation extends Command
{
    protected $signature = 'hospitals:check-deactivation';

    protected $description = 'Deactivate hospitals with expired subscriptions, trials, or empty wallets';

    public function handle(HospitalDeactivationService $service): int
    {
        $count = $service->processAll();

        $this->info("Deactivated {$count} hospital(s).");

        return self::SUCCESS;
    }
}
