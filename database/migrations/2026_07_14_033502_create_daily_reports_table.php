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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->foreignId('fuelman_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gl_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('spv_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('gl_feedback')->nullable();
            $table->text('spv_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
