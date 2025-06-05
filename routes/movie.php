<?php
use App\Http\Controllers\MovieCategoryAndTagController;
use App\Http\Controllers\NewMovieController;
use Illuminate\Support\Facades\Route;

// 動画カテゴリ・タグ
Route::get('/create/categoryAndTag',[MovieCategoryAndTagController::class, 'index'])->name('movie2.categoryAndTag');
Route::get('/getCategories', [MovieCategoryAndTagController::class, 'getCategories'])->name('movie2.getCategories');
Route::get('/getCategoryTags/{category_id}', [MovieCategoryAndTagController::class, 'getTags'])->name('movie2.getTags');
Route::post('/create/category', [MovieCategoryAndTagController::class, 'create_category'])->name('movie2.create.category');
Route::post('/create/tag', [MovieCategoryAndTagController::class, 'create_tag'])->name('movie2.create.tag');

// 動画
Route::get('/', [NewMovieController::class, 'index'])->name('movie2');
Route::get('/searchMovie', [NewMovieController::class, 'searchMovie'])->name('searchMovie');
Route::get('/create', [NewMovieController::class, 'create'])->name('movie2.create');
Route::get('/{movie_id}', [NewMovieController::class, 'show'])->name('movie2.show');
Route::post('/store', [NewMovieController::class, 'store'])->name('movie2.store');
Route::delete('/delete', [NewMovieController::class, 'delete'])->name('movie2.delete');

Route::get('/getMemos/{movie_id}', [NewMovieController::class, 'getMemos'])->name('movie2.getMemos');
Route::post('/addMemo', [NewMovieController::class, 'addMemo'])->name('movie2.addMemo');
Route::get('/deleteMemo/{memo_id}', [NewMovieController::class, 'deleteMemo'])->name('movie2.deleteMemo');
Route::post('/saveMemo', [NewMovieController::class, 'saveMemo'])->name('movie2.saveMemo');
