<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /** Katalog yang sama dengan index.php di FlipPlay Modul 3. */
    public function run(): void
    {
        $catalog = [
            ['Sousou no Frieren', 'tv', 9.30, 2023, ['Adventure', 'Drama', 'Fantasy'], 28, 24],
            ['Fullmetal Alchemist: Brotherhood', 'tv', 9.10, 2009, ['Action', 'Adventure', 'Drama'], 64, 24],
            ['Steins;Gate', 'tv', 9.07, 2011, ['Drama', 'Sci-Fi', 'Suspense'], 24, 24],
            ['Hunter x Hunter (2011)', 'tv', 9.00, 2011, ['Action', 'Adventure', 'Fantasy'], 148, 24],
            ['Violet Evergarden', 'tv', 8.60, 2018, ['Drama'], 13, 24],
            ['Your Name.', 'movie', 8.85, 2016, ['Drama', 'Romance', 'Supernatural'], 1, 106],
            ['A Silent Voice', 'movie', 8.93, 2016, ['Drama', 'Romance'], 1, 130],
            ['Bocchi the Rock!', 'tv', 8.76, 2022, ['Comedy', 'Slice of Life'], 12, 24],
            ['Spy x Family', 'tv', 8.60, 2022, ['Action', 'Comedy'], 12, 24],
            ['Death Note', 'tv', 8.62, 2006, ['Suspense', 'Supernatural'], 37, 24],
            ['Haikyuu!! Karasuno vs Shiratorizawa', 'tv', 8.70, 2016, ['Sports'], 10, 24],
            ['Silver Spoon', 'tv', 8.20, 2013, ['Comedy', 'Slice of Life'], 11, 24],
        ];

        foreach ($catalog as [$title, $type, $score, $year, $genres, $episodes, $minutes]) {
            Media::firstOrCreate(
                ['title' => $title],
                [
                    'type' => $type,
                    'score' => $score,
                    'year' => $year,
                    'genres' => $genres,
                    'episodes' => $episodes,
                    'minutes_per_episode' => $minutes,
                ]
            );
        }
    }
}
