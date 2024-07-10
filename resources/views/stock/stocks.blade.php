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
        </div>
        <div class="lg:w-4/5 w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">画像</th>
                    
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ID</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">JANコード</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">在庫No</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">品名</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">品番</th>
                        
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">納品先</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">主な使用先</th>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">価格</th>
                        <!-- <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">url</th> -->

                        <!-- <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">単位(単位)</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">単位(まとまり)</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">ひとまとまりの数量</th> -->
                        <!-- <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">メモ</th> -->
                        
                        <!-- <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th> -->
                    </tr>
                </thead>

                <tbody>
                    @foreach($stocks as $stock)
                        <tr>
                            <td class="w-24 px-4 py-3 text-lg text-gray-900"><a href="" class="hover:text-blue-200"><img class="w-16" src="{{ $stock->img_path && strpos($stock->img_path, 'https://') !== false ? $stock->img_path : 'http://monokanri-app.local/' . $stock->img_path }}" alt=""></a></td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->id }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->jan_code }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->stock_no }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->name }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->s_name }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->deli_location }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->process_code }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->price }}</td>
                            <!-- <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->url }}</td>
                            <td class="px-4 py-3 text-lg text-gray-900">{{ $stock->memo }}</td> -->
                            <!-- <td class="px-4 py-3 text-lg text-gray-900">Free</td>
                            <td class="px-4 py-3 text-lg text-gray-900">Free</td>
                            <td class="px-4 py-3 text-lg text-gray-900">Free</td> -->
    
                        </tr>
                    @endforeach


                </tbody>
            </table>
        </div>
        <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">
            <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Learn More
                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                </svg>
            </a>
            <button class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Button</button>
        </div>
    </div>
</section>



@endsection