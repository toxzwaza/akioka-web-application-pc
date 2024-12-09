<?php

namespace App\Http\Controllers;

use App\Models\RemoteComputer;
use App\Services\Method;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RemoteController extends Controller
{
    //
    public function index()
    {
        if (!Method::isLogin()) {
            session()->put('bef_url', 'remote');
            return to_route('login');
        }
        $user = session('user');

        $remoteComputer = RemoteComputer::where('user_id', $user->id)->first();

        return Inertia::render('Remote/Index', ['user' => $user, 'remoteComputer' => $remoteComputer ]);
    }

    public function create()
    {
        if (!Method::isLogin()) {
            session()->put('bef_url', 'remote');
            return to_route('login');
        }
        $user = session('user');

        // 登録済みコンピュータを取得
        $remoteComputers = RemoteComputer::where('user_id', $user->id)->get();


        return Inertia::render('Remote/Create', ['user' => $user, 'remoteComputers' => $remoteComputers]);
    }

    public function store(Request $request)
    {
        $user_id = $request->user_id;
        $machine_name = $request->machine_name;
        $mac_address = $request->mac_address;

        if ($user_id && $machine_name && $mac_address) {
            try {
                $remoteComputer = new RemoteComputer();
                $remoteComputer->user_id = $user_id;
                $remoteComputer->machine_name = $machine_name;
                $remoteComputer->mac_address = $mac_address;
                $remoteComputer->save();

                return response()->json(['status' => 'ok', 'user_id' => $user_id, 'machine_name' => $machine_name, 'mac_address' => $mac_address]);
            } catch (Exception $e) {
                return response()->json(['status' => 'no']);
            }
        } else {
            return response()->json(['status' => 'no']);
        }
    }
}
