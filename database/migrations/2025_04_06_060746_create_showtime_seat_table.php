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
        Schema::create('showtime_seat', function (Blueprint $table) {
            $table->string('showtime_seat_id', 24)->primary();
            $table->string('showtime_id', 24);
            $table->string('seat_id', 24);
            $table->boolean('is_booked')->default(false);
            $table->timestamps();

            $table->foreign('showtime_id')->references('showtime_id')->on('showtime')->onDelete('cascade');
            $table->foreign('seat_id')->references('seat_id')->on('seat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtime_seat');
    }
};