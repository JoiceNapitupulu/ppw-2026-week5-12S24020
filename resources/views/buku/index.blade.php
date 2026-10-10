<x-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Katalog Buku Perpustakaan Del</h2>
            <p class="text-muted mb-0 small">Sistem Informasi Perpustakaan (SIPUS-Del)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('kategori.index') }}" class="btn btn-outline-del">
                <i class="bi bi-tags-fill me-1"></i> Kelola Kategori
            </a>
            <a href="{{ route('buku.create') }}" class="btn btn-del shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Buku Baru
            </a>
        </div>
    </div>

    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 140px;">ISBN</th>
                        <th>Judul Buku</th>
                        <th style="width: 150px;">Kategori</th>
                        <th style="width: 150px;">Penulis</th>
                        <th class="text-center" style="width: 80px;">Tahun</th>
                        <th class="text-center" style="width: 90px;">Stok</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
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
                                    <small class="text-muted d-block text-truncate" style="max-width: 320px;">
                                        {{ Str::limit($buku->sinopsis, 60) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kategori.show', $buku->kategori) }}" class="badge badge-del text-decoration-none px-2 py-1">
                                    <i class="bi bi-tag me-1"></i>{{ $buku->kategori->nama_kategori }}
                                </a>
                            </td>
                            <td class="text-secondary small">{{ $buku->penulis }}</td>
                            <td class="text-center text-secondary small">{{ $buku->tahun_terbit }}</td>
                            <td class="text-center">
                                @if($buku->stok > 10)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        {{ $buku->stok }} eks
                                    </span>
                                @elseif($buku->stok > 0)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        {{ $buku->stok }} eks
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        Habis
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('buku.show', $buku) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('buku.edit', $buku) }}" class="btn btn-sm btn-outline-primary" title="Sunting">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('buku.destroy', $buku) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku {{ $buku->judul }}?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                <strong>Katalog buku masih kosong.</strong>
                                <p class="small mb-3 text-muted">Belum ada koleksi pustaka yang didaftarkan ke dalam sistem.</p>
                                <a href="{{ route('buku.create') }}" class="btn btn-sm btn-del">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Koleksi Buku Baru
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bukus->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="text-muted small">
                        Menampilkan {{ $bukus->firstItem() ?? 0 }} sampai {{ $bukus->lastItem() ?? 0 }} dari {{ $bukus->total() }} buku
                    </span>
                    <div>
                        {{ $bukus->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layout>