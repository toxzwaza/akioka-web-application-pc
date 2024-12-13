<?php

namespace App\Http\Controllers;

use App\Models\RaspiData;
use App\Services\Line;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RaspiController extends Controller
{
    //
    public function raspi_data_store(Request $request)
    {
        try {
            $process_id = $request->process_id;
            $temperature = $request->temperature;
            $humidity = $request->humidity;
            $created_at = $request->created_at;

            $raspi_data = new RaspiData();
            $raspi_data->process_id = $process_id;
            $raspi_data->temperature = $temperature;
            $raspi_data->humidity = $humidity;
            $raspi_data->created_at = $created_at;
            $raspi_data->save();

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            Log::error('温湿度登録でエラーが発生しました: ' . $e->getMessage());
            return response()->json(['status' => 'error']);
        }
    }
}
