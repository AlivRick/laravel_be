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
        Schema::create('seat', function (Blueprint $table) {
            $table->id('seat_id');
            $table->foreignId('room_id')->constrained('theaterroom', 'room_id');
            $table->string('seat_row');
            $table->string('seat_number');
            $table->string('seat_type')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_merged')->default(false);
            $table->foreignId('merged_with_seat_id')->nullable()->constrained('seat', 'seat_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat');
    }
};