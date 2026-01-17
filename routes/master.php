<?php

use App\Http\Controllers\MasterController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ApprovalFlowController;

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

Route::get('/create/groups', [MasterController::class, 'create_group'])->name('master.create.group');
Route::post('/store/groups', [MasterController::class, 'store_group'])->name('master.store.groups');

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

// 承認フロー管理
Route::get('/approval-flows', [ApprovalFlowController::class, 'index'])->name('master.approval-flows.index');
Route::get('/approval-flows/create', [ApprovalFlowController::class, 'create'])->name('master.approval-flows.create');
Route::post('/approval-flows', [ApprovalFlowController::class, 'store'])->name('master.approval-flows.store');
Route::get('/approval-flows/{approvalFlow}', [ApprovalFlowController::class, 'show'])->name('master.approval-flows.show');
Route::get('/approval-flows/{approvalFlow}/edit', [ApprovalFlowController::class, 'edit'])->name('master.approval-flows.edit');
Route::put('/approval-flows/{approvalFlow}', [ApprovalFlowController::class, 'update'])->name('master.approval-flows.update');
Route::delete('/approval-flows/{approvalFlow}', [ApprovalFlowController::class, 'destroy'])->name('master.approval-flows.destroy');

// 承認フローテスト機能
Route::get('/approval-flows-test', [ApprovalFlowController::class, 'test'])->name('master.approval-flows.test');
Route::post('/approval-flows-test', [ApprovalFlowController::class, 'runTest'])->name('master.approval-flows.run-test');
Route::get('/approval-flows-bulk-test', [ApprovalFlowController::class, 'bulkTest'])->name('master.approval-flows.bulk-test');
Route::post('/approval-flows-test-api', [ApprovalFlowController::class, 'runTestApi'])->name('master.approval-flows.run-test-api');

// Helper::createApprovalFlowのテスト機能
Route::get('/approval-flows-helper-test', [ApprovalFlowController::class, 'helperTest'])->name('master.approval-flows.helper-test');
Route::post('/approval-flows-helper-test', [ApprovalFlowController::class, 'runHelperTest'])->name('master.approval-flows.run-helper-test');
