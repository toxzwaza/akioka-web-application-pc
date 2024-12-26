<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\StockTabletController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ MessageController::class, 'index'])->name('message');
Route::post('/sendNotify', [MessageController::class, 'sendNotify'])->name('message.sendNotify');

Route::post('/create/group', [MessageController::class, 'create_group'])->name('message.create.group');
