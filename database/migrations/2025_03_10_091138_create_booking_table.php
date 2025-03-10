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
        Schema::create('booking', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('user_id')->constrained('user', 'user_id');
            $table->dateTime('booking_time');
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('payment_method_id')->constrained('paymentmethod', 'payment_method_id');
            $table->string('payment_status');
            $table->string('booking_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};