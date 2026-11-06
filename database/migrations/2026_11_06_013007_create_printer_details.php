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
        Schema::create('printer_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->unique()->constrained('assets')->cascadeOnDelete();

            $table->enum('printer_type', ['Normal', 'Labeling'])->default('Normal');

            $table->enum('ownership', ['Owned', 'Leased', 'Borrowed'])->default('Owned');
            $table->string('supplier')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printer_details');
    }
};
