<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoapTest extends TestCase
{
    use RefreshDatabase;

    public function test_wsdl_endpoint_returns_valid_xml_contract(): void
    {
        $response = $this->get('/soap/sipus.wsdl');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');
        $this->assertStringContainsString('LibrarySoapService', $response->getContent());
        $this->assertStringContainsString('CheckClearanceStatus', $response->getContent());
    }

    public function test_check_clearance_status_cleared(): void
    {
        $soapEnvelope = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:m="http://sipus.del.ac.id/soap/library">
    <soap:Body>
        <m:CheckClearanceStatusRequest>
            <m:nim_peminjam>12S24099</m:nim_peminjam>
        </m:CheckClearanceStatusRequest>
    </soap:Body>
</soap:Envelope>
XML;

        $response = $this->call('POST', '/soap/sipus', [], [], [], [
            'CONTENT_TYPE' => 'text/xml; charset=utf-8',
        ], $soapEnvelope);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');
        $this->assertStringContainsString('<m:nim_peminjam>12S24099</m:nim_peminjam>', $response->getContent());
        $this->assertStringContainsString('<m:active_loans>0</m:active_loans>', $response->getContent());
        $this->assertStringContainsString('<m:is_cleared>true</m:is_cleared>', $response->getContent());
    }

    public function test_check_clearance_status_not_cleared(): void
    {
        $kategori = Kategori::create([
            'kode_kategori' => 'KAT-TEST',
            'nama_kategori' => 'Test Kategori',
        ]);

        $buku = Buku::create([
            'isbn' => '978-0000000001',
            'judul' => 'Buku Uji SOAP',
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'tahun_terbit' => 2024,
            'kategori_id' => $kategori->id,
            'stok' => 5,
        ]);

        Peminjaman::create([
            'kode_pinjam' => 'PINJAM-TEST-001',
            'buku_id' => $buku->id,
            'nim_peminjam' => '12S24020',
            'nama_peminjam' => 'Joice Napitupulu',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $soapEnvelope = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:m="http://sipus.del.ac.id/soap/library">
    <soap:Body>
        <m:CheckClearanceStatusRequest>
            <m:nim_peminjam>12S24020</m:nim_peminjam>
        </m:CheckClearanceStatusRequest>
    </soap:Body>
</soap:Envelope>
XML;

        $response = $this->call('POST', '/soap/sipus', [], [], [], [
            'CONTENT_TYPE' => 'text/xml; charset=utf-8',
        ], $soapEnvelope);

        $response->assertStatus(200);
        $this->assertStringContainsString('<m:nim_peminjam>12S24020</m:nim_peminjam>', $response->getContent());
        $this->assertStringContainsString('<m:active_loans>1</m:active_loans>', $response->getContent());
        $this->assertStringContainsString('<m:is_cleared>false</m:is_cleared>', $response->getContent());
    }

    public function test_empty_or_malformed_xml_returns_soap_fault(): void
    {
        // Test empty body
        $response = $this->call('POST', '/soap/sipus', [], [], [], [
            'CONTENT_TYPE' => 'text/xml; charset=utf-8',
        ], '');
        $response->assertStatus(500);
        $this->assertStringContainsString('<faultcode>soap:Client</faultcode>', $response->getContent());

        // Test malformed xml
        $response = $this->call('POST', '/soap/sipus', [], [], [], [
            'CONTENT_TYPE' => 'text/xml; charset=utf-8',
        ], '<unclosed_xml');
        $response->assertStatus(500);
        $this->assertStringContainsString('Format XML tidak valid', $response->getContent());

        // Test missing nim
        $missingNimXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <m:CheckClearanceStatusRequest xmlns:m="http://sipus.del.ac.id/soap/library">
            <m:nim_peminjam></m:nim_peminjam>
        </m:CheckClearanceStatusRequest>
    </soap:Body>
</soap:Envelope>
XML;
        $response = $this->call('POST', '/soap/sipus', [], [], [], [
            'CONTENT_TYPE' => 'text/xml; charset=utf-8',
        ], $missingNimXml);
        $response->assertStatus(500);
        $this->assertStringContainsString('<faultcode>soap:Client</faultcode>', $response->getContent());
    }
}
