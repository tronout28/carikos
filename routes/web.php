<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\OrderController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/images-kost/{filename}', function ($filename) {
    $path = public_path('images-kost/'.$filename);

    if (! File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header('Content-Type', $type);

    return $response;
});

Route::get('/snap_view/{orderId}', [OrderController::class, 'snapView'])->name('snap.view');
Route::get('/invoice/{id}', [OrderController::class, 'invoiceView'])->name('invoice.view');