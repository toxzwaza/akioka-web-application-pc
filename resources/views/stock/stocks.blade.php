@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">在庫一覧</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Banh mi cornhole echo 従業員一覧を表示します。<br>
                こちらから、編集・削除ページへ遷移することが可能です。
            </p>

            <div class="mt-6 text-gray-600 text-left">
                <p class="text-sm mb-2 text-red-400">品名又は品番から検索されます。</p>

                <form class="w-1/3 flex items-center relative" action="{{ route('stock.stocks') }}" method="get">


                    <input name="keyword" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="キーワード検索" />
                    <span class="absolute right-2 ml-2 material-symbols-outlined">search</span>
                </form>
            </div>


        </div>
        <div class="mb-4">
            {{ $stocks->links() }}
        </div>
        <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">画像</th>

                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">ID</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">JANコード</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">在庫No</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">品名</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">品番</th>

                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">納品先</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">備品カテゴリ</th>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">価格</th>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"></th>

                    </tr>
                </thead>

                <tbody>
                    @foreach($stocks as $stock)
                    <tr class="border-b border-gray-200 my-4 hover:bg-slate-200 transition">
                        <td class="w-24 px-4 py-8 text-lg text-gray-900"><a href="{{ route('stock.edit.stocks', ['stock_id' => $stock->id ]) }}" class="hover:text-blue-200"><img class="w-16" src="{{ $stock->img_path && strpos($stock->img_path, 'https://') !== false ? $stock->img_path : 'http://monokanri-app.local/' . $stock->img_path }}" alt=""></a></td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->id }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->jan_code }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->stock_no }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->name }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->s_name }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->deli_location }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->classification_name }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $stock->price > 0 && is_numeric($stock->price) ? number_format($stock->price) . '円' : ''}}</td>
                        <td class="px-4 py-8 text-lg text-gray-400">
                            <a href="{{ route('stock.edit.stocks', ['stock_id' => $stock->id ]) }}">
                                <span class="material-symbols-outlined">edit_square</span>
                            </a>
                        </td>




                    </tr>
                    @endforeach


                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $stocks->links() }}
        </div>


    </div>
</section>



@endsection