<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user interactively';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================');
        $this->info('   CREATE NEW ADMIN USER');
        $this->info('=================================');
        $this->newLine();

        // Get input
        $name = $this->ask('Enter full name');
        $email = $this->ask('Enter email address');
        $employeeId = $this->ask('Enter employee ID (optional, press Enter to skip)');
        $password = $this->secret('Enter password (min 6 characters)');
        $passwordConfirm = $this->secret('Confirm password');

        // Validate
        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match!');
            return 1;
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'employee_id' => $employeeId,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }
            return 1;
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'employee_id' => $employeeId ?: null,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->newLine();
        $this->info('✓ Admin user created successfully!');
        $this->newLine();
        $this->table(
            ['ID', 'Name', 'Email', 'Employee ID', 'Role'],
            [[$user->id, $user->name, $user->email, $user->employee_id ?: '-', $user->role]]
        );

        return 0;
    }
}
