<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'plan' => $this->plan,
            'collection_limit' => $this->collection_limit,

            // Data pivot (muncul saat user diambil lewat media->users)
            'is_watched' => $this->whenPivotLoaded('media_user', fn () => (bool) $this->pivot->is_watched),
            'added_at' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->created_at),

            'media_count' => $this->whenCounted('media'),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
