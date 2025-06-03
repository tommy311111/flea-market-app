<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Public\HomeController;

// --- 会員登録関連（ログイン前） ---
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::get('/', [HomeController::class, 'index'])->name('home');

// --- 登録直後のプロフィール入力（ログイン後になる） ---
Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile/setup', [RegisterController::class, 'showProfileForm'])->name('register.profile.form');
    Route::post('/mypage/profile/setup', [RegisterController::class, 'storeProfile'])->name('register.profile');

    // ログイン後のプロフィール編集（マイページ）URLが同じだから上手く動かないかも⇒やっぱ駄目だった今だけ変えとく
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
});

// --- ログイン関連 ---
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
