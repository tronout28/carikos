<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register-user', [AuthController::class, 'RegisterasUser']);
    Route::post('/register-owner', [AuthController::class, 'RegisterasOwner']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::group(['prefix' => '/admin'], function () {
    Route::post('/register-admin', [AdminController::class, 'Registerasadmin']);
    Route::post('/login', [AdminController::class, 'Login']);
    Route::post('/forgot-password', [AdminController::class, 'forgotPassword']);
});

Route::group(['prefix' => '/user', 'middleware' => ['auth:sanctum']], function () {
    Route::put('/update-profile', [UserController::class, 'updateprofile']);
    Route::get('/get-profile', [UserController::class, 'getprofile']);
});

Route::group(['prefix' => '/university'], function () {
    Route::get('/', [UniversityController::class, 'index']);
    Route::get('/detail/{id}', [UniversityController::class, 'show']);

    Route::group(['middleware' => ['auth:sanctum','role:owner|admin']], function () {
        Route::post('/insert-university', [UniversityController::class, 'Insertuniversity']);
        Route::put('/update-university/{id}', [UniversityController::class, 'Updateuniversity']);
        Route::delete('/delete/{id}', [UniversityController::class, 'Deleteuniversity']);
    });
});

Route::group(['prefix' => '/kost'], function () {
    Route::get('/', [KostController::class, 'index']);
    Route::get('/detail/{id}', [KostController::class, 'show']);

    Route::group(['middleware' => ['auth:sanctum', 'role:owner,admin']], function () {
        Route::post('/insert-kost', [KostController::class, 'Insertkost']);
        Route::put('/update-kost/{id}', [KostController::class, 'Updatekost']);
        Route::post('/attach-university', [KostController::class, 'Attachuniversity']);
        Route::delete('/delete/{id}', [KostController::class, 'Deletekost']);
    });
});
