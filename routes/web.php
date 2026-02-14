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
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\EventPostController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\PostCommentController;

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

    
    // Program Flow
    Route::get('/program-flow', [ProgramFlowController::class, 'index'])->name('program-flow.index');
    Route::get('/program-flow/{event}', [ProgramFlowController::class, 'show'])->name('program-flow.show');

    // Program item management (protected by permission:manage scheduling in controller)
    Route::post('/program-flow/{event}/items', [ProgramFlowController::class, 'storeItem'])->name('program-flow.items.store');
    Route::put('/program-flow/items/{item}', [ProgramFlowController::class, 'updateItem'])->name('program-flow.items.update');
    Route::delete('/program-flow/items/{item}', [ProgramFlowController::class, 'destroyItem'])->name('program-flow.items.destroy');
    Route::post('/program-flow/{event}/items/reorder', [ProgramFlowController::class, 'reorderItems'])->name('program-flow.items.reorder');

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
| MULTIMEDIA MODULE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view multimedia'])->group(function () {

    Route::get('/multimedia', [MultimediaController::class, 'index'])
        ->name('multimedia.index');

    Route::get('/multimedia/posts/create', [EventPostController::class, 'create'])
        ->middleware('permission:create multimedia post')
        ->name('multimedia.posts.create');

    Route::post('/multimedia/posts', [EventPostController::class, 'store'])
        ->middleware('permission:create multimedia post')
        ->name('multimedia.posts.store');

    // Reactions (all authenticated users can react)
    Route::post('/multimedia/posts/{post}/reactions', [PostReactionController::class, 'store'])
        ->middleware('permission:react multimedia post')
        ->name('multimedia.posts.reactions.store');

    Route::delete('/multimedia/posts/{post}/reactions', [PostReactionController::class, 'destroy'])
        ->middleware('permission:react multimedia post')
        ->name('multimedia.posts.reactions.destroy');

    // Comments (all authenticated users can comment)
    Route::post('/multimedia/posts/{post}/comments', [PostCommentController::class, 'store'])
        ->middleware('permission:comment multimedia post')
        ->name('multimedia.posts.comments.store');

    Route::put('/multimedia/posts/{post}/comments/{comment}', [PostCommentController::class, 'update'])
        ->middleware('permission:comment multimedia post')
        ->name('multimedia.posts.comments.update');

    Route::delete('/multimedia/posts/{post}/comments/{comment}', [PostCommentController::class, 'destroy'])
        ->middleware('permission:comment multimedia post')
        ->name('multimedia.posts.comments.destroy');
});

require __DIR__ . '/auth.php';
