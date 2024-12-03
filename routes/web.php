<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CalcProductController;
use App\Http\Controllers\CalcProductTabletController;
use App\Http\Controllers\FaxController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LunchController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MovieCategoryAndTagController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NewMovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RaspiController;
use App\Http\Controllers\SignageContentController;
use App\Http\Controllers\SignageController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockTabletController;
use App\Http\Controllers\TestController;
use App\Models\RaspiData;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemperatureAndHumidity;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/test', [TestController::class, 'test'])->name('test');
Route::get('/storage_address/test', [TestController::class, 'storage_address_test'])->name('storage_address.test');
Route::get('/suppliers/test', [TestController::class, 'supplier_test'])->name('supplier.test');


Route::get('login', [MainController::class, 'login'])->name('login');
Route::get('login_store', [MainController::class, 'login_store'])->name('login.store');
Route::get('logout', [MainController::class, 'logout'])->name('logout');

// ホーム
Route::get('/', [MainController::class, 'index'])->name('home');

// 基幹マスタ管理
Route::get('/master', [MasterController::class, 'index'])->name('master');
Route::get('/master/users', [MasterController::class, 'users'])->name('master.users');
Route::get('/master/akioka-users', [MasterController::class, 'akioka_users'])->name('master.akioka-users');

Route::get('/master/create/users', [MasterController::class, 'create_user'])->name('master.create.user');
Route::post('/master/store/users', [MasterController::class, 'store_user'])->name('master.store.users');
Route::get('/master/store', [MasterController::class, 'store'])->name('master.store');
Route::get('/master/edit/users/{user_id}', [MasterController::class, 'edit_user'])->name('master.edit.user');

// 在庫管理システム
Route::get('/stock/test', [StockController::class, 'test'])->name('stock.test');
Route::get('/stock', [StockController::class, 'index'])->name('stock');
Route::get('/stock/stocks', [StockController::class, 'stocks'])->name('stock.stocks');
Route::get('/stock/edit/stocks/{stock_id}', [StockController::class, 'stock_edit'])->name('stock.edit.stocks');
Route::post('/stock/store/stocks', [StockController::class, 'store_stocks'])->name('stock.store.stocks');
Route::get('/stock/stocks/create', [StockController::class, 'create_stocks'])->name('stock.stocks.create');
Route::get('/stock/stocks/taking', [StockController::class, 'stock_taking'])->name('stock.stocks.taking');

// 発注登録
Route::get('/stock/initialOrder/stocks/{stock_id}', [StockController::class, 'order_stock'])->name('stock.order');
Route::post('/stock/initialOrder/store', [StockController::class, 'order_store'])->name('stock.order.store');

// 滞留品
Route::get('/stock/retained/stocks', [StockController::class, 'retained_stocks'])->name('stock.retained.stocks');
Route::post('/stock/reatained/store', [StockController::class, 'store_retained_stocks'])->name('stock.store.retained.stocks');
Route::post('/stock/last_reatained/store', [StockController::class, 'store_last_treat_record'])->name('stock.store.last_retained.stocks');

// 製品棚卸し
Route::get('/calc/product/test', [CalcProductController::class, 'test'])->name('calc.product.test');

Route::get('/calc/product', [CalcProductController::class, 'index'])->name('calc.product');
Route::post('/calc/product/store', [CalcProductController::class, 'store'])->name('calc.product.store');
Route::get('/calc/product/start', [CalcProductController::class, 'start'])->name('calc.product.start');

//// タブレット画面
Route::get('/calc/product/tablet', [CalcProductTabletController::class, 'index'])->name('/calc/product/tablet');

Route::get('/stock/tablet/test', [StockTabletController::class, 'test']);
// 納品タブレット画面
Route::get('/stock/tablet/receive', [StockTabletController::class, 'index'])->name('stock.tablet.receive');
Route::get('/stock/tablet/getInitialOrders', [StockTabletController::class, 'getInitialOrders'])->name('stock.tablet.getInitialOrders');
// 納品書履歴確認画面(再登録や納品確定登録)
Route::get('/stock/tablet/archive', [StockTabletController::class, 'archive'])->name('stock.tablet.archive');
Route::get('/stock/tablet/getAlreadDelifileInitialOrders', [StockTabletController::class, 'getAlreadDelifileInitialOrders'])->name('stock.tablet.getAlreadDelifileInitialOrders');
// 納品登録画面
Route::get('/stock/tablet/delivery/{id}', [StockTabletController::class, 'delivery'])->name('stock.tablet.delivery');
// 納品数量登録
Route::get('/stock/tablet/updateDelivery', [StockTabletController::class, 'updateDelivery'])->name('stock.tablet.updateDelivery');    

// 納品書アップロード
Route::post('/stock/tablet/uploadFile', [StockTabletController::class, 'uploadFile'])->name('stock.tablet.uploadFile');

// 納品受領登録
Route::get('/stock/tablet/receipt', [StockTabletController::class, 'receipt'])->name('stock.tablet.receipt');
Route::get('/stock/tablet/getAReceiptOrders', [StockTabletController::class, 'getReceiptOrders'])->name('stock.tablet.getReceiptOrders');
Route::get('/stock/tablet/updateReceipt/{id}', [StockTabletController::class, 'updateReceipt'])->name('stock.tablet.updateReceipt');   


Route::get('/stock/stocks/add_supplier', [StockController::class, 'stock_add_supplier'])->name('stock.stocks.add_supplier');
Route::post('/stock/stock_suppliers/store', [StockController::class, 'store_stock_suppliers'])->name('stock.store.stock_suppliers');
Route::get('/stock/stock_suppliers/delete', [StockController::class, 'delete_stock_suppliers'])->name('stock.delete.stock_suppliers');


Route::get('/stock/storage_addresses', [StockController::class, 'storage_address'])->name('stock.storage_addresses');
Route::get('/stock/storage_addresses/create', [StockController::class, 'create_storage_addresses'])->name('stock.storage_addresses.create');
Route::get('stock/create/storage_addresses', [StockController::class, 'store_storage_address'])->name('stock.storage_address.create');

Route::post('/stock/stock_storage/update', [StockController::class, 'update_stock_storage'])->name('stock.stock_storage.update');
Route::get('/stock/stock_storage/delete', [StockController::class, 'delete_stock_storage'])->name('stock.stock_storage.delete');
Route::post('/stock/stock_storage/create', [StockController::class, 'create_stock_storage'])->name('stock.stock_storage.create');

Route::get('/stock/suppliers', [StockController::class, 'suppliers'])->name('stock.suppliers');
Route::get('/stock/suppliers/create', [StockController::class, 'create_suppliers'])->name('stock.suppliers.create');
Route::get('/stock/edit/suppliers/{supplier_id}', [StockController::class, 'supplier_edit'])->name('stock.suppliers.edit');

// Location追加
Route::get('/stock/create/locations', [StockController::class, 'store_location'])->name('stock.locations.create');




Route::get('/order', [OrderController::class, 'index'])->name('order');
// 消耗品発注依頼リスト
Route::get('/order/consumOrders', [OrderController::class, 'consumOrders'])->name('order.consumOrders');
// 消耗品発注編集
Route::post('/order/consumOrders/store',[OrderController::class, 'store_consumOrders'])->name('order.store.consumOrders');

// 消耗品発注完了
Route::get('/order/consumOrders/complete', [OrderController::class, 'complete_consumOrders'])->name('order.complete.consumOrders');
// 消耗品発注依頼削除
Route::get('/order/consumOrders/delete', [OrderController::class, 'delete_consumOrders'])->name('order.delete.consumOrders');

Route::get('/order/consumOrders/{consumOrder_id}', [OrderController::class, 'print_consumOrders'])->name('order.print.consumOrders');



Route::get('/order/already/orders', [OrderController::class, 'already_orders'])->name('order.already_orders');
Route::get('/order/orders/create', [OrderController::class, 'create_orders'])->name('order.orders.create');
Route::get('/order/already_requests', [OrderController::class, 'already_requests'])->name('order.already_requests');
Route::get('/order/approval/{id}', [OrderController::class, 'approval_judge'])->name('order.approval.judge');
Route::get('/order/object_request/{id}', [OrderController::class, 'object_request_judge'])->name('order.object_request.judge');

Route::get('/lunch', [LunchController::class, 'index'])->name('lunch');
Route::get('/lunch/order-archive', [LunchController::class, 'order_archive'])->name('lunch.order-archive');
Route::get('/lunch/order-users', [LunchController::class, 'order_users'])->name('lunch.order-users');
Route::get('/lunch/create-description', [LunchController::class, 'create_description'])->name('lunch.create_description');
Route::post('/lunch/store-description', [LunchController::class, 'store_description'])->name('lunch.store_description');
Route::get('/getMonthOrders', [LunchController::class, 'getMonthOrders']);

Route::get('/movie', [MovieController::class, 'index'])->name('movie');
Route::get('/movie/create', [MovieController::class, 'movie_create'])->name('movie.create');
Route::post('/movie/store', [MovieController::class, 'movie_store'])->name('movie.store');
Route::get('/movie/delete', [MovieController::class, 'movie_delete'])->name('movie.delete');
Route::get('/movie/change_status', [MovieController::class, 'movie_change_status'])->name('movie.change_status');
Route::get('movie/create/tag', [MovieController::class, 'movie_tag_create'])->name('movie.create.tag');

// Route::get('/movie', [MovieController::class, 'index'])->name('movie');

Route::get('/movie/{movie_id}', [MovieController::class, 'show'])->name('movie.show');
Route::post('/movie.update',[MovieController::class, 'movie_update'])->name('movie.update');
Route::get('movie/memo/delete/{memo_id}', [MovieController::class, 'movie_memo_delete'])->name('movie.memo.delete');

Route::post('movie/memo/update', [MovieController::class, 'movie_memo_update'])->name('movie.memo.update');

// FAX振り分け設定
Route::get('/fax', [FaxController::class, 'index'])->name('fax');
Route::get('/fax/manual', [FaxController::class, 'manual'])->name('fax.manual');
Route::post('/fax/sort/create', [FaxController::class, 'fax_sort_create'])->name('fax.sort.create');
Route::get('/fax/sort/delete', [FaxController::class, 'fax_sort_delete'])->name('fax.sort.delete');
Route::get('/fax/group', [FaxController::class, 'group'])->name('fax.group');
Route::post('/fax/group/create', [FaxController::class, 'group_create'])->name('fax.group.create');
Route::post('/fax/group/user/create', [FaxController::class, 'group_user_create'])->name('fax.group.user.create');
Route::get('/fax/getUserGroups', [FaxController::class, 'getUserGroups'])->name('fax.getUserGroups');
Route::get('fax/group/delete/{id}', [FaxController::class, 'group_delete'])->name('fax.group.delete');

Route::get('/fax/folder', [FaxController::class, 'folder'])->name('fax.folder');
Route::get('/fax/folder/update', [FaxController::class, "folder_update"])->name('fax.folder.update');

Route::get('/fax/getFaxSortUsers', [FaxController::class, 'getFaxSortUsers'])->name('fax.getFaxSortUsers');

// ファイル管理システム
Route::get('file', [FileController::class, 'index'])->name('file');

// Route::get('/movie/create', [MovieController::class, 'movie_create'])->name('movie.create');
// Route::post('/movie/store', [MovieController::class, 'movie_store'])->name('movie.store');
// Route::get('/movie/delete', [MovieController::class, 'movie_delete'])->name('movie.delete');
// Route::get('/movie/change_status', [MovieController::class, 'movie_change_status'])->name('movie.change_status');
// Route::get('movie/create/tag', [MovieController::class, 'movie_tag_create'])->name('movie.create.tag');

