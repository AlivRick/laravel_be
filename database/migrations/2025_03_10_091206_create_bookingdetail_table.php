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
            $table->id('booking_detail_id');
            $table->foreignId('booking_id')->constrained('booking', 'booking_id')->onDelete('cascade');
            $table->foreignId('showtime_id')->constrained('showtime', 'showtime_id');
            $table->foreignId('seat_id')->constrained('seat', 'seat_id');
            $table->foreignId('ticket_type_id')->constrained('tickettype', 'ticket_type_id');
            $table->decimal('price', 10, 2);
            $table->timestamps();
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