<?php

namespace App\Http\Controllers;

use App\Models\FaxGroup;
use App\Models\FaxSortSetting;
use App\Models\FaxUserGroup;
use App\Models\User;
use App\Services\Method;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaxController extends Controller
{
    //
    public function index()
    {
        // 振り分け一覧
        $fax_sort_settings = FaxSortSetting::select('fax_sort_settings.*', 'fax_groups.name as group_name')->join('fax_groups', 'fax_groups.id', 'fax_sort_settings.fax_group_id')->get();

        $fax_groups = FaxGroup::all();
        foreach ($fax_groups as $fax_group) {
            $fax_group_users = FaxUserGroup::select('users.id', 'users.name as user_name')->where('fax_group_id', $fax_group->id)->join('users', 'users.id', 'fax_user_groups.user_id')->get();
            $fax_group->users = $fax_group_users;
        }


        return Inertia::render('Fax/Index', ['fax_groups' => $fax_groups, 'fax_sort_settings' => $fax_sort_settings]);
    }
    public function manual(Request $request){
        return Inertia::render('Fax/Manual');
    }

    public function fax_sort_create(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $fax = $request->fax;
        $fax_group_id = $request->group_id;
        if (!$id) {
            $fax_sort_setting = new FaxSortSetting();
            $fax_sort_setting->name = $name;
            $fax_sort_setting->fax = $fax;
            $fax_sort_setting->fax_group_id = $fax_group_id;
            $fax_sort_setting->save();
            Method::msg('success', 'FAX振り分けを作成しました');
        } else {
            $fax_sort_setting = FaxSortSetting::find($id);
            $fax_sort_setting->name = $name;
            $fax_sort_setting->fax = $fax;
            $fax_sort_setting->fax_group_id = $fax_group_id;
            $fax_sort_setting->save();
            Method::msg('success', 'FAX振り分けを更新しました');
        }



        
        return to_route('fax');
    }
    public function fax_sort_delete(Request $request)
    {
        $fax_sort_setting_id = $request->fax_sort_setting_id;
        $fax_sort_setting = FaxSortSetting::find($fax_sort_setting_id);
        $fax_sort_setting->delete();

        Method::msg('success', 'FAX振り分けを削除しました');
        return to_route('fax');
    }
    public function group()
    {
        $groups = FaxGroup::all();
        $users = User::select('users.id', 'users.name as user_name', 'groups.name as group_name')
            ->join('groups', 'groups.id', '=', 'users.group_id')
            ->where('users.del_flg', 0)
            ->whereNotNull('users.email')
            ->get();
        foreach($groups as $group){
            $group->users = FaxUserGroup::select('users.id', 'users.name as user_name')->join('users', 'users.id', 'fax_user_groups.user_id')->where('fax_group_id', $group->id)->get();
        }


        return Inertia::render('Fax/Group', ['groups' => $groups, 'users' => $users]);
    }
    public function group_create(Request $request)
    {
        $group_name = $request->group_name;

        if ($group_name) {
            $group = new FaxGroup();
            $group->name = $group_name;
            $group->save();
            Method::msg('success', 'グループを作成しました');
        }
        return redirect()->back();
    }
    public function group_user_create(Request $request)
    {
        $group_id = $request->id;
        $group_name = $request->name;
        $mount_users = $request->mount_users;


        $fax_group = FaxGroup::find($group_id);
        if ($fax_group->name != $group_name) {
            $fax_group->name = $group_name;
            $fax_group->save();
        }
        if (count($mount_users) > 0) {
            // 全取得して削除
            $fax_user_groups = FaxUserGroup::where('fax_group_id', $group_id)->get();
            foreach ($fax_user_groups as $fax_user_group) {
                $fax_user_group->delete();
            }


            // 送信されたもので更新
            foreach ($mount_users as $user) {
                $fax_group_user = new FaxUserGroup();
                $fax_group_user->fax_group_id = $group_id;
                $fax_group_user->user_id = $user['user_id'];
                $fax_group_user->notify_flg = $user['notify_flg'];
                $fax_group_user->save();
            }
        }

        Method::msg('success', 'グループを更新しました');
        return to_route('fax.group');
    }
    public function getUserGroups(Request $request)
    {
        $group_id = $request->group_id;

        $fax_group_users = FaxUserGroup::select('fax_user_groups.notify_flg', 'users.id as user_id', 'users.name as user_name', 'groups.name as group_name')->join('users', 'users.id', 'fax_user_groups.user_id')->join('groups', 'groups.id', 'users.group_id')->where('fax_group_id', $group_id)->get();
        return response()->json($fax_group_users);
    }
    public function folder(Request $request)
    {
        $searchName = $request->searchName;
        $users = User::where('del_flg', 0)->get();

        return Inertia::render('Fax/Folder', ['users' => $users, 'searchName' => $searchName]);
    }
    public function group_delete($id)
    {
        $fax_group = FaxGroup::find($id);
        $fax_user_groups = FaxUserGroup::where('fax_group_id', $id)->get();
        foreach ($fax_user_groups as $fax_user_group) {
            $fax_user_group->delete();
        }
        $fax_group->delete();
        Method::msg('success', 'グループを削除しました');
        return to_route('fax.group');
    }
    public function folder_update(Request $request)
    {
        $user_id = $request->user_id;
        $folder_name = $request->folder_name;
        $searchName = $request->searchName;

        $user = User::find($user_id);
        if ($user) {
            $user->fax_folder_name = $folder_name;
            $user->save();
        }

        Method::msg('success', 'FAXフォルダを更新しました');
        return to_route('fax.folder', ['searchName' => $searchName]);
    }


    public function getFaxSortUsers(Request $request)
    {
        $name = $request->name;
        $fax = $request->fax;

        $query = FaxSortSetting::query();
        if ($name) {
            $query->where('name', $name);
        } else {
            $query->whereNull('name');
        }

        if ($fax) {
            $query->where('fax', $fax);
        } else {
            $query->whereNull('fax');
        }

        $fax_sort_setting = $query->first();

        if($fax_sort_setting){
            $fax_user_groups = FaxUserGroup::select('users.fax_folder_name','fax_user_groups.notify_flg','users.email')->join('users', 'users.id', 'fax_user_groups.user_id')->where('fax_group_id', $fax_sort_setting->fax_group_id)->get();
        }else{
            $fax_user_groups = [];
        }

        

        return response()->json($fax_user_groups);
    }
}
