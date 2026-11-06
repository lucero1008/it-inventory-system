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
        Schema::create('desktop_laptop_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->unique()->constrained('assets')->cascadeOnDelete();

            $table->string('processor')->nullable(); // e.g., Core 2 Duo
            $table->string('ram')->nullable(); // e.g., '16GB'
            $table->string('storage')->nullable(); // e.g., '512GB SSD'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desktop_laptop_details');
    }
};
