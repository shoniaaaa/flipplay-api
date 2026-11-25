<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    // GET /api/media?title=&type=&genre=&sort=score
    public function index(Request $request)
    {
        $media = Media::query()
            ->withCount('users')
            ->search($request->query('title'))
            ->ofType($request->query('type'))
            ->ofGenre($request->query('genre'))
            ->when(
                $request->query('sort') === 'score',
                fn ($q) => $q->orderByDesc('score'),
                fn ($q) => $q->orderBy('id')
            )
            ->paginate(10)
            ->withQueryString();

        return MediaResource::collection($media);
    }

    // GET /api/media/{media}  -> detail + pengguna yang memasukkannya ke koleksi
    public function show(Media $media)
    {
        return new MediaResource($media->load('users'));
    }

    // POST /api/media
    public function store(StoreMediaRequest $request)
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    // PATCH /api/media/{media}
    public function update(StoreMediaRequest $request, Media $media)
    {
        $media->update($request->validated());

        return new MediaResource($media);
    }

    // DELETE /api/media/{media}
    public function destroy(Media $media)
    {
        $media->delete(); // baris pivot ikut terhapus (cascadeOnDelete)

        return response()->json(['message' => 'Media berhasil dihapus']);
    }
}
