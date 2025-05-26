<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    //
    public function index()
    {
        // 基本のクエリを構築
        $baseQuery = Contact::select('contacts.*', 'users.name as user_name')
            ->join('users', 'users.id', 'contacts.user_id');

        // メインの問い合わせ一覧を取得
        $contacts = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // 集計データを1回のクエリで取得
        $stats = Contact::selectRaw('
            COUNT(*) as total_count,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as current_month_count,
            SUM(CASE WHEN progress = 0 THEN 1 ELSE 0 END) as in_progress_count,
            SUM(CASE WHEN progress = 1 THEN 1 ELSE 0 END) as solved_count
        ', [now()->month, now()->year])
        ->first();

        return Inertia::render('Contact/Index', [
            'contacts' => $contacts,
            'stats' => [
                'current_month_count' => $stats->current_month_count,
                'in_progress_count' => $stats->in_progress_count,
                'solved_count' => $stats->solved_count,
                'total_count' => $stats->total_count
            ]
        ]);
    }

    public function show($id)
    {
        $contact = Contact::find($id);

        // 担当者取得
        $users = User::where('del_flg', 0)->get();


        return Inertia::render('Contact/Show', ['contact' => $contact, 'users' => $users]);
    }

    public function update(Request $request)
    {
        $status = true;

        $contact_id = $request->contact_id;
        $flg = $request->flg;
        $val = $request->val;


        if ($flg) {
            $contact = Contact::find($contact_id);
            switch ($flg) {
                case 'progress':
                    $contact->progress = $val;
                    $contact->save();
                    break;
                case 'user_id':
                    $contact->user_id = $val;
                    $contact->save();
                    break;
                case 'memo':
                    $contact->memo = $val;
                    $contact->save();
                    break;
            }
        } else {
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
