<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Kategori Buku SIPUS-Del
        $kategoriList = [
            ['kode_kategori' => 'KAT-AKAD', 'nama_kategori' => 'Sumeru Akademiya: Riset & Rekayasa Perangkat Lunak'],
            ['kode_kategori' => 'KAT-FONTAINE', 'nama_kategori' => 'Fontaine Institut: Sistem Informasi & Hukum Enterprise'],
            ['kode_kategori' => 'KAT-INAZUMA', 'nama_kategori' => 'Tenshukaku: Teknik Elektro & Tenaga Petir'],
            ['kode_kategori' => 'KAT-LIYUE', 'nama_kategori' => 'Pelabuhan Liyue: Manajemen Kontrak, Bisnis & Rekayasa'],
            ['kode_kategori' => 'KAT-MOND', 'nama_kategori' => 'Favonius: Kesusastraan, Puisi & Sejarah Benua Teyvat'],
            ['kode_kategori' => 'KAT-ALKEMIS', 'nama_kategori' => 'Sains Alkimia Khaenri\'ah & Bioproses Alam'],
        ];

        $createdKategoris = [];
        foreach ($kategoriList as $kat) {
            $createdKategoris[] = Kategori::firstOrCreate(['kode_kategori' => $kat['kode_kategori']], $kat);
        }

        // 2. Koleksi Buku Spesifik
        $sampleBooks = [
            [
                'isbn' => '978-602-01-0001-1',
                'judul' => 'Prinsip Rekayasa Perangkat Lunak: Teori Akasha System',
                'penulis' => 'Alhaitham (Haravatat)',
                'penerbit' => 'Sumeru Akademiya Press',
                'tahun_terbit' => 2023,
                'kategori_id' => $createdKategoris[0]->id,
                'stok' => 15,
                'sinopsis' => 'Kajian komprehensif struktur data, algoritma pengenalan pola, serta pemrosesan informasi berbasis jaringan Akasha Terminal.',
            ],
            [
                'isbn' => '978-602-01-0002-2',
                'judul' => 'Manajemen Proyek Enterprise: Golden House & Kontrak Abadi',
                'penulis' => 'Ningguang & Zhongli',
                'penerbit' => 'Liyue Qixing Publishing',
                'tahun_terbit' => 2022,
                'kategori_id' => $createdKategoris[3]->id,
                'stok' => 20,
                'sinopsis' => 'Panduan eksekutif tata kelola modal, analisa resiko rantai pasok global, dan etika kontrak perjanjian bisnis modern.',
            ],
            [
                'isbn' => '978-602-01-0003-3',
                'judul' => 'Teknologi Arkhe & Mesin Pneuma-Ousia',
                'penulis' => 'Alain Guillotin & Neuvillette',
                'penerbit' => 'Fontaine Research Institute Press',
                'tahun_terbit' => 2024,
                'kategori_id' => $createdKategoris[1]->id,
                'stok' => 12,
                'sinopsis' => 'Kompilasi mekanisme sirkuit tenaga kinetik Pneuma dan Ousia untuk kestabilan pembangkit daya perkotaan Fontaine.',
            ],
            [
                'isbn' => '978-602-01-0004-4',
                'judul' => 'Transmisi Daya Petir & Manipulasi Medan Elektro Mikroskopik',
                'penulis' => 'Yae Miko & Lisa Minci',
                'penerbit' => 'Yae Publishing House',
                'tahun_terbit' => 2021,
                'kategori_id' => $createdKategoris[2]->id,
                'stok' => 9,
                'sinopsis' => 'Teori elektromagnetik frekuensi tinggi, proteksi lonjakan arus kilat, dan perancangan sirkuit kendali otonom.',
            ],
            [
                'isbn' => '978-602-01-0005-5',
                'judul' => 'Antologi Balada Angin: Puisi & Kearifan Mondstadt Kuno',
                'penulis' => 'Venti (Barbatos)',
                'penerbit' => 'Knights of Favonius Library',
                'tahun_terbit' => 2020,
                'kategori_id' => $createdKategoris[4]->id,
                'stok' => 18,
                'sinopsis' => 'Kumpulan puisi lirik pengembara, elegi para pahlawan pembela kebebasan, dan catatan budaya rakyat Mondstadt.',
            ],
            [
                'isbn' => '978-602-01-0006-6',
                'judul' => 'Dasar Sintesis Organik & Alkimia Terapan Alam Semesta',
                'penulis' => 'Albedo & Sucrose',
                'penerbit' => 'Favonius Research Division',
                'tahun_terbit' => 2022,
                'kategori_id' => $createdKategoris[5]->id,
                'stok' => 14,
                'sinopsis' => 'Penelitian transmutasi senyawa hayati, kristalisasi klorofil magis, dan isolasi enzim rekayasa bioproses tingkat lanjut.',
            ],
            [
                'isbn' => '978-602-01-0007-7',
                'judul' => 'Strategi Perang & Logika Pengambilan Keputusan Watatsumi',
                'penulis' => 'Sangonomiya Kokomi',
                'penerbit' => 'Yae Publishing House',
                'tahun_terbit' => 2023,
                'kategori_id' => $createdKategoris[2]->id,
                'stok' => 10,
                'sinopsis' => 'Petunjuk taktis manuver formasi, logistik medan tempur, serta analisis game theory dalam situasi kritis.',
            ],
            [
                'isbn' => '978-602-01-0008-8',
                'judul' => 'Audit Keuangan & Standar Akuntansi Perusahaan Navia Spina',
                'penulis' => 'Navia & Keqing',
                'penerbit' => 'Spina di Rosula Press',
                'tahun_terbit' => 2024,
                'kategori_id' => $createdKategoris[1]->id,
                'stok' => 11,
                'sinopsis' => 'Penerapan buku kas berpasangan, pengendalian intern organisasi, dan auditing kepatuhan regulasi publik.',
            ],
        ];

        foreach ($sampleBooks as $book) {
            Buku::firstOrCreate(['isbn' => $book['isbn']], $book);
        }

        // 3. Tambah buku pelengkap menggunakan factory agar memenuhi minimal 20 buku realistis
        foreach ($createdKategoris as $kategori) {
            Buku::factory()->count(3)->create([
                'kategori_id' => $kategori->id,
            ]);
        }
    }
}
