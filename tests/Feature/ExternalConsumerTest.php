<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalConsumerTest extends TestCase
{
    use RefreshDatabase;

    private Buku $buku;

    protected function setUp(): void
    {
        parent::setUp();

        $kategori = Kategori::create([
            'kode_kategori' => 'KAT-RPL',
            'nama_kategori' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->buku = Buku::create([
            'isbn'         => '978-0132350884',
            'judul'        => 'Clean Code',
            'penulis'      => 'Robert C. Martin',
            'penerbit'     => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'kategori_id'  => $kategori->id,
            'stok'         => 5,
        ]);
    }

    public function test_external_metadata_success(): void
    {
        Http::fake([
            'https://openlibrary.org/isbn/*' => Http::response([
                'title'           => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'publish_date'    => 'August 1, 2008',
                'number_of_pages' => 464,
                'publishers'      => ['Prentice Hall'],
                'covers'          => [12345],
            ], 200),
        ]);

        $response = $this->getJson("/api/v1/bukus/{$this->buku->id}/referensi-eksternal");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'external_service' => [
                    'provider'        => 'Open Library Books API',
                    'number_of_pages' => 464,
                    'publishers'      => ['Prentice Hall'],
                ],
            ],
        ]);
    }

    public function test_external_metadata_not_found_on_openlibrary(): void
    {
        Http::fake([
            'https://openlibrary.org/isbn/*' => Http::response([], 404),
        ]);

        $response = $this->getJson("/api/v1/bukus/{$this->buku->id}/referensi-eksternal");

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'status' => 404,
            'title'  => 'External Metadata Not Found',
        ]);
    }

    public function test_external_service_timeout_or_error_returns_502_rfc7807(): void
    {
        Http::fake([
            'https://openlibrary.org/*' => Http::response('Server Error', 500),
        ]);

        $response = $this->getJson("/api/v1/bukus/{$this->buku->id}/referensi-eksternal");

        $response->assertStatus(502);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'status' => 502,
        ]);
    }

    public function test_non_existent_local_book_returns_404(): void
    {
        $response = $this->getJson('/api/v1/bukus/999999/referensi-eksternal');

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'status' => 404,
            'title'  => 'Resource Not Found',
        ]);
    }
}
