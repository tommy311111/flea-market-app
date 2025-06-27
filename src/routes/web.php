<?php

use App\Http\Controllers\Item\CommentController;
use App\Http\Controllers\Item\ItemController;
use App\Http\Controllers\Item\LikeController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// --- 会員登録関連（ログイン前） ---
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// 認証済ユーザーがアクセスできるルート（edit画面など）とは別に定義
Route::get('/email/verify', function () {
    return view('auth.verify'); // ここが verify.blade.php を返す
})->middleware('auth')->name('verification.notice');

// 認証メール内のリンクから来たときの処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証を完了
    return redirect('/mypage/profile'); // 認証後に遷移したい画面に変更
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの再送
Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// --- 商品関連 ---
Route::get('/', [ItemController::class, 'index'])->name('items.index'); // PG01, PG02
Route::get('/search', [ItemController::class, 'index']); // 検索フォーム
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show'); // PG05

Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create'); // PG08
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
});

// --- いいね機能 ---
Route::post('/items/{item}/like', [LikeController::class, 'toggle'])
    ->middleware('auth')
    ->name('items.like');

// --- コメント機能 ---
Route::middleware(['auth'])->group(function () {
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comment.store');
});

// --- 購入機能 ---
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show'); // PG06
    Route::post('/purchase/{item}/save-payment-method', [PurchaseController::class, 'savePaymentMethod'])->name('purchase.savePaymentMethod');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit'); // PG07
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

// --- プロフィール機能 ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index'); // PG09, PG11, PG12
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // PG10
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// --- ログイン関連 ---
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
