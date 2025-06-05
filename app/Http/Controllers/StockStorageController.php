<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockStorage;
use Exception;

class StockStorageController extends Controller
{
    //
    public function update(Request $request)
    {
        $stock_storage_id = $request->stock_storage_id;
        $quantity = $request->quantity;
        $reorder_point = $request->reorder_point;

        $status = true;
        $msg = "";

        try {

            $stock_supplier = StockStorage::find($stock_storage_id);
            $stock_supplier->quantity = $quantity;
            $stock_supplier->reorder_point = $reorder_point;
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

        $stock_storage_id = $request->stock_storage_id;
        try {
            if ($stock_storage_id) {
                $stock_storage =StockStorage::find($stock_storage_id);
                $stock_storage->delete();
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
