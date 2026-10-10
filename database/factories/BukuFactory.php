<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    protected $model = Buku::class;

    public function definition(): array
    {
        return [
            'isbn' => fake()->unique()->isbn13(),
            'judul' => fake()->sentence(fake()->numberBetween(3, 7)),
            'penulis' => fake()->name(),
            'penerbit' => fake()->company(),
            'tahun_terbit' => fake()->numberBetween(2005, 2026),
            'kategori_id' => Kategori::factory(),
            'stok' => fake()->numberBetween(1, 50),
            'sinopsis' => fake()->paragraph(3),
        ];
    }
}
