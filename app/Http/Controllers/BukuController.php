<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource with stats, withCount('peminjamans'), dynamic filters, and pagination.
     */
    public function index(Request $request)
    {
        // 1. Simpan histori pencarian di Session jika ada input pencarian
        if ($request->filled('search')) {
            session(['last_book_search' => $request->search]);
        }

        // 2. Statistik Perpustakaan menggunakan Fluent Query Builder (DB::table)
        $stats = [
            'total_judul'        => DB::table('bukus')->count(),
            'total_stok'         => DB::table('bukus')->sum('stok') ?? 0,
            'total_dipinjam'     => DB::table('peminjamans')->where('status', 'dipinjam')->count(),
            'total_dikembalikan' => DB::table('peminjamans')->where('status', 'dikembalikan')->count(),
        ];

        // 3. Query Katalog Buku dengan Eager Loading, withCount, Filter when(), & withQueryString()
        $kategoriId = $request->query('kategori_id');
        $search = $request->query('search');

        $bukus = Buku::with('kategori')
            ->withCount('peminjamans')
            ->when($kategoriId, function ($query, $katId) {
                return $query->where('kategori_id', $katId);
            })
            ->when($search, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('judul', 'like', "%{$keyword}%")
                      ->orWhere('penulis', 'like', "%{$keyword}%")
                      ->orWhere('isbn', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('buku.index', compact('bukus', 'stats', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('buku.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn',
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|min:3|max:150',
            'penerbit'     => 'required|string|min:3|max:150',
            'tahun_terbit' => 'required|integer|digits:4|between:1900,' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ]);

        Buku::create($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Buku baru berhasil ditambahkan ke katalog perpustakaan!');
    }

    public function show(Buku $buku)
    {
        $buku->load(['kategori', 'peminjamans' => function ($q) {
            $q->latest();
        }]);
        return view('buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('buku.edit', compact('buku', 'kategoris'));
    }

    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn,' . $buku->id,
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|min:3|max:150',
            'penerbit'     => 'required|string|min:3|max:150',
            'tahun_terbit' => 'required|integer|digits:4|between:1900,' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ]);

        $buku->update($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil dihapus dari sistem perpustakaan!');
    }
}
