<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\ImportProduct;
use App\Models\InitialOrder;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StockSupplier;
use App\Models\StorageAddress;
use App\Models\Supplier;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TestController extends Controller
{
    //
    public function test()
    {
        $price = 900000;
        // 承認フローを作成
        $user_id = 91; //電気炉一般
        $user = User::find($user_id);
        if (!$user->group_id >= 8) {
            return [];
        }

        $approval_list = [];


        // 部署ごとの承認者マッピング
        $approvalMap = [
            1 => 94,
            2 => 16, 
            3 => 37,
            4 => 84,
            5 => 2
        ];

        // 基本の承認フロー
        $approval_list[] = $approvalMap[$user->group_id] ?? 63;

        // 10,000円以上の場合の追加承認
        if ($price >= 10000) {
            if ($user->group_id == 2) {
                $approval_list[] = 94;
            } else if (in_array($user->group_id, [3, 4, 5])) {
                $approval_list[] = 2;
            }
        }

        // 150,000円以上の場合の追加承認
        if ($price >= 150000 && in_array($user->group_id, [1, 2, 6, 7])) {
            $approval_list[] = 2;
        }

        return $approval_list;
        
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
