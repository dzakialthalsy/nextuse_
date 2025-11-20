<?php

use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ChatMessageStoreController;
use App\Http\Controllers\ChatMessageDeleteController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\Item\BulkDestroyController as ItemBulkDestroyController;
use App\Http\Controllers\Item\DestroyController as ItemDestroyController;
use App\Http\Controllers\Item\EditController as ItemEditController;
use App\Http\Controllers\Item\ShowController as ItemShowController;
use App\Http\Controllers\Item\UpdateController as ItemUpdateController;
use App\Http\Controllers\Item\UpdateStatusController as ItemUpdateStatusController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PostItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\ReportItemController;
use App\Http\Controllers\ReportUserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SyaratKetentuanController;
use App\Http\Controllers\CreateReviewController;
use App\Http\Controllers\ReadReviewController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\MengelolaDataBarang\DestroyItemController as AdminMengelolaDataBarangDestroyItemController;
use App\Http\Controllers\Admin\MengelolaDataBarang\DestroyUserController as AdminMengelolaDataBarangDestroyUserController;
use App\Http\Controllers\Admin\MengelolaDataBarang\IndexController as AdminMengelolaDataBarangIndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SearchController::class, 'index'])->name('beranda');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/filter', [FilterController::class, 'index'])->name('filter');

Route::get('/home', function () {
    return redirect()->route('beranda');
})->name('home');

Route::get('/registrasi', [RegistrasiController::class, 'index'])->name('registrasi');
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/syarat-ketentuan', [SyaratKetentuanController::class, 'index'])->name('syarat-ketentuan');

Route::post('/registrasi', [RegistrasiController::class, 'store'])->name('registrasi.store');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', LogoutController::class)->name('logout');

Route::get('/inventory', [ItemController::class, 'index'])->name('inventory.index');
Route::get('/items/{id}/edit', ItemEditController::class)->name('items.edit');
Route::put('/items/{id}', ItemUpdateController::class)->name('items.update');
Route::delete('/items/{id}', ItemDestroyController::class)->name('items.destroy');
Route::post('/items/update-status', ItemUpdateStatusController::class)->name('items.update-status');
Route::post('/items/bulk-delete', ItemBulkDestroyController::class)->name('items.bulk-delete');
Route::get('/items/{id}', ItemShowController::class)->name('items.show');

Route::get('/post-item', [PostItemController::class, 'create'])->name('post-item.create');
Route::post('/post-item', [PostItemController::class, 'store'])->name('post-item.store');

Route::get('/report-user', [ReportUserController::class, 'create'])->name('report-user.create');
Route::post('/report-user', [ReportUserController::class, 'store'])->name('report-user.store');

Route::get('/report-item', [ReportItemController::class, 'create'])->name('report-item.create');
Route::post('/report-item', [ReportItemController::class, 'store'])->name('report-item.store');

Route::get('/post-item', [PostItemController::class, 'create'])->name('post-item.create');
Route::post('/post-item', [PostItemController::class, 'store'])->name('post-item.store');

Route::get('/review/create', [CreateReviewController::class, 'create'])->name('review.create');
Route::post('/review/create', [CreateReviewController::class, 'store'])->name('review.create.store');
Route::get('/review', [ReadReviewController::class, 'show'])->name('review.read');

Route::get('/admin/review/{type}/{id}', [AdminReviewController::class, 'show'])->name('admin.review.show');
Route::put('/admin/review/{type}/{id}', [AdminReviewController::class, 'update'])->name('admin.review.update');
Route::get('/admin/tinjau', [AdminReviewController::class, 'redirectToFirst'])->name('admin.tinjau');
Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications');
Route::get('/dukung-nextuse', [DonationController::class, 'index'])->name('dukung-nextuse');
Route::post('/dukung-nextuse', [DonationController::class, 'store'])->name('dukung-nextuse.store');

Route::get('/seller/{organization}', SellerProfileController::class)->name('seller.profile.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/mengelola-data-barang', AdminMengelolaDataBarangIndexController::class)->name('mengelola-data.index');
    Route::delete('/mengelola-data-barang/users/{organization}', AdminMengelolaDataBarangDestroyUserController::class)->name('mengelola-data.users.destroy');
    Route::delete('/mengelola-data-barang/items/{item}', AdminMengelolaDataBarangDestroyItemController::class)->name('mengelola-data.items.destroy');
});

// Chat Routes - Read Operations
Route::controller(ChatMessageController::class)->group(function () {
    Route::get('/chat', 'index')->name('chat.index');
    Route::get('/chat/{conversationId}', 'show')->name('chat.show');
    Route::get('/chat/notifications', 'notifications')->name('chat.notifications');
});

// Chat Routes - Create Operations
Route::controller(ChatMessageStoreController::class)->group(function () {
    Route::post('/chat/start', 'start')->name('chat.start');
    Route::post('/chat/messages', 'store')->name('chat.store');
});

// Chat Routes - Delete Operations
Route::controller(ChatMessageDeleteController::class)->group(function () {
    Route::delete('/chat/messages/{chatMessage}', 'destroy')->name('chat.destroy');
});

Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'index')->name('profile.index');
    Route::get('/profile/{profile}/edit', 'edit')->name('profile.edit');
    Route::put('/profile/{profile}', 'update')->name('profile.update');
    Route::delete('/profile/{profile}', 'destroy')->name('profile.destroy');
});
Route::get('/profile/organization/{organization}', [ProfileController::class, 'showPublic'])->name('profile.public');