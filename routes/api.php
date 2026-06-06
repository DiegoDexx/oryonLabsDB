<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectFieldController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRequirementController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;

/*
|--------------------------------------------------------------------------
| Rutas API públicas
|--------------------------------------------------------------------------
*/

Route::post('/login', [UserController::class, 'login']);

// Rutas públicas para el formulario de leads
Route::post('leads', [LeadController::class, 'store']);
Route::post('clients', [ClientController::class, 'store']);
Route::get('projects', [ProjectController::class, 'indexFull']);
Route::post('projects', [ProjectController::class, 'store']);
Route::get('projects/{project}', [ProjectController::class, 'show']);
Route::get('projects/{project}/full', [ProjectController::class, 'showFull']);
Route::apiResource('project-requirements', ProjectRequirementController::class);

// Get project fields by category
Route::get('project-fields/category/{category}', [ProjectFieldController::class, 'byCategory']);

/*
|--------------------------------------------------------------------------
| Rutas API protegidas (auth:sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);

    // clients — store y index son públicos, el resto protegido
    Route::get('clients', [ClientController::class, 'index']);
    Route::get('clients/{client}', [ClientController::class, 'show']);
    Route::put('clients/{client}', [ClientController::class, 'update']);
    Route::patch('clients/{client}', [ClientController::class, 'update']);
    Route::delete('clients/{client}', [ClientController::class, 'destroy']);

    // projects — store/index/show son públicos, el resto protegido
    Route::put('projects/{project}', [ProjectController::class, 'update']);
    Route::patch('projects/{project}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);
    Route::patch('projects/{project}/stage', [ProjectController::class, 'updateStage']);

    // Recursos completamente protegidos
    Route::apiResources([
        'subscriptions' => SubscriptionController::class,
        'activities'    => ActivityController::class,
        'invoices'      => InvoiceController::class,
    ]);

    // Rutas de estado personalizadas
    Route::patch('subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus']);
    Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid']);

    // Rutas administrativas
    Route::get('admin/projects/full', [ProjectController::class, 'indexFull']);
    Route::get('admin/projects/{project}/full', [ProjectController::class, 'showFull']);

});
