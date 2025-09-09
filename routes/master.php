<?php

use App\Http\Controllers\MasterController;
use App\Http\Controllers\DeviceController;

use Illuminate\Support\Facades\Route;

// 基幹マスタ管理
Route::get('/', [MasterController::class, 'index'])->name('master');
Route::get('/users', [MasterController::class, 'users'])->name('master.users');
Route::get('/akioka-users', [MasterController::class, 'akioka_users'])->name('master.akioka-users');
Route::get('/calender', [MasterController::class, 'calender'])->name('master.calender');
Route::get('/get/holidays', [MasterController::class, 'get_holidays'])->name('master.get.holidays');
Route::post('/store/holiday', [MasterController::class, 'store_holiday'])->name('master.store.holiday');

Route::get('/create/users', [MasterController::class, 'create_user'])->name('master.create.user');
Route::post('/store/users', [MasterController::class, 'store_user'])->name('master.store.users');

Route::get('/show/users/{user_id}', [MasterController::class, 'show_user'])->name('master.show.user');

// デバイス情報管理
Route::get('/devices', [DeviceController::class, 'index'])->name('master.devices');
Route::get('/devices/get', [DeviceController::class, 'getDevices'])->name('master.devices.get');
Route::get('/devices/create', [DeviceController::class, 'create'])->name('master.devices.create');
Route::post('/devices/store', [DeviceController::class, 'store'])->name('master.devices.store');
Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('master.devices.edit');
Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('master.devices.update');
Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('master.devices.destroy');
Route::post('/devices/update-last-access', [DeviceController::class, 'updateLastAccess'])->name('master.devices.update-last-access');
