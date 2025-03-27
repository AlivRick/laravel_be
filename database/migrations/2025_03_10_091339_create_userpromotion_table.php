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
        Schema::create('userpromotion', function (Blueprint $table) {
            $table->string('user_id', 24);
            $table->string('promotion_id', 24);
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->primary(['user_id', 'promotion_id']);
            $table->foreign('user_id')->references('user_id')->on('user')->onDelete('cascade');
            $table->foreign('promotion_id')->references('promotion_id')->on('promotion')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userpromotion');
    }
};