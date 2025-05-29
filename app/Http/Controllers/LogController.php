<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    //
    public function index(){

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
