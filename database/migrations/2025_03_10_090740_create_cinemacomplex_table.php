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
        Schema::create('cinemacomplex', function (Blueprint $table) {
            $table->char('cinema_complex_id', 24)->primary();
            $table->string('complex_name');
            $table->text('address');
            $table->string('city');
            $table->string('province');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->time('opening_time');
            $table->time('closing_time');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cinemacomplex');
    }
};