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
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ChatController;

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/search', [ItemController::class, 'index']);
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
    Route::post('/orders/{order}/rating', [RatingController::class, 'store'])->name('rating.store');
});

Route::post('/items/{item}/like', [LikeController::class, 'toggle'])
    ->middleware('auth')
    ->name('items.like');

Route::middleware(['auth'])->group(function () {
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comment.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}/save-payment-method', [PurchaseController::class, 'savePaymentMethod'])->name('purchase.savePaymentMethod');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/items/{item}/start-order', [ChatController::class, 'start'])->name('orders.start');
    Route::get('/orders/{order}/chats', [ChatController::class, 'show'])->name('chats.show');
    Route::post('/orders/{order}/chats', [ChatController::class, 'store'])->name('chats.store');
    Route::get('/chats/{chat}/edit', [ChatController::class, 'edit'])->name('chats.edit');
    Route::put('/chats/{chat}', [ChatController::class, 'update'])->name('chats.update');
    Route::delete('/chats/{chat}', [ChatController::class, 'destroy'])->name('chats.destroy');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
