<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\Classification;
use App\Models\Holiday;
use App\Models\InitialOrder;
use App\Models\InventoryOperationRecord;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $initial_orders = InitialOrder::
        select('initial_orders.*', 'stocks.img_path', 'stocks.url', 'stock_suppliers.lead_time as base_lead_time', 'suppliers.tel' , 'suppliers.fax')
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

        $order_user_name = '';

        $user = User::where('id', $order_user)->first();
        if ($user) {
            $order_user_name = $user->name;
        }

        try {
            $supplier = Supplier::find($supplier_id);

            if (!$stock_id) {
                DB::transaction(function () use ($name, $s_name, $jan_code, $url, $img_path, $price, $purchase_identification_number, $solo_unit, $org_unit, $quantity_per_org, $classification_id, $deli_location, $order_user, $user_id, $supplier_id, $lead_time, $quantity, $calc_price, $supplier, $order_user_name) {
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

                    $initial_order = new InitialOrder();
                    $initial_order->stock_id = $stock->id;
                    $initial_order->order_no = Helper::createOrderNo();
                    $initial_order->order_date = date('Y-m-d');
                    $initial_order->com_no = $supplier->supplier_no;
                    $initial_order->com_name = $supplier->name;
                    $initial_order->name = $name;
                    $initial_order->s_name = $s_name;
                    $initial_order->order_unit = $solo_unit;
                    $initial_order->deli_location = $deli_location;
                    $initial_order->order_user = $order_user_name;
                    $initial_order->order_user_id = $order_user;
                    $initial_order->user_id = $user_id;
                    $initial_order->supplier_id = $supplier_id;
                    $initial_order->price = $price;
                    $initial_order->quantity = $quantity;
                    $initial_order->calc_price = $calc_price;
                    $initial_order->expected_delivery_date = date('Y-m-d', strtotime('+' . $lead_time . ' days'));
                    $initial_order->save();

                    $stock_supplier = new StockSupplier();
                    $stock_supplier->stock_id = $stock->id;
                    $stock_supplier->supplier_id = $supplier_id;
                    $stock_supplier->lead_time = $lead_time;
                    $stock_supplier->save();
                });
            } else {

                $initial_order = new InitialOrder();
                $initial_order->stock_id = $stock_id;
                $initial_order->order_no = Helper::createOrderNo();
                $initial_order->order_date = date('Y-m-d');
                $initial_order->com_no = $supplier->supplier_no;
                $initial_order->com_name = $supplier->name;
                $initial_order->name = $name;
                $initial_order->s_name = $s_name;
                $initial_order->order_unit = $solo_unit;
                $initial_order->deli_location = $deli_location;
                $initial_order->user_id = $user_id;
                $initial_order->order_user_id = $order_user;
                $initial_order->order_user = $order_user_name;
                $initial_order->supplier_id = $supplier_id;
                $initial_order->lead_time = $lead_time;
                $initial_order->price = $price;
                $initial_order->quantity = $quantity;
                $initial_order->calc_price = $calc_price;
                $initial_order->expected_delivery_date = date('Y-m-d', strtotime('+' . $lead_time . ' days'));
                $initial_order->save();
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
