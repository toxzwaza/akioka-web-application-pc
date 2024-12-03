<?php

namespace App\Http\Controllers;

use App\Models\InitialOrder;
use App\Models\SplitOrderQuantity;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Services\Method;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockTabletController extends Controller
{
    public function test()
    {
        $orders = InitialOrder::all();
        foreach ($orders as $order) {
            $order->delifile_path = null;
            $order->save();
        }
    }

    //納品書登録
    public function index()
    {
        return Inertia::render('Stock/Tablet/Receive');
    }
    // 納品履歴
    public function archive()
    {
        return Inertia::render('Stock/Tablet/Archive');
    }
    // 受領登録
    public function receipt()
    {
        return Inertia::render('Stock/Tablet/Receipt');
    }

    // 納品登録画面
    public function delivery($id)
    {
        $order = InitialOrder::find($id);
        if (!$order) {
            Method::msg('error', 'オーダーIDが見つかりませんでした。');
            return redirect()->back();
        }

        // 分納個数を取得
        $quantity_sum = 0;

        // 分納データが存在するかチェック
        $split_order_quantities = SplitOrderQuantity::where('initial_order_id', $id)->get();
        // 存在する場合、合計を取得
        if (!$split_order_quantities->isEmpty()) {
            $quantity_sum = $split_order_quantities->sum('quantity');
        }
        $order->split_quantity_sum = $quantity_sum;

        // 物品データ取得
        $stock = Stock::where('name', $order->name)->first();
        if ($stock) {

            // 画像を取得
            $order->img_path = $stock->img_path;

            // 現在の格納先アドレスリストを取得
            $stock_storages = StockStorage::select('stock_storages.id as stock_storage_id', 'stock_storages.quantity as storage_quantity', 'storage_addresses.id as address_id', 'storage_addresses.address', 'storage_addresses.location_id', 'stock_storages.quantity', 'locations.name')->join('storage_addresses', 'storage_addresses.id', 'stock_storages.storage_address_id')->join('locations', 'locations.id', 'location_id')->where('stock_id', $stock->id)->get();

            $order->stock_storages = $stock_storages;
        } else {
            $order->found_flg = 1;
        }



        return Inertia::render('Stock/Tablet/Delivery', ['order' => $order]);
    }


    // 納品書が登録されていないリスト
    public function getInitialOrders()
    {
        $initial_orders = InitialOrder::where(function ($query) {
            $query->whereNull('receive_flg')
                ->orWhere('receive_flg', 0);
        })->whereNull('delifile_path')->get();

        foreach ($initial_orders as $order) {
            $stock = Stock::where('name', $order->name)->first();
            if ($stock) {
                $order->img_path = $stock->img_path;
            } else {
                $order->found_flg = 1;
            }
        }

        return response()->json($initial_orders);
    }

    // 既に納品書を登録しているモノのリスト(再登録用)
    public function getAlreadDelifileInitialOrders()
    {
        $initial_orders = InitialOrder::where(function ($query) {
            $query->whereNull('receive_flg')
                ->orWhere('receive_flg', 0);
        })->whereNotNull('delifile_path')->orderby('updated_at', 'desc')->get();

        foreach ($initial_orders as $order) {
            $stock = Stock::where('name', $order->name)->first();
            if ($stock) {
                $order->img_path = $stock->img_path;
            } else {
                $order->found_flg = 1;
            }
        }

        return response()->json($initial_orders);
    }
    // 納品確定済みで受領されていないもののリスト
    public function getReceiptOrders()
    {
        $initial_orders = InitialOrder::where('receive_flg', 1)->where('receipt_flg', 0)->orderby('updated_at', 'desc')->get();

        foreach ($initial_orders as $order) {
            $stock = Stock::where('name', $order->name)->first();
            if ($stock) {
                $order->img_path = $stock->img_path;
            } else {
                $order->found_flg = 1;
            }
        }

        return response()->json($initial_orders);
    }
    // 注文済みで未引き渡しのリスト
    public function getDeliveryOrders(){
        $initial_orders = InitialOrder::where('receipt_flg', 0)->orderby('receive_flg', 'desc')->get();

        foreach ($initial_orders as $order) {
            $stock = Stock::where('name', $order->name)->first();
            if ($stock) {
                $order->img_path = $stock->img_path;
            } else {
                $order->found_flg = 1;
            }
        }

        return response()->json($initial_orders);
        
    }

    public function uploadFile(Request $request)
    {
        $is_success = true;

        $id = $request->id;
        $file = $request->file('file');
        try {
            if ($id && $file) {
                $timestamp = time();
                $filename = $timestamp . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/deli_file', $filename);
            }
            $order = InitialOrder::find($id);
            if ($order) {
                $order->delifile_path = '/deli_file/' . $filename;
                $order->save();
            }
        } catch (Exception $e) {
            $is_success = false;
        }

        if ($is_success) {
            return response()->json(['status' => $is_success]);
        }
    }
    // 納品数量登録
    public function updateDelivery(Request $request)
    {
        $id = $request->id;
        $quantity = $request->quantity;
        $stock_storage_id = $request->stock_storage_id;



        // 在庫数量を加算する処理を追加する
        $stock_storage = StockStorage::find($stock_storage_id);
        if ($stock_storage) {
            $order = InitialOrder::find($id);
            $quantity_sum = 0;

            // 分納データが存在するかチェック
            $split_order_quantities = SplitOrderQuantity::where('initial_order_id', $id)->get();
            // 存在する場合、合計を取得
            if (!$split_order_quantities->isEmpty()) {
                $quantity_sum = $split_order_quantities->sum('quantity');
            }
            
            
            
            if ($order->quantity == ($quantity_sum + $quantity)) {
                // 分納でない場合受け取りフラグを立てる
                $order->receive_flg = 1;
                $order->save();
            } else {
                // 分納の場合、split_order_quantitiesテーブルを作成
                $split_order_quantity = new SplitOrderQuantity();
                $split_order_quantity->initial_order_id = $order->id;
                $split_order_quantity->quantity = $quantity;
                $split_order_quantity->save();
                
            }

            // 分納した分を格納先に追加
            $stock_storage->quantity += $quantity;
            $stock_storage->save();
        }

        Method::msg('success', '納品数量登録が完了しました。');

        return response()->json(['status' => "ok"]);
    }

    // 引き渡し登録
    public function updateReceipt($id){
        $order = InitialOrder::find($id);

        $order->receipt_flg = 1;
        $order->save();
        Method::msg('success' ,'引き渡し登録を実行しました');
        
        return redirect()->back();
    }
}
