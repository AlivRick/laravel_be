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
        Schema::create('seatchangehistory', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('seat_id')->constrained('seat', 'seat_id');
            $table->foreignId('room_id')->constrained('theaterroom', 'room_id');
            $table->foreignId('changed_by')->constrained('user', 'user_id');
            $table->string('previous_state');
            $table->string('current_state');
            $table->text('change_reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seatchangehistory');
    }
};