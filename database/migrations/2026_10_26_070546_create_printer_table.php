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
        Schema::create('printer', function (Blueprint $table) {
            $table->id();

            $table->string('asset_tag')->unique()->nullable();
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->unique()->nullable();
            $table->enum('printer_type', ['Normal', 'Labeling'])->default('Normal');

            $table->enum('ownership', ['Owned', 'Leased', 'Borrowed'])->default('Owned');
            $table->string('supplier')->nullable();

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
        Schema::dropIfExists('printer');
    }
};
