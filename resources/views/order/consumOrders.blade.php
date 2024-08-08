@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">消耗品発注依頼リスト</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">

            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                タブレットより、注文依頼のあったリストを表示します。

            </p>
        </div>
        <div class="my-4">
            {{ $consumOrders->links() }}
        </div>
        <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">発注No.</th>
                        <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">発注画像</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">表示</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">注文依頼品</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">注文者</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">価格</th>
                        <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>

                        <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                        <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                        <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consumOrders as $consumOrder)

                    <tr class="{{ $consumOrder->del_flg == 1 ? 'bg-red-100' : ''}} {{ $consumOrder->order_flg == 1 ? 'bg-gray-100' : ''}}">
                        <td class="w-16 border-t-2 border-b-2 border-gray-200 px-4 py-8 text-center font-bold">{{ $consumOrder->id }}</td>

                        <td class="w-32 border-t-2 border-b-2 border-gray-200 py-2">
                            <img class="w-full" src="{{ $consumOrder->img_path && strpos($consumOrder->img_path, 'https://') !== false ? $consumOrder->img_path : 'http://monokanri-app.local/' . $consumOrder->img_path }}" alt="">
                        </td>
                        <td class="w-16 border-t-2 border-b-2 border-gray-200 px-4 py-8">
                            @if($consumOrder->del_flg == 0)
                            <span class="text-green-400 material-symbols-outlined">
                                visibility
                            </span>
                            @else
                            <span class="text-red-400 material-symbols-outlined">
                                visibility_off
                            </span>

                            @endif
                        </td>
                        <td class=" border-t-2 border-b-2 border-gray-200 px-4 py-8"><a class="hover:text-indigo-600 hover:font-semibold" href="{{ route('stock.edit.stocks', ['stock_id' => $consumOrder->stock_id]) }}">{{ $consumOrder->stock_name }}</a></td>
                        <td class="whitespace-nowrap border-t-2 border-b-2 border-gray-200 px-4 py-8">{{ $consumOrder->user_name }}</td>
                        <td class="whitespace-nowrap border-t-2 border-b-2 border-gray-200 px-4 py-8 text-lg text-gray-900">{{ number_format($consumOrder->price) }}円</td>
                        <td class="w-1/5 border-t-2 border-b-2 border-gray-200  text-center">
                            {{ $consumOrder->quantity_per_org }}{{ $consumOrder->solo_unit }} / {{ $consumOrder->org_unit }}
                        </td>


                        <td class="border-t-2 border-b-2 border-gray-200 w-1/6 text-center">
                            @if($consumOrder->url)
                            <a target="blank" class="mt-4 border bg-white hover:bg-blue-400 hover:text-white text-blue-700 font-bold py-2 px-4 rounded" href="{{ $consumOrder->url }}">URL遷移</a>
                            @else
                            <a class="mt-4 border bg-white hover:bg-blue-400 hover:text-white text-blue-700 font-bold py-2 px-4 rounded" href="{{ route('order.print.consumOrders', ['consumOrder_id' => $consumOrder->id ]) }}">発注書</a>
                        </td>
                        @endif

                        <td class="border-t-2 border-b-2 border-gray-200 w-1/6 text-center">
                            @if($consumOrder->order_flg == 0 && $consumOrder->del_flg == 0)
                            <a href="{{ route('order.complete.consumOrders', ['consumOrder_id' => $consumOrder->id ]) }}" class="border bg-white hover:bg-blue-400 hover:text-white text-blue-700 font-bold py-2 px-4 rounded">発注完了</a>
                            @elseif($consumOrder->order_flg == 1 && $consumOrder->del_flg == 0)
                            <button class=" bg-gray-100 text-gray-600 font-bold py-2 px-4 rounded">発注済み</button>
                            @endif
                        </td>

                        <td class="border-t-2 border-b-2 border-gray-200 w-16 text-center">
                            @if($consumOrder->del_flg == 0 && $consumOrder->order_flg == 0)
                            <a href="{{ route('order.delete.consumOrders', ['consumOrder_id' => $consumOrder->id ]) }}">
                                <span id="delete" class="text-red-400 material-symbols-outlined">
                                    delete
                                </span>
                            </a>
                            @endif

                        </td>
                    </tr>
                    @endforeach


                </tbody>
            </table>
        </div>
        <div class="my-4">
            {{ $consumOrders->links() }}
        </div>

    </div>
</section>



@endsection