<?php

use App\Http\Controllers\Api\ApiIntegrationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventCustodianController;
use App\Http\Controllers\Api\EventFinanceController;
use App\Http\Controllers\Api\EventLogisticsController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Integration Routes (Public)
Route::prefix('integration')->group(function () {
    Route::get('/', [ApiIntegrationController::class, 'index']);
    Route::get('/quick-start', [ApiIntegrationController::class, 'quickStart']);
    Route::get('/auth-guide', [ApiIntegrationController::class, 'authGuide']);
    Route::get('/examples', [ApiIntegrationController::class, 'examples']);
    Route::get('/sdk', [ApiIntegrationController::class, 'sdk']);
    Route::get('/webhooks', [ApiIntegrationController::class, 'webhooks']);
    Route::get('/support', [ApiIntegrationController::class, 'support']);
    
    // Registration and token generation
    Route::post('/register', [ApiIntegrationController::class, 'register']);
    Route::post('/token', [ApiIntegrationController::class, 'generateToken']);
});

// Events API Routes
Route::prefix('events')->middleware('auth:api')->group(function () {
    // General event routes
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store']);
    Route::get('/my', [EventController::class, 'myEvents']);
    Route::get('/statistics', [EventController::class, 'statistics']);
    Route::get('/venues', [EventController::class, 'venues']);
    
    // Specific event routes
    Route::get('/{id}', [EventController::class, 'show']);
    Route::put('/{id}', [EventController::class, 'update']);
    Route::delete('/{id}', [EventController::class, 'destroy']);
    Route::patch('/{id}/status', [EventController::class, 'updateStatus']);
    
    // Event participants
    Route::post('/{id}/participants', [EventController::class, 'addParticipant']);
    Route::delete('/{id}/participants/{participantId}', [EventController::class, 'removeParticipant']);
    
    // Event Logistics Routes
    Route::prefix('/{eventId}/logistics')->group(function () {
        Route::get('/', [EventLogisticsController::class, 'index']);
        Route::post('/', [EventLogisticsController::class, 'store']);
        Route::get('/summary', [EventLogisticsController::class, 'summary']);
        Route::put('/{logisticsId}', [EventLogisticsController::class, 'update']);
        Route::delete('/{logisticsId}', [EventLogisticsController::class, 'destroy']);
    });
    
    // Event Finance Routes
    Route::prefix('/{eventId}/finance')->group(function () {
        Route::get('/', [EventFinanceController::class, 'index']);
        Route::post('/', [EventFinanceController::class, 'store']);
        Route::patch('/{financeRequestId}/status', [EventFinanceController::class, 'updateStatus']);
    });
    
    // Event Custodian Routes
    Route::prefix('/{eventId}/custodian')->group(function () {
        Route::get('/', [EventCustodianController::class, 'index']);
        Route::post('/', [EventCustodianController::class, 'store']);
        Route::get('/summary', [EventCustodianController::class, 'summary']);
        Route::put('/{custodianRequestId}', [EventCustodianController::class, 'update']);
        Route::patch('/{custodianRequestId}/status', [EventCustodianController::class, 'updateStatus']);
        Route::delete('/{custodianRequestId}', [EventCustodianController::class, 'destroy']);
    });
});

// Standalone API Routes
Route::middleware('auth:api')->group(function () {
    // Logistics Resources
    Route::get('/logistics/resources', [EventLogisticsController::class, 'resources']);
    
    // Finance Routes
    Route::prefix('finance')->group(function () {
        Route::get('/pending', [EventFinanceController::class, 'pendingRequests']);
        Route::get('/my', [EventFinanceController::class, 'myRequests']);
        Route::get('/dashboard', [EventFinanceController::class, 'dashboard']);
    });
    
    // Custodian Routes
    Route::prefix('custodian')->group(function () {
        Route::get('/materials', [EventCustodianController::class, 'materials']);
        Route::get('/pending', [EventCustodianController::class, 'pendingRequests']);
        Route::get('/my', [EventCustodianController::class, 'myRequests']);
        Route::get('/dashboard', [EventCustodianController::class, 'dashboard']);
    });
    
    // User API Management Routes
    Route::prefix('user')->group(function () {
        Route::get('/applications', [UserApiController::class, 'applications']);
        Route::get('/tokens', [UserApiController::class, 'tokens']);
        Route::post('/tokens', [UserApiController::class, 'createToken']);
        Route::delete('/tokens/{tokenId}', [UserApiController::class, 'revokeToken']);
        Route::get('/statistics', [UserApiController::class, 'statistics']);
    });
});
