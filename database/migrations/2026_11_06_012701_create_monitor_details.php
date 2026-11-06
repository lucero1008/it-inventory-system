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
        Schema::create('monitor_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->unique()->constrained('assets')->cascadeOnDelete();

            $table->string('resolution')->nullable();
            $table->string('refresh_rate')->nullable(); // e.g., 60hz
            $table->string('screen_size')->nullable(); // eg., 24 inches

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_details');
    }
};
