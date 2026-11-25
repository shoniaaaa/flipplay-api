<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'score' => $this->score,
            'year' => $this->year,
            'genres' => $this->genres,
            'episodes' => $this->episodes,
            'watch_minutes' => $this->watch_minutes,

            // Hanya muncul saat media diambil lewat koleksi pengguna (data pivot)
            'is_watched' => $this->whenPivotLoaded('media_user', fn () => (bool) $this->pivot->is_watched),
            'added_at' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->created_at),

            // Hanya muncul kalau di-load: withCount('users') / load('users')
            'users_count' => $this->whenCounted('users'),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
