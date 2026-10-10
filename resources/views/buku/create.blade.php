<x-layout>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Tambah Buku Baru</h2>
                    <p class="text-muted small mb-0">Lengkapi data pustaka untuk menambah koleksi buku perpustakaan.</p>
                </div>
                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card card-custom shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('buku.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- ISBN -->
                            <div class="col-md-6">
                                <label for="isbn" class="form-label fw-semibold">Nomor ISBN <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" 
                                       name="isbn" 
                                       value="{{ old('isbn') }}" 
                                       placeholder="Contoh: 978-602-01-0001-1" 
                                       required>
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Kode unik standar internasional (ISBN).</div>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-6">
                                <label for="kategori_id" class="form-label fw-semibold">Kategori Buku <span class="text-danger">*</span></label>
                                <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                        id="kategori_id" 
                                        name="kategori_id" 
                                        required>
                                    <option value="" disabled {{ old('kategori_id') ? '' : 'selected' }}>Pilih Kategori Bidang Pustaka...</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                            [{{ $kategori->kode_kategori }}] {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Judul Buku -->
                            <div class="col-12">
                                <label for="judul" class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('judul') is-invalid @enderror" 
                                       id="judul" 
                                       name="judul" 
                                       value="{{ old('judul') }}" 
                                       placeholder="Contoh: Prinsip Rekayasa Perangkat Lunak Modern" 
                                       required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Minimal 5 karakter, maksimal 200 karakter.</div>
                                @enderror
                            </div>

                            <!-- Penulis -->
                            <div class="col-md-6">
                                <label for="penulis" class="form-label fw-semibold">Nama Penulis <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('penulis') is-invalid @enderror" 
                                       id="penulis" 
                                       name="penulis" 
                                       value="{{ old('penulis') }}" 
                                       placeholder="Contoh: Ian Sommerville" 
                                       required>
                                @error('penulis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Penerbit -->
                            <div class="col-md-6">
                                <label for="penerbit" class="form-label fw-semibold">Penerbit <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('penerbit') is-invalid @enderror" 
                                       id="penerbit" 
                                       name="penerbit" 
                                       value="{{ old('penerbit') }}" 
                                       placeholder="Contoh: Pearson Education" 
                                       required>
                                @error('penerbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tahun Terbit -->
                            <div class="col-md-6">
                                <label for="tahun_terbit" class="form-label fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('tahun_terbit') is-invalid @enderror" 
                                       id="tahun_terbit" 
                                       name="tahun_terbit" 
                                       value="{{ old('tahun_terbit', date('Y')) }}" 
                                       min="1900" 
                                       max="{{ date('Y') + 1 }}" 
                                       required>
                                @error('tahun_terbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Format 4 digit angka tahun (1900 - {{ date('Y') + 1 }}).</div>
                                @enderror
                            </div>

                            <!-- Stok Fisik -->
                            <div class="col-md-6">
                                <label for="stok" class="form-label fw-semibold">Jumlah Stok Buku <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('stok') is-invalid @enderror" 
                                       id="stok" 
                                       name="stok" 
                                       value="{{ old('stok', 0) }}" 
                                       min="0" 
                                       required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Jumlah eksemplar fisik yang tersedia (minimal 0).</div>
                                @enderror
                            </div>

                            <!-- Sinopsis / Ringkasan -->
                            <div class="col-12">
                                <label for="sinopsis" class="form-label fw-semibold">Sinopsis / Ringkasan Buku</label>
                                <textarea class="form-control @error('sinopsis') is-invalid @enderror" 
                                          id="sinopsis" 
                                          name="sinopsis" 
                                          rows="4" 
                                          placeholder="Tuliskan ulasan ringkas mengenai isi atau topik utama buku ini...">{{ old('sinopsis') }}</textarea>
                                @error('sinopsis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
                            <a href="{{ route('buku.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-del">
                                <i class="bi bi-save me-1"></i> Simpan Buku ke Katalog
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>