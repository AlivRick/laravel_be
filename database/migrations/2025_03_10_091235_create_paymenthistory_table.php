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
        Schema::create('paymenthistory', function (Blueprint $table) {
            $table->string('payment_id', 24)->primary();
            $table->string('booking_id', 24);
            $table->string('payment_method_id', 24);
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id')->nullable();
            $table->dateTime('payment_time');
            $table->string('payment_status');
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('booking')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('payment_method_id')->on('paymentmethod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymenthistory');
    }
};