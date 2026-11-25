<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ===== CRUD Media =====
// parameters() dipakai agar nama parameter pasti {media} (kata "media" tidak beraturan untuk singular/plural)
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

// ===== Pengguna (read-only, untuk kebutuhan pengujian) =====
Route::apiResource('users', UserController::class)->only(['index', 'show']);

// ===== Custom action: koleksi media milik pengguna (ubah relasi pivot) =====
Route::get('/users/{user}/collection', [UserController::class, 'collection']);
Route::post('/users/{user}/collection', [UserController::class, 'addToCollection']);
Route::delete('/users/{user}/collection/{media}', [UserController::class, 'removeFromCollection']);
Route::patch('/users/{user}/collection/{media}/watched', [UserController::class, 'markAsWatched']);
