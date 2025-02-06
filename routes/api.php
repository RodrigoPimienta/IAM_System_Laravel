<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\profileController;
use App\Http\Controllers\Api\moduleController;
use App\Http\Controllers\Api\modulePermissionController;
use App\Http\Controllers\Api\moduleRoleController;
use App\Http\Controllers\authController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
;

// crear un routeGroup para los modulos /modules

Route::controller(moduleController::class)->group(function () {
    Route::get('/modules', 'all');
    Route::get('/modules/{id}', 'show');
    Route::post('/modules', 'store');
    Route::put('/modules/{id}', 'update');
    Route::patch('modules/{id}/status', 'updateStatus');
    Route::get('/modules/{id}/permissions', 'permissionsByModule');
    Route::get('/modules/{id}/roles', 'rolesByModule');

});

Route::controller(modulePermissionController::class)->group(function () {
    Route::get('/modules/permissions/{id}', 'show');
    Route::post('/modules/permissions', 'store');
    Route::put('/modules/permissions/{id}', 'update');
    Route::patch('modules/permissions/{id}/status', 'updateStatus');
});

Route::controller(moduleRoleController::class)->group(function () {
    Route::get('/modules/roles/{id}', 'show');
    Route::post('/modules/roles', 'store');
    Route::put('/modules/roles/{id}', 'update');
    Route::patch('modules/roles/{id}/status', 'updateStatus');
});

Route::controller(profileController::class)->group(function () {
    Route::get('/profiles', 'all');
    Route::get('/profiles/{id}', 'show');
    Route::post('/profiles', 'store');
    Route::put('/profiles/{id}', 'update');
    Route::patch('profiles/{id}/status', 'updateStatus');
});


Route::controller(authController::class)->group(function(){
    Route::get('/auth/permissions','allPermissions');
});

// definir ruta default en caso de que no exista
Route::fallback(function(){
    return response()->json(['error'=> true,'status'=>404, 'message' => 'Not Found'], 404);
});