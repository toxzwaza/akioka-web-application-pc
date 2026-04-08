<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\LunchOrder;
use App\Models\OrderRequest;
use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\User;
use App\Services\Method;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    //
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');

        $stocksCount = Stock::where('del_flg', 0)->count();

        $orderRequestsPending = OrderRequest::where('del_flg', 0)
            ->where('accept_flg', 1)
            ->count();

        $lowStockLocations = StockStorage::query()
            ->where('reorder_point', '>', 0)
            ->whereColumn('quantity', '<=', 'reorder_point')
            ->count();

        $lunchTodayCount = LunchOrder::query()
            ->whereDate('date', $today)
            ->where('order_flg', 1)
            ->count();

        $announcements = Announcement::query()
            ->activeForDate($today)
            ->orderByDesc('display_order')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'content', 'type', 'start_date', 'end_date']);

        return Inertia::render('Home', [
            'dashboard' => [
                'stats' => [
                    'stocks_count' => $stocksCount,
                    'order_requests_pending' => $orderRequestsPending,
                    'low_stock_locations' => $lowStockLocations,
                    'lunch_today_count' => $lunchTodayCount,
                ],
                'announcements' => $announcements,
                'today_label' => now()->format('Y年n月j日'),
            ],
        ]);
    }
    public function login()
    {
        $users = User::all();
        return view('login', compact('users'));
    }
    public function login_store(Request $request)
    {
        $user_id = $request->user_id;

        if (!$user_id) {
            Method::msg('error', 'user_idが選択されていません。');
            return redirect()->back();
        }
        $user = User::find($user_id);
        if (!$user) {
            Method::msg('error', 'ユーザー情報を取得できませんでした。');
            return redirect()->back();
        }

        session()->put('user', $user);
        // Method::msg('success', 'ログインが完了しました。');

        if (session('bef_url')) {
            $bef_url = session('bef_url');
            session()->forget('bef_url');

            switch ($bef_url) {
                default:
                    // 動画IDが指定されている場合
                    if (session('movie_id')) {
                        return redirect()->route('movie2.show', ['movie_id' => session('movie_id')]);
                    }
                    break;
            }

            return redirect()->route($bef_url);
        }
        return redirect()->route('home');
    }
    public function logout()
    {
        $user_id = session('user.id');
        if (!$user_id) {
            Method::msg('error', 'ログインされていません。');
            return redirect()->back();
        }

        session()->forget('user');
        Method::msg('success', 'ログアウトが完了しました。');
        return redirect()->route('home');
    }
}
