<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CampaignCategoryController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\InfakController;
use App\Http\Controllers\QrisController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WakafController;
use App\Http\Controllers\ZakatController;
use App\Http\Controllers\LatestNewsController;
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

Route::prefix('campaigns')->group(function () {
    // Set priority to true
    Route::put('set-priority/{id}', [CampaignController::class, 'setPriorityTrue']);
    
    // Set priority to false
    Route::put('unset-priority/{id}', [CampaignController::class, 'setPriorityFalse']);
    
});

Route::apiResource('campaign-categories', CampaignCategoryController::class);
Route::apiResource('infaks', InfakController::class);
Route::apiResource('zakats', ZakatController::class);;
Route::apiResource('wakafs', WakafController::class);

Route::prefix('latestNews')->group(function () {
    Route::get('list/{category}', [LatestNewsController::class, 'index']); // Menampilkan berita berdasarkan kategori
    Route::post('{category}/{id}', [LatestNewsController::class, 'store']);
    Route::put('{category}/{id}', [LatestNewsController::class, 'update']); // Memperbarui berita berdasarkan ID
    Route::delete('{category}/{id}', [LatestNewsController::class, 'destroy']); // Menghapus berita berdasarkan ID
    Route::get('list/{category}/{id}', [LatestNewsController::class, 'getByCategoryAndEntityId']);
});


    // Get all priority campaigns
Route::get('/campaign/get-priority', [CampaignController::class, 'getPriorityCampaigns']);


Route::post('/billing/create/{categoryType}/{id}', [BillingController::class, 'createBilling']);
Route::get('/generate-qris', [QrisController::class, 'generate']);
Route::get('/check-status', [QrisController::class, 'checkStatus']);
Route::get('/push-notification', [QrisController::class, 'pushNotification']);
Route::post('/push-notification', [QrisController::class, 'pushNotification']);


Route::get('transactions', [TransactionController::class, 'index']);

Route::get('transactions/category/{category}', [TransactionController::class, 'getTransactionsByCategory']);

Route::get('transactions/campaign/{campaignId}', [TransactionController::class, 'getTransactionsByCampaignId']);

// Menampilkan daftar pengguna dengan pagination dan search
Route::get('users', [UserController::class, 'index']);

// Menampilkan data pengguna berdasarkan ID
Route::get('users/{id}', [UserController::class, 'show']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('register-admin', [AuthController::class, 'registerAdmin']);
Route::post('login-admin', [AuthController::class, 'loginAdmin']);