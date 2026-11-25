<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot koleksi media milik pengguna (nama: gabungan singular, alfabetis)
        Schema::create('media_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_watched')->default(false);
            $table->timestamps();

            // Jaring pengaman di level database: pasangan yang sama tidak bisa masuk dua kali
            $table->unique(['media_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_user');
    }
};
