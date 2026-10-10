# Sistem Informasi Perpustakaan Kampus Del (SIPUS-Del)
**Tugas Mandiri Praktikum Pemrograman & Pengujian Web (12S3101) — Minggu 05**  
**Institut Teknologi Del**

Proyek ini mengimplementasikan sistem informasi pengelolaan perpustakaan kampus berbasis **Laravel 11.x** dan **PostgreSQL** berlandaskan pola arsitektur **Model-View-Controller (MVC)**, **Eloquent ORM (Relasi One-to-Many & Eager Loading)**, **Validasi Form Server-Side**, **Blade Component Engine**, dan **SQL Query Performance Audit**.

---

## 📌 Spesifikasi Teknis & Fitur (Checklist Persyaratan)

### 1. Skema Data & Migrations (Bobot: 20%)
- **Tabel `kategoris`**:
  - `id`: Primary key bigint unsigned auto-increment.
  - `kode_kategori`: String (max 20), unik.
  - `nama_kategori`: String (max 100).
  - `timestamps`: `created_at` & `updated_at`.
- **Tabel `bukus`**:
  - `id`: Primary key bigint unsigned auto-increment.
  - `isbn`: String (max 20), unik.
  - `judul`: String (max 200).
  - `penulis`: String (max 150).
  - `penerbit`: String (max 150).
  - `tahun_terbit`: Year (4 digit).
  - `kategori_id`: Foreign key ke tabel `kategoris` dengan opsi `cascadeOnDelete()`.
  - `stok`: Integer, default 0.
  - `sinopsis`: Text, nullable.
  - `timestamps`: `created_at` & `updated_at`.

### 2. Model Eloquent & Relasi (Bobot: 20%)
- **Relasi Kardinalitas One-to-Many**:
  - Model `Kategori`: Relasi `hasMany(Buku::class)` via method `bukus()`.
  - Model `Buku`: Relasi `belongsTo(Kategori::class)` via method `kategori()`.
- **Proteksi Mass Assignment**: Atribut yang dapat diisi diamankan melalui properti `$fillable`.
- **Mitigasi N+1 Problem via Eager Loading**: Pemanggilan `Buku::with('kategori')->latest()->paginate(10)` pada katalog buku memastikan relasi dimuat secara efisien dalam 2 kueri SQL terpadu.

### 3. Routing RESTful & Resource Controller (Bobot: 20%)
- Mendaftarkan rute resource baku pada `routes/web.php`:
  ```php
  Route::resource('kategori', KategoriController::class);
  Route::resource('buku', BukuController::class);
  ```
- Mengimplementasikan 7 aksi CRUD baku pada `BukuController` dan `KategoriController`:
  - `index`: Menampilkan daftar koleksi dengan eager loading & paginasi.
  - `create`: Menampilkan form penambahan data.
  - `store`: Menyimpan data baru dengan validasi ketat.
  - `show`: Menampilkan detail data spesifik via *Implicit Route Model Binding*.
  - `edit`: Menampilkan form penyuntingan data.
  - `update`: Memperbarui data yang ada dengan validasi ketat.
  - `destroy`: Menghapus entitas data dari basis data.

### 4. Validasi Form Server-Side & CSRF (Bobot: 15%)
- **Validasi Server-Side Ketat via `$request->validate()`**:
  - `isbn`: Wajib, unik pada tabel `bukus` (kecuali ID buku bersangkutan saat pembaruan).
  - `judul`: Wajib, minimal 5 karakter, maksimal 200 karakter.
  - `penulis` & `penerbit`: Wajib, minimal 3 karakter, maksimal 150 karakter.
  - `tahun_terbit`: Wajib, 4 digit angka, antara 1900 hingga tahun berikutnya.
  - `kategori_id`: Wajib, harus valid terdaftar pada tabel `kategoris`.
  - `stok`: Wajib, bilangan bulat non-negatif (minimal 0).
- **Keamanan Formulir Web**: Seluruh form formulir menyertakan token proteksi `@csrf` dan method spoofing `@method('PUT')` serta `@method('DELETE')`.

### 5. Blade Templating & Desain UI (Bobot: 15%)
- Menggunakan master layout `<x-layout>` bertema resmi **Del Purple (`#4C1D95`)** dengan font modern (*Plus Jakarta Sans* & *Instrument Serif*).
- Flash session messages interaktif untuk notifikasi sukses (`session('success')`) dan galat.
- Penanganan tampilan data kosong yang aman dan elegan menggunakan direktif `@forelse ... @empty`.
- Paginasi Bootstrap 5 yang berfungsi aktif.

### 6. Database Seeders & Mock Data Realistis (Bobot: 10%)
- Menyediakan Seeder dan Factory untuk menghasilkan 6 kategori dan 30 koleksi buku referensi teknologi informasi (Computer Science / Software Engineering).
- Perintah eksekusi:
  ```bash
  php artisan migrate:fresh --seed
  ```

### 7. Audit Kueri Kinerja SQL (Mitigasi N+1 Problem)
Telah dipasang listener `DB::listen` pada `AppServiceProvider.php` untuk membuktikan efektivitas Eager Loading.

**Bukti Log Audit SQL dari `storage/logs/laravel.log`:**
```log
[2026-10-10 10:52:11] local.INFO: [SQL AUDIT] (50.30 ms) select count(*) as aggregate from "bukus"
[2026-10-10 10:52:11] local.INFO: [SQL AUDIT] (1.23 ms) select * from "bukus" order by "created_at" desc limit 10 offset 0
[2026-10-10 10:52:11] local.INFO: [SQL AUDIT] (3.66 ms) select * from "kategoris" where "kategoris"."id" in (1, 2, 3, 4, 5, 6)
```

> **Hasil Audit:** 
> Pengambilan seluruh katalog buku dan data relasi kategorinya terbukti hanya mengeksekusi **2 kueri data terpadu** (ditambah 1 kueri agregat pagination), meniadakan bahaya *N+1 Query Problem*.

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

1. **Salin Berkas Environment & Instal Dependensi**:
   ```bash
   cp .env.example .env
   composer install
   npm install
   ```

2. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

3. **Konfigurasi Database di `.env` (PostgreSQL)**:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=sipus_del
   DB_USERNAME=postgres
   DB_PASSWORD=123456
   ```

4. **Migrasi & Jalankan Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui peramban: `http://localhost:8000/buku`.