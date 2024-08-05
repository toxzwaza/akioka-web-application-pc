@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">注文履歴</h1>
<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">

            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                毎日８時５５分に自動発注が実行されます。なお、管理画面からマニュアル発注することも可能です。
            </p>

            <h2 class="text-2xl mt-16 font-bold">注文合計：{{ $user_count }}個</h2>
        </div>


        <div class="lg:w-3/4 w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">注文時刻</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">注文者</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">品名</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($order_users as $order)
                    <tr class="border-b-2 ">
                        <td class="w-1/3 px-4 py-8 {{ $order->created_at == Carbon\Carbon::today()->format('Y-m-d') ? 'text-red-400 font-semibold' : '' }}">{{ Carbon\Carbon::parse($order->created_at)->format('H:i') }}</td>
                        <td class="w-1/3 px-4 py-8">{{ $order->user_name }}</td>
                        <td class="w-1/3 px-4 py-8 flex">
                            <span class="text-gray-300 mr-1 material-symbols-outlined">
                                bento
                            </span>
                            {{ $order->lunch_name }}</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</section>


@endsection