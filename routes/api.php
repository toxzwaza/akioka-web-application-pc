<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FaxJobController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NewMovieController;
use App\Http\Controllers\SignageController;
use App\Http\Controllers\StockController;
use App\Models\Signage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/getStorageAddresses', [ApiController::class, 'getStorageAddresses'])->name('api.getStorageAddresses');

// ログ
Route::post('/log/create', [LogController::class, 'createLog'])->name('log.create');


// 文字お越し待ち取得
Route::get('/getWaitingTranscription', [NewMovieController::class, 'getWaitingTranscription'])->name('movie2.getWaitingTranscription');

// 物品発注可能状況確認待ち取得
Route::get('/stock/urlStocks', [StockController::class, 'urlStocks']);
// 価格変更を行う
Route::post('/stock/price/store', [StockController::class, 'priceStore']);

// FAXサーバー連携API（Bearerトークン必須）
Route::middleware('fax.token')->prefix('fax')->group(function () {
    Route::get('/jobs', [FaxJobController::class, 'index']);
    Route::post('/jobs', [FaxJobController::class, 'store']);
    Route::get('/jobs/{id}', [FaxJobController::class, 'show']);
    Route::patch('/jobs/{id}/status', [FaxJobController::class, 'updateStatus']);
    Route::patch('/jobs/{id}/converted-pdf', [FaxJobController::class, 'updateConvertedPdf']);
    Route::post('/jobs/retry-errors', [FaxJobController::class, 'retryErrors']);
    Route::post('/jobs/{id}/retry', [FaxJobController::class, 'retryById']);
    Route::delete('/jobs/completed', [FaxJobController::class, 'clearCompleted']);
    Route::delete('/jobs', [FaxJobController::class, 'clearAll']);

    Route::get('/initial-orders/{id}', [FaxJobController::class, 'showInitialOrder']);
    Route::patch('/initial-orders/{id}', [FaxJobController::class, 'updateInitialOrder']);
});