<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // GET /api/users
    public function index()
    {
        return UserResource::collection(User::withCount('media')->paginate(10));
    }

    // GET /api/users/{user}
    public function show(User $user)
    {
        return new UserResource($user->loadCount('media'));
    }

    // GET /api/users/{user}/collection
    public function collection(User $user)
    {
        $media = $user->media()->get();

        $remaining = $media
            ->filter(fn ($m) => ! $m->pivot->is_watched)
            ->sum(fn ($m) => $m->watch_minutes);

        return MediaResource::collection($media)->additional([
            'meta' => [
                'user' => $user->name,
                'plan' => $user->plan,
                'collection_limit' => $user->collection_limit,
                'total' => $media->count(),
                'remaining_minutes' => $remaining,
            ],
        ]);
    }

    // POST /api/users/{user}/collection   body: { "media_id": 1 }
    public function addToCollection(AddToCollectionRequest $request, User $user)
    {
        $mediaId = $request->validated('media_id');

        // Pola "baca kondisi -> validasi -> ubah" (cek duplikat + cek batas paket)
        // dibungkus transaksi + lockForUpdate supaya dua request bersamaan
        // tidak sama-sama lolos (race condition, lihat Modul 4 bagian 4.4).
        return DB::transaction(function () use ($mediaId, $user) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($locked->media()->wherePivot('media_id', $mediaId)->exists()) {
                return response()->json(['message' => 'Media sudah ada di koleksi'], 409);
            }

            if ($locked->media()->count() >= $locked->collection_limit) {
                return response()->json([
                    'message' => "Koleksi akun {$locked->plan} maksimal {$locked->collection_limit} media",
                ], 403);
            }

            $locked->media()->attach($mediaId);

            return response()->json([
                'message' => 'Media berhasil ditambahkan ke koleksi',
                'data' => new MediaResource(Media::find($mediaId)),
            ], 201);
        });
    }

    // DELETE /api/users/{user}/collection/{media}
    public function removeFromCollection(User $user, Media $media)
    {
        if (! $user->media()->wherePivot('media_id', $media->id)->exists()) {
            return response()->json(['message' => 'Media tidak ada di koleksi'], 404);
        }

        $user->media()->detach($media->id);

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi']);
    }

    // PATCH /api/users/{user}/collection/{media}/watched   body (opsional): { "is_watched": true }
    public function markAsWatched(Request $request, User $user, Media $media)
    {
        if (! $user->media()->wherePivot('media_id', $media->id)->exists()) {
            return response()->json(['message' => 'Media tidak ada di koleksi'], 404);
        }

        $user->media()->updateExistingPivot($media->id, [
            'is_watched' => $request->boolean('is_watched', true),
        ]);

        return response()->json(['message' => 'Status tontonan berhasil diperbarui']);
    }
}
