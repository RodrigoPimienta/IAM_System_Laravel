<?php

use App\Http\Controllers\Api\moduleController;
use App\Http\Controllers\Api\modulePermissionController;
use App\Http\Controllers\Api\moduleRoleController;
use App\Http\Controllers\Api\profileController;
use App\Http\Controllers\Api\userController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckPermissions;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

// rate limits

RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip()); // Máximo 5 intentos por IP en 1 minuto
});

RateLimiter::for('user_actions', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip()); // Máximo 60 peticiones por IP en 1 minuto
});

// Routes
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware(['auth:sanctum', 'throttle:user_actions'])->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('/auth/permissions', 'permissions');
        Route::post('/auth/logout', 'logout');
    });

    Route::controller(moduleController::class)->group(function () {
        Route::get('/modules', 'all')->middleware(CheckPermissions::class . ':modules,show');
        Route::get('/modules/{id}', 'show')->middleware(CheckPermissions::class . ':modules,show');
        Route::post('/modules', 'store')->middleware(CheckPermissions::class . ':modules,create');
        Route::put('/modules/{id}', 'update')->middleware(CheckPermissions::class . ':modules,update');
        Route::patch('modules/{id}/status', 'updateStatus')->middleware(CheckPermissions::class . ':modules,updateStatus');
        Route::get('/modules/{id}/permissions', 'permissionsByModule')->middleware(CheckPermissions::class . ':modules,showPermissions');
        Route::get('/modules/{id}/roles', 'rolesByModule')->middleware(CheckPermissions::class . ':modules,showRoles');
    });

    Route::controller(modulePermissionController::class)->group(function () {
        Route::get('/modules/permissions/{id}', 'show')->middleware(CheckPermissions::class . ':modules,showPermissions');
        Route::post('/modules/permissions', 'store')->middleware(CheckPermissions::class . ':modules,addPermission');
        Route::put('/modules/permissions/{id}', 'update')->middleware(CheckPermissions::class . ':modules,updatePermission');
        Route::patch('modules/permissions/{id}/status', 'updateStatus')->middleware(CheckPermissions::class . ':modules,updateStatusPermission');
    });

    Route::controller(moduleRoleController::class)->group(function () {
        Route::get('/modules/roles/{id}', 'show')->middleware(CheckPermissions::class . 'modules,showRoles');
        Route::post('/modules/roles', 'store')->middleware(CheckPermissions::class . ':modules,addRole');
        Route::put('/modules/roles/{id}', 'update')->middleware(CheckPermissions::class . ':modules,updateRole');
        Route::patch('modules/roles/{id}/status', 'updateStatus')->middleware(CheckPermissions::class . ':modules,updateStatusRole');
    });

    Route::controller(profileController::class)->group(function () {
        Route::get('/profiles', 'all')->middleware(CheckPermissions::class . ':profiles,show');
        Route::get('/profiles/{id}', 'show')->middleware(CheckPermissions::class . ':profiles,show');
        Route::post('/profiles', 'store')->middleware(CheckPermissions::class . ':profiles,create');
        Route::put('/profiles/{id}', 'update')->middleware(CheckPermissions::class . ':profiles,update');
        Route::patch('profiles/{id}/status', 'updateStatus')->middleware(CheckPermissions::class . ':profiles,updateStatus');
    });

    Route::controller(userController::class)->group(function () {
        Route::get('/users', 'all')->middleware(CheckPermissions::class . ':users,show');
        Route::get('/users/{id}', 'show')->middleware(CheckPermissions::class . ':users,show');
        Route::post('/users', 'store')->middleware(CheckPermissions::class . ':users,create');
        Route::put('/users/{id}', 'update')->middleware(CheckPermissions::class . ':users,update');
        Route::patch('users/{id}/status', 'updateStatus')->middleware(CheckPermissions::class . ':users,updateStatus');
        Route::patch('users/{id}/password', 'updateStatus')->middleware(CheckPermissions::class . ':users,updatePassword');

    });
});

// // definir ruta default en caso de que no exista
Route::fallback(function () {
    return response()->json(['error' => true, 'status' => 404, 'message' => 'Not Found'], 404);
});
