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
            $table->id('payment_id');
            $table->foreignId('booking_id')->constrained('booking', 'booking_id');
            $table->foreignId('payment_method_id')->constrained('paymentmethod', 'payment_method_id');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id')->nullable();
            $table->dateTime('payment_time');
            $table->string('payment_status');
            $table->timestamps();
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