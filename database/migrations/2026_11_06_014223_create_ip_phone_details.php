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
        Schema::create('ip_phone_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->unique()->constrained('assets')->cascadeOnDelete();

            $table->string('extension')->nullable();
            $table->string('mac_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_phone_details');
    }
};
