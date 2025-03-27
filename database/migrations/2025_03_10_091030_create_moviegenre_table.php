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
        Schema::create('moviegenre', function (Blueprint $table) {
            $table->string('movie_id', 24);
            $table->string('genre_id', 24);
            $table->primary(['movie_id', 'genre_id']);
            $table->foreign('movie_id')->references('movie_id')->on('movie')->onDelete('cascade');
            $table->foreign('genre_id')->references('genre_id')->on('genre')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moviegenre');
    }
};