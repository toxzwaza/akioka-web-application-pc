<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\ImportProduct;
use App\Models\InitialOrder;
use App\Models\InventoryOperation;
use App\Models\InventoryOperationRecord;
use App\Models\LastTreatRecord;
use App\Models\Log;
use App\Models\Movie;
use App\Models\MovieMemo;
use App\Models\Stock;
use App\Models\StockPriceArchive;
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
        $users = User::select(
            'users.name',
            'groups.name as group_name',
            'positions.name as position_name',
            'users.id',
            'users.group_id'
        )
            ->where('del_flg', 0)
            ->where('position_id', 6)
            ->join('groups', 'users.group_id', 'groups.id')
            ->join('positions', 'positions.id', 'users.position_id')
            ->orderBy('group_id', 'asc')->orderBy('position_id', 'desc')->get();
        $prices = [10000, 10001, 100000, 100001, 150000, 150001];
        $new_flgs = [0, 1];


        foreach ($users as $user) {
            echo ("$user->group_name : $user->position_name : {$user->name}");
            echo ("<br />");
            foreach ($prices as $price) {
                echo ("金額: $price");
                echo ("<br />");
                foreach ($new_flgs as $new_flg) {
                    echo ($new_flg ? '・新規品' : '・既存品');
                    echo ("<br />");

                    $approval_users = Helper::createApprovalFlow($price, $user->id, $new_flg);
                    if (count($approval_users) > 0) {
                        echo (implode('->', $approval_users));
                        echo ("<br />");
                    } else {
                        echo ("承認者なし");
                        echo ("<br />");
                    }
                }
            }
        }
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
