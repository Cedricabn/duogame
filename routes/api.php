 <?php

use App\Http\Controllers\DuoSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/session/{code}', [DuoSessionController::class, 'show']);
Route::post('/session', [DuoSessionController::class, 'store']);
