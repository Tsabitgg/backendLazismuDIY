<?php

use App\Http\Controllers\CampaignCategoryController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\InfakController;
use App\Http\Controllers\WakafController;
use App\Http\Controllers\ZakatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('campaigns', CampaignController::class);
Route::apiResource('campaign-categories', CampaignCategoryController::class);
Route::apiResource('infaks', InfakController::class);
Route::apiResource('zakats', ZakatController::class);;
Route::apiResource('wakafs', WakafController::class);
