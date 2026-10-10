<x-layout title="Kategori Buku">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Kategori Buku Perpustakaan</h2>
            <p class="text-muted mb-0 small">Sistem Informasi Perpustakaan (SIPUS-Del)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('buku.index') }}" class="btn btn-outline-del">
                <i class="bi bi-book-half me-1"></i> Katalog Buku
            </a>
            <a href="{{ route('kategori.create') }}" class="btn btn-del shadow-sm">
                <i class="bi bi-folder-plus me-1"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th style="width: 180px;">Kode Kategori</th>
                        <th>Nama Kategori</th>
                        <th class="text-center" style="width: 160px;">Jumlah Buku</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $index => $kat)
                        <tr>
                            <td class="text-center fw-semibold text-secondary">
                                {{ $kategoris->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge badge-del px-2 py-1">
                                    {{ $kat->kode_kategori }}
                                </span>
                            </td>
                            <td class="fw-medium text-dark">
                                {{ $kat->nama_kategori }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1">
                                    {{ $kat->bukus_count }} Koleksi
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('kategori.show', $kat) }}" class="btn btn-outline-secondary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('kategori.edit', $kat) }}" class="btn btn-outline-primary" title="Sunting">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('kategori.destroy', $kat) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $kat->nama_kategori }}? Seluruh buku dalam kategori ini akan ikut terhapus!');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                <strong>Belum ada kategori buku yang tersimpan.</strong>
                                <p class="small mb-3 text-muted">Silakan tambahkan data kategori baru terlebih dahulu.</p>
                                <a href="{{ route('kategori.create') }}" class="btn btn-sm btn-del">
                                    <i class="bi bi-folder-plus me-1"></i> Tambah Kategori Baru
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kategoris->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                {{ $kategoris->links() }}
            </div>
        @endif
    </div>
</x-layout>