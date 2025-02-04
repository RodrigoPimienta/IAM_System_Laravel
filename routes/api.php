<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\profileController;
use App\Http\Controllers\Api\moduleController;
use App\Http\Controllers\Api\modulePermissionController;
use App\Http\Controllers\Api\moduleRoleController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/profiles', [ProfileController::class,'all']);

Route::get('/profiles/{id}', [ProfileController::class,'show']);

Route::post('/profiles', [ProfileController::class,'store']);


// crear un routeGroup para los modulos /modules

Route::controller(moduleController::class)->group(function () {
    Route::get('/modules', 'all');
    Route::get('/modules/{id}', 'show');
    Route::post('/modules', 'store');
    Route::put('/modules/{id}', 'update');
    Route::patch('modules/{id}/status', 'updateStatus');
});

Route::controller(modulePermissionController::class)->group(function () {
    Route::get('/modules/{id}/permissions', 'allByModule');
    Route::get('/modules/permissions/{id}', 'show');
    Route::post('/modules/permissions', 'store');
    Route::put('/modules/permissions/{id}', 'update');
    Route::patch('modules/permissions/{id}/status', 'updateStatus');
});

Route::controller(moduleRoleController::class)->group(function () {
    Route::get('/modules/{id}/roles', 'allByModule');
    Route::get('/modules/roles/{id}', 'show');
    Route::post('/modules/roles', 'store');
    Route::put('/modules/roles/{id}', 'update');
    Route::patch('modules/roles/{id}/status', 'updateStatus');
});