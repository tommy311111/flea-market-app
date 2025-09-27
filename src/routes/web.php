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
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 認証関連
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // 会員登録
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    // ログイン・ログアウト
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| メール認証
|--------------------------------------------------------------------------
*/
Route::prefix('email')->middleware('auth')->group(function () {
    Route::get('/verify', fn() => view('auth.verify'))->name('verification.notice');
    Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile');
    })->middleware('signed')->name('verification.verify');
    Route::post('/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| 商品関連
|--------------------------------------------------------------------------
*/
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/search', [ItemController::class, 'index'])->name('items.search');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// 出品（要ログイン）
Route::prefix('sell')->middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'create'])->name('items.create');
    Route::post('/', [ItemController::class, 'store'])->name('items.store');
});

// いいね・コメント（要ログイン）
Route::prefix('items')->middleware('auth')->group(function () {
    Route::post('/{item}/like', [LikeController::class, 'toggle'])->name('items.like');
    Route::post('/{item}/comments', [CommentController::class, 'store'])->name('comment.store');
});

/*
|--------------------------------------------------------------------------
| 購入関連（要ログイン）
|--------------------------------------------------------------------------
*/
Route::prefix('purchase')->middleware('auth')->group(function () {
    Route::get('/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/{item}/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::post('/{item}/save-payment-method', [PurchaseController::class, 'savePaymentMethod'])->name('purchase.savePaymentMethod');

    // 配送先住所
    Route::get('/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

/*
|--------------------------------------------------------------------------
| マイページ関連（要ログイン & 認証済み）
|--------------------------------------------------------------------------
*/
Route::prefix('mypage')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
