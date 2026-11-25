<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    // PENTING: default hasil make:request adalah false -> semua request ditolak (403)
    public function authorize(): bool
    {
        return true;
    }

    // Movie selalu 1 episode
    protected function prepareForValidation(): void
    {
        if ($this->input('type') === 'movie') {
            $this->merge(['episodes' => 1]);
        }
    }

    public function rules(): array
    {
        // POST = semua wajib; PATCH/PUT = hanya field yang dikirim yang divalidasi
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';
        $isCreate = $this->isMethod('POST');

        return [
            'title' => [
                $required, 'string', 'max:255',
                // saat update, abaikan baris milik media itu sendiri
                Rule::unique('media', 'title')->ignore($this->route('media')),
            ],
            'type' => [$required, Rule::in(['tv', 'movie'])],
            'score' => [$required, 'numeric', 'between:0,10'],
            'year' => [$required, 'integer', 'between:1900,' . (date('Y') + 1)],
            'genres' => [$required, 'array', 'min:1'],
            'genres.*' => ['string', 'max:50'],
            'episodes' => ['sometimes', 'integer', 'min:1'],
            'minutes_per_episode' => [
                Rule::requiredIf(fn () => $isCreate && $this->input('type') === 'movie'),
                'integer', 'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi',
            'title.unique' => 'Judul media sudah ada',
            'type.required' => 'Tipe media wajib diisi',
            'type.in' => 'Tipe media harus tv atau movie',
            'score.required' => 'Skor wajib diisi',
            'score.between' => 'Skor harus di antara 0 sampai 10',
            'year.required' => 'Tahun rilis wajib diisi',
            'genres.required' => 'Genre wajib diisi minimal satu',
            'genres.array' => 'Genre harus berupa array',
            'minutes_per_episode.required' => 'Durasi (menit) wajib diisi untuk movie',
        ];
    }
}
