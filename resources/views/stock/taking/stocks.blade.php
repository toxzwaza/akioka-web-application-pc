@extends('layouts.main')
@section('content')


<h1 class="text-center text-xl font-bold text-gray-800">棚卸し</h1>



<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                下の検索ボックスより、棚アドレスから在庫情報を検索してください。
            </p>

            <div class="w-1/2 mx-auto mt-6 text-gray-600 text-left">
                <p class="text-sm mb-2 text-red-400">棚アドレスから検索されます。</p>

                <form class="w-2/3 flex items-center relative" action="{{ route('stock.stocks.taking') }}" method="get">

                    <input type="hidden" name="storage_address_id" value="{{ request('storage_address_id') ?? '' }}">
                    <input list="storage_addresses" name="keyword" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="キーワード検索" />
                    <datalist id="storage_addresses">
                        @foreach($storage_addresses as $storage_address)
                            <option value="{{ $storage_address->id }}">{{ $storage_address->address }}</option>
                        @endforeach

                    </datalist>

                    <button class="absolute right-2 ml-2 flex items-center">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                </form>
            </div>


        </div>
        <div class="mb-4">

        </div>


        @if(count($stocks) != 0)


        <div class="lg:w-full w-full mx-auto overflow-auto">
            <p class=" mb-2 text-md">絞り込み中アドレス：<span class="text-lg text-indigo-400 font-semibold">{{ $storage_address_name }}</span></p>

            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-200 rounded-tl rounded-bl">画像</th>

                        <th class="px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-200">品名</th>
                        <th class="px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-200">品番</th>

                        <th class=" px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-200">単価 (※手動変更可)</th>
                        <th class=" px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-200">想定在庫数</th>
                        <th class=" w-1/6  px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-100">実際在庫数量</th>

                        <th class=" w-1/6  px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-200">合計金額</th>

                        <th class="w-1/6 px-2 py-2 title-font tracking-wider font-medium text-gray-900 bg-gray-200"></th>


                    </tr>
                </thead>

                <tbody>
                    @foreach($stocks as $stock)
                    <tr class="border-b border-gray-200 my-2 hover:bg-slate-200 transition">

                        <td class="w-24 px-4 py-8 text-lg text-gray-900"><a href="{{ route('stock.edit.stocks', ['stock_id' => $stock->id ]) }}" class="hover:text-blue-200"><img class="w-16" src="{{ $stock->img_path && strpos($stock->img_path, 'https://') !== false ? $stock->img_path : 'http://monokanri-app.local/' . $stock->img_path }}" alt=""></a></td>

                        <td class="px-2 py-2 text-lg text-gray-900">{{ $stock->name }}</td>
                        <td class="px-2 py-2 text-lg text-gray-900">{{ $stock->s_name }}</td>
                        <td class="px-2 py-2 text-lg text-gray-900">@<input class="{{ $stock->id }}-price w-24 text-center" type="text" value="{{ $stock->price > 0 && is_numeric($stock->price) ? $stock->price  : ''}}">円</td>

                        <td class="px-2 py-2 text-lg text-gray-900">{{ $stock->quantity }}</td>
                        <td class="{{ $stock->id }}-quantity px-2 py-2 text-lg text-gray-900"><input class="{{ $stock->id }} quantity outline outline-2 outline-offset-2 px-2 py-2" type="number" name="" id=""></td>

                        <!-- 合計金額 -->
                        <td class="{{ $stock->id }}-calc px-2 py-2 text-lg text-gray-900"></td>

                        <td class="px-2 py-2 text-lg text-gray-900"><input class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded" type="button" value="確定"></td>




                    </tr>
                    @endforeach


                </tbody>
            </table>
        </div>

       @endif

        <div class="mt-8">

        </div>


    </div>
</section>

<script>

    const calc = document.querySelectorAll('.quantity');
    calc.forEach((e) => {
        e.addEventListener('change', (el)=>{
        console.log(el.target.value); 
        console.log(el.target.classList[0]); 
        });
    });
</script>





@endsection