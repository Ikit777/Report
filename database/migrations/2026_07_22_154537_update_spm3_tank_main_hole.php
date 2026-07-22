<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete old SPM3 tank with (D+B)/2 main_hole
        DB::table('tanks')
            ->where('code', 'SPM3')
            ->where('main_hole', '(D+B)/2')
            ->delete();
        
        // Create or update new SPM3 tank with (DEPAN + BELAKANG) / 2
        DB::table('tanks')->updateOrInsert(
            [
                'code' => 'SPM3',
                'main_hole' => '(DEPAN + BELAKANG) / 2'
            ],
            [
                'is_active' => true,
                'capacity' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old tank
        DB::table('tanks')->updateOrInsert(
            [
                'code' => 'SPM3',
                'main_hole' => '(D+B)/2'
            ],
            [
                'is_active' => true,
                'capacity' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Delete new tank
        DB::table('tanks')
            ->where('code', 'SPM3')
            ->where('main_hole', '(DEPAN + BELAKANG) / 2')
            ->delete();
    }
};
