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
            $table->foreignId('user_id')->constrained('user', 'user_id')->onDelete('cascade');
            $table->foreignId('promotion_id')->constrained('promotion', 'promotion_id')->onDelete('cascade');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->primary(['user_id', 'promotion_id']);
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