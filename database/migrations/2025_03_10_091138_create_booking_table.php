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
            $table->string('booking_id', 24)->primary();
            $table->string('user_id', 24);
            $table->dateTime('booking_time');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method_id', 24);
            $table->string('payment_status');
            $table->string('booking_status');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('payment_method_id')->on('paymentmethod')->onDelete('cascade');
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