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
}
