<?php

use App\Http\Controllers\DuoSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('deux');
});
Route::get('/session/{code}', [DuoSessionController::class, 'show']);
Route::post('/session', [DuoSessionController::class, 'store']);
Route::post('/session/{code}/game', [DuoSessionController::class, 'updateGame']);
Route::post('/session/{code}/ttt', [DuoSessionController::class, 'updateTtt']);
Route::post('/session/{code}/answers', [DuoSessionController::class, 'updateAnswer']);
Route::post('/session/{code}/chat', [DuoSessionController::class, 'appendChat']);