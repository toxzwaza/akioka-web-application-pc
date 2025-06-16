<?php

use App\Http\Controllers\ApiController;
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
Route::get('/stock/urlStocks', [ StockController::class, 'urlStocks']);
