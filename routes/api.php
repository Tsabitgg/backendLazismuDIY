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
use App\Http\Controllers\ReportsController;
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

Route::prefix('latestNews')->group(function () {
    Route::get('list/{category}', [LatestNewsController::class, 'index']); // Menampilkan berita berdasarkan kategori
    Route::post('{category}/{id}', [LatestNewsController::class, 'store']);
    Route::put('{category}/{id}', [LatestNewsController::class, 'update']); // Memperbarui berita berdasarkan ID
    Route::delete('delete/{id}', [LatestNewsController::class, 'destroy']); // Menghapus berita berdasarkan ID
    Route::get('list/{category}/{id}', [LatestNewsController::class, 'getByCategoryAndEntityId']);
});

// priority campaigns
Route::get('/campaign/get-priority', [CampaignController::class, 'getPriorityCampaigns']);
Route::put('/campaign/set-priority/{id}', [CampaignController::class, 'setPriorityTrue']);
Route::put('/campaign/unset-priority/{id}', [CampaignController::class, 'setPriorityFalse']);

// recomendation campaigns
Route::get('/campaign/get-recomendation', [CampaignController::class, 'getRecomendationCampaigns']);
Route::put('/campaign/set-recomendation/{id}', [CampaignController::class, 'setRecomendationTrue']);
Route::put('/campaign/unset-recomendation/{id}', [CampaignController::class, 'setRecomendationFalse']);

// active campaign
Route::get('/campaign/get-active', [CampaignController::class, 'getActiveCampaigns']);
Route::get('/campaign/get-nonactive', [CampaignController::class, 'getNonActiveCampaigns']);
Route::put('/campaign/set-active/{id}', [CampaignController::class, 'setActiveTrue']);
Route::put('/campaign/unset-active/{id}', [CampaignController::class, 'setActiveFalse']);


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
Route::get('/get-me', [AuthController::class, 'getMe']);

Route::post('register-admin', [AuthController::class, 'registerAdmin']);
Route::post('login-admin', [AuthController::class, 'loginAdmin']);

Route::post('/upload-report', [ReportsController::class, 'upload']); // Upload PDF
Route::get('/get-report', [ReportsController::class, 'index']);   // Get PDFs with filters

Route::get('/transactions-donatur', [TransactionController::class, 'getUserTransactions']);
Route::get('/transactions-donatur/summary', [TransactionController::class, 'getTransactionSummary']);