<x-layout title="Sirkulasi Peminjaman Buku">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Riwayat Sirkulasi Peminjaman</h2>
            <p class="text-muted mb-0 small">Audit transaksi peminjaman & pengembalian buku berbasis ACID (Concurrency Locking)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-book me-1"></i> Katalog Buku
            </a>
            <a href="{{ route('peminjaman.create') }}" class="btn btn-del shadow-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> Catat Peminjaman Baru
            </a>
        </div>
    </div>

    <!-- Filter Status Tabs -->
    <ul class="nav nav-pills mb-3 gap-2">
        <li class="nav-item">
            <a class="nav-link {{ !request('status') ? 'active bg-dark' : 'bg-white text-dark border' }}" href="{{ route('peminjaman.index') }}">
                Semua Transaksi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'dipinjam' ? 'active bg-warning text-dark fw-semibold' : 'bg-white text-dark border' }}" href="{{ route('peminjaman.index', ['status' => 'dipinjam']) }}">
                <i class="bi bi-hourglass-split me-1"></i> Sedang Dipinjam
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'dikembalikan' ? 'active bg-success' : 'bg-white text-dark border' }}" href="{{ route('peminjaman.index', ['status' => 'dikembalikan']) }}">
                <i class="bi bi-check-all me-1"></i> Telah Dikembalikan
            </a>
        </li>
    </ul>

    <div class="card card-custom shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Kode Transaksi</th>
                            <th>Buku Dipinjam</th>
                            <th>Identitas Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamans as $pinjam)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark font-monospace border px-2 py-1">
                                        {{ $pinjam->kode_pinjam }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('buku.show', $pinjam->buku) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                        {{ $pinjam->buku->judul }}
                                    </a>
                                    <small class="text-muted font-monospace">ISBN: {{ $pinjam->buku->isbn }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $pinjam->nama_peminjam }}</div>
                                    <span class="badge badge-del font-monospace px-2">{{ $pinjam->nim_peminjam }}</span>
                                </td>
                                <td class="text-muted small">
                                    {{ $pinjam->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="small">
                                    <span class="{{ ($pinjam->status === 'dipinjam' && $pinjam->tanggal_jatuh_tempo < now()) ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ $pinjam->tanggal_jatuh_tempo->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($pinjam->status === 'dipinjam')
                                        <span class="badge bg-warning text-dark px-2 py-1">
                                            <i class="bi bi-arrow-repeat me-1"></i> Dipinjam
                                        </span>
                                    @else
                                        <span class="badge bg-success px-2 py-1">
                                            <i class="bi bi-check-circle me-1"></i> Dikembalikan
                                        </span>
                                        <div class="small text-muted mt-1 font-monospace" style="font-size: 0.75rem;">
                                            {{ $pinjam->tanggal_kembali ? $pinjam->tanggal_kembali->format('d/m/Y') : '-' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($pinjam->status === 'dipinjam')
                                        <form action="{{ route('peminjaman.kembali', $pinjam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Proses pengembalian buku \'{{ $pinjam->buku->judul }}\' dan pulihkan stok?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-arrow-return-left me-1"></i> Kembalikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small"><i class="bi bi-check2"></i> Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <p class="mb-0 fw-semibold">Belum ada transaksi peminjaman.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($peminjamans->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted">
                    Menampilkan {{ $peminjamans->firstItem() }} - {{ $peminjamans->lastItem() }} dari {{ $peminjamans->total() }} catatan
                </span>
                <div>
                    {{ $peminjamans->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</x-layout>
