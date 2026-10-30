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
        Schema::create('desktop_laptop', function (Blueprint $table) {
            $table->id();

            $table->string('asset_tag')->unique();
            $table->string('device_type'); // Desktop or Laptop
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->unique()->nullable();

            // Specifications
            $table->string('processor')->nullable();
            $table->string('ram')->nullable(); // e.g., '16GB'
            $table->string('storage')->nullable(); // e.g., '512GB SSD'

            // Tracking & Status
            $table->enum('status', ['Available', 'In Use', 'For Repair', 'For Disposal', 'Disposed', 'For Testing', 'Undefined'])->default('Available');
            $table->string('current_user')->nullable();
            $table->string('department')->nullable();
            $table->longText('remarks')->nullable();
            $table->date('date_of_asset_transfer')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desktop_laptop');
    }
};
