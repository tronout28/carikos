<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
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

    //insert kost dari admin
    Route::post('/insert-kost', [KostController::class, 'Insertkost']);
    Route::put('/update-kost/{id}', [KostController::class, 'Updatekost']);
    Route::post('/attach-university', [KostController::class, 'Attachuniversity']);
    Route::delete('/delete/{id}', [KostController::class, 'Deletekost']);
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

Route::get('/whatsapp', [UserController::class, 'whatsapp']);

Route::group(['prefix' => '/kost'], function () {
    Route::get('/', [KostController::class, 'index']);
    Route::get('/detail/{id}', [KostController::class, 'show']);

    Route::group(['middleware' => ['auth:sanctum', 'role:owner,admin']], function () {
        Route::post('/insert-kost', [KostController::class, 'Insertkost']);
        Route::put('/update-kost/{id}', [KostController::class, 'Updatekost']);
        Route::post('/attach-university', [KostController::class, 'Attachuniversity']);
        Route::delete('/delete/{id}', [KostController::class, 'Deletekost']);
        Route::get('/my-kost', [KostController::class, 'getMykosts']);
        Route::get('/invoices',[OrderController::class, 'getAllInvoice']);
        Route::get('/invoice-by-kost/{id}', [OrderController::class, 'getInvoiceByKost']);
    });
});

Route::group(['prefix' => '/payment','middleware' => ['auth:sanctum']], function () {
    Route::post('/checkout', [OrderController::class, 'checkoutKost']);
});

Route::post('/midtrans-callback', [OrderController::class, 'callback']);
Route::get('/payment/invoice/{id}', [OrderController::class, 'invoiceView']);
