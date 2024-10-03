<?php

namespace App\Http\Controllers;

use App\Models\CalcProductFile;
use App\Services\Method;
use Illuminate\Http\Request;

class CalcProductController extends Controller
{
    //
    public function test(){

    }
    
    public function index()
    {
        $calcProductFiles = CalcProductFile::orderby('id', 'desc')->get();

        return view('calc.product', compact('calcProductFiles'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');

            $timestamp = time();
            $filename = $timestamp . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $filename);

            // 製品棚卸しテーブルに保存
            $calcProductFile = new CalcProductFile();
            $calcProductFile->file_path = $filePath;
            $calcProductFile->save();

            Method::msg('success', 'ファイルを保存しました。');
        } else {
            Method::msg('error', 'ファイルが見つかりませんでした。');
        }
        return redirect()->back();
    }

    public function start(Request $request)
    {
        // 削除メソッドに変更
        

    }
}
