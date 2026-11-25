<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'plan',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ===== Relationship: koleksi media milik pengguna =====
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_user')
            ->withPivot('is_watched')
            ->withTimestamps();
    }

    // Setara getCollectionLimit() di Modul 3: Free = 5, Premium = 100
    protected function collectionLimit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->plan === 'Premium' ? 100 : 5,
        );
    }
}
