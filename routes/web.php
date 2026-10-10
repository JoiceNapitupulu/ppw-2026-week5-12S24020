<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Route;

// Redirect root ke Katalog Buku
Route::get('/', fn() => redirect()->route('buku.index'));

// SIPUS-Del Resource Routes (7 RESTful Actions)
Route::resource('kategori', KategoriController::class);
Route::resource('buku', BukuController::class);