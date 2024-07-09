<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    //
    public function index()
    {

        return view('master.index');
    }

    public function create_user()
    {
        return view('master.create_user');
    }
    public function users()
    {
        // 課長・係長
        $high_users = User::select('users.id as user_id', 'users.name as user_name', 'positions.name as position_name', 'groups.name as group_name')
            ->join('positions', 'users.position_id', 'positions.id')
            ->join('groups', 'groups.id', 'users.group_id')
            ->where('del_flg', '=', 0)
            ->orderBy('group_id', 'asc')
            ->orderBy('position_id', 'asc')
            ->where('dispatch_flg', '=', 0)
            ->whereIn('position_id', [6, 7])
            ->whereIn('group_id', [1, 2, 3, 4, 5])
            ->get();

        // 製造部
        $product_users = User::select('users.id as user_id', 'users.name as user_name', 'users.dispatch_flg', 'positions.name as position_name', 'groups.name as group_name', 'processes.name as process_name')->join('positions', 'users.position_id', 'positions.id')->join('groups', 'groups.id', 'users.group_id')->join('processes', 'processes.id', 'users.process_id')->where('del_flg', '=', 0)->whereIn('dispatch_flg', [0, 1])->whereIn('position_id', [8, 9])->orderby('process_id', 'asc')->orderby('position_id', 'asc')->orderby('dispatch_flg', 'asc')->get();

        $a = 0;
        $b = 0;
        $c = 0;
        $d = 0;
        $e = 0;
        $f = 0;

        foreach ($product_users as $p_user) {


            switch ($p_user->process_name) {
                case '電気炉':
                    $a++;
                    break;
                case '生型造型':
                    $b++;
                    break;
                case 'フラン':
                    $c++;
                    break;
                case '中子':
                    $d++;
                    break;
                case '仕上げ':
                    $e++;
                    break;
                case '出荷検査':
                    $f++;
                    break;
            }
        }
        $product_count = [
            '電気炉' => $a,
            '生型造型' => $b,
            'フラン' => $c,
            '中子' => $d,
            '仕上げ' => $e,
            '出荷検査' => $f
        ];
        // dd($product_count);


        // 業務管理統括部
        $office_users = User::select('users.id as user_id', 'users.name as user_name', 'positions.name as position_name', 'groups.name as group_name')->join('positions', 'users.position_id', 'positions.id')->join('groups', 'groups.id', 'users.group_id')->whereIn('users.group_id', [6, 7])->where('del_flg', '=', 0)->orderby('group_id', 'asc')->orderby('position_id', 'asc')->where('dispatch_flg', '=', 0)->whereIn('position_id', [5, 6, 9])->get();
        $g = 0;
        $h = 0;
        foreach ($office_users as $o_user) {
            switch ($o_user->group_name) {
                case '業務部':
                    $g++;
                    break;
                case "総務部":
                    $h++;
                    break;
            }
        }
        $office_count = [
            '業務部' => $g,
            '総務部' => $h
        ];

        $i = 0;
        $j = 0;
        $k = 0;

        // 技術・品証・保全
        $tec_users = User::select('users.id as user_id', 'users.name as user_name', 'positions.name as position_name', 'groups.name as group_name')->join('positions', 'users.position_id', 'positions.id')->join('groups', 'groups.id', 'users.group_id')->whereIn('users.group_id', [1, 2, 5])->where('del_flg', '=', 0)->orderby('group_id', 'asc')->orderby('position_id', 'asc')->where('dispatch_flg', '=', 0)->whereIn('position_id', [9])->get();
        foreach ($tec_users as $t_user) {
            switch ($t_user->group_name) {
                case '品質保証部(鋳造技術課)':
                    $i++;
                    break;
                case "品質保証部(品質保証課)":
                    $j++;
                    break;
                case "製造部(設備保全・TPM課)":
                    $k++;
                    break;
            }
        }
        $tec_count = [
            '品質保証部(鋳造技術課)' => $i,
            '品質保証部(品質保証課)' => $j,
            '製造部(設備保全・TPM課)' => $k
        ];

   



        return view('master.users', compact('high_users', 'product_users', 'product_count', 'office_users', 'office_count', 'tec_users', 'tec_count'));
    }

    public function store()
    {

        return;
    }
}
