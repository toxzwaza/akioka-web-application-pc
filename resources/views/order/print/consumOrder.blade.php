@extends('layouts.print_main')
@section('content')
<div class="my-16 text-center print_hidden">

    <button id="order_save" class="mx-4 inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">注文書保存</button>

    <button id="print" class="mx-4 inline-block rounded-lg bg-white px-8 py-3 text-center text-sm font-semibold text-indigo-600 outline ring-indigo-300 transition duration-100 hover:bg-indigo-600 hover:text-white focus-visible:ring active:bg-indigo-700 md:text-base">印刷</button>
</div>

<div id="print_container" style="height: 190mm; width: 297mm;" class="border mx-auto  px-8 py-4">
    <h1 class=" text-center font-bold text-4xl py-8">注文書</h1>
    <div class="flex justify-between">
        <div class="w-1/4">
            <h2 class="text-xl mb-1"><span class="font-semibold mr-8">{{ $consumOrder->supplier_name }}</span>御中</h2>
            <p class="indent-6">TEL {{ $consumOrder->tel}}</p>
            <p class="indent-6">FAX {{ $consumOrder->fax }}</p>

            <p class="mt-4 font-serif text-sm">
                お世話になります。<br>
                注文をお願いします。<br>
                納期に間に合わない時は、事前に納期回答をお願いします。<br>
                納品書・請求書には「注文No」の記入をお願いします。

            </p>

        </div>
        <div class="w-1/3 py-4 px-8 border-4 rounded-lg">
            <h2 class="text-xl font-semibold text-center mb-8">休業日</h2>
            <p class="my-2">8月3~4日・10~18日・24~25日・31日</p>
            <p class="my-2">9月1日・7~8日・14~15日・21~22日・28~29日</p>

        </div>
        <div class="w-1/4">
            <h4 class="font-semibold flex justify-between">注文書作成日: <span class=" font-normal">{{ Carbon\Carbon::parse($consumOrder->created_at)->format('Y/m/d') }}</span></h4>
            <div class="mt-4">
                <p>(株)アキオカ</p>
                <p class="indent-6">〒713-8103</p>
                <p class="indent-6">倉敷市玉島乙島8252-35</p>
            </div>
            <div class="mt-4">
                <p>TEL:086-522-7686</p>
                <p>FAX:086-522-7674</p>
                <p class="mt-4 text-right font-semibold">注文発注者: <span>{{ session('user.name') ?? '' }}</span></p>
            </div>

        </div>
    </div>

    <form id="print_form" class="mt-12" action="{{ route('order.store.consumOrders') }}" method="post">
        @csrf
        <input type="hidden" name="consumOrder_id" value="{{ $consumOrder->id }}">
        <table id="print_table" class="w-full">
            <tr>
                <th class="text-gray-600 whitespace-nowrap text-sm px-4 py-2">注文No.</th>
                <th class="text-gray-600 whitespace-nowrap text-sm px-4 py-2">品番</th>
                <th class="text-gray-600 whitespace-nowrap text-sm px-4 py-2">社内在庫数</th>
                <th class="text-gray-600 whitespace-nowrap text-sm px-4 py-2">在庫消化<br>予定日</th>
                <th class="imp text-gray-600 whitespace-nowrap text-sm px-4 py-2">数量</th>
                <th class="imp text-gray-600 whitespace-nowrap text-sm px-4 py-2">単価</th>
                <th class="imp text-gray-600 whitespace-nowrap text-sm px-4 py-2">金額<br>(税抜き価格)</th>
                <th class="imp text-gray-600 whitespace-nowrap text-sm px-4 py-2 print_bg {{ $consumOrder->limit ?? 'bg-indigo-100' }}">納期</th>
                <th class="text-gray-600 whitespace-nowrap text-sm px-4 py-2">納品場所</th>
                <th class="text-gray-600 whitespace-nowrap text-sm px-4 py-2">注文支持者</th>
            </tr>
            <tr>
                <td class="text-center px-4 py-4">{{ Carbon\Carbon::parse($consumOrder->created_at)->format('Y-md-') . $consumOrder->id }}</td>
                <td class="whitespace-nowrap text-center px-4 py-4">{{ $consumOrder->stock_name}} <br> <span class="text-sm">{{ $consumOrder->s_name}}</span></td>
                <td class="text-center px-4 py-4">-</td>
                <td class="text-center px-4 py-4">-</td>

                <td class="whitespace-nowrap text-center imp px-4 py-4">{{ '1' . $consumOrder->org_unit }} / {{ $consumOrder->quantity_per_org . $consumOrder->solo_unit }}</td>

                <td class="whitespace-nowrap text-center imp px-4 py-4">{{ '@' . ($consumOrder->price / $consumOrder->quantity_per_org) }} / {{ $consumOrder->solo_unit}}</td>
                <td class="text-center imp px-4 py-4">{{ number_format($consumOrder->price) }}円</td>
                <td class="text-center imp px-2 py-4"><input id="limit" class="text-sm text-center w-full" type="date" name="limit" value="{{ Carbon\Carbon::parse($consumOrder->limit)->format('Y-m-d') }}"></td>
                <td class="text-center px-4 py-4">{{ $consumOrder->deli_location}}</td>
                <td class="px-4 py-4">{{ $consumOrder->user_name}}</td>

            </tr>
        </table>

    </form>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const print_button = document.querySelector('#print');


        const print_hidden = document.querySelectorAll('.print_hidden');

        print_button.addEventListener('click', (e) => {
            print_hidden.forEach((el) => {
                el.classList.toggle('hidden');
            });
            setTimeout(function() {
                window.print();
            }, 300); // 3000ミリ秒 = 3秒



        });


        // 印刷キャンセルが押された場合の処理
        window.onbeforeprint = function() {
            if (!window.matchMedia('print').matches) {
                console.log('実行');
                // キャンセルボタンが押された時の処理をここに記述します
                setTimeout(function() {
                    print_hidden.forEach((el) => {
                        el.classList.remove('hidden');
                    });
                }, 2000); // 3000ミリ秒 = 3秒


            }
        };



    });


    const order_save = document.querySelector('#order_save');
    const limit = document.querySelector('#limit');
    const print_form = document.querySelector('#print_form');

    order_save.addEventListener('click', (e)=>{
        e.preventDefault();
        if(limit.value){
            console.log(limit.value);
            print_form.submit();
        }else{
            alert('納期が設定されていません。');
        }
    });
</script>
@endsection