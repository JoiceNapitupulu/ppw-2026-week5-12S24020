<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeminjamanApiController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/peminjamans (Wajib auth:sanctum)
     * Mengambil daftar transaksi peminjaman dengan filter status dan NIM
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 10), 1), 50);

        $paginator = Peminjaman::query()
            ->with('buku')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('nim'), fn ($q, $nim) => $q->where('nim_peminjam', $nim))
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return $this->successResponse(
            data: PeminjamanResource::collection($paginator->items()),
            message: 'Daftar riwayat transaksi peminjaman berhasil diambil.',
            meta: [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total_items'  => $paginator->total(),
                'total_pages'  => $paginator->lastPage(),
                'links'        => [
                    'first' => $paginator->url(1),
                    'prev'  => $paginator->previousPageUrl(),
                    'next'  => $paginator->nextPageUrl(),
                    'last'  => $paginator->url($paginator->lastPage()),
                ],
            ]
        );
    }

    /**
     * GET /api/v1/peminjamans/{id} (Wajib auth:sanctum)
     * Detail satu transaksi peminjaman
     */
    public function show(string $id): JsonResponse
    {
        $peminjaman = Peminjaman::with('buku')->find($id);

        if (!$peminjaman) {
            return $this->problemResponse(
                title: 'Resource Not Found',
                detail: "Transaksi peminjaman dengan ID '{$id}' tidak ditemukan.",
                status: 404,
                errorSlug: 'loan-not-found'
            );
        }

        return $this->successResponse(
            data: new PeminjamanResource($peminjaman),
            message: 'Detail transaksi peminjaman berhasil ditemukan.'
        );
    }

    /**
     * POST /api/v1/peminjamans (Wajib auth:sanctum)
     * Mencatat transaksi peminjaman buku baru dengan jaminan ACID + lockForUpdate()
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'buku_id'       => 'required|integer|exists:bukus,id',
            'nim_peminjam'  => 'required|string|max:15',
            'nama_peminjam' => 'required|string|max:150',
            'durasi_hari'   => 'nullable|integer|min:1|max:30',
        ]);

        return DB::transaction(function () use ($validated) {
            // 1. Kunci baris buku secara eksklusif (SELECT ... FOR UPDATE) untuk menangkal Race Condition
            $buku = Buku::where('id', $validated['buku_id'])->lockForUpdate()->first();

            if (!$buku) {
                return $this->problemResponse(
                    title: 'Resource Not Found',
                    detail: 'Buku yang akan dipinjam tidak ditemukan.',
                    status: 404,
                    errorSlug: 'buku-not-found'
                );
            }

            // 2. Cek apakah mahasiswa masih meminjam buku yang sama & belum mengembalikannya -> 409 Conflict
            $alreadyBorrowed = Peminjaman::where('buku_id', $buku->id)
                ->where('nim_peminjam', $validated['nim_peminjam'])
                ->where('status', 'dipinjam')
                ->exists();

            if ($alreadyBorrowed) {
                return $this->problemResponse(
                    title: 'Duplicate Loan Conflict',
                    detail: "Mahasiswa dengan NIM '{$validated['nim_peminjam']}' masih meminjam buku '{$buku->judul}' dan belum mengembalikannya.",
                    status: 409,
                    errorSlug: 'duplicate-loan-conflict'
                );
            }

            // 3. Cek ketersediaan sisa stok -> 422 Unprocessable Content jika stok habis
            if ($buku->stok < 1) {
                return $this->problemResponse(
                    title: 'Book Stock Exhausted',
                    detail: 'Stok buku sedang habis.',
                    status: 422,
                    errorSlug: 'stock-exhausted'
                );
            }

            // 4. Kurangi stok (-1) secara atomik
            $buku->decrement('stok');

            // 5. Catat transaksi peminjaman baru
            $durasiHari = (int) ($validated['durasi_hari'] ?? 7);
            $tanggalPinjam = now();
            $tanggalJatuhTempo = now()->addDays($durasiHari);
            $kodePinjam = 'PINJAM-' . strtoupper(Str::random(8));

            $peminjaman = Peminjaman::create([
                'kode_pinjam'         => $kodePinjam,
                'buku_id'             => $buku->id,
                'nim_peminjam'        => strtoupper(trim($validated['nim_peminjam'])),
                'nama_peminjam'       => $validated['nama_peminjam'],
                'tanggal_pinjam'      => $tanggalPinjam->toDateString(),
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->toDateString(),
                'tanggal_kembali'     => null,
                'status'              => 'dipinjam',
            ]);

            $peminjaman->load('buku');

            return $this->createdResponse(
                data: new PeminjamanResource($peminjaman),
                locationUrl: url("/api/v1/peminjamans/{$peminjaman->id}"),
                message: "Peminjaman buku '{$buku->judul}' berhasil dicatat. Sisa stok kini: {$buku->fresh()->stok}."
            );
        });
    }

    /**
     * PATCH /api/v1/peminjamans/{id}/kembalikan (Wajib auth:sanctum)
     * Mengembalikan buku dengan jaminan ACID + lockForUpdate()
     */
    public function kembalikan(string $id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            // 1. Kunci transaksi peminjaman
            $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->first();

            if (!$peminjaman) {
                return $this->problemResponse(
                    title: 'Resource Not Found',
                    detail: "Transaksi peminjaman dengan ID '{$id}' tidak ditemukan.",
                    status: 404,
                    errorSlug: 'loan-not-found'
                );
            }

            // 2. Cek apakah sudah berstatus 'dikembalikan' -> 409 Conflict
            if ($peminjaman->status === 'dikembalikan') {
                return $this->problemResponse(
                    title: 'Loan Already Returned Conflict',
                    detail: 'Buku pada transaksi peminjaman ini sudah dikembalikan sebelumnya.',
                    status: 409,
                    errorSlug: 'loan-already-returned'
                );
            }

            // 3. Kunci buku dan tambahkan stok (+1)
            $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->first();
            if ($buku) {
                $buku->increment('stok');
            }

            // 4. Ubah status transaksi
            $peminjaman->status = 'dikembalikan';
            $peminjaman->tanggal_kembali = now()->toDateString();
            $peminjaman->save();

            $peminjaman->load('buku');

            return $this->successResponse(
                data: new PeminjamanResource($peminjaman),
                message: "Buku '{$buku?->judul}' berhasil dikembalikan. Sisa stok kini: {$buku?->fresh()->stok}."
            );
        });
    }
}
