<?php

namespace App\Http\Controllers;

use App\Models\RaspiData;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemperatureAndHumidity extends Controller
{
    //
    public function temperatureAndHumidity()
    {


        $data_lists = [];

        for ($process_id = 1; $process_id <= 7; $process_id++) {
            $startOfDay = \Carbon\Carbon::today();
            $endOfDay = \Carbon\Carbon::today();

            $data = RaspiData::where('process_id', $process_id)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->orderby('id', 'desc')
                ->get(['created_at', 'temperature', 'humidity']);

            $labels = $data->pluck('created_at')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('G時');
            });


            $temperatures = $data->pluck('temperature');
            $humidities = $data->pluck('humidity');

            $wbgt = [];
            for ($i = 0; $i < min(10, $data->count()); $i++) {
                $wbgt[] = round(0.725 * $temperatures[$i] + 0.0368 * $humidities[$i] + 0.00364 * ($temperatures[$i] * $humidities[$i]), 1);
            }

            $data_lists[] = [
                'process_id' => $process_id,
                'labels' => $labels,
                'temperatures' => $temperatures,
                'humidities' => $humidities,
                'wbgt' => $wbgt
            ];
        }




        return Inertia::render('TemperatureAndHumidity', ['data_lists' => $data_lists]);
    }

    public function getData(Request $request)
    {
        $data_lists = [];
        $start_date = $request->start_date;
        $finish_date = $request->finish_date;
        if ($start_date && $finish_date) {
            $start_date = \Carbon\Carbon::parse($start_date);
            $finish_date = \Carbon\Carbon::parse($finish_date);
        } else {
            $start_date = \Carbon\Carbon::today();
            $finish_date = \Carbon\Carbon::today();
        }


        for ($process_id = 1; $process_id <= 7; $process_id++) {


            $data = RaspiData::where('process_id', $process_id)
                ->whereBetween('created_at', [$start_date, $finish_date])
                ->orderby('id', 'desc')
                ->get(['created_at', 'temperature', 'humidity']);

            $labels = $data->pluck('created_at')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('G時');
            });


            $temperatures = $data->pluck('temperature');
            $humidities = $data->pluck('humidity');

            $wbgt = [];
            for ($i = 0; $i < min(10, $data->count()); $i++) {
                $wbgt[] = round(0.725 * $temperatures[$i] + 0.0368 * $humidities[$i] + 0.00364 * ($temperatures[$i] * $humidities[$i]), 1);
            }

            $data_lists[] = [
                'process_id' => $process_id,
                'labels' => $labels,
                'temperatures' => $temperatures,
                'humidities' => $humidities,
                'wbgt' => $wbgt
            ];
        }

        return response()->json($data_lists);
    }
}