// 動画視聴Inertia
// 動画カテゴリ・タグ
Route::get('movie2/create/categoryAndTag',[MovieCategoryAndTagController::class, 'index'])->name('movie2.categoryAndTag');
Route::get('movie2/getCategories', [MovieCategoryAndTagController::class, 'getCategories'])->name('movie2.getCategories');
Route::get('movie2/getCategoryTags/{category_id}', [MovieCategoryAndTagController::class, 'getTags'])->name('movie2.getTags');
Route::post('movie2/create/category', [MovieCategoryAndTagController::class, 'create_category'])->name('movie2.create.category');
Route::post('movie2/create/tag', [MovieCategoryAndTagController::class, 'create_tag'])->name('movie2.create.tag');

// 動画
Route::get('/movie2', [NewMovieController::class, 'index'])->name('movie2');
Route::get('/searchMovie', [NewMovieController::class, 'searchMovie'])->name('searchMovie');
Route::get('/movie2/create', [NewMovieController::class, 'create'])->name('movie2.create');
Route::get('/movie2/{movie_id}', [NewMovieController::class, 'show'])->name('movie2.show');
Route::post('/movie2/store', [NewMovieController::class, 'store'])->name('movie2.store');

Route::get('/getMemos/{movie_id}', [NewMovieController::class, 'getMemos'])->name('movie2.getMemos');
Route::post('/addMemo', [NewMovieController::class, 'addMemo'])->name('movie2.addMemo');
Route::get('movie2/deleteMemo/{memo_id}', [NewMovieController::class, 'deleteMemo'])->name('movie2.deleteMemo');
Route::post('movie2/saveMemo', [NewMovieController::class, 'saveMemo'])->name('movie2.saveMemo');

// サイネージ
// テスト用
Route::get('/signage/test', [SignageController::class, 'test'])->name('signage.test');

// サイネージ用PDFの追加・一覧表示
Route::get('/signage', [SignageController::class, 'index'])->name('signage.home');
Route::post('/signage/store', [SignageController::class, 'store'])->name('signage.store');
Route::get('/signage/show/{id}', [SignageController::class, 'show'])->name('signage.show');

// サイネージコンテンツ
// --連続安全日数--
Route::get('/signage/content/safety', [SignageContentController::class, 'safety'])->name('signage.content.safety');
// --点検色--
Route::get('/signage/content/inspectionCraneColor', [SignageContentController::class, 'inspectionCraneColor'])->name('signage.content.inspectionCraneColor');
// --納品状況--
Route::get('/signage/content/stockDeliveryList', [SignageContentController::class, 'stockDeliveryList'])->name('signage.content.stockDeliveryList');
Route::get('/stock/tablet/getDeliveryOrders', [StockTabletController::class, 'getDeliveryOrders'])->name('stock.tablet.getDeliveryOrders');


// 全てのデータ取得
Route::get('/signage/getData', [SignageController::class, 'getData'])->name('signage.getData');
// データ削除
Route::get('/signage/deleteData/{asset_id}', [SignageController::class, "deleteData"])->name('signage.deleteData');
// データ更新
Route::get('/signage/updateData', [SignageController::class, 'updateData'])->name('signage.updateData');

// Route::post('/movie.update',[MovieController::class, 'movie_update'])->name('movie.update');
// Route::get('movie/memo/delete/{memo_id}', [MovieController::class, 'movie_memo_delete'])->name('movie.memo.delete');

// Route::post('movie/memo/update', [MovieController::class, 'movie_memo_update'])->name('movie.memo.update');




// API
Route::get('/api/getAddress', [ApiController::class, 'getAddress']);
// Route::get('/api/getMovieTags', [ApiController::class, 'getMovieTags']);
Route::get('/api/getSuppliers',[ApiController::class, 'getSuppliers']);
Route::get('/api/respi', [RaspiController::class, 'raspi_data_store'])->name('raspi.data.store');

// 現場温度
Route::get('/temperatureAndHumidity', [TemperatureAndHumidity::class, "temperatureAndHumidity"])->name('api.temperatureAndHumidity');
Route::get('/getData', [TemperatureAndHumidity::class, "getData"])->name('api.getData');

// 温度書き出し用
Route::get('/export/RaspiData', [TemperatureAndHumidity::class, 'export_data'])->name('raspi.export.data');