<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ContractController;

class UpdateContractStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update contract statuses to pending renewal if end date has passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new ContractController();
        $controller->checkAndUpdateContractStatuses();
        $this->info('Contract statuses updated successfully.');
        return 0;
    }
}
