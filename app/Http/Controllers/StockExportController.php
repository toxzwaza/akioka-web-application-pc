<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockExportController extends Controller
{
    /**
     * 在庫データをCSV形式でエクスポートする
     * main_flgが1のサプライヤーを優先的に結合
     */
    public function exportStocksWithSuppliers()
    {
        // main_flgが1のstock_suppliersを優先的に結合
        $stocks = Stock::select(
            'suppliers.name as supplier_name',
            'stocks.name',
            'stocks.memo',
            'stocks.s_name',
            'stocks.id',
            'stocks.price',
            'stocks.solo_unit',
            'stocks.org_unit',
            'stocks.quantity_per_org',
            'stocks.deli_location',
            'stocks.created_at',
            'prioritized_stock_suppliers.lead_time'
        )
        ->join(DB::raw('(
            SELECT ss1.*
            FROM stock_suppliers ss1
            INNER JOIN (
                SELECT stock_id, 
                       COALESCE(MAX(CASE WHEN main_flg = 1 THEN id END), MIN(id)) as selected_id
                FROM stock_suppliers
                GROUP BY stock_id
            ) ss2 ON ss1.id = ss2.selected_id
        ) as prioritized_stock_suppliers'), 'prioritized_stock_suppliers.stock_id', '=', 'stocks.id')
        ->join('suppliers', 'suppliers.id', '=', 'prioritized_stock_suppliers.supplier_id')
        ->get();

        // CSVファイルとして書き出し
        $filename = 'stocks_export_' . date('Ymd_His') . '.csv';
        $filepath = public_path('csv/' . $filename);
        
        $file = fopen($filepath, 'w');
        
        // BOM付きUTF-8でExcelで正しく表示されるようにする
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // ヘッダー行
        fputcsv($file, [
            'supplier_name',  // 1列目
            '',               // 2列目
            '',               // 3列目
            '',               // 4列目
            '',               // 5列目
            'name',           // 6列目
            'memo',           // 7列目
            's_name',         // 8列目
            'id',             // 9列目
            'price',          // 10列目
            'solo_unit',      // 11列目
            '',               // 12列目
            'solo_unit',      // 13列目
            'quantity_per_org', // 14列目
            'org_unit',       // 15列目
            'deli_location',  // 16列目
            '',               // 17列目
            '',               // 18列目
            '',               // 19列目
            'lead_time'       // 20列目
        ]);
        
        // データ行
        foreach ($stocks as $stock) {
            fputcsv($file, [
                $stock->supplier_name,    // 1列目
                '',                       // 2列目
                '',                       // 3列目
                '',                       // 4列目
                '',                       // 5列目
                $stock->name,             // 6列目
                $stock->memo,             // 7列目
                $stock->s_name,           // 8列目
                $stock->id,               // 9列目
                $stock->price,            // 10列目
                $stock->solo_unit,        // 11列目
                '',                       // 12列目
                $stock->solo_unit,        // 13列目
                $stock->quantity_per_org, // 14列目
                $stock->org_unit,         // 15列目
                $stock->deli_location,    // 16列目
                '',                       // 17列目
                '',                       // 18列目
                '',                       // 19列目
                $stock->lead_time         // 20列目
            ]);
        }
        
        fclose($file);
        
        return response()->json([
            'success' => true,
            'message' => 'CSVファイルを作成しました',
            'filename' => $filename,
            'filepath' => '/csv/' . $filename,
            'count' => count($stocks)
        ]);
    }
}

