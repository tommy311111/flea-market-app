<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

// --- 会員登録関連（ログイン前） ---
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// 商品関連
Route::get('/', [ItemController::class, 'index'])->name('items.index'); // PG01, PG02
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show'); // PG05
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create'); // PG08
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
});

// いいね機能
Route::post('/items/{item}/like', [LikeController::class, 'toggle'])->name('likes.toggle');

// コメント機能
Route::middleware(['auth'])->group(function () {
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comment.store');
});

// 購入機能
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show'); // PG06
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit'); // PG07
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});


// --- 登録直後のプロフィール入力（ログイン後になる） ---
Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile/setup', [RegisterController::class, 'showProfileForm'])->name('register.profile.form');
    Route::post('/mypage/profile/setup', [RegisterController::class, 'storeProfile'])->name('register.profile');

    Route::get('/item/{id}', [ItemController::class, 'show']);

    // ログイン後のプロフィール編集（マイページ）URLが同じだから上手く動かないかも⇒やっぱ駄目だった今だけ変えとく
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
});

// --- ログイン関連 ---
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
