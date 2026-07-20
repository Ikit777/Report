<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add rows introduced after the first production deployment.
     *
     * Seeders do not automatically update an existing database, so this data
     * migration keeps the production report form in sync with a fresh install.
     */
    public function up(): void
    {
        $tanks = [
            ['code' => 'SPM3', 'main_hole' => '(D+B)/2'],
            ['code' => 'FT05', 'main_hole' => 'TENGAH'],
        ];

        foreach ($tanks as $tank) {
            $exists = DB::table('tanks')
                ->where('code', $tank['code'])
                ->where('main_hole', $tank['main_hole'])
                ->exists();

            if (! $exists) {
                DB::table('tanks')->insert([
                    ...$tank,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Do not remove operational tank data when rolling back.
     */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
