<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PingMicroservice extends Command
{
    protected $signature = 'microservice:ping';
    protected $description = 'Ping the chess microservice to keep it awake';

    public function handle(): int
    {
        $url = config('services.chess_microservice_url', 'https://von-pgn-microservice.onrender.com');
        
        $this->info("Pinging microservice: {$url}");
        
        try {
            // Ping root - any request wakes up the container
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $this->info('Microservice is awake');
                return Command::SUCCESS;
            }
            
            $this->warn('Microservice returned: ' . $response->status());
            return Command::FAILURE;
            
        } catch (\Exception $e) {
            $this->error('Failed to ping microservice: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}