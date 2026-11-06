<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BukuApiTest extends TestCase
{
    use RefreshDatabase;

    private User $pustakawan;
    private Kategori $kategoriRpl;
    private Kategori $kategoriAlgo;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pustakawan = User::create([
            'name'     => 'Pustakawan Del',
            'email'    => 'pustakawan@del.ac.id',
            'password' => Hash::make('password123'),
        ]);

        $this->token = $this->pustakawan->createToken('test-token')->plainTextToken;

        $this->kategoriRpl = Kategori::create([
            'kode_kategori' => 'KAT-RPL',
            'nama_kategori' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->kategoriAlgo = Kategori::create([
            'kode_kategori' => 'KAT-ALGO',
            'nama_kategori' => 'Algoritma & Struktur Data',
        ]);
    }

    public function test_get_bukus_index_with_pagination_and_meta(): void
    {
        Buku::create([
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 5,
        ]);

        Buku::create([
            'isbn'         => '978-0262046305',
            'judul'        => 'Introduction to Algorithms',
            'penulis'      => 'Cormen',
            'penerbit'     => 'MIT Press',
            'tahun_terbit' => 2022,
            'kategori_id'  => $this->kategoriAlgo->id,
            'stok'         => 0,
        ]);

        $response = $this->getJson('/api/v1/bukus?per_page=1');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id', 'isbn', 'judul', 'penulis', 'penerbit', 'tahun_terbit',
                    'kategori_id', 'kategori', 'stok', 'status_ketersediaan',
                    'total_peminjaman', '_links',
                ],
            ],
            'meta' => [
                'current_page', 'per_page', 'total_items', 'total_pages', 'links',
            ],
        ]);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals(2, $response->json('meta.total_items'));
    }

    public function test_filter_and_search_bukus(): void
    {
        Buku::create([
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 5,
        ]);

        Buku::create([
            'isbn'         => '978-0262046305',
            'judul'        => 'Introduction to Algorithms',
            'penulis'      => 'Cormen',
            'penerbit'     => 'MIT Press',
            'tahun_terbit' => 2022,
            'kategori_id'  => $this->kategoriAlgo->id,
            'stok'         => 0,
        ]);

        // Filter search
        $searchRes = $this->getJson('/api/v1/bukus?search=Clean');
        $searchRes->assertStatus(200);
        $this->assertCount(1, $searchRes->json('data'));
        $this->assertEquals('Clean Code', $searchRes->json('data.0.judul'));

        // Filter tersedia=1 (stok > 0)
        $availRes = $this->getJson('/api/v1/bukus?tersedia=1');
        $availRes->assertStatus(200);
        $this->assertCount(1, $availRes->json('data'));
        $this->assertEquals('Clean Code', $availRes->json('data.0.judul'));
    }

    public function test_get_buku_detail_and_dynamic_hateoas_links(): void
    {
        $bukuAvailable = Buku::create([
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 3,
        ]);

        $bukuEmpty = Buku::create([
            'isbn'         => '978-0201616224',
            'judul'        => 'The Pragmatic Programmer',
            'penulis'      => 'Andrew Hunt',
            'penerbit'     => 'Addison-Wesley',
            'tahun_terbit' => 2019,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 0,
        ]);

        // Detail buku stok > 0: harus memiliki link 'pinjam_buku'
        $resAvail = $this->getJson("/api/v1/bukus/{$bukuAvailable->id}");
        $resAvail->assertStatus(200);
        $this->assertArrayHasKey('pinjam_buku', $resAvail->json('data._links'));
        $this->assertEquals('TERSEDIA', $resAvail->json('data.status_ketersediaan'));

        // Detail buku stok 0: TIDAK boleh memiliki link 'pinjam_buku'
        $resEmpty = $this->getJson("/api/v1/bukus/{$bukuEmpty->isbn}");
        $resEmpty->assertStatus(200);
        $this->assertArrayNotHasKey('pinjam_buku', $resEmpty->json('data._links'));
        $this->assertEquals('HABIS', $resEmpty->json('data.status_ketersediaan'));

        // Detail not found: 404 RFC 7807
        $resNotFound = $this->getJson('/api/v1/bukus/999999');
        $resNotFound->assertStatus(404);
        $resNotFound->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_store_buku_success_201_with_location_header(): void
    {
        $payload = [
            'isbn'         => '978-0134494166',
            'judul'        => 'Clean Architecture',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2017,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 10,
            'sinopsis'     => 'Panduan mendesain arsitektur aplikasi modular.',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/bukus', $payload);

        $response->assertStatus(201);
        $this->assertTrue($response->headers->has('Location'));
        $newId = $response->json('data.id');
        $this->assertEquals(url("/api/v1/bukus/{$newId}"), $response->headers->get('Location'));
    }

    public function test_put_full_replacement_vs_patch_partial_update(): void
    {
        $buku = Buku::create([
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 5,
            'sinopsis'     => 'Sinopsis lama yang akan hilang pada PUT.',
        ]);

        // 1. Uji PATCH: Hanya update stok
        $patchRes = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson("/api/v1/bukus/{$buku->id}", [
                'stok' => 25,
            ]);

        $patchRes->assertStatus(200);
        $this->assertEquals(25, $patchRes->json('data.stok'));
        $this->assertEquals('Clean Code', $patchRes->json('data.judul'));
        $this->assertEquals('Sinopsis lama yang akan hilang pada PUT.', $patchRes->json('data.sinopsis'));

        // 2. Uji PUT: Mengirim seluruh representasi tanpa sinopsis -> sinopsis harus menjadi null
        $putPayload = [
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code (Updated)',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 30,
            // sinopsis diabaikan
        ];

        $putRes = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/v1/bukus/{$buku->id}", $putPayload);

        $putRes->assertStatus(200);
        $this->assertEquals('Clean Code (Updated)', $putRes->json('data.judul'));
        $this->assertNull($putRes->json('data.sinopsis'));
    }

    public function test_delete_buku_conflict_when_active_loan_exists(): void
    {
        $buku = Buku::create([
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $this->kategoriRpl->id,
            'stok'         => 1,
        ]);

        Peminjaman::create([
            'kode_pinjam'         => 'PINJAM-TEST-ACTIVE',
            'buku_id'             => $buku->id,
            'nim_peminjam'        => '12S24020',
            'nama_peminjam'       => 'Joice Napitupulu',
            'tanggal_pinjam'      => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status'              => 'dipinjam',
        ]);

        // Percobaan delete saat status 'dipinjam' -> 409 Conflict
        $delConflict = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/v1/bukus/{$buku->id}");

        $delConflict->assertStatus(409);
        $delConflict->assertHeader('Content-Type', 'application/problem+json');
        $delConflict->assertJson([
            'status' => 409,
            'title'  => 'Conflict — Book Has Active Loans',
        ]);

        // Ubah status peminjaman jadi 'dikembalikan'
        Peminjaman::where('kode_pinjam', 'PINJAM-TEST-ACTIVE')->update([
            'status'          => 'dikembalikan',
            'tanggal_kembali' => now()->toDateString(),
        ]);

        // Sekarang delete harus berhasil 204 No Content
        $delSuccess = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/v1/bukus/{$buku->id}");

        $delSuccess->assertStatus(204);
        $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
    }
}
