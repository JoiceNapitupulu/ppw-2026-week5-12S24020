<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PeminjamanApiTest extends TestCase
{
    use RefreshDatabase;

    private User $pustakawan;
    private Kategori $kategori;
    private Buku $bukuReady;
    private Buku $bukuEmpty;
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

        $this->kategori = Kategori::create([
            'kode_kategori' => 'KAT-RPL',
            'nama_kategori' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->bukuReady = Buku::create([
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $this->kategori->id,
            'stok'         => 1, // Stok 1 untuk verifikasi atomic decrement
        ]);

        $this->bukuEmpty = Buku::create([
            'isbn'         => '978-0201616224',
            'judul'        => 'The Pragmatic Programmer',
            'penulis'      => 'Andrew Hunt',
            'penerbit'     => 'Addison-Wesley',
            'tahun_terbit' => 2019,
            'kategori_id'  => $this->kategori->id,
            'stok'         => 0, // Stok 0
        ]);
    }

    public function test_get_peminjamans_requires_sanctum_auth(): void
    {
        $response = $this->getJson('/api/v1/peminjamans');

        $response->assertStatus(401);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_get_peminjamans_with_filters(): void
    {
        Peminjaman::create([
            'kode_pinjam'         => 'PINJAM-001',
            'buku_id'             => $this->bukuReady->id,
            'nim_peminjam'        => '12S24020',
            'nama_peminjam'       => 'Joice Napitupulu',
            'tanggal_pinjam'      => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status'              => 'dipinjam',
        ]);

        Peminjaman::create([
            'kode_pinjam'         => 'PINJAM-002',
            'buku_id'             => $this->bukuReady->id,
            'nim_peminjam'        => '11S22001',
            'nama_peminjam'       => 'Mahasiswa Lain',
            'tanggal_pinjam'      => now()->subDays(10)->toDateString(),
            'tanggal_jatuh_tempo' => now()->subDays(3)->toDateString(),
            'tanggal_kembali'     => now()->subDays(2)->toDateString(),
            'status'              => 'dikembalikan',
        ]);

        // Filter status = dipinjam
        $resDipinjam = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/peminjamans?status=dipinjam');

        $resDipinjam->assertStatus(200);
        $this->assertCount(1, $resDipinjam->json('data'));
        $this->assertEquals('PINJAM-001', $resDipinjam->json('data.0.kode_pinjam'));

        // Filter NIM
        $resNim = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/peminjamans?nim=12S24020');

        $resNim->assertStatus(200);
        $this->assertCount(1, $resNim->json('data'));
        $this->assertEquals('12S24020', $resNim->json('data.0.nim_peminjam'));
    }

    public function test_post_peminjaman_success_decrements_stock_and_returns_201_with_location(): void
    {
        $payload = [
            'buku_id'       => $this->bukuReady->id,
            'nim_peminjam'  => '12S24020',
            'nama_peminjam' => 'Joice Napitupulu',
            'durasi_hari'   => 7,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/peminjamans', $payload);

        $response->assertStatus(201);
        $this->assertTrue($response->headers->has('Location'));

        // Stok berkurang dari 1 menjadi 0
        $this->assertEquals(0, $this->bukuReady->fresh()->stok);

        // HATEOAS: status dipinjam harus memuat link 'kembalikan_buku'
        $this->assertArrayHasKey('kembalikan_buku', $response->json('data._links'));
    }

    public function test_post_peminjaman_rejects_duplicate_active_loan_with_409(): void
    {
        // Peminjaman pertama
        Peminjaman::create([
            'kode_pinjam'         => 'PINJAM-EXISTING',
            'buku_id'             => $this->bukuReady->id,
            'nim_peminjam'        => '12S24020',
            'nama_peminjam'       => 'Joice Napitupulu',
            'tanggal_pinjam'      => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status'              => 'dipinjam',
        ]);

        // Coba pinjam lagi buku yang sama oleh mahasiswa yang sama
        $payload = [
            'buku_id'       => $this->bukuReady->id,
            'nim_peminjam'  => '12S24020',
            'nama_peminjam' => 'Joice Napitupulu',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/peminjamans', $payload);

        $response->assertStatus(409);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'status' => 409,
            'title'  => 'Duplicate Loan Conflict',
        ]);

        // Stok tidak boleh berkurang
        $this->assertEquals(1, $this->bukuReady->fresh()->stok);
    }

    public function test_post_peminjaman_rejects_when_stock_exhausted_with_422(): void
    {
        $payload = [
            'buku_id'       => $this->bukuEmpty->id,
            'nim_peminjam'  => '12S24099',
            'nama_peminjam' => 'Mahasiswa Lain',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/peminjamans', $payload);

        $response->assertStatus(422);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'status' => 422,
            'title'  => 'Book Stock Exhausted',
            'detail' => 'Stok buku sedang habis.',
        ]);

        // Stok tetap 0
        $this->assertEquals(0, $this->bukuEmpty->fresh()->stok);
    }

    public function test_return_book_increments_stock_and_marks_returned(): void
    {
        // Set stok awal = 0 karena sedang dipinjam
        $this->bukuReady->update(['stok' => 0]);

        $peminjaman = Peminjaman::create([
            'kode_pinjam'         => 'PINJAM-RETURN-TEST',
            'buku_id'             => $this->bukuReady->id,
            'nim_peminjam'        => '12S24020',
            'nama_peminjam'       => 'Joice Napitupulu',
            'tanggal_pinjam'      => now()->subDays(5)->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(2)->toDateString(),
            'status'              => 'dipinjam',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson("/api/v1/peminjamans/{$peminjaman->id}/kembalikan");

        $response->assertStatus(200);
        $this->assertEquals('dikembalikan', $response->json('data.status'));
        $this->assertNotNull($response->json('data.tanggal_kembali'));

        // Stok buku bertambah dari 0 menjadi 1
        $this->assertEquals(1, $this->bukuReady->fresh()->stok);

        // HATEOAS: Setelah dikembalikan, link 'kembalikan_buku' harus hilang
        $this->assertArrayNotHasKey('kembalikan_buku', $response->json('data._links'));
    }

    public function test_return_book_already_returned_returns_409_conflict(): void
    {
        $peminjaman = Peminjaman::create([
            'kode_pinjam'         => 'PINJAM-ALREADY-RETURNED',
            'buku_id'             => $this->bukuReady->id,
            'nim_peminjam'        => '12S24020',
            'nama_peminjam'       => 'Joice Napitupulu',
            'tanggal_pinjam'      => now()->subDays(10)->toDateString(),
            'tanggal_jatuh_tempo' => now()->subDays(3)->toDateString(),
            'tanggal_kembali'     => now()->subDays(2)->toDateString(),
            'status'              => 'dikembalikan',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson("/api/v1/peminjamans/{$peminjaman->id}/kembalikan");

        $response->assertStatus(409);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'status' => 409,
            'title'  => 'Loan Already Returned Conflict',
        ]);

        // Stok tidak boleh bertambah lagi
        $this->assertEquals(1, $this->bukuReady->fresh()->stok);
    }
}
