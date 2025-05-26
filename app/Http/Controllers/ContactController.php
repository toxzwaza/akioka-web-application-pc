<?php

namespace App\Http\Controllers;

use App\Http\Services\Helper;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    //
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $start_contact_date = $request->start_contact_date;
        $end_contact_date = $request->end_contact_date;
        $progress = $request->progress;
        $kind = $request->kind;
        $user_id = $request->user_id;

        // 基本のクエリを構築
        $baseQuery = Contact::select('contacts.*', 'users.name as user_name')
            ->join('users', 'users.id', 'contacts.user_id');

        // 検索条件に基づいてクエリを絞り込む
        if ($keyword) {
            $baseQuery->where('contacts.subject', 'like', "%{$keyword}%")
                ->orWhere('contacts.name', 'like', "%{$keyword}%")
                ->orWhere('contacts.email', 'like', "%{$keyword}%")
                ->orWhere('contacts.summary', 'like', "%{$keyword}%")
                ->orWhere('contacts.content', 'like', "%{$keyword}%")
                ->orWhere('contacts.memo', 'like', "%{$keyword}%");
        }

        if ($start_contact_date && $end_contact_date) {
            $baseQuery->whereBetween('contacts.created_at', [$start_contact_date, $end_contact_date]);
        } elseif ($start_contact_date) {
            $baseQuery->where('contacts.created_at', '>=', $start_contact_date);
        } elseif ($end_contact_date) {
            $baseQuery->where('contacts.created_at', '<=', $end_contact_date);
        }

        if (!is_null($progress)) {
            $baseQuery->where('contacts.progress', $progress);
        }

        if (!is_null($kind)) {
            $baseQuery->where('contacts.kind', $kind);
        }

        if ($user_id) {
            $baseQuery->where('contacts.user_id', $user_id);
        }

        // メインの問い合わせ一覧を取得
        $contacts = $baseQuery->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // 集計データを1回のクエリで取得
        $stats = Contact::selectRaw('
            COUNT(*) as total_count,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as current_month_count,
            SUM(CASE WHEN progress = 0 THEN 1 ELSE 0 END) as in_progress_count,
            SUM(CASE WHEN progress = 1 THEN 1 ELSE 0 END) as solved_count
        ', [now()->month, now()->year])
            ->first();

        // 担当者取得
        $users = User::where('del_flg', 0)->get();

        return Inertia::render('Contact/Index', [
            'contacts' => $contacts,
            'stats' => [
                'current_month_count' => $stats->current_month_count,
                'in_progress_count' => $stats->in_progress_count,
                'solved_count' => $stats->solved_count,
                'total_count' => $stats->total_count
            ],
            'users' => $users
        ]);
    }

    public function show($id)
    {
        $contact = Contact::find($id);

        if ($contact && !$contact->progress) {
            $contact->progress = 1;
            $contact->save();
        }

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

                    $url = route('contact.show', ['id' => $contact->id]);

                    $title = "お問い合わせ管理システムからの通知です。";
                    $message = "問い合わせ回答依頼を受け付けました。\n\n以下のURLから回答を行ってください。";

                    Helper::createNotifyQueue($title, $message, $url, [$contact->user_id]);
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
