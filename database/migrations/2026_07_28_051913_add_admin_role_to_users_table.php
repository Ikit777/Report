<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration adds 'admin' role support to the system.
        // No schema changes needed since role is stored as string.
        // Admin role has same permissions as supervisor but can also:
        // - Create/edit users with 'supervisor' role
        // - Create/edit users with 'admin' role
        
        // Optional: Convert existing supervisors to admin
        // Uncomment if you want to upgrade existing supervisors:
        // DB::table('users')->where('role', 'supervisor')->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Revert admin back to supervisor
        // Uncomment if you want to downgrade:
        // DB::table('users')->where('role', 'admin')->update(['role' => 'supervisor']);
    }
};
