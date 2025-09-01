<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockStorage;
use App\Models\StorageAddress;
use Exception;

class StockStorageController extends Controller
{
    //
    public function store(Request $request)
    {
        $status = true;
        $storage_address_id = $request->storage_address_id;
        $location_id = $request->location_id;

        $shelf = $request->shelf;
        $row = $request->row;
        $col = $request->col;
        $sub_row = $request->sub_row;
        
        $parts = array_filter([$shelf, $row, $col, $sub_row], function ($value) {
            return !is_null($value) && $value !== '';
        });
        $address = implode('-', $parts);

        try {
            if ($storage_address_id) { //編集
                $storage_address = StorageAddress::find($storage_address_id);
            } else { //新規登録
                $storage_address = new StorageAddress();
                $storage_address->location_id = $location_id;
            }


            $storage_address->address = $address;
            $storage_address->shelf = $shelf;
            $storage_address->row = $row;
            $storage_address->col = $col;
            $storage_address->sub_row = $sub_row;
            $storage_address->save();
        } catch (Exception $e) {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }

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
