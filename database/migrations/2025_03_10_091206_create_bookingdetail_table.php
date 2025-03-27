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
        Schema::create('bookingdetail', function (Blueprint $table) {
            $table->string('booking_detail_id', 24)->primary();
            $table->string('booking_id', 24);
            $table->string('showtime_id', 24);
            $table->string('seat_id', 24);
            $table->string('ticket_type_id', 24);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('booking')->onDelete('cascade');
            $table->foreign('showtime_id')->references('showtime_id')->on('showtime');
            $table->foreign('seat_id')->references('seat_id')->on('seat');
            $table->foreign('ticket_type_id')->references('ticket_type_id')->on('tickettype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookingdetail');
    }
};