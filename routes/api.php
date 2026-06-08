<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\UserManagement\Role\RoleController;
use App\Http\Controllers\UserManagement\User\UserController;
use App\Http\Controllers\UserManagement\Permission\PermissionController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\FileManagement\FileController;
use Illuminate\Support\Facades\Route;

// 
Route::prefix('/auth')->group(function (){
    Route::controller(AuthController::class)->group(function (){
        Route::post('/register', 'register');
        Route::post('/login', 'login');
    });
}); 

//
Route::prefix('/reset-password')->group(function (){
    Route::controller(ResetPasswordController::class)->group(function (){
        Route::post('/reset-link', 'forgotPassword')->middleware('guest')->name('password.email');
        Route::post('/', 'resetPassword')->middleware('guest')->name('password.reset');
    });
});

// 
Route::middleware(['auth:sanctum'])->prefix('/user')->group(function (){
    Route::controller(UserController::class)->group(function (){
        Route::get('/index', 'index')->middleware('can:view user');
        Route::get('/show/{id}', 'show')->middleware('can:view user');
        Route::post('/store', 'store')->middleware('can:create user');
        Route::put('/update/{id}', 'update')->middleware('can:update user');
        Route::delete('/delete/{id}', 'destroy')->middleware('can:delete user');
        Route::put('/update/status/{id}', 'updateStatus');

    });
}); 

Route::middleware(['auth:sanctum'])->prefix('/role')->group(function (){
    Route::controller(RoleController::class)->group(function (){
        Route::get('/index', 'index')->middleware('can:view role');
        Route::get('/show/{id}', 'show')->middleware('can:view role');
        Route::post('/store', 'store')->middleware('can:create role');
        Route::put('/update/{id}', 'update')->middleware('can:update role');
        Route::delete('/delete/{id}', 'destroy')->middleware('can:delete role');
    });
}); 

Route::middleware(['auth:sanctum'])->prefix('/customer')->group(function (){
    Route::controller(CustomerController::class)->group(function (){
        Route::get('/index', 'index')->middleware('can:view customer');
        Route::get('/show/{id}', 'show')->middleware('can:view customer');
        Route::post('/store', 'store')->middleware('can:create customer');
        Route::put('/update/{id}', 'update')->middleware('can:update customer');
        Route::delete('/delete/{id}', 'destroy')->middleware('can:delete customer');
    });
}); 

Route::middleware(['auth:sanctum'])->prefix('/file')->group(function (){
    Route::controller(FileController::class)->group(function (){
        Route::post('/{entityType}/{entityId}', 'store')->middleware('can:create file');
        Route::get('/user', 'userFiles')->middleware('can:view file');
        Route::get('/index', 'index')->middleware('can:view file');
        Route::get('/show/{id}', 'show')->middleware('can:view file');
        Route::delete('/delete/{id}', 'destroy')->middleware('can:delete file');
    });
}); 

Route::middleware(['auth:sanctum'])->prefix('/permission')->group(function (){
    Route::controller(PermissionController::class)->group(function (){
        Route::get('/index', 'index')->middleware('can:view role');
    });
});

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'emailVerification'])
            ->middleware(['auth:sanctum', 'signed'])
            ->name('verification.verify');
