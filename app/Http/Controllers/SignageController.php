<?php

namespace App\Http\Controllers;

use App\Models\Signage;
use App\Models\SignageDisplay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class SignageController extends Controller
{
    public function test()
    {
    }
    //
    public function index(Request $request)
    {
        $display_id = $request->display_id;
        
        if(!$display_id){

            $signage_display = SignageDisplay::first();
        }else{
            $signage_display = SignageDisplay::find($display_id);
        }

        $signages = Signage::all();


        return Inertia::render('Signage/Index', ['display' => $signage_display,'signages' => $signages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf',
            'name' => "required"
        ]);
        $name = $request->name;

        $file = $request->file('file');
        $timestamp = now()->timestamp;
        $fileName = $timestamp . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('pdf', $fileName, 'public');

        // サイネージ用DBに追加
        $signage = new Signage();
        $signage->name = $name;
        $signage->file_name = $fileName;
        $signage->save();

        // サイネージコンピュータに登録
        $url = 'http://192.168.210.90/api/v1.2/assets';

        $data = [
            "name" => $name,  // 変数nameを使用
            "uri" => 'http://monokanri-manage.local/signage/show/' . $signage->id,  // 変数nameを使用
            "start_date" => now()->toIso8601String(),
            "end_date" => now()->addYear()->toIso8601String(),
            "duration" => 10,
            "mimetype" => "webpage",
            "is_enabled" => 1,
            "is_processing" => 0,
            "nocache" => 0,
            "play_order" => 1,
            "skip_asset_check" => 1,
            "is_active" => 1
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $data);



        return redirect()->route('signage.home');
    }

    public function show($id)
    {
        $signage = Signage::findOrFail($id);

        // 常に表示するメインサイネージ
        $signage_main = Signage::where('is_main', 1)->first();
        if(!$signage_main){
            $signage_main = Signage::first();
        }

        return Inertia::render('Signage/Show', [
            'file_name' => $signage->file_name,
            'main_file_name' => $signage_main->file_name
        ]);
    }

    public function getData(Request $request)
    {
        $address = $request->address;

        $url = "http://{$address}/api/v1.2/assets";

        // GETリクエストを送信してすべてのアセットを取得
        $response = file_get_contents($url);

        // レスポンスの確認
        if ($response !== false) {
            $assets = json_decode($response, true);
            foreach($assets as &$asset){  // 参照渡しを使用
                $signage = Signage::where('name', $asset['name'])->first();
                if ($signage) {  // Signageが見つかった場合のみ
                    $asset['id'] = $signage->id;
                } else {
                    $asset['id'] = null;  // または適切なデフォルト値
                }
            }
            unset($asset);  // 参照を解除

            return response()->json($assets);
        } else {
            return response()->json(['error' => 'エラーが発生しました: ' . $http_response_header[0]], 500);
        }
    }

    public function deleteData($asset_id)
    {
        $url = 'http://192.168.210.90/api/v1.2/assets/' . $asset_id;

        // DELETEリクエストを送信
        $response = Http::delete($url);

        // レスポンスの確認
        if ($response->successful()) {
            return response()->json(['message' => 'アセットが正常に削除されました。']);
        } else {
            return response()->json(['error' => 'エラーが発生しました: ' . $response->body()], 500);
        }
    }
    public function updateData(Request $request)
    {
        $asset_id = $request->asset_id;
        $data = $request->setData;


        $url = 'http://192.168.210.90/api/v1.2/assets/' . $asset_id;

        // return response()->json(['url' => $url, 'data' => $data]);

        // 更新するアセットのデータ
        // $data = [
        //     "asset_id" => "95f63a8a75f745af887a14c4a2a3095d",
        //     "duration" => "600",
        //     "end_date" => "2024-12-16T10:15:00+09:00",
        //     "is_active" => 0,
        //     "is_enabled" => 0,
        //     "is_processing" => 0,
        //     "mimetype" => "webpage",
        //     "name" => "http://monokanri-manage.local/signage/show/4",
        //     "nocache" => 0,
        //     "play_order" => 1,
        //     "skip_asset_check" => 0,
        //     "start_date" => "2024-11-16T10:15:00+09:00",
        //     "uri" => "http://monokanri-manage.local/signage/show/4"
        // ];

        // PATCHリクエストを送信
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->patch($url, $data);

        // レスポンスの確認
        if ($response->successful()) {
            return response()->json(['message' => 'アセットのdurationが正常に更新されました。', 'log' => $response]);
        } else {
            return response()->json(['error' => 'エラーが発生しました: ' . $response->body()], 500);
        }
    }
}
