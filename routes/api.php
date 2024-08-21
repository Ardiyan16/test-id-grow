<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\ApiUserController;
use App\Http\Controllers\API\ApiBarangController;
use App\Http\Controllers\API\ApiMutasiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiController::class, 'index']);
    Route::post('/login', [ApiController::class, 'login']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('v1')->group(function () {

        //user
        Route::get('/data-user', [ApiUserController::class, 'index']);
        Route::get('/show-user/{id}', [ApiUserController::class, 'show']);
        Route::put('/update-user/{id}', [ApiUserController::class, 'update']);
        Route::get('/delete-user/{id}', [ApiUserController::class, 'delete']);

        //barang
        Route::get('/data-barang', [ApiBarangController::class, 'index']);
        Route::post('/tambah-barang', [ApiBarangController::class, 'store']);
        Route::get('/show-barang/{id}', [ApiBarangController::class, 'show']);
        Route::put('/update-barang/{id}', [ApiBarangController::class, 'update']);
        Route::get('/delete-barang/{id}', [ApiBarangController::class, 'delete']);

        //mutasi
        Route::get('/data-mutasi', [ApiMutasiController::class, 'index']);
        Route::post('/tambah-mutasi', [ApiMutasiController::class, 'store']);
        Route::get('/show-mutasi/{id}', [ApiMutasiController::class, 'show']);
        Route::put('/update-mutasi/{id}', [ApiMutasiController::class, 'update']);
        Route::get('/delete-mutasi/{id}', [ApiMutasiController::class, 'delete']);

        //history
        Route::get('/history-mutasi-user/{id}', [ApiMutasiController::class, 'history_mutasi_user']);
        Route::get('/history-mutasi-barang/{kode}', [ApiMutasiController::class, 'history_mutasi_barang']);

    });

});
