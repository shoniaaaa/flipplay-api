<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'title', 'type', 'score', 'year', 'genres', 'episodes', 'minutes_per_episode',
    ];

    // Nilai default agar model baru (sebelum di-refresh) tetap punya episodes & durasi
    protected $attributes = [
        'episodes' => 1,
        'minutes_per_episode' => 24,
    ];

    protected $casts = [
        'score' => 'float',
        'year' => 'integer',
        'genres' => 'array',
        'episodes' => 'integer',
        'minutes_per_episode' => 'integer',
    ];

    // ===== Relationship: Media <-> User (many-to-many lewat pivot media_user) =====
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
            ->withPivot('is_watched')
            ->withTimestamps();
    }

    // ===== Accessor (setara getWatchMinutes() & getType() di Modul 3) =====
    protected function watchMinutes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->episodes * $this->minutes_per_episode,
        );
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'movie' ? 'Movie' : 'TV Series',
        );
    }

    // ===== Query scope untuk filter di endpoint index =====
    public function scopeSearch(Builder $query, ?string $title): Builder
    {
        return $query->when($title, fn (Builder $q) => $q->where('title', 'like', "%{$title}%"));
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $q) => $q->where('type', $type));
    }

    public function scopeOfGenre(Builder $query, ?string $genre): Builder
    {
        return $query->when($genre, fn (Builder $q) => $q->whereJsonContains('genres', $genre));
    }
}
