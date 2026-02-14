<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventCheckInController;
use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProgramFlowController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\EventPostController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\PostCommentController;

Route::get('/', function () {
    abort(419);
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
    
    // API Integration
    Route::get('/account/api-integration', function () {
        return view('account.api-integration');
    })->name('account.api-integration')->middleware('auth');
    
    Route::get('/account/api-integration-guide', function () {
        return view('account.api-integration-guide');
    })->name('account.api-integration-guide')->middleware('auth');

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
    Route::get('/notifications/list', [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::patch('/notifications/{notification}/unread', [NotificationController::class, 'markUnread'])->name('notifications.mark-unread');
    Route::get('/notifications/settings', [NotificationController::class, 'settings'])->name('notifications.settings');
    Route::put('/notifications/settings', [NotificationController::class, 'updateSettings'])->name('notifications.settings.update');

    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/messages', [SupportController::class, 'storeMessage'])->name('support.messages.store');
    Route::patch('/support/{ticket}/close', [SupportController::class, 'close'])->name('support.close');
    Route::patch('/support/{ticket}/status', [SupportController::class, 'updateStatus'])->name('support.status.update');

    // Shared analytics dashboard with graphs
    Route::get('/dashboard/insights', [DashboardController::class, 'insights'])->name('dashboard.insights');

    // Reports (permission-based, available to any role with view reports permission)
    Route::middleware('permission:view reports')
        ->prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/pipeline', [ReportController::class, 'pipeline'])->name('pipeline');
            Route::get('/participants', [ReportController::class, 'participants'])->name('participants');
            Route::get('/venues', [ReportController::class, 'venues'])->name('venues');
            Route::get('/finance', [ReportController::class, 'finance'])->name('finance');
            Route::get('/engagement', [ReportController::class, 'engagement'])->name('engagement');
            Route::get('/support', [ReportController::class, 'support'])->name('support');
            Route::get('/export/{section}', [ReportController::class, 'export'])
                ->where('section', 'overview|pipeline|participants|venues|finance|engagement|support')
                ->name('export');
        });

    // Venue availability endpoint
    Route::get('/venues/{venue}/availability', [VenueController::class, 'availability'])->name('venues.availability');

    // Event check-in module
    Route::prefix('check-in')
        ->name('checkin.')
        ->middleware('permission:event check-in access')
        ->group(function () {
            Route::get('/', [EventCheckInController::class, 'index'])->name('index');
            Route::get('/events/{event}', [EventCheckInController::class, 'show'])->name('show');
            Route::post('/events/{event}/scan', [EventCheckInController::class, 'scan'])
                ->middleware('permission:event check-in scan')
                ->name('scan');
            Route::post('/events/{event}/manual', [EventCheckInController::class, 'manual'])
                ->middleware('permission:event check-in manual')
                ->name('manual');
            Route::get('/events/{event}/logs', [EventCheckInController::class, 'logs'])
                ->middleware('permission:event check-in logs')
                ->name('logs');
            Route::get('/events/{event}/qr/{payload}', [EventCheckInController::class, 'qrEntry'])
                ->middleware('permission:event check-in scan')
                ->name('qr-entry');
            Route::get('/events/{event}/participants/{participant}/ticket', [EventCheckInController::class, 'ticket'])
                ->middleware('permission:event tickets print')
                ->name('ticket');
            Route::post('/events/{event}/participants/{participant}/ticket/resend', [EventCheckInController::class, 'resendTicket'])
                ->middleware('permission:event tickets print')
                ->name('ticket.resend');
            Route::get('/events/{event}/participants/{participant}/qr/download', [EventCheckInController::class, 'downloadQr'])
                ->middleware('permission:event tickets print')
                ->name('qr.download');
        });
});

Route::get('/tickets/{participant}', [EventCheckInController::class, 'publicTicket'])
    ->middleware('signed')
    ->name('checkin.tickets.show');


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
        Route::get('/events/bulk-upload-template', [EventController::class, 'downloadCsvTemplate'])
            ->name('events.bulk-upload-template');
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

        // Attendance Monitoring
        Route::prefix('attendance')
            ->name('attendance.')
            ->middleware('permission:manage participants')
            ->group(function () {
                Route::get('/', [AttendanceController::class, 'index'])->name('index');
                Route::get('/events/{event}', [AttendanceController::class, 'show'])->name('show');
                Route::post('/events/{event}/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
                Route::post('/events/{event}/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
                Route::post('/events/{event}/verify', [AttendanceController::class, 'verify'])->name('verify');
                Route::post('/events/{event}/bulk-check-in', [AttendanceController::class, 'bulkCheckIn'])->name('bulk-check-in');
                Route::post('/events/{event}/bulk-check-out', [AttendanceController::class, 'bulkCheckOut'])->name('bulk-check-out');
                Route::get('/events/{event}/export', [AttendanceController::class, 'export'])->name('export');
            });

        Route::resource('documents', DocumentController::class);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::post('/events/{event}/generate-attendance-report', [DocumentController::class, 'generateAttendanceReport'])->name('documents.generate-attendance-report');
        Route::resource('users', UserController::class)->except(['show']);

        // Roles & Permissions
        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::get('/roles/create-role', [RolePermissionController::class, 'createRole'])->name('roles.create-role');
        Route::post('/roles/create-role', [RolePermissionController::class, 'storeRole'])->name('roles.store-role');
        Route::delete('/roles/role/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy-role');
        Route::get('/roles/permissions', [RolePermissionController::class, 'permissionsIndex'])->name('roles.permissions.index');
        Route::get('/roles/create-permission', [RolePermissionController::class, 'createPermission'])->name('roles.create-permission');
        Route::post('/roles/create-permission', [RolePermissionController::class, 'storePermission'])->name('roles.store-permission');
        Route::get('/roles/permissions/{permission}/edit', [RolePermissionController::class, 'editPermission'])->name('roles.permissions.edit');
        Route::put('/roles/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])->name('roles.permissions.update');
        Route::delete('/roles/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('roles.permissions.destroy');
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

// API Developer Portal
Route::get('/api/developer', function () {
    return view('api.developer-portal');
})->name('api.developer.portal');

Route::get('/api/docs', function () {
    return redirect('/API_DOCUMENTATION.md');
})->name('api.docs');

Route::get('/api/url', [App\Http\Controllers\ApiUrlController::class, 'getApiUrl'])->name('api.url');

Route::get('/test-domain', [App\Http\Controllers\DomainTestController::class, 'test'])->name('test.domain');

require __DIR__ . '/auth.php';
