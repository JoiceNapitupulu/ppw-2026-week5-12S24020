<x-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-del px-2 py-1">{{ $kategori->kode_kategori }}</span>
                <h2 class="fw-bold text-dark mb-0">{{ $kategori->nama_kategori }}</h2>
            </div>
            <p class="text-muted small mb-0">Daftar seluruh buku yang terklasifikasi dalam kategori ini.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-outline-del">
                <i class="bi bi-pencil-square me-1"></i> Sunting
            </a>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-book-half me-2 text-primary" style="color: var(--del-purple) !important;"></i>
                Koleksi Buku Terkait ({{ $kategori->bukus->count() }} Judul)
            </h5>
            <a href="{{ route('buku.create') }}" class="btn btn-sm btn-del">
                <i class="bi bi-plus-lg me-1"></i> Tambah Buku ke Kategori Ini
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th style="width: 150px;">ISBN</th>
                        <th>Judul Buku</th>
                        <th style="width: 180px;">Penulis</th>
                        <th class="text-center" style="width: 100px;">Tahun</th>
                        <th class="text-center" style="width: 90px;">Stok</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori->bukus as $index => $buku)
                        <tr>
                            <td class="text-center text-secondary fw-semibold">{{ $index + 1 }}</td>
                            <td><span class="badge bg-light text-dark font-monospace">{{ $buku->isbn }}</span></td>
                            <td class="fw-medium text-dark">{{ $buku->judul }}</td>
                            <td class="text-muted">{{ $buku->penulis }}</td>
                            <td class="text-center text-secondary">{{ $buku->tahun_terbit }}</td>
                            <td class="text-center">
                                @if($buku->stok > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        {{ $buku->stok }} eks
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        Habis
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('buku.show', $buku) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Buku">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                <strong>Belum ada buku pada kategori ini.</strong>
                                <p class="small mb-3 text-muted">Tambahkan pustaka pertama untuk kategori {{ $kategori->nama_kategori }}.</p>
                                <a href="{{ route('buku.create') }}" class="btn btn-sm btn-del">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Buku Baru
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>