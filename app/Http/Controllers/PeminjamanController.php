<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PeminjamanController extends Controller
{
    /**
     * Tampilkan riwayat transaksi peminjaman buku
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $peminjamans = Peminjaman::with('buku')
            ->when($status, function ($q, $st) {
                return $q->where('status', $st);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * Tampilkan form peminjaman buku tertentu
     */
    public function create(Request $request)
    {
        $bukuId = $request->query('buku_id');
        $selectedBuku = $bukuId ? Buku::find($bukuId) : null;
        $availableBukus = Buku::where('stok', '>', 0)->orderBy('judul')->get();

        return view('peminjaman.create', compact('selectedBuku', 'availableBukus'));
    }

    /**
     * Transaksi Peminjaman Buku Berbasis ACID (DB::transaction & lockForUpdate)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'buku_id'             => 'required|exists:bukus,id',
            'nim_peminjam'        => 'required|string|min:6|max:20',
            'nama_peminjam'       => 'required|string|min:3|max:150',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        $nim = strtoupper(trim($validated['nim_peminjam']));
        $bukuId = (int) $validated['buku_id'];

        try {
            DB::transaction(function () use ($validated, $nim, $bukuId) {
                // Concurrency Locking dengan lockForUpdate()
                $buku = Buku::where('id', $bukuId)->lockForUpdate()->firstOrFail();

                // 1. Cek stok buku > 0
                if ($buku->stok < 1) {
                    throw new \RuntimeException("Transaksi ditolak! Stok untuk buku '{$buku->judul}' telah habis (stok = 0).");
                }

                // 2. Mencegah NIM yang sama meminjam buku yang sama jika belum dikembalikan
                $existingActiveBorrow = Peminjaman::where('buku_id', $bukuId)
                    ->where('nim_peminjam', $nim)
                    ->where('status', 'dipinjam')
                    ->lockForUpdate()
                    ->exists();

                if ($existingActiveBorrow) {
                    throw new \RuntimeException("NIM {$nim} masih meminjam buku ini dan belum mengembalikannya!");
                }

                // 3. Kurangi stok secara atomik
                $buku->decrement('stok');

                // 4. Catat transaksi peminjaman
                Peminjaman::create([
                    'kode_pinjam'         => 'PINJAM-' . strtoupper(Str::random(6)) . '-' . time(),
                    'buku_id'             => $buku->id,
                    'nim_peminjam'        => $nim,
                    'nama_peminjam'       => $validated['nama_peminjam'],
                    'tanggal_pinjam'      => $validated['tanggal_pinjam'],
                    'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                    'status'              => 'dipinjam',
                ]);
            }, 3);

            // Simpan riwayat peminjam terakhir di session
            session(['last_borrower' => [
                'nim'  => $nim,
                'nama' => $validated['nama_peminjam'],
                'time' => now()->format('d M Y, H:i') . ' WIB'
            ]]);

            return redirect()->route('buku.index')
                ->with('success', 'Buku berhasil dipinjam secara atomik dan stok telah diperbarui!');
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            Log::error('[DB EXCEPTION PEMINJAMAN]: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem basis data: ' . $e->getMessage());
        }
    }

    /**
     * Transaksi Pengembalian Buku Berbasis ACID (DB::transaction & lockForUpdate)
     */
    public function returnBook(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Lock transaksi peminjaman
                $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($peminjaman->status === 'dikembalikan') {
                    throw new \RuntimeException("Buku ini telah dikembalikan sebelumnya.");
                }

                // Lock record buku terkait
                $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->firstOrFail();

                // Ubah status dan catat tanggal kembali
                $peminjaman->update([
                    'status'          => 'dikembalikan',
                    'tanggal_kembali' => now()->toDateString(),
                ]);

                // Kembalikan stok buku
                $buku->increment('stok');
            }, 3);

            return back()->with('success', 'Buku berhasil dikembalikan secara atomik dan stok telah dipulihkan!');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            Log::error('[DB EXCEPTION PENGEMBALIAN]: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pengembalian buku: ' . $e->getMessage());
        }
    }
}
