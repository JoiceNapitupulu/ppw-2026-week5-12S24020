<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriFactory extends Factory
{
    protected $model = Kategori::class;

    public function definition(): array
    {
        return [
            'kode_kategori' => strtoupper(fake()->unique()->bothify('KAT-###')),
            'nama_kategori' => fake()->unique()->randomElement([
                'Teknologi Informasi & Pemrograman',
                'Sains & Matematika',
                'Kesusastraan & Novel',
                'Filsafat & Agama',
                'Bisnis & Manajemen',
                'Sejarah & Kebudayaan',
                'Psikologi & Pengembangan Diri'
            ]),
        ];
    }
}
