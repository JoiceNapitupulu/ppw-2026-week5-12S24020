<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pustakawan Uji Praktikum
        User::updateOrCreate(
            ['email' => 'pustakawan@del.ac.id'],
            [
                'name'     => 'Pustakawan Del',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Kategori Buku Rujukan Bidang Teknologi Informasi / Ilmu Komputer
        $kategoriList = [
            'KAT-RPL' => [
                'kode_kategori' => 'KAT-RPL',
                'nama_kategori' => 'Rekayasa Perangkat Lunak & Pemrograman',
            ],
            'KAT-ALGO' => [
                'kode_kategori' => 'KAT-ALGO',
                'nama_kategori' => 'Struktur Data & Algoritma',
            ],
            'KAT-BD' => [
                'kode_kategori' => 'KAT-BD',
                'nama_kategori' => 'Basis Data & Manajemen Data',
            ],
            'KAT-JAR' => [
                'kode_kategori' => 'KAT-JAR',
                'nama_kategori' => 'Jaringan Komputer & Keamanan Siber',
            ],
            'KAT-AI' => [
                'kode_kategori' => 'KAT-AI',
                'nama_kategori' => 'Kecerdasan Buatan & Sains Data',
            ],
            'KAT-OS' => [
                'kode_kategori' => 'KAT-OS',
                'nama_kategori' => 'Sistem Operasi & Arsitektur Komputer',
            ],
        ];

        $kategoris = [];
        foreach ($kategoriList as $key => $data) {
            $kategoris[$key] = Kategori::updateOrCreate(
                ['kode_kategori' => $data['kode_kategori']],
                $data
            );
        }

        // 3. Koleksi Buku Realistis (Memiliki Buku dengan Stok 0 dan Stok 1 untuk Pengujian ACID)
        $bukuList = [
            // --- Rekayasa Perangkat Lunak & Pemrograman ---
            [
                'isbn' => '978-0132350884',
                'judul' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'penulis' => 'Robert C. Martin',
                'penerbit' => 'Prentice Hall',
                'tahun_terbit' => 2008,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 1, // Stok = 1 untuk pengujian concurrency/transaksi ACID
                'sinopsis' => 'Buku standar industri tentang prinsip penulisan kode yang bersih, mudah dirawat, modular, dan teruji secara profesional.',
            ],
            [
                'isbn' => '978-0201616224',
                'judul' => 'The Pragmatic Programmer: Your Journey To Mastery (20th Anniversary Edition)',
                'penulis' => 'David Thomas, Andrew Hunt',
                'penerbit' => 'Addison-Wesley Professional',
                'tahun_terbit' => 2019,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 0, // Stok = 0 untuk pengujian penolakan peminjaman & rollback
                'sinopsis' => 'Pedoman esensial bagi pengembang perangkat lunak profesional mengenai arsitektur kode fleksibel dan metodologi pragmatis.',
            ],
            [
                'isbn' => '978-0201633610',
                'judul' => 'Design Patterns: Elements of Reusable Object-Oriented Software',
                'penulis' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 1994,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 10,
                'sinopsis' => 'Karya klasik "Gang of Four" (GoF) yang memperkenalkan 23 pola desain perangkat lunak berorientasi objek yang paling berpengaruh di dunia komputasi.',
            ],
            [
                'isbn' => '978-0321125217',
                'judul' => 'Domain-Driven Design: Tackling Complexity in the Heart of Software',
                'penulis' => 'Eric Evans',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 2003,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 8,
                'sinopsis' => 'Pendekatan sistematis memodelkan logika bisnis yang kompleks menjadi struktur kode modular yang terisolasi dengan baik.',
            ],
            [
                'isbn' => '978-0134494166',
                'judul' => 'Clean Architecture: A Craftsman\'s Guide to Software Structure and Design',
                'penulis' => 'Robert C. Martin',
                'penerbit' => 'Prentice Hall',
                'tahun_terbit' => 2017,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 15,
                'sinopsis' => 'Panduan mendesain arsitektur aplikasi modular yang independen terhadap framework, antarmuka pengguna, dan basis data.',
            ],

            // --- Struktur Data & Algoritma ---
            [
                'isbn' => '978-0262046305',
                'judul' => 'Introduction to Algorithms (4th Edition)',
                'penulis' => 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein',
                'penerbit' => 'MIT Press',
                'tahun_terbit' => 2022,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 20,
                'sinopsis' => 'Dikenal sebagai "CLRS", buku referensi komprehensif tentang teori kompleksitas algoritma, struktur data canggih, dan teknik optimasi kueri.',
            ],
            [
                'isbn' => '978-0321573513',
                'judul' => 'Algorithms (4th Edition)',
                'penulis' => 'Robert Sedgewick, Kevin Wayne',
                'penerbit' => 'Addison-Wesley Professional',
                'tahun_terbit' => 2011,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 12,
                'sinopsis' => 'Eksplorasi mendalam struktur data graf, algoritma pencarian, pengurutan, kompresi data, dan manipulasi memori terstruktur.',
            ],
            [
                'isbn' => '978-0134845623',
                'judul' => 'The Algorithm Design Manual (3rd Edition)',
                'penulis' => 'Steven S. Skiena',
                'penerbit' => 'Springer',
                'tahun_terbit' => 2020,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 9,
                'sinopsis' => 'Buku pegangan praktis dalam memecahkan masalah komputasional dunia nyata menggunakan katalog algoritma dan war story implementasi.',
            ],

            // --- Basis Data & Manajemen Data ---
            [
                'isbn' => '978-0078022159',
                'judul' => 'Database System Concepts (7th Edition)',
                'penulis' => 'Abraham Silberschatz, Henry F. Korth, S. Sudarshan',
                'penerbit' => 'McGraw-Hill Education',
                'tahun_terbit' => 2019,
                'kategori_id' => $kategoris['KAT-BD']->id,
                'stok' => 18,
                'sinopsis' => 'Buku teks standar universitas yang mencakup model relasional, transaksi ACID, konkurensi indexing, dan arsitektur database modern.',
            ],
            [
                'isbn' => '978-1449373320',
                'judul' => 'Designing Data-Intensive Applications: The Big Ideas Behind Reliable, Scalable, and Maintainable Systems',
                'penulis' => 'Martin Kleppmann',
                'penerbit' => 'O\'Reilly Media',
                'tahun_terbit' => 2017,
                'kategori_id' => $kategoris['KAT-BD']->id,
                'stok' => 25,
                'sinopsis' => 'Kajian mutakhir mengenai penyimpanan data terdistribusi, replikasi, partisi, konsistensi transaksi, dan stream processing.',
            ],

            // --- Jaringan Komputer & Keamanan Siber ---
            [
                'isbn' => '978-0132126953',
                'judul' => 'Computer Networks (5th Edition)',
                'penulis' => 'Andrew S. Tanenbaum, David J. Wetherall',
                'penerbit' => 'Prentice Hall',
                'tahun_terbit' => 2010,
                'kategori_id' => $kategoris['KAT-JAR']->id,
                'stok' => 12,
                'sinopsis' => 'Karya klasik Tanenbaum yang membedah arsitektur protokol jaringan, algoritma routing, transmisi data, dan aspek keamanan komunikasi data.',
            ],

            // --- Kecerdasan Buatan & Sains Data ---
            [
                'isbn' => '978-0134610993',
                'judul' => 'Artificial Intelligence: A Modern Approach (4th Edition)',
                'penulis' => 'Stuart Russell, Peter Norvig',
                'penerbit' => 'Pearson',
                'tahun_terbit' => 2020,
                'kategori_id' => $kategoris['KAT-AI']->id,
                'stok' => 14,
                'sinopsis' => 'Buku standar emas kecerdasan buatan dunia, mengupas agen cerdas, algoritma pencarian ruang keadaan, dan pembelajaran mesin.',
            ],

            // --- Sistem Operasi & Arsitektur Komputer ---
            [
                'isbn' => '978-1119456339',
                'judul' => 'Operating System Concepts (10th Edition)',
                'penulis' => 'Abraham Silberschatz, Peter B. Galvin, Greg Gagne',
                'penerbit' => 'Wiley',
                'tahun_terbit' => 2018,
                'kategori_id' => $kategoris['KAT-OS']->id,
                'stok' => 21,
                'sinopsis' => 'Dikenal sebagai "Buku Dinosaurus", buku teks pokok yang membahas manajemen proses, memori virtual, dan sinkronisasi konkurensi.',
            ],
        ];

        $createdBooks = [];
        foreach ($bukuList as $book) {
            $createdBooks[] = Buku::updateOrCreate(
                ['isbn' => $book['isbn']],
                $book
            );
        }

        // 4. Sample Transaksi Peminjaman untuk Audit Sirkulasi
        Peminjaman::updateOrCreate(
            ['kode_pinjam' => 'PINJAM-DEMO-001'],
            [
                'buku_id'             => $createdBooks[0]->id, // Clean Code
                'nim_peminjam'        => '12S24020',
                'nama_peminjam'       => 'Joice Napitupulu',
                'tanggal_pinjam'      => now()->subDays(5)->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(2)->toDateString(),
                'tanggal_kembali'     => null,
                'status'              => 'dipinjam',
            ]
        );

        Peminjaman::updateOrCreate(
            ['kode_pinjam' => 'PINJAM-DEMO-002'],
            [
                'buku_id'             => $createdBooks[2]->id, // Design Patterns
                'nim_peminjam'        => '11S22001',
                'nama_peminjam'       => 'Raiden Shogun',
                'tanggal_pinjam'      => now()->subDays(10)->toDateString(),
                'tanggal_jatuh_tempo' => now()->subDays(3)->toDateString(),
                'tanggal_kembali'     => now()->subDays(2)->toDateString(),
                'status'              => 'dikembalikan',
            ]
        );
    }
}
