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
            $table->char('seat_id', 24)->primary();
            $table->char('room_id', 24);
            $table->string('seat_row');
            $table->string('seat_number');
            $table->string('seat_type')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_merged')->default(false);
            $table->char('merged_with_seat_id', 24)->nullable();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('room_id')->references('room_id')->on('theaterroom')->onDelete('cascade');
            $table->foreign('merged_with_seat_id')->references('seat_id')->on('seat')->onDelete('set null');
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