<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/approvals', [DashboardController::class, 'adminApprovals'])->name('approvals');
});

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
    Route::get('/requests', [DashboardController::class, 'userRequests'])->name('requests');
});

Route::middleware(['auth', 'role:multimedia_staff'])->prefix('media')->name('media.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'media'])->name('dashboard');
    Route::get('/posts', [DashboardController::class, 'mediaPosts'])->name('posts');
});

require __DIR__.'/auth.php';
