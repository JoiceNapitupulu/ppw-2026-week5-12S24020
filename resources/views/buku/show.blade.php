<x-layout>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Detail Informasi Buku</h2>
                    <p class="text-muted small mb-0">Informasi komprehensif pustaka dan relasi klasifikasi bidangnya.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <a href="{{ route('buku.edit', $buku) }}" class="btn btn-outline-del">
                        <i class="bi bi-pencil-square me-1"></i> Sunting
                    </a>
                </div>
            </div>

            <div class="card card-custom shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="badge badge-del px-3 py-2">
                        <i class="bi bi-tag-fill me-1"></i>{{ $buku->kategori->nama_kategori }}
                    </span>
                    <span class="text-muted small">ISBN: <strong class="text-dark font-monospace">{{ $buku->isbn }}</strong></span>
                </div>
                <div class="card-body p-4">
                    <h3 class="fw-bold text-dark mb-3">{{ $buku->judul }}</h3>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Penulis</small>
                                <strong class="text-dark fs-6">{{ $buku->penulis }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Penerbit</small>
                                <strong class="text-dark fs-6">{{ $buku->penerbit }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Tahun Terbit</small>
                                <strong class="text-dark fs-6">{{ $buku->tahun_terbit }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Status Ketersediaan</small>
                                @if($buku->stok > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-6">
                                        <i class="bi bi-check-circle me-1"></i> Tersedia ({{ $buku->stok }} Eksemplar)
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6">
                                        <i class="bi bi-x-circle me-1"></i> Stok Habis
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-2">Sinopsis / Ringkasan Pustaka</h5>
                    <div class="p-3 rounded-3 bg-white border text-secondary" style="line-height: 1.8;">
                        @if($buku->sinopsis)
                            {{ $buku->sinopsis }}
                        @else
                            <em class="text-muted">Tidak ada sinopsis yang dilampirkan untuk buku ini.</em>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Ditambahkan pada: {{ $buku->created_at->format('d M Y, H:i') }}</small>
                    <form action="{{ route('buku.destroy', $buku) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini dari katalog?');" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Hapus Buku Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>