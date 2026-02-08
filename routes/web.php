<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProgramFlowController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\VenueController;
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

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/multimedia', [MultimediaController::class, 'index'])->name('multimedia.index');
    Route::get('/program-flow', [ProgramFlowController::class, 'index'])->name('program-flow.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/approvals', [DashboardController::class, 'adminApprovals'])->name('approvals');
    Route::resource('venues', VenueController::class)->only(['index']);
    Route::resource('participants', ParticipantController::class)->only(['index']);
    Route::resource('reports', ReportController::class)->only(['index']);
    Route::resource('documents', DocumentController::class)->only(['index']);
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::get('/roles/users/{user}/edit', [RolePermissionController::class, 'editUser'])->name('roles.edit-user');
    Route::put('/roles/users/{user}', [RolePermissionController::class, 'updateUser'])->name('roles.update-user');
    Route::get('/roles/role/{role}/edit', [RolePermissionController::class, 'editRole'])->name('roles.edit-role');
    Route::put('/roles/role/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update-role');
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
