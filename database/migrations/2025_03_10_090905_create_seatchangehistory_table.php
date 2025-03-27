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
            $table->char('history_id', 24)->primary();
            $table->char('seat_id', 24);
            $table->char('room_id', 24);
            $table->char('changed_by', 24);
            $table->string('previous_state');
            $table->string('current_state');
            $table->text('change_reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            
            $table->foreign('seat_id')->references('seat_id')->on('seat')->onDelete('cascade');
            $table->foreign('room_id')->references('room_id')->on('theaterroom')->onDelete('cascade');
            $table->foreign('changed_by')->references('user_id')->on('user')->onDelete('cascade');
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