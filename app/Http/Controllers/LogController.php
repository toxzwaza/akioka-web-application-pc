<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogController extends Controller
{
    //
    public function index(){
        $logs = Log::orderBy('created_at', 'desc')->get();
        return Inertia::render('Log/Index', [
            'logs' => $logs
        ]);
    }

    public function getLogs(Request $request){
        $query = Log::query();

        if ($device_name = $request->input('device_name')) {
            $query->where('device_name', $device_name);
        }

        if ($service_name = $request->input('service_name')) {
            $query->where('service_name', $service_name);
        }

        if (($level = $request->input('level')) !== null) {
            $query->where('level', $level);
        }

        if($request->input('message')){
            $query->where('message', 'like', '%'.$request->input('message').'%');
        }

        if ($request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        } elseif ($request->input('start_date')) {
            $query->where('created_at', '>=', $request->input('start_date'));
        } elseif ($request->input('end_date')) {
            $query->where('created_at', '<=', $request->input('end_date'));
        }

        $logs = $query->orderBy('created_at', 'desc')->get();
        $device_names = Log::select('device_name')->distinct()->pluck('device_name');
        $service_names = Log::select('service_name')->distinct()->pluck('service_name');

        return response()->json(['logs' => $logs, 'device_names' => $device_names, 'service_names' => $service_names]);
    }

    public function createLog(Request $request){
        $device_name = $request->input('device_name'); // EDI etc...
        $service_name = $request->input('service_name'); // EDI etc...
        $level = $request->input('level'); // 0:info, 1:warning, 2:error 
        $message = $request->input('message');

        $log = new Log();
        $log->device_name = $device_name;
        $log->service_name = $service_name;
        $log->level = $level;
        $log->message = $message;
        $log->save();

        return (string) true;
    }
}
