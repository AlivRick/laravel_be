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
        Schema::create('movie', function (Blueprint $table) {
            $table->string('movie_id', 24)->primary();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->string('director')->nullable();
            $table->text('cast')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration');
            $table->date('release_date');
            $table->date('end_date')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->string('age_restriction')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('poster_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie');
    }
};