<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Holiday;
use App\Models\Position;
use App\Models\Process;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterController extends Controller
{
    public function index()
    {
        $tab = request()->query('tab');
        $initialTab = in_array($tab, ['group', 'user', 'list'], true) ? $tab : 'list';

        $users = User::select('users.id', 'users.name', 'users.group_id', 'users.position_id', 'users.process_id', 'users.gender_flg', 'users.email', 'users.emp_no', 'groups.name as group_name', 'positions.name as position_name', 'processes.name as process_name')
            ->leftJoin('groups', 'groups.id', 'users.group_id')
            ->leftJoin('positions', 'positions.id', 'users.position_id')
            ->leftJoin('processes', 'processes.id', 'users.process_id')
            ->orderBy('emp_no', 'asc')
            ->where('del_flg', 0)
            ->get();

        $groups = Group::all();
        $processes = Process::all();
        $positions = Position::all();

        return Inertia::render('Master/Home', [
            'users' => $users,
            'groups' => $groups,
            'processes' => $processes,
            'positions' => $positions,
            'initialTab' => $initialTab,
            'stats' => [
                'user_count' => User::where('del_flg', 0)->count(),
                'group_count' => Group::count(),
            ],
        ]);
    }


    public function store_user(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $emp_no = $request->emp_no;
        $gender_flg = $request->gender_flg;
        $email = $request->email;
        $pwd = $request->password ?? 'pwd';
        $fax_folder_name = $request->fax_folder_name;
        $group_id = $request->group_id;
        $position_id = $request->position_id;
        $process_id = $request->process_id;
        $is_admin = $request->is_admin ? 1 : 0;
        $dispatch_flg = $request->dispatch_flg ? 1 : 0;
        $part_flg = $request->part_flg ? 1 : 0;
        $always_order_flg = $request->always_order_flg ? 1 : 0;
        $del_flg = $request->del_flg ? 1 : 0;

        $status = true;
        $msg = "";


        try {
            $user = $id ? User::find($id) : new User();
            $user->fill([
                'name' => $name,
                'emp_no' => $emp_no,
                'gender_flg' => $gender_flg,
                'email' => $email,
                'password' => $pwd,
                'group_id' => $group_id,
                'position_id' => $position_id,
                'process_id' => $process_id,
                'is_admin' => $is_admin,
                'dispatch_flg' => $dispatch_flg,
                'part_flg' => $part_flg,
                'always_order_flg' => $always_order_flg,
                'fax_folder_name' => $fax_folder_name,
                'del_flg' => $del_flg,
            ]);
            $user->save();
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }


        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function show_user($user_id)
    {
        $user = User::where('id', $user_id)->first();

        $groups = Group::all();
        $positions = Position::all();
        $processes = Process::all();

        return Inertia::render('Master/Users/Show', ['user' => $user, 'groups' => $groups, 'processes' => $processes, 'positions' => $positions]);
    }

    public function store_group(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $phone_number = $request->phone_number;

        $status = true;
        $msg = "";

        try {
            $group = $id ? Group::find($id) : new Group();
            $group->fill([
                'name' => $name,
                'phone_number' => $phone_number,
            ]);
            $group->save();
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }

        return response()->json(['status' => $status, 'msg' => $msg]);
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
            // 既存の休日をすべて削除（is_holiday = 0に設定）
            Holiday::where('is_holiday', 1)->update(['is_holiday' => 0]);

            // 新しい休日を登録（既存の日付があればupdateし、なければinsert）
            foreach ($holidays as $holiday_date) {
                Holiday::updateOrCreate(
                    ['date' => $holiday_date],
                    ['is_holiday' => 1]
                );
            }
        } catch (Exception $e) {
            $status = false;
            return response()->json(['status' => $status, 'error' => $e->getMessage()]);
        }


        return response()->json(['status' => $status]);
    }
}
