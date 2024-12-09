<?php

use App\Http\Controllers\FaxController;
use Illuminate\Support\Facades\Route;



// FAX振り分け設定
Route::get('/', [FaxController::class, 'index'])->name('fax');
Route::get('/manual', [FaxController::class, 'manual'])->name('fax.manual');
Route::post('/sort/create', [FaxController::class, 'fax_sort_create'])->name('fax.sort.create');
Route::get('/sort/delete', [FaxController::class, 'fax_sort_delete'])->name('fax.sort.delete');
Route::get('/group', [FaxController::class, 'group'])->name('fax.group');
Route::post('/group/create', [FaxController::class, 'group_create'])->name('fax.group.create');
Route::post('/group/user/create', [FaxController::class, 'group_user_create'])->name('fax.group.user.create');
Route::get('/getUserGroups', [FaxController::class, 'getUserGroups'])->name('fax.getUserGroups');
Route::get('fax/group/delete/{id}', [FaxController::class, 'group_delete'])->name('fax.group.delete');

Route::get('/folder', [FaxController::class, 'folder'])->name('fax.folder');
Route::get('/folder/update', [FaxController::class, "folder_update"])->name('fax.folder.update');

Route::get('/getFaxSortUsers', [FaxController::class, 'getFaxSortUsers'])->name('fax.getFaxSortUsers');