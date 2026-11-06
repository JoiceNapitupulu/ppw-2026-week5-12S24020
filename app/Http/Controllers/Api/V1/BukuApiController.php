<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BukuResource;
use App\Models\Buku;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BukuApiController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/bukus (Publik)
     * Mengambil koleksi buku terpaginasi bebas N+1 problem
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Parameter sorting yang diizinkan (mencegah SQL Injection pada ORDER BY)
        $allowedSorts = ['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok', 'created_at'];
        $sortBy = in_array($request->query('sort'), $allowedSorts) ? $request->query('sort') : 'created_at';
        $sortDir = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->query('per_page', 10), 1), 50);

        // 2. Kueri Eloquent bebas N+1 (with kategori & withCount peminjamans)
        $paginator = Buku::query()
            ->with('kategori')
            ->withCount('peminjamans')
            ->when($request->query('search'), function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($request->query('kategori_id'), fn ($q, $catId) => $q->where('kategori_id', $catId))
            ->when($request->boolean('tersedia'), fn ($q) => $q->where('stok', '>', 0))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        // 3. Kembalikan koleksi terbungkus BukuResource beserta metadata paginasi
        return $this->successResponse(
            data: BukuResource::collection($paginator->items()),
            message: 'Daftar koleksi buku berhasil diambil.',
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
     * GET /api/v1/bukus/{id} (Publik)
     * Mengambil detail buku berdasarkan ID atau ISBN
     */
    public function show(string $identifier): JsonResponse
    {
        $query = Buku::with(['kategori', 'peminjamans'])->withCount('peminjamans');

        if (is_numeric($identifier)) {
            $query->where(function ($q) use ($identifier) {
                $q->where('id', (int) $identifier)
                  ->orWhere('isbn', $identifier);
            });
        } else {
            $query->where('isbn', $identifier);
        }

        $buku = $query->first();

        if (!$buku) {
            return $this->problemResponse(
                title: 'Resource Not Found',
                detail: "Buku dengan pengenal '{$identifier}' tidak ditemukan di basis data.",
                status: 404,
                errorSlug: 'buku-not-found'
            );
        }

        return $this->successResponse(
            data: new BukuResource($buku),
            message: 'Detail buku berhasil ditemukan.'
        );
    }

    /**
     * POST /api/v1/bukus (Wajib auth:sanctum)
     * Menambah buku baru -> 201 Created + Header Location
     */
    public function store(Request $request): JsonResponse
    {
        $maxYear = (int) date('Y') + 1;

        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn',
            'judul'        => 'required|string|max:200',
            'penulis'      => 'required|string|max:150',
            'penerbit'     => 'required|string|max:150',
            'tahun_terbit' => "required|integer|min:1900|max:{$maxYear}",
            'kategori_id'  => 'required|integer|exists:kategoris,id',
            'stok'         => 'required|integer|min:0|max:1000',
            'sinopsis'     => 'nullable|string|max:2000',
        ]);

        $buku = Buku::create($validated);
        $buku->load('kategori')->loadCount('peminjamans');

        return $this->createdResponse(
            data: new BukuResource($buku),
            locationUrl: url("/api/v1/bukus/{$buku->id}"),
            message: 'Buku baru berhasil ditambahkan.'
        );
    }

    /**
     * PUT / PATCH /api/v1/bukus/{id} (Wajib auth:sanctum)
     * Membuktikan perbedaan semantik PUT (Full Replacement) vs PATCH (Partial Update)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return $this->problemResponse(
                title: 'Resource Not Found',
                detail: "Buku dengan ID '{$id}' tidak ditemukan.",
                status: 404,
                errorSlug: 'buku-not-found'
            );
        }

        $isPut = $request->isMethod('put');
        $maxYear = (int) date('Y') + 1;

        if ($isPut) {
            // SEMANTIK PUT: Seluruh field representasi WAJIB dikirimkan
            $validated = $request->validate([
                'isbn'         => "required|string|max:20|unique:bukus,isbn,{$buku->id}",
                'judul'        => 'required|string|max:200',
                'penulis'      => 'required|string|max:150',
                'penerbit'     => 'required|string|max:150',
                'tahun_terbit' => "required|integer|min:1900|max:{$maxYear}",
                'kategori_id'  => 'required|integer|exists:kategoris,id',
                'stok'         => 'required|integer|min:0|max:1000',
                'sinopsis'     => 'nullable|string|max:2000',
            ]);

            // Pada PUT murni, jika field nullable tidak disertakan, nilainya direset ke null
            $validated['sinopsis'] = $request->input('sinopsis', null);
        } else {
            // SEMANTIK PATCH: Hanya memvalidasi & mengubah atribut yang dikirim saja
            $validated = $request->validate([
                'isbn'         => "sometimes|required|string|max:20|unique:bukus,isbn,{$buku->id}",
                'judul'        => 'sometimes|required|string|max:200',
                'penulis'      => 'sometimes|required|string|max:150',
                'penerbit'     => 'sometimes|required|string|max:150',
                'tahun_terbit' => "sometimes|required|integer|min:1900|max:{$maxYear}",
                'kategori_id'  => 'sometimes|required|integer|exists:kategoris,id',
                'stok'         => 'sometimes|required|integer|min:0|max:1000',
                'sinopsis'     => 'sometimes|nullable|string|max:2000',
            ]);

            if (empty($validated)) {
                return $this->problemResponse(
                    title: 'Bad Request',
                    detail: 'Permintaan PATCH wajib menyertakan minimal satu atribut yang ingin diperbarui.',
                    status: 400,
                    errorSlug: 'empty-patch-payload'
                );
            }
        }

        $buku->update($validated);
        $buku->load('kategori')->loadCount('peminjamans');

        return $this->successResponse(
            data: new BukuResource($buku),
            message: $isPut
                ? 'Seluruh representasi buku berhasil diganti (PUT).'
                : 'Atribut buku berhasil diperbarui sebagian (PATCH).'
        );
    }

    /**
     * DELETE /api/v1/bukus/{id} (Wajib auth:sanctum)
     * Menolak jika masih ada peminjaman aktif ('dipinjam'), 204 No Content jika sukses
     */
    public function destroy(string $id): JsonResponse
    {
        $buku = Buku::withCount(['peminjamans as active_loans_count' => function ($q) {
            $q->where('status', 'dipinjam');
        }])->find($id);

        if (!$buku) {
            return $this->problemResponse(
                title: 'Resource Not Found',
                detail: "Buku dengan ID '{$id}' tidak ditemukan.",
                status: 404,
                errorSlug: 'buku-not-found'
            );
        }

        // Aturan Bisnis: Tolak 409 Conflict jika sedang dipinjam
        if ($buku->active_loans_count > 0) {
            return $this->problemResponse(
                title: 'Conflict — Book Has Active Loans',
                detail: "Buku '{$buku->judul}' tidak dapat dihapus karena masih sedang dipinjam oleh {$buku->active_loans_count} transaksi peminjaman aktif.",
                status: 409,
                errorSlug: 'book-has-active-loans'
            );
        }

        $buku->delete();

        return $this->noContentResponse();
    }
}
