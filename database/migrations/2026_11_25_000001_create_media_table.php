<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();                  // judul tidak boleh duplikat
            $table->enum('type', ['tv', 'movie'])->default('tv'); // Anime (seri TV) / Movie
            $table->decimal('score', 4, 2)->default(0);
            $table->unsignedSmallInteger('year');
            $table->json('genres');                              // di-cast ke array di Model
            $table->unsignedSmallInteger('episodes')->default(1);
            $table->unsignedSmallInteger('minutes_per_episode')->default(24); // untuk movie = durasi film
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
