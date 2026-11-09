<x-layout title="Login Pustakawan">
    <div class="row justify-content-center py-4">
        <div class="col-md-5">
            <div class="card card-custom shadow-sm border-0">
                <div class="card-header bg-white py-4 text-center border-bottom">
                    <span class="badge bg-purple-subtle text-primary rounded-pill p-3 mb-2" style="background-color: var(--del-purple-light); color: var(--del-purple) !important;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </span>
                    <h3 class="fw-bold mb-1" style="color: var(--del-purple);">Portal Pustakawan</h3>
                    <p class="text-muted small mb-0">Masuk untuk mengelola inventaris buku & transaksi sirkulasi</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Alamat Email Pustakawan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', Cookie::get('del_last_user_email', 'pustakawan@del.ac.id')) }}" 
                                       placeholder="nama@del.ac.id" 
                                       required 
                                       autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Masukkan password akun Anda" 
                                       value="password123"
                                       required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                            <label class="form-check-label text-muted small" for="remember">
                                Ingat saya pada peramban ini (Remember Me)
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-del w-100 py-2 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                        </button>
                    </form>

                    <div class="alert alert-info border-0 mt-4 mb-0 small">
                        <strong><i class="bi bi-info-circle-fill me-1"></i> Kredensial Uji Praktikum:</strong><br>
                        Email: <code>pustakawan@del.ac.id</code><br>
                        Password: <code>password123</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
