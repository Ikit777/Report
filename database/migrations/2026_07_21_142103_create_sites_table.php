<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default sites
        DB::table('sites')->insert([
            ['code' => 'SPT1', 'name' => 'Sungai Puting', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'LBR1', 'name' => 'Lok Buntar', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'IDMG', 'name' => 'Ida Manggala', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
