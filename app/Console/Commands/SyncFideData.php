<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FideService;

class SyncFideData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-fide {--federations : Sync only federations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and sync FIDE player and federation data';

    /**
     * Execute the console command.
     */
    public function handle(FideService $fideService)
    {
        if ($this->option('federations')) {
            $this->info('Syncing federations...');
            $fideService->syncFederations();
            $this->info('Federations synced!');
            return;
        }

        $this->info('Starting full FIDE sync (this may take a few minutes)...');
        
        $this->info('1/3: Syncing federation metadata...');
        $fideService->syncFederations();
        
        $this->info('2/3: Downloading and processing players list...');
        $fideService->downloadAndSyncPlayers();
        
        $this->info('3/3: Cleanup completed!');
    }
}
