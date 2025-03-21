<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Group;
use App\Models\Holiday;
use App\Models\Position;
use App\Models\Process;
use App\Models\User;
use App\Services\Method;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterController extends Controller
{
    //
    public function index()
    {
        // 現状は、従業員追加ページへリダイレクト
        return to_route('master.create.user');


        return view('master.index');
    }


    // ユーザー作成
    public function create_user()
    {
        $groups = Group::all();
        $positions = Position::all();
        $processes = Process::all();
        return view('master.create_user', compact('groups', 'positions', 'processes'));
    }


    public function users()
    {
        $users  = User::
        select('users.id', 'users.name', 'users.group_id', 'users.position_id','users.process_id','users.gender_flg','users.email','users.emp_no', 'groups.name as group_name', 'positions.name as position_name', 'processes.name as process_name')
        ->leftJoin('groups', 'groups.id', 'users.group_id')
        ->leftJoin('positions', 'positions.id', 'users.position_id')
        ->leftJoin('processes', 'processes.id', 'users.process_id')
        ->orderBy('emp_no', 'asc')
        ->where('del_flg', 0)
        ->get();

        // 部署一覧
        $groups  = Group::all();
        // 工程一覧
        $processes  = Process::all();
        // 役職一覧
        $positions  = Position::all();

        return Inertia::render('Master/Users', ['users' => $users, 'groups' => $groups, 'processes' => $processes, 'positions' => $positions]);

    }




    public function store_user(Request $request)
    {

        $name = $request->name;
        $email = $request->email;
        $pwd = $request->pwd ?? 'pwd';
        $fax_folder_name = $request->fax_folder_name;
        $group_id = $request->group_id;
        $position_id = $request->position_id;
        $process_id = $request->process_id;
        $is_admin = $request->is_admin ? 1 : 0;
        $dispatch_flg = $request->dispatch_flg ? 1 : 0;
        $part_flg = $request->part_flg ? 1 : 0;
        $always_order_flg = $request->always_order_flg ? 1 : 0;

        try {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = $pwd;
            $user->group_id = $group_id;
            $user->position_id = $position_id;
            $user->process_id = $process_id;
            $user->is_admin = $is_admin;
            $user->dispatch_flg = $dispatch_flg;
            $user->part_flg = $part_flg;
            $user->always_order_flg = $always_order_flg;
            $user->fax_folder_name = $fax_folder_name;
            $user->save();
        } catch (Exception $e) {
            Method::errorMsg();
            return redirect()->back();
        }


        Method::msg('success', 'ユーザー登録が完了しました。');
        return redirect()->back();
    }

    public function edit_user($user_id)
    {
        $user = User::where('id', '=', $user_id)->first();

        $groups = Group::all();
        $positions = Position::all();
        $processes = Process::all();

        return view('master.edit_user', compact('user', 'groups', 'positions', 'processes'));
    }

    public function store()
    {

        return;
    }

    // カレンダー編集
    public function calender()
    {

        return Inertia::render('Master/Calender');
    }
    public function get_holidays(Request $request)
    {
        $holidays = Holiday::where('is_holiday', '=', 1)->pluck('date');

        return response()->json($holidays);
    }
    public function store_holiday(Request $request)
    {
        $holidays = $request->collect('holidays');
        $status = true;

        try {
            foreach ($holidays as $holiday_date) {
                $holiday = new Holiday();
                $holiday->date = $holiday_date;
                $holiday->is_holiday = 1;
                $holiday->save();
            }
        } catch (Exception $e) {
            $status = false;
            return response()->json(['status' => $status, 'error' => $e->getMessage()]);
        }


        return response()->json(['status' => $status]);
    }
}
