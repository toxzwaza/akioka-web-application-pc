<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\Classification;
use App\Models\Holiday;
use App\Models\InitialOrder;
use App\Models\InventoryOperationRecord;
use App\Models\OrderRequest;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class InitialOrderController extends Controller
{
    //
    public function index()
    {
        // 今月と来月のカレンダー情報取得
        $current_month_holidays = Holiday::select('date')
            ->where('date', '>=', now()->startOfMonth())
            ->where('date', '<=', now()->endOfMonth())
            ->where('is_holiday', 1)
            ->get();

        $next_month_holidays = Holiday::select('date')
            ->where('date', '>=', now()->addMonth()->startOfMonth())
            ->where('date', '<=', now()->addMonth()->endOfMonth())
            ->where('is_holiday', 1)
            ->get();

        $initial_orders = InitialOrder::select('initial_orders.*', 'stocks.img_path', 'stocks.url', 'stock_suppliers.lead_time as base_lead_time', 'suppliers.tel', 'suppliers.fax')
            ->leftJoin('stocks', 'stocks.id', 'initial_orders.stock_id')
            ->leftJoin('stock_suppliers', function ($join) {
                $join->on('stock_suppliers.stock_id', '=', 'initial_orders.stock_id')
                    ->on('stock_suppliers.supplier_id', '=', 'initial_orders.supplier_id');
            })
            ->leftJoin('suppliers', 'suppliers.id', 'initial_orders.supplier_id')
            ->orderBy('order_date', 'desc')
            ->paginate(50);

        return Inertia::render('Stock/InitialOrders', ['initial_orders' => $initial_orders, 'current_month_holidays' => $current_month_holidays, 'next_month_holidays' => $next_month_holidays]);
    }

    public function create()
    {

        $classifications = Classification::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $suppliers = Supplier::select('id', 'name', 'supplier_no')->get();

        return Inertia::render('Stock/Stocks/InitialOrder', ['classifications' => $classifications, 'users' => $users, 'suppliers' => $suppliers]);
    }


    public function store(Request $request)
    {
        $status = true;
        $msg = '';

        $stock_id = $request->stock_id;
        $name = $request->name;
        $s_name = $request->s_name;
        $jan_code = $request->jan_code;
        $img_path = $request->img_path;
        $url = $request->url;
        $purchase_identification_number = $request->purchase_identification_number;
        $price = $request->price;
        $solo_unit = $request->solo_unit;
        $org_unit = $request->org_unit;
        $quantity_per_org = $request->quantity_per_org;
        $classification_id = $request->classification_id;
        $deli_location = $request->deli_location;

        $order_user = $request->order_user;
        $user_id = $request->user_id;
        $supplier_id = $request->supplier_id;
        $lead_time = $request->lead_time;
        $quantity = $request->quantity;
        $calc_price = $request->calc_price;
        $stock_storage_id = $request->stock_storage_id;
        $postage = $request->postage;

        $upload_file = $request->file('upload_file');

        try {

            if (!$stock_id) {
                $stock = new Stock();
                $stock->name = $name;
                $stock->s_name = $s_name;
                $stock->jan_code = $jan_code;
                $stock->url = $url;
                $stock->img_path = $img_path ?? 'storage/stock/not-image-sample2.png';
                $stock->price = $price;
                $stock->purchase_identification_number = $purchase_identification_number;
                $stock->solo_unit = $solo_unit;
                $stock->org_unit = $org_unit;
                $stock->quantity_per_org = $quantity_per_org;
                $stock->classification_id = $classification_id;
                $stock->deli_location = $deli_location;
                $stock->save();

                // 発注依頼データ作成
                $order_request = new OrderRequest();
                $order_request->stock_id = $stock->id;
                $order_request->request_user_id = $order_user;
                $order_request->user_id = $user_id;
                $order_request->supplier_id = $supplier_id;
                $order_request->lead_time = $lead_time;
                $order_request->quantity = $quantity;
                $order_request->price = $price;
                $order_request->calc_price = $calc_price;
                $order_request->new_stock_flg = 1;
                $order_request->postage = $postage;
                $order_request->save();

                // 稟議書がある場合
                if ($upload_file) {
                    // VPSのLaravel APIのエンドポイントURL（例）
                    $vpsApiUrl = 'https://akioka.cloud/api/order_request/upload_file';

                    // POSTリクエストをマルチパート形式で送信
                    $response = Http::asMultipart()->attach(
                        'upload_file', // 相手側のAPIが期待するinput名
                        file_get_contents($upload_file->getRealPath()),
                        $upload_file->getClientOriginalName()
                    )->post($vpsApiUrl, [
                        // 他に送信したいデータがあればここに追加
                        'order_request_id' => $order_request->id,
                    ]);
                }
                
                $stock_supplier = new StockSupplier();
                $stock_supplier->stock_id = $stock->id;
                $stock_supplier->supplier_id = $supplier_id;
                $stock_supplier->lead_time = $lead_time;
                $stock_supplier->postage = $postage ?? 0;
                $stock_supplier->save();
            } else {

                // 発注依頼データを作成
                $order_request = new OrderRequest();
                $order_request->stock_id = $stock_id;
                $order_request->request_user_id = $order_user;
                $order_request->user_id = $user_id;
                $order_request->supplier_id = $supplier_id;
                $order_request->lead_time = $lead_time;
                $order_request->quantity = $quantity;
                $order_request->price = $price;
                $order_request->calc_price = $calc_price;
                $order_request->new_stock_flg = 0;
                $order_request->postage = $postage;
                $order_request->save();
            }

            // 格納先が設定されている場合、発注点を更新
            if ($stock_storage_id) {
                // 発注点を自動更新
                $stock = StockStorage::select(
                    'stocks.*',
                    'stock_storages.reorder_point',
                    'stock_storages.quantity'
                )
                    ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
                    ->where('stock_storages.id', $stock_storage_id)->first();

                // 発注依頼を記録
                $inventoryOperationRecord = new InventoryOperationRecord();
                $inventoryOperationRecord->inventory_operation_id = 7;
                $inventoryOperationRecord->stock_id = $stock_id;
                $inventoryOperationRecord->stock_storage_id = $stock_storage_id;
                $inventoryOperationRecord->bef_quantity = $stock->quantity;
                $inventoryOperationRecord->save();

                // 発注点再計算
                $reorder_point_avg = InventoryOperationRecord::where('stock_storage_id', $stock_storage_id)
                    ->where('inventory_operation_id', 7)
                    ->avg('bef_quantity');

                // 発注点を更新
                $stock_storage = StockStorage::find($stock_storage_id);
                $stock_storage->reorder_point = $reorder_point_avg;
                $stock_storage->save();
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'message' => $msg]);
    }

    public function update_desired_delivery_date(Request $request)
    {
        $initial_order_id = $request->initial_order_id;
        $desired_delivery_date = $request->desired_delivery_date;

        $status = true;

        try {
            $initial_order = InitialOrder::find($initial_order_id);
            $initial_order->desired_delivery_date = $desired_delivery_date;
            $initial_order->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
