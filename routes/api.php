<?php

use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SpotController;
use App\Http\Controllers\Api\VaccinationController;
use App\Http\Controllers\Api\ConsultationController;

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

Route::middleware('check_token')->get('/user', function (Request $request) {
    $society = Society::where('login_tokens', $request->token)->with('regional')->first();

    return response()->json($society, 200);
});

Route::prefix('v1')->group(function() {
    Route::prefix('auth')->group(function() {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::middleware('check_token')->group(function() {
        Route::get('consultations', [ConsultationController::class, 'index']);
        Route::post('consultations', [ConsultationController::class, 'store']);

        Route::get('spots', [SpotController::class, 'index']);
        Route::get('spots/{spot}', [SpotController::class, 'show']);

        Route::get('vaccinations', [VaccinationController::class, 'index']);
        Route::post('vaccinations', [VaccinationController::class, 'store']);
    });
});
