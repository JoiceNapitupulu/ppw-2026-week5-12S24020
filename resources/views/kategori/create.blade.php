<x-layout>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Tambah Kategori Buku</h2>
                    <p class="text-muted small mb-0">Daftarkan klasifikasi bidang pustaka baru.</p>
                </div>
                <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card card-custom shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('kategori.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="kode_kategori" class="form-label fw-semibold">Kode Kategori <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('kode_kategori') is-invalid @enderror" 
                                   id="kode_kategori" 
                                   name="kode_kategori" 
                                   value="{{ old('kode_kategori') }}" 
                                   placeholder="Contoh: KAT-001" 
                                   required>
                            @error('kode_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Kode unik kategori maksimal 20 karakter.</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   id="nama_kategori" 
                                   name="nama_kategori" 
                                   value="{{ old('nama_kategori') }}" 
                                   placeholder="Contoh: Teknologi Informasi & Pemrograman" 
                                   required>
                            @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Nama klasifikasi bidang pustaka (minimal 3 karakter).</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="{{ route('kategori.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-del">
                                <i class="bi bi-save me-1"></i> Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>