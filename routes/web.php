<?php

use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\VenueController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware(['auth'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USERS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Events (normal users)
    Route::resource('events', EventController::class);

    // Nested participants under events
    Route::resource('events.participants', ParticipantController::class);
    Route::get('/events/{event}/participants/export', [ParticipantController::class, 'export'])->name('events.participants.export');

    // Event actions
    Route::post('/events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');
    Route::post('/events/{event}/reject', [EventController::class, 'reject'])->name('events.reject');
    Route::post('/events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');

    // Multimedia
    Route::get('/multimedia', [MultimediaController::class, 'index'])->name('multimedia.index');

    // Media posts (general authenticated access) served by MultimediaController
    Route::get('/media/posts', [MultimediaController::class, 'posts'])->name('media.posts');
    Route::get('/media/posts/{post}', [MultimediaController::class, 'postsShow'])->name('media.posts.show');

    // Comments and reactions (authenticated users)
    Route::post('/media/posts/{post}/comments', [\App\Http\Controllers\PostCommentController::class, 'store'])->name('media.posts.comments.store');
    Route::post('/media/posts/{post}/reactions', [\App\Http\Controllers\PostReactionController::class, 'toggle'])->name('media.posts.reactions.toggle');

    // Event ratings
    Route::post('/events/{event}/ratings', [\App\Http\Controllers\EventRatingController::class, 'store'])->name('events.ratings.store');

    // Multimedia creation/editing protected by permissions (managed via RolePermissionController)
    Route::get('/media/posts/create', [MultimediaController::class, 'postsCreate'])
        ->middleware(['permission:create posts'])->name('media.posts.create');

    Route::post('/media/posts', [MultimediaController::class, 'postsStore'])
        ->middleware(['permission:create posts'])->name('media.posts.store');

    // Deletion is authorized in controller (owner, admin, or 'manage all posts' permission)
    Route::delete('/media/posts/{post}', [MultimediaController::class, 'postsDestroy'])->name('media.posts.destroy');

    // Program Flow
    Route::get('/program-flow', [ProgramFlowController::class, 'index'])->name('program-flow.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');

    // Venue availability endpoint
    Route::get('/venues/{venue}/availability', [VenueController::class, 'availability'])->name('venues.availability');
});


/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Admin dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/approvals', [DashboardController::class, 'adminApprovals'])->name('approvals');

        // Admin Events
        Route::resource('events', EventController::class);

        // Admin event bulk upload
        Route::post('/events/bulk-upload', [EventController::class, 'bulkUpload'])
            ->name('events.bulk-upload');

        // Admin event actions
        Route::post('/events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');
        Route::post('/events/{event}/reject', [EventController::class, 'reject'])->name('events.reject');
        Route::post('/events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');

        // Admin Modules
        Route::resource('venues', VenueController::class);
        
        // FIX: Prioritize nested resource so {event} is always captured
        Route::resource('events.participants', ParticipantController::class);
        Route::get('/events/{event}/participants/export', [ParticipantController::class, 'export'])->name('events.participants.export');

        // General participants list (Only for index/listing, avoiding create/store conflict)
        Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
        
        Route::resource('reports', ReportController::class)->only(['index']);
        Route::resource('documents', DocumentController::class)->only(['index']);

        // Roles & Permissions
        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::get('/roles/users/{user}/edit', [RolePermissionController::class, 'editUser'])->name('roles.edit-user');
        Route::put('/roles/users/{user}', [RolePermissionController::class, 'updateUser'])->name('roles.update-user');
        Route::get('/roles/role/{role}/edit', [RolePermissionController::class, 'editRole'])->name('roles.edit-role');
        Route::put('/roles/role/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update-role');
    });


/*
|--------------------------------------------------------------------------
| USER ROLE ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
        Route::get('/requests', [DashboardController::class, 'userRequests'])->name('requests');
    });


/*
|--------------------------------------------------------------------------
| MULTIMEDIA STAFF ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:multimedia_staff'])
    ->prefix('media')
    ->name('media.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'media'])->name('dashboard');
        Route::get('/posts', [DashboardController::class, 'mediaPosts'])->name('posts');
    });

require __DIR__ . '/auth.php';