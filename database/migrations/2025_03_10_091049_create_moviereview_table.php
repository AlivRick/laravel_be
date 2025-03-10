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
        Schema::create('moviereview', function (Blueprint $table) {
            $table->id('review_id');
            $table->foreignId('movie_id')->constrained('movie', 'movie_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('user', 'user_id')->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moviereview');
    }
};