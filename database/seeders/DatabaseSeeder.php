<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Kategori Buku Rujukan Bidang Teknologi Informasi / Ilmu Komputer
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
            $kategoris[$key] = Kategori::firstOrCreate(
                ['kode_kategori' => $data['kode_kategori']],
                $data
            );
        }

        // 2. Daftar 30 Buku Rujukan Akademis & Industri IT
        $bukuList = [
            // --- Rekayasa Perangkat Lunak ---
            [
                'isbn' => '978-0132350884',
                'judul' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'penulis' => 'Robert C. Martin',
                'penerbit' => 'Prentice Hall',
                'tahun_terbit' => 2008,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 12,
                'sinopsis' => 'Panduan fundamental mengenai penulisan kode yang bersih, mudah dipahami, serta prinsip refactoring untuk membangun perangkat lunak berkualitas tinggi.',
            ],
            [
                'isbn' => '978-0201633610',
                'judul' => 'Design Patterns: Elements of Reusable Object-Oriented Software',
                'penulis' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 1994,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 10,
                'sinopsis' => 'Buku legendaris Gang of Four (GoF) yang menguraikan 23 pola desain perangkat lunak berorientasi objek untuk solusi arsitektural yang dapat digunakan kembali.',
            ],
            [
                'isbn' => '978-0134494166',
                'judul' => 'Clean Architecture: A Craftsman\'s Guide to Software Structure',
                'penulis' => 'Robert C. Martin',
                'penerbit' => 'Prentice Hall',
                'tahun_terbit' => 2017,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 8,
                'sinopsis' => 'Penjelasan mendalam tentang aturan universal arsitektur perangkat lunak independen dari framework, database, dan antarmuka eksternal.',
            ],
            [
                'isbn' => '978-0135974445',
                'judul' => 'The Pragmatic Programmer: Your Journey to Mastery',
                'penulis' => 'David Thomas, Andrew Hunt',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 2019,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 15,
                'sinopsis' => 'Buku wajib bagi perekayasa perangkat lunak modern yang membahas kebiasaan, pola pikir, dan praktik terbaik profesional pemrograman.',
            ],
            [
                'isbn' => '978-0134757599',
                'judul' => 'Refactoring: Improving the Design of Existing Code',
                'penulis' => 'Martin Fowler',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 2018,
                'kategori_id' => $kategoris['KAT-RPL']->id,
                'stok' => 7,
                'sinopsis' => 'Teknik merombak struktur internal basis kode yang sudah ada tanpa mengubah perilaku eksternalnya guna meningkatkan keterbacaan dan perawatan sistem.',
            ],

            // --- Struktur Data & Algoritma ---
            [
                'isbn' => '978-0262046305',
                'judul' => 'Introduction to Algorithms (CLRS)',
                'penulis' => 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein',
                'penerbit' => 'MIT Press',
                'tahun_terbit' => 2022,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 20,
                'sinopsis' => 'Buku standar internasional kurikulum universitas yang menyajikan kajian komprehensif analisis kompleksitas, graf, pemrograman dinamis, dan struktur data tingkat lanjut.',
            ],
            [
                'isbn' => '978-0321573513',
                'judul' => 'Algorithms (4th Edition)',
                'penulis' => 'Robert Sedgewick, Kevin Wayne',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 2011,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 14,
                'sinopsis' => 'Pendekatan praktis dan elegan untuk mempelajari algoritma pencarian, pengurutan, graf, dan pemrosesan string menggunakan implementasi nyata.',
            ],
            [
                'isbn' => '978-0984782857',
                'judul' => 'Cracking the Coding Interview: 189 Programming Questions and Solutions',
                'penulis' => 'Gayle Laakmann McDowell',
                'penerbit' => 'CareerCup',
                'tahun_terbit' => 2015,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 25,
                'sinopsis' => 'Panduan pemecahan masalah algoritma dan struktur data intensif untuk wawancara teknis di perusahaan teknologi global papan atas.',
            ],
            [
                'isbn' => '978-1617292231',
                'judul' => 'Grokking Algorithms: An Illustrated Guide for Programmers',
                'penulis' => 'Aditya Bhargava',
                'penerbit' => 'Manning Publications',
                'tahun_terbit' => 2016,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 18,
                'sinopsis' => 'Pengenalan algoritma visual yang ramah pemula dengan ilustrasi menarik, mencakup binary search, graf Dijkstra, dan algoritma greedy.',
            ],
            [
                'isbn' => '978-0201896831',
                'judul' => 'The Art of Computer Programming: Fundamental Algorithms',
                'penulis' => 'Donald E. Knuth',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 2011,
                'kategori_id' => $kategoris['KAT-ALGO']->id,
                'stok' => 5,
                'sinopsis' => 'Karya monumen ilmu komputer mengenai dasar-dasar matematika algoritma dan struktur data diskret.',
            ],

            // --- Basis Data & Manajemen Data ---
            [
                'isbn' => '978-0078022159',
                'judul' => 'Database System Concepts (7th Edition)',
                'penulis' => 'Abraham Silberschatz, Henry F. Korth, S. Sudarshan',
                'penerbit' => 'McGraw-Hill Education',
                'tahun_terbit' => 2019,
                'kategori_id' => $kategoris['KAT-BD']->id,
                'stok' => 16,
                'sinopsis' => 'Rujukan primer perancangan basis data relasional, aljabar relasional, normalisasi skema, indexing B-Tree, optimasi kueri, dan transaksi ACID.',
            ],
            [
                'isbn' => '978-1449373320',
                'judul' => 'Designing Data-Intensive Applications',
                'penulis' => 'Martin Kleppmann',
                'penerbit' => 'O\'Reilly Media',
                'tahun_terbit' => 2017,
                'kategori_id' => $kategoris['KAT-BD']->id,
                'stok' => 22,
                'sinopsis' => 'Eksplorasi mendalam arsitektur sistem penyimpanan data berskala besar, replikasi, partisi, konsistensi data, dan pemrosesan stream.',
            ],
            [
                'isbn' => '978-1492057611',
                'judul' => 'Learning SQL: Generate, Manipulate, and Retrieve Data',
                'penulis' => 'Alan Beaulieu',
                'penerbit' => 'O\'Reilly Media',
                'tahun_terbit' => 2020,
                'kategori_id' => $kategoris['KAT-BD']->id,
                'stok' => 15,
                'sinopsis' => 'Tutorial komprehensif menguasai sintaks SQL, pembuatan kueri kompleks, join tabel, subquery, ekspresi kondisional, dan manipulasi data transaksi.',
            ],
            [
                'isbn' => '978-0133970777',
                'judul' => 'Fundamentals of Database Systems (7th Edition)',
                'penulis' => 'Ramez Elmasri, Shamkant B. Navathe',
                'penerbit' => 'Pearson',
                'tahun_terbit' => 2015,
                'kategori_id' => $kategoris['KAT-BD']->id,
                'stok' => 9,
                'sinopsis' => 'Buku teks standar model pemodelan konseptual ER/EER, sistem manajemen basis data SQL dan NoSQL, serta keamanan basis data.',
            ],
            [
                'isbn' => '978-0072465631',
                'judul' => 'Database Management Systems (3rd Edition)',
                'penulis' => 'Raghu Ramakrishnan, Johannes Gehrke',
                'penerbit' => 'McGraw-Hill',
                'tahun_terbit' => 2003,
                'kategori_id' => $kategoris['KAT-BD']->id,
                'stok' => 6,
                'sinopsis' => 'Dikenal sebagai "Buku Sapi", mengulas secara terperinci perancangan mesin database, manajemen buffer, dan pemulihan crash.',
            ],

            // --- Jaringan Komputer & Keamanan Siber ---
            [
                'isbn' => '978-0136681557',
                'judul' => 'Computer Networking: A Top-Down Approach (8th Edition)',
                'penulis' => 'James F. Kurose, Keith W. Ross',
                'penerbit' => 'Pearson',
                'tahun_terbit' => 2020,
                'kategori_id' => $kategoris['KAT-JAR']->id,
                'stok' => 18,
                'sinopsis' => 'Rujukan utama mata kuliah jaringan komputer dengan pendekatan top-down dari lapisan aplikasi (HTTP, DNS) hingga lapisan fisik dan jaringan nirkabel.',
            ],
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
            [
                'isbn' => '978-0134444284',
                'judul' => 'Network Security Essentials: Applications and Standards',
                'penulis' => 'William Stallings',
                'penerbit' => 'Pearson',
                'tahun_terbit' => 2017,
                'kategori_id' => $kategoris['KAT-JAR']->id,
                'stok' => 8,
                'sinopsis' => 'Konsep fundamental kriptografi simetris/asimetris, otentikasi jaringan IPsec, SSL/TLS, proteksi firewall, dan penanganan intrusi keamanan.',
            ],
            [
                'isbn' => '978-1118026472',
                'judul' => 'The Web Application Hacker\'s Handbook: Finding and Exploiting Security Flaws',
                'penulis' => 'Dafydd Stuttard, Marcus Pinto',
                'penerbit' => 'Wiley',
                'tahun_terbit' => 2011,
                'kategori_id' => $kategoris['KAT-JAR']->id,
                'stok' => 7,
                'sinopsis' => 'Panduan terdepan dalam metodologi uji penetrasi aplikasi web (web pentest), kerentanan XSS, SQL Injection, dan mekanisme pertahanan modern.',
            ],
            [
                'isbn' => '978-0321336316',
                'judul' => 'TCP/IP Illustrated, Volume 1: The Protocols',
                'penulis' => 'W. Richard Stevens, Kevin R. Fall',
                'penerbit' => 'Addison-Wesley',
                'tahun_terbit' => 2011,
                'kategori_id' => $kategoris['KAT-JAR']->id,
                'stok' => 11,
                'sinopsis' => 'Analisis paling mendalam dan otoritatif mengenai implementasi arsitektur protokol TCP/IP di lingkungan sistem komputasi nyata.',
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
                'sinopsis' => 'Buku standar emas kecerdasan buatan dunia, mengupas agen cerdas, algoritma pencarian ruang keadaan, logika penalaran, probabilitas, dan pembelajaran mesin.',
            ],
            [
                'isbn' => '978-0262035612',
                'judul' => 'Deep Learning',
                'penulis' => 'Ian Goodfellow, Yoshua Bengio, Aaron Courville',
                'penerbit' => 'MIT Press',
                'tahun_terbit' => 2016,
                'kategori_id' => $kategoris['KAT-AI']->id,
                'stok' => 13,
                'sinopsis' => 'Landasan teori matematika dan implementasi jaringan saraf tiruan mendalam (deep neural networks), convolutional networks, dan generative models.',
            ],
            [
                'isbn' => '978-0387310732',
                'judul' => 'Pattern Recognition and Machine Learning',
                'penulis' => 'Christopher M. Bishop',
                'penerbit' => 'Springer',
                'tahun_terbit' => 2006,
                'kategori_id' => $kategoris['KAT-AI']->id,
                'stok' => 9,
                'sinopsis' => 'Buku teks komprehensif pembelajaran mesin berbasis pendekatan probabilitas Bayesian, regresi linier, SVM, model grafis, dan estimasi densitas.',
            ],
            [
                'isbn' => '978-1098125974',
                'judul' => 'Hands-On Machine Learning with Scikit-Learn, Keras, and TensorFlow',
                'penulis' => 'Aurelien Geron',
                'penerbit' => 'O\'Reilly Media',
                'tahun_terbit' => 2022,
                'kategori_id' => $kategoris['KAT-AI']->id,
                'stok' => 17,
                'sinopsis' => 'Panduan terapan membangun model machine learning end-to-end, pipeline data, klasifikasi, klastering, dan model deep learning siap produksi.',
            ],
            [
                'isbn' => '978-0262039246',
                'judul' => 'Reinforcement Learning: An Introduction (2nd Edition)',
                'penulis' => 'Richard S. Sutton, Andrew G. Barto',
                'penerbit' => 'MIT Press',
                'tahun_terbit' => 2018,
                'kategori_id' => $kategoris['KAT-AI']->id,
                'stok' => 6,
                'sinopsis' => 'Kajian mendasar algoritma pembelajaran penguatan (reinforcement learning), Markov Decision Processes, Q-learning, dan metode policy gradient.',
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
                'sinopsis' => 'Dikenal sebagai "Buku Dinosaurus", buku teks pokok yang membahas manajemen proses, memori virtual, sinkronisasi konkurensi, dan sistem berkas.',
            ],
            [
                'isbn' => '978-0133591620',
                'judul' => 'Modern Operating Systems (4th Edition)',
                'penulis' => 'Andrew S. Tanenbaum, Herbert Bos',
                'penerbit' => 'Pearson',
                'tahun_terbit' => 2014,
                'kategori_id' => $kategoris['KAT-OS']->id,
                'stok' => 15,
                'sinopsis' => 'Analisis mendalam konsep arsitektur sistem operasi modern, manajemen deadlock, virtualisasi sistem, multiprosesor, dan keamanan OS.',
            ],
            [
                'isbn' => '978-0134092669',
                'judul' => 'Computer Systems: A Programmer\'s Perspective (3rd Edition)',
                'penulis' => 'Randal E. Bryant, David R. O\'Hallaron',
                'penerbit' => 'Pearson',
                'tahun_terbit' => 2015,
                'kategori_id' => $kategoris['KAT-OS']->id,
                'stok' => 11,
                'sinopsis' => 'Menghubungkan pemahaman hardware dan software: kompilasi kode assembly, hierarki memori cache, I/O virtual, dan arsitektur prosesor.',
            ],
            [
                'isbn' => '978-0134997193',
                'judul' => 'Computer Organization and Architecture (11th Edition)',
                'penulis' => 'William Stallings',
                'penerbit' => 'Pearson',
                'tahun_terbit' => 2018,
                'kategori_id' => $kategoris['KAT-OS']->id,
                'stok' => 10,
                'sinopsis' => 'Penjelasan komprehensif struktur dan fungsi komponen komputer, desain arsitektur instruksi (ISA), aritmatika komputer, dan pipelining.',
            ],
            [
                'isbn' => '978-1593272203',
                'judul' => 'The Linux Programming Interface',
                'penulis' => 'Michael Kerrisk',
                'penerbit' => 'No Starch Press',
                'tahun_terbit' => 2010,
                'kategori_id' => $kategoris['KAT-OS']->id,
                'stok' => 8,
                'sinopsis' => 'Buku pegangan terlengkap untuk pemrograman sistem Linux dan UNIX, mencakup system calls, manajemen proses, sinyal, IPC, dan sockets.',
            ],
        ];

        foreach ($bukuList as $book) {
            Buku::create($book);
        }
    }
}