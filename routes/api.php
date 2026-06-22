<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadLookupController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFieldController;
use App\Http\Controllers\ProjectRequirementController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Rutas API públicas
|--------------------------------------------------------------------------
*/

Route::post('/login', [UserController::class, 'login']);

// Lead form — submitted by chatbot / public form
Route::post('leads', [LeadController::class, 'store']);

// Lookup & merge — called by chatbot, Vapi, n8n without auth
Route::get('leads/lookup', [LeadLookupController::class, 'lookup'])
    ->middleware('throttle:60,1');
Route::patch('leads/{id}/merge', [LeadLookupController::class, 'mergeUpdate'])
    ->middleware('throttle:60,1');

Route::post('clients', [ClientController::class, 'store']);

// Projects — public read (chatbot uses these)
Route::get('projects', [ProjectController::class, 'indexFull']);
Route::post('projects', [ProjectController::class, 'store']);
Route::get('projects/{project}', [ProjectController::class, 'show']);
Route::get('projects/{project}/full', [ProjectController::class, 'showFull']);

Route::apiResource('project-requirements', ProjectRequirementController::class);
Route::get('project-fields/category/{category}', [ProjectFieldController::class, 'byCategory']);

/*
|--------------------------------------------------------------------------
| Rutas API protegidas (auth:sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);

    // Current user profile + org features + dashboard metrics
    Route::get('/me',          [MeController::class, 'profile']);
    Route::get('/me/features', [MeController::class, 'features']);
    Route::get('/me/metrics',  [MetricsController::class, 'dashboard']);

    // User management
    Route::get('users',           [UserController::class, 'index']);
    Route::post('users',          [UserController::class, 'store']);
    Route::get('users/{user}',    [UserController::class, 'show']);
    Route::put('users/{user}',    [UserController::class, 'update']);
    Route::patch('users/{user}',  [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    // Clients — store & public index handled above, rest protected
    Route::get('clients',              [ClientController::class, 'index']);
    Route::get('clients/{client}',     [ClientController::class, 'show']);
    Route::put('clients/{client}',     [ClientController::class, 'update']);
    Route::patch('clients/{client}',   [ClientController::class, 'update']);
    Route::delete('clients/{client}',  [ClientController::class, 'destroy']);

    // Leads — CRM management
    Route::get('leads',                  [LeadController::class, 'index']);
    Route::get('leads/{lead}',           [LeadController::class, 'show']);
    Route::patch('leads/{lead}/status',  [LeadController::class, 'updateStatus']);
    Route::patch('leads/{lead}/notes',   [LeadController::class, 'updateNotes']);
    Route::post('leads/{lead}/convert',  [LeadController::class, 'convert']);
    Route::delete('leads/{lead}',        [LeadController::class, 'destroy']);

    // Projects — write operations (Pro+)
    Route::middleware(['module:projects'])->group(function () {
        Route::put('projects/{project}',          [ProjectController::class, 'update']);
        Route::patch('projects/{project}',        [ProjectController::class, 'update']);
        Route::delete('projects/{project}',       [ProjectController::class, 'destroy']);
        Route::patch('projects/{project}/stage',  [ProjectController::class, 'updateStage']);
        Route::get('admin/projects/full',         [ProjectController::class, 'indexFull']);
        Route::get('admin/projects/{project}/full', [ProjectController::class, 'showFull']);
    });

    // Activities (Pro+)
    Route::middleware(['module:activities'])->group(function () {
        Route::apiResource('activities', ActivityController::class);
    });

    // Subscriptions (Professional only)
    Route::middleware(['module:subscriptions'])->group(function () {
        Route::apiResource('subscriptions', SubscriptionController::class);
        Route::patch('subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus']);
    });

    // Invoices (Professional only)
    Route::middleware(['module:invoices'])->group(function () {
        Route::apiResource('invoices', InvoiceController::class);
        Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid']);
    });

});
