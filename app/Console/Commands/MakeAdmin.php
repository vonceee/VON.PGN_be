<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {identifier=vonchess : The username or email of the user} {--revoke : Revoke admin status instead of granting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant or revoke administrator privileges for a user by name or email';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('identifier');
        $revoke = $this->option('revoke');

        $user = User::where('name', $identifier)
            ->orWhere('email', $identifier)
            ->orWhere('email', 'like', "%{$identifier}%")
            ->first();

        if (!$user) {
            $this->error("User '{$identifier}' not found in the database.");
            return Command::FAILURE;
        }

        $user->is_admin = !$revoke;
        $user->save();

        $action = $revoke ? 'revoked from' : 'granted to';
        $this->info("Success: Administrator privileges {$action} user '{$user->name}' ({$user->email}, ID: {$user->id}).");

        return Command::SUCCESS;
    }
}
