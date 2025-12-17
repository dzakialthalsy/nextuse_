<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MelihatBerandaController;
use App\Http\Controllers\MencariDanMemfilterBarangController;
use App\Http\Controllers\MendaftarkanAkunOrganisasiController;
use App\Http\Controllers\MelakukanLoginController;
use App\Http\Controllers\Auth\MelakukanLogoutController;
use App\Http\Controllers\MelihatProfilController;
use App\Http\Controllers\Donatur\MempostingBarangController;
use App\Http\Controllers\Donatur\MengelolaProfilController;
use App\Http\Controllers\Donatur\MelihatPermohonanMasukController;
use App\Http\Controllers\Donatur\MenentukanPenerimaController;
use App\Http\Controllers\Pemohon\MengunduhTemplateSuratController;
use App\Http\Controllers\Pemohon\MengajukanPermohonanController;
use App\Http\Controllers\Pemohon\MelihatPermohonanYangDiajukanController;
use App\Http\Controllers\Pemohon\MelihatDetailBarangController;
use App\Http\Controllers\Admin\MenghapusUnggahanBarangController;
use App\Http\Controllers\Admin\MenghapusAkunOrganisasiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Use Case: Melihat beranda (Pengunjung, Donatur, Pemohon)
Route::get('/', [MelihatBerandaController::class, 'index'])->name('beranda');

// Use Case: Mencari dan memfilter barang (Pemohon)
Route::get('/search', [MencariDanMemfilterBarangController::class, 'index'])->name('search');
Route::get('/filter', [MencariDanMemfilterBarangController::class, 'index'])->name('filter');

Route::get('/home', function () {
    return redirect()->route('beranda');
})->name('home');

// Use Case: Mendaftarkan akun organisasi (Pengunjung)
Route::get('/registrasi', [MendaftarkanAkunOrganisasiController::class, 'index'])->name('registrasi');
Route::post('/registrasi', [MendaftarkanAkunOrganisasiController::class, 'store'])->name('registrasi.store');

// Use Case: Melakukan login (Pengunjung)
Route::get('/login', [MelakukanLoginController::class, 'index'])->name('login');
Route::post('/login', [MelakukanLoginController::class, 'authenticate'])->name('login.authenticate');

// Use Case: Melakukan logout (Pengunjung/User)
Route::post('/logout', MelakukanLogoutController::class)->name('logout');

// Use Case: Melihat syarat ketentuan (dipindahkan ke MelihatDetailBarangController)
Route::get('/syarat-ketentuan', [MelihatDetailBarangController::class, 'syaratKetentuan'])->name('syarat-ketentuan');

// Use Case: Memposting barang (Donatur) & Inventory Management
Route::get('/inventory', [MempostingBarangController::class, 'index'])->name('inventory.index');
Route::get('/items/{id}/edit', [MempostingBarangController::class, 'edit'])->name('items.edit');
Route::put('/items/{id}', [MempostingBarangController::class, 'update'])->name('items.update');
Route::delete('/items/{id}', [MempostingBarangController::class, 'destroy'])->name('items.destroy');
Route::post('/items/update-status', [MempostingBarangController::class, 'updateStatus'])->name('items.update-status');
Route::post('/items/bulk-delete', [MempostingBarangController::class, 'bulkDestroy'])->name('items.bulk-delete');

Route::get('/post-item', [MempostingBarangController::class, 'create'])->name('post-item.create');
Route::post('/post-item', [MempostingBarangController::class, 'store'])->name('post-item.store');

// Use Case: Mengajukan permohonan (Pemohon)
Route::get('/items/{item}/permohonan', [MengajukanPermohonanController::class, 'create'])->name('item-requests.create');
Route::post('/items/{item}/permohonan', [MengajukanPermohonanController::class, 'store'])->name('item-requests.store');

// Use Case: Melihat detail barang (Pemohon)
Route::get('/items/{id}', [MelihatDetailBarangController::class, 'show'])->name('items.show');

// Use Case: Mengunduh template surat (Pemohon)
Route::get('/permohonan/surat-kuasa-template', MengunduhTemplateSuratController::class)->name('item-requests.template');

// Use Case: Melihat permohonan yang diajukan (Pemohon)
Route::get('/permohonan', MelihatPermohonanYangDiajukanController::class)->name('item-requests.index');

// Use Case: Melihat permohonan masuk (Donatur)
Route::get('/donatur/permohonan', [MelihatPermohonanMasukController::class, 'index'])->name('donatur.requests.index');

// Use Case: Menentukan penerima (Donatur)
Route::post('/donatur/permohonan/{itemRequest}/keputusan', [MenentukanPenerimaController::class, 'keputusan'])->name('donatur.requests.decide');
Route::post('/donatur/permohonan/{itemRequest}/tetapkan', [MenentukanPenerimaController::class, 'tetapkan'])->name('donatur.requests.assign');

// Use Case: Melihat profil (Pengunjung/Donatur/Pemohon)
Route::get('/seller/{organization}', MelihatProfilController::class)->name('seller.profile.show');
Route::get('/profile/organization/{organization}', MelihatProfilController::class)->name('profile.public');

// Admin Use Cases: Menghapus akun organisasi, Menghapus unggahan barang
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard untuk melihat data (Items & Users)
    Route::get('/mengelola-data-barang', [MenghapusUnggahanBarangController::class, 'index'])->name('mengelola-data.index');
    
    // Use Case: Menghapus akun organisasi
    Route::delete('/mengelola-data-barang/users/{organization}', [MenghapusAkunOrganisasiController::class, 'destroy'])->name('mengelola-data.users.destroy');
    
    // Use Case: Menghapus unggahan barang
    Route::delete('/mengelola-data-barang/items/{item}', [MenghapusUnggahanBarangController::class, 'destroy'])->name('mengelola-data.items.destroy');

    // Use Case: Melihat dokumen pemohon (Admin)
    Route::get('/dokumen-pemohon/{id}', [App\Http\Controllers\Admin\MelihatDokumenPemohonController::class, 'show'])->name('dokumen.show');
});

// Use Case: Mengelola profil (Donatur)
Route::controller(MengelolaProfilController::class)->group(function () {
    Route::get('/profile', 'index')->name('profile.index');
    Route::get('/profile/{profile}/edit', 'edit')->name('profile.edit');
    Route::put('/profile/{profile}', 'update')->name('profile.update');
    Route::delete('/profile/{profile}', 'destroy')->name('profile.destroy');
});
