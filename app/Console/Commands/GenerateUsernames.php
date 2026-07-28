<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateUsernames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate-usernames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate usernames for users who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usersWithoutUsername = User::whereNull('username')->get();
        
        if ($usersWithoutUsername->isEmpty()) {
            $this->info('All users already have usernames!');
            return 0;
        }

        $this->info('Found ' . $usersWithoutUsername->count() . ' users without username.');
        $this->newLine();

        $updated = [];

        foreach ($usersWithoutUsername as $user) {
            // Generate username from email (part before @)
            $baseUsername = explode('@', $user->email)[0];
            
            // Replace non-alphanumeric chars with underscore
            $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '_', $baseUsername);
            
            // Ensure uniqueness
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            
            // Save
            $user->username = strtolower($username);
            $user->save();
            
            $updated[] = [
                $user->id,
                $user->name,
                $user->username,
                $user->email,
            ];
            
            $this->line("✓ Generated username '{$user->username}' for {$user->name}");
        }

        $this->newLine();
        $this->info('Successfully generated ' . count($updated) . ' usernames!');
        $this->newLine();
        
        $this->table(
            ['ID', 'Name', 'Username', 'Email'],
            $updated
        );

        return 0;
    }
}
