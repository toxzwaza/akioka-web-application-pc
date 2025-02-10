<?php

namespace App\Http\Controllers;

use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TestController extends Controller
{
    //
    public function test()
    {
        $records = InventoryOperationRecord::select('stock_id', 'stocks.name', 'stocks.img_path','stocks.s_name',DB::raw('count(*) as count'))
            ->leftJoin('stocks', 'stocks.id', 'inventory_operation_records.stock_id')
            ->where('inventory_operation_id', 2)
            ->groupBy('stock_id', 'stocks.name', 'stocks.img_path', 'stocks.s_name')
            ->orderBy('count', 'desc')
            ->get();

        return Inertia::render('Test/Test', [
            'records' => $records
        ]);
        


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
