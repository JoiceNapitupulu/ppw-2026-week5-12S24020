<x-layout title="Form Peminjaman Buku">
    <div class="row justify-content-center py-2">
        <div class="col-lg-7">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Form Transaksi Peminjaman</h2>
                    <p class="text-muted small mb-0">Catat peminjaman buku sirkulasi perpustakaan secara atomik (ACID).</p>
                </div>
                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card card-custom shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('peminjaman.store') }}" method="POST">
                        @csrf

                        <!-- Buku Yang Dipinjam -->
                        <div class="mb-3">
                            <label for="buku_id" class="form-label fw-semibold">Pilih Buku Pustaka <span class="text-danger">*</span></label>
                            <select name="buku_id" id="buku_id" class="form-select @error('buku_id') is-invalid @enderror" required>
                                <option value="" disabled {{ !isset($selectedBuku) ? 'selected' : '' }}>-- Pilih Buku (Hanya Stok Tersedia) --</option>
                                @foreach($availableBukus as $b)
                                    <option value="{{ $b->id }}" {{ (old('buku_id', $selectedBuku->id ?? null) == $b->id) ? 'selected' : '' }}>
                                        {{ $b->judul }} (Sisa Stok: {{ $b->stok }} eks) — [ISBN: {{ $b->isbn }}]
                                    </option>
                                @endforeach
                            </select>
                            @error('buku_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NIM Peminjam -->
                        <div class="mb-3">
                            <label for="nim_peminjam" class="form-label fw-semibold">NIM Mahasiswa Peminjam <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nim_peminjam" 
                                   id="nim_peminjam" 
                                   class="form-control @error('nim_peminjam') is-invalid @enderror" 
                                   value="{{ old('nim_peminjam', session('last_borrower.nim', '')) }}" 
                                   placeholder="Contoh: 12S24020" 
                                   required>
                            @error('nim_peminjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Sistem ACID akan menolak otomatis jika NIM yang sama masih meminjam buku ini.</div>
                        </div>

                        <!-- Nama Lengkap Peminjam -->
                        <div class="mb-3">
                            <label for="nama_peminjam" class="form-label fw-semibold">Nama Lengkap Peminjam <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nama_peminjam" 
                                   id="nama_peminjam" 
                                   class="form-control @error('nama_peminjam') is-invalid @enderror" 
                                   value="{{ old('nama_peminjam', session('last_borrower.nama', '')) }}" 
                                   placeholder="Contoh: Joice Napitupulu" 
                                   required>
                            @error('nama_peminjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Tanggal Pinjam -->
                            <div class="col-md-6">
                                <label for="tanggal_pinjam" class="form-label fw-semibold">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" 
                                       name="tanggal_pinjam" 
                                       id="tanggal_pinjam" 
                                       class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                                       value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" 
                                       required>
                                @error('tanggal_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tanggal Jatuh Tempo -->
                            <div class="col-md-6">
                                <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                                <input type="date" 
                                       name="tanggal_jatuh_tempo" 
                                       id="tanggal_jatuh_tempo" 
                                       class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                                       value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+7 days'))) }}" 
                                       required>
                                @error('tanggal_jatuh_tempo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('buku.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-del">
                                <i class="bi bi-check-circle-fill me-1"></i> Proses Peminjaman (ACID Lock)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
