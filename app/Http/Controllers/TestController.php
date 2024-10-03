<?php

namespace App\Http\Controllers;

use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    //
    public function test()
    {

        $retained_stocks = LastTreatRecord::select('stocks.name as stock_name', 'stocks.price', 'stock_storages.quantity')->join('stocks', 'stocks.id', 'last_treat_records.stock_id')->join('stock_storages', 'stock_storages.stock_id', 'last_treat_records.stock_id')->where('treat', '廃棄')->get();

        $total = 0;
        foreach($retained_stocks as $stock){
            $cal = $stock->price * $stock->quantity;
            $total += $cal;

            echo "<p>{$stock->stock_name} 金額:{$total}</p>";
        }

        echo "<p>合計：{$total}</p>";
    }

    public function storage_address_test()
    {
        $storage_addresses = StorageAddress::all();

        foreach ($storage_addresses as $storage_address) {
            list($alphabet, $number1, $number2) = explode('-', $storage_address->address);
            // dd($alphabet,$number1,$number2);
            $storage_address->shelf = $alphabet;
            $storage_address->row = $number1;
            $storage_address->col = $number2;
            $storage_address->save();
        }
    }

    public function supplier_test()
    {
        $stock_id = 5361;
        $stock_suppliers = StockSupplier::select('suppliers.*', 'stock_suppliers.lead_time', 'stock_suppliers.act_flg')->where('stock_id', $stock_id)->join('suppliers', 'suppliers.id', 'stock_suppliers.supplier_id')->get();
        dd($stock_suppliers);
    }
}
