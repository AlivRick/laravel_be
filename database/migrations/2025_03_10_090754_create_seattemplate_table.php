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
        Schema::create('seattemplate', function (Blueprint $table) {
            $table->char('template_id', 24)->primary(); // ID kiểu MongoDB
            $table->string('template_name');
            $table->text('description')->nullable();
            $table->integer('total_rows');
            $table->integer('seats_per_row');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seattemplate');
    }
};