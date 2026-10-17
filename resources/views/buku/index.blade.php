<x-layout title="Katalog Buku">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Katalog Buku Perpustakaan Del</h2>
            <p class="text-muted mb-0 small">Sistem Informasi Perpustakaan (SIPUS-Del) — Manajemen Sirkulasi & ACID Transactions</p>
        </div>
        <div class="d-flex gap-2">
            @auth
                <a href="{{ route('peminjaman.create') }}" class="btn btn-outline-success">
                    <i class="bi bi-arrow-right-circle me-1"></i> Pinjam Buku
                </a>
                <a href="{{ route('kategori.index') }}" class="btn btn-outline-del">
                    <i class="bi bi-tags-fill me-1"></i> Kelola Kategori
                </a>
                <a href="{{ route('buku.create') }}" class="btn btn-del shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Buku Baru
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-del shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login Pustakawan
                </a>
            @endauth
        </div>
    </div>

    <!-- Statistik Query Builder (DB::table) Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Judul Buku</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total_judul']) }}</div>
                    </div>
                    <div class="badge rounded-circle p-3" style="background-color: var(--del-purple-light); color: var(--del-purple);">
                        <i class="bi bi-book-half fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Stok Tersedia</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ number_format($stats['total_stok']) }} <small class="fs-6 text-muted fw-normal">eks</small></div>
                    </div>
                    <div class="badge bg-success-subtle text-success rounded-circle p-3">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Sedang Dipinjam</div>
                        <div class="fs-4 fw-bold text-warning-emphasis mt-1">{{ number_format($stats['total_dipinjam']) }} <small class="fs-6 text-muted fw-normal">eks</small></div>
                    </div>
                    <div class="badge bg-warning-subtle text-warning-emphasis rounded-circle p-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom p-3 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Telah Dikembalikan</div>
                        <div class="fs-4 fw-bold text-primary mt-1">{{ number_format($stats['total_dikembalikan']) }} <small class="fs-6 text-muted fw-normal">eks</small></div>
                    </div>
                    <div class="badge bg-primary-subtle text-primary rounded-circle p-3">
                        <i class="bi bi-arrow-return-left fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Dinamis -->
    <div class="card card-custom p-3 mb-4 bg-white border-0 shadow-sm">
        <form action="{{ route('buku.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari judul, penulis, atau ISBN..." 
                           value="{{ request('search', session('last_book_search', '')) }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="kategori_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-del flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                @if(request('search') || request('kategori_id'))
                    <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Buku -->
    <div class="card card-custom shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 140px;">ISBN</th>
                        <th>Judul Buku</th>
                        <th style="width: 190px;">Kategori</th>
                        <th style="width: 140px;">Penulis</th>
                        <th class="text-center" style="width: 70px;">Tahun</th>
                        <th class="text-center" style="width: 90px;">Stok</th>
                        <th class="text-center" style="width: 110px;">Riwayat Pinjam</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bukus as $index => $buku)
                        <tr>
                            <td class="text-center fw-semibold text-secondary">
                                {{ $bukus->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border px-2 py-1">
                                    {{ $buku->isbn }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $buku->judul }}</div>
                                @if($buku->sinopsis)
                                    <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                        {{ Str::limit($buku->sinopsis, 60) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kategori.show', $buku->kategori) }}" class="badge badge-del text-decoration-none text-wrap text-start px-2 py-1">
                                    {{ $buku->kategori->nama_kategori }}
                                </a>
                            </td>
                            <td class="text-secondary small">{{ $buku->penulis }}</td>
                            <td class="text-center text-secondary small">{{ $buku->tahun_terbit }}</td>
                            <td class="text-center">
                                @if($buku->stok > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        {{ $buku->stok }} eks
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        Habis (0)
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                    {{ $buku->peminjamans_count }}x dipinjam
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('buku.show', $buku) }}" class="btn btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @auth
                                        @if($buku->stok > 0)
                                            <a href="{{ route('peminjaman.create', ['buku_id' => $buku->id]) }}" class="btn btn-outline-success" title="Pinjam Buku">
                                                <i class="bi bi-box-arrow-right"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('buku.edit', $buku) }}" class="btn btn-outline-primary" title="Sunting">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('buku.destroy', $buku) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku {{ $buku->judul }}?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                <strong>Tidak ada data buku yang sesuai filter.</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bukus->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                {{ $bukus->links() }}
            </div>
        @endif
    </div>
</x-layout>
