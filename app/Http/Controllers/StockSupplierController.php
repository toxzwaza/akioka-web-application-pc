<?php

namespace App\Http\Controllers;

use App\Models\StockSupplier;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockSupplierController extends Controller
{
    public function store(Request $request)
    {
        $status = true;

        $stock_id = $request->stock_id;
        $supplier_id = $request->supplier_id;
        $lead_time = $request->lead_time;

        $stock_supplier = null;

        try {
            // 既に登録されていないかチェック
            $stock_supplier = StockSupplier::where('stock_id', $stock_id)->where('supplier_id', $supplier_id)->first();

            if ($stock_supplier) {
                $stock_supplier->lead_time = $lead_time;
                $stock_supplier->save();
            } else {
                $stock_supplier = new StockSupplier();
                $stock_supplier->stock_id = $stock_id;
                $stock_supplier->supplier_id = $supplier_id;
                $stock_supplier->lead_time = $lead_time;
                $stock_supplier->save();
            }
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

    public function update(Request $request)
    {
        $stock_supplier_id = $request->stock_supplier_id;
        $lead_time = $request->lead_time;
        $postage = $request->postage;

        $status = true;
        $msg = "";

        try {

            $stock_supplier = StockSupplier::find($stock_supplier_id);
            $stock_supplier->lead_time = $lead_time;
            $stock_supplier->postage = $postage;
            $stock_supplier->save();
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }
        
        return response()->json(['status' => $status, 'msg' => $msg]);
    }
    public function delete(Request $request)
    {
        $status = true;
        $msg = "";

        $stock_supplier_id = $request->stock_supplier_id;
        try {
            if ($stock_supplier_id) {
                $stock_supplier = StockSupplier::find($stock_supplier_id);
                $stock_supplier->delete();
            } else {
                $status = false;
                $msg = "データが見つかりません";
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }
        return response()->json(['status' => $status, 'msg' => $msg]);
    }
}
