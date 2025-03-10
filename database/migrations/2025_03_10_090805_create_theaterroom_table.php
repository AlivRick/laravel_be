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
        Schema::create('theaterroom', function (Blueprint $table) {
            $table->id('room_id');
            $table->foreignId('cinema_complex_id')->constrained('cinemacomplex', 'cinema_complex_id');
            $table->foreignId('template_id')->constrained('seattemplate', 'template_id');
            $table->string('room_name');
            $table->string('room_type');
            $table->integer('capacity');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theaterroom');
    }
};