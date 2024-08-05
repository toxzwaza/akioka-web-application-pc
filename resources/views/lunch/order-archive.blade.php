@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">注文履歴</h1>
<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">

            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                毎日８時５５分に自動発注が実行されます。なお、管理画面からマニュアル発注することも可能です。
            </p>
        </div>
        <div class="my-4">
            {{ $orders->links() }}
        </div>

        <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">注文日時</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">注文者</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">弁当個数</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">おかず個数</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">弁当金額合計</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">おかず金額合計</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">備考</th>

                        <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="border-b-2 ">
                        <td class="px-4 py-8 {{ $order->created_at == Carbon\Carbon::today()->format('Y-m-d') ? 'text-red-400 font-semibold' : '' }}">{{ Carbon\Carbon::parse($order->created_at)->isoFormat('Y/M/D (ddd)') }}</td>
                        <td class="px-4 py-8">{{ $order->user_name }}</td>
                        <td class="px-4 py-8">{{ $order->lunch_count }}個</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $order->side_dish_count }}個</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ number_format($order->lunch_calc) }}円</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ number_format($order->side_dish_calc) }}円</td>
                        <td class="w-1/4 px-4 py-8 text-lg text-gray-900">{{ $order->memo }}</td>
                        
                        <td class="w-32 text-center">
                            <a href="{{ route('lunch.order-users', ['date' => Carbon\Carbon::parse($order->created_at)->format('Y-m-d') ]) }}" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" >注文状況</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="my-4">
            {{ $orders->links() }}
        </div>
    </div>
</section>


@endsection