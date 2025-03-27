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
            $table->char('room_id', 24)->primary(); // ID kiểu MongoDB
            $table->char('cinema_complex_id', 24); // Khóa ngoại kiểu char(24)
            $table->char('template_id', 24); // Khóa ngoại kiểu char(24)
            $table->string('room_name');
            $table->string('room_type');
            $table->integer('capacity');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
             // Thiết lập khóa ngoại
            $table->foreign('cinema_complex_id')->references('cinema_complex_id')->on('cinemacomplex')->onDelete('cascade');
            $table->foreign('template_id')->references('template_id')->on('seattemplate')->onDelete('cascade');
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