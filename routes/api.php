<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectFieldController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRequirementController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Rutas API públicas
|--------------------------------------------------------------------------
*/

// CRUD de proyectos, campos y requisitos (si se pueden usar desde frontend)

 Route::post('/login', [UserController::class, 'login']);

//no api resource si no todas las funciones correspondientes
Route::get('projects', [ProjectController::class, 'indexFull']);
Route::post('projects', [ProjectController::class, 'store']);
Route::get('projects/{project}', [ProjectController::class, 'show']);
Route::post('clients', [ClientController::class, 'store']);
Route::apiResource('project-requirements', ProjectRequirementController::class);

//get project fields by category
Route::get('project-fields/category/{category}', [ProjectFieldController::class, 'byCategory']);

// Endpoint para obtener la solicitud completa de un proyecto
Route::get('projects/{project}/full', [ProjectController::class, 'showFull']);

Route::middleware(['auth:sanctum'])->group(function () {

    //logout
    Route::post('/logout', [UserController::class, 'logout']);

    // CRUD de clientes salvo post , por lo cual no hay apiresource
    Route::get('clients', [ClientController::class, 'index']);
    Route::get('clients/{client}', [ClientController::class, 'show']);
    Route::put('clients/{client}', [ClientController::class, 'update']);
    Route::delete('clients/{client}', [ClientController::class, 'destroy']);

    // Todos los proyectos con cliente y requisitos completos
    Route::get('admin/projects/full', [ProjectController::class, 'indexFull']);

    // Obtener un proyecto en particular con todos los datos
    Route::get('admin/projects/{project}/full', [ProjectController::class, 'showFull']);

});

