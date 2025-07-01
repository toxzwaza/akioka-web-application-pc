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
        try {


            // $stocks = Stock::where('classification_id', 34)->get();
            // foreach ($stocks as $stock) {
            //     $stock_price_archive = new StockPriceArchive();
            //     if ($stock->price) {
            //         $stock_price_archive->stock_id = $stock->id;
            //         $stock_price_archive->price = $stock->price;
            //         $stock_price_archive->save();
            //     }
            // }
            // return;
            $filePath = public_path('csv/原材料.csv');
            if (File::exists($filePath)) {
                $fileContent = File::get($filePath);

                // CSVを配列に変換（改行で分割）
                $lines = explode("\n", $fileContent);

                // 最初の20行を処理
                for ($i = 265; $i < 300 && $i < count($lines); $i++) {
                    $line = trim($lines[$i]);
                    if (empty($line)) continue; // 空行をスキップ

                    // CSVの各列を配列に分割
                    $columns = str_getcsv($line);

                    if (count($columns) >= 10) {
                        // 各列を変数に格納
                        $final_order_date = $columns[0]; // 最終注文日
                        $orderer = $columns[1];           // 注文者
                        $product_name = $columns[2];      // 品名
                        $product_code = $columns[3];      // 品番
                        $supplier_name = $columns[4];          // 注文先
                        $unit_price = $columns[5];        // 単価
                        $order_quantity = $columns[6];    // 発注数
                        $unit_1 = $columns[7];              // 単位1
                        $unit_2 = $columns[8];      // 単位2
                        $price = $columns[9];      // 金額
                        $process_code = $columns[10];      // 工程コード

                        if ($product_code == '0') {
                            $product_code = '';
                        }
                    }

                    $user = User::where('name', "like", "%" . $orderer . "%")->first();
                    // if (!$user) {
                    //     echo "ユーザーを取得できませんでした。<br>";
                    //     echo "ユーザー名: " . $orderer . "<br>";
                    //     echo "<hr>";

                    // }else{
                    //     // echo "ユーザーを取得できました。<br>";
                    //     // echo "ユーザー名: " . $user->name . "<br>";
                    //     // echo "ユーザーID: " . $user->id . "<br>";
                    //     // echo "<hr>";
                    // }
                    if ($user) {
                        $stock = Stock::select('stocks.id', 'stock_suppliers.id as stock_supplier_id')
                            ->where('name', $product_name)
                            ->where('s_name', $product_code)
                            ->leftJoin('stock_suppliers', 'stock_suppliers.stock_id', 'stocks.id')
                            ->first();

                        $initial_order = new InitialOrder();
                        $initial_order->stock_id = $stock->id;
                        $initial_order->calc_price = $stock->price ?? 0;
                        $initial_order->order_user_id = $user->id;
                        $initial_order->save();
                    }

                        ///////////////////////////////////
                    ;
                    ///////////////////////////////////

                    // if (!$stock->stock_supplier_id) {
                    //     $supplier = Supplier::where('name', $supplier_name)->first();
                    //     if (!$supplier) {
                    //         echo "発注先が登録されていません。<br>";
                    //         $supplier = new Supplier();
                    //         $supplier->name = $supplier_name;
                    //         $supplier->save();
                    //     }

                    //     echo "発注先を紐づけます。<br>";
                    //     $stock_supplier = new StockSupplier();
                    //     $stock_supplier->stock_id = $stock->id;
                    //     $stock_supplier->supplier_id = $supplier->id;
                    //     $stock_supplier->lead_time = 999; //とりあえず
                    //     $stock_supplier->act_flg = 1;
                    //     $stock_supplier->save();

                    //     // デバッグ用：各変数の値を表示
                    //     echo "行 " . $i . ":<br>";
                    //     echo "最終注文日: " . $final_order_date . "<br>";
                    //     echo "注文者: " . $orderer . "<br>";
                    //     echo "品名: " . $product_name . "<br>";
                    //     echo "品番: " . $product_code . "<br>";
                    //     echo "<hr>";
                    // } else {
                    //     echo "発注先が登録されています。<br>";
                    //     echo "品名: " . $product_name . "<br>";
                    //     echo "品番: " . $product_code . "<br>";
                    //     echo "<hr>";
                    // }

                    // if ($stock) {
                    //     echo "存在します。<br>";
                    //     $stock->classification_id = 34;
                    //     $stock->solo_unit = $unit_1 != '0' ? $unit_1 : $unit_2;
                    //     $stock->price = $unit_price;
                    //     $stock->save();

                    // } else {
                    //     echo "存在しません。<br>";
                    //     $stock = new Stock();
                    //     $stock->name = $product_name;
                    //     $stock->s_name = $product_code;
                    //     $stock->price = $unit_price;
                    //     $stock->classification_id = 34;
                    //     $stock->solo_unit = $unit_1 != '0' ? $unit_1 : $unit_2;
                    //     $stock->save();
                    // }

                }
            } else {
                echo "ファイルが見つかりません。";
            }
        } catch (Exception $e) {
            echo "エラーが発生しました: " . $e->getMessage();
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
