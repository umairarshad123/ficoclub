<?php

namespace App\Console\Commands;

use App\Services\CommasService;
use App\Services\CommasTransactionSync;
use Illuminate\Console\Command;

/** Mirror all Commas transactions into commas_transactions (dashboard data). */
class CommasSyncCommand extends Command
{
    protected $signature   = 'commas:sync';
    protected $description = 'Sync all Commas transactions into the local commas_transactions table';

    public function handle(CommasService $commas, CommasTransactionSync $sync): int
    {
        if (! $commas->isConfigured()) {
            $this->line('COMMAS_API_KEY not set — nothing to sync.');
            return self::SUCCESS;
        }

        $stats = $sync->run();
        $this->info(sprintf('Commas sync: %d transactions · %d new · %d updated', $stats['seen'], $stats['created'], $stats['updated']));

        return self::SUCCESS;
    }
}
