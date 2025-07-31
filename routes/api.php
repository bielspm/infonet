<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [ApiController::class, 'login'])->name('login.post');
Route::get('servicos', [ApiController::class, 'listarServicos']);
Route::post('coordenadas', [ApiController::class, 'buscarCoordenadas']);
Route::post('prestadores', [ApiController::class, 'buscarPrestadores']);

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/dashboard', [ApiController::class, 'dashboard'])->name('dashboard');
});

