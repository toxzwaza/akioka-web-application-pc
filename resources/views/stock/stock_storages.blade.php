@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">格納先一覧</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Banh mi cornhole echo 従業員一覧を表示します。<br>
                こちらから、編集・削除ページへ遷移することが可能です。
            </p>
        </div>

        <div class="lg:w-4/5 w-full mx-auto overflow-auto">
            <div class="mb-4">
                {{ $storage_location_addresses->links() }}

            </div>

            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="px-4 py-8 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">格納先</th>
                        <th class="px-4 py-8 title-font tracking-wider font-medium text-gray-900 bg-gray-100">アドレス</th>
                        <th class="px-4 py-8 title-font tracking-wider font-medium text-gray-900 bg-gray-100">格納物品数</th>
                        <th class="px-4 py-8 title-font tracking-wider font-medium text-gray-900 bg-gray-100">作成日</th>


                    </tr>
                </thead>

                <tbody>
                    @foreach($storage_location_addresses as $sla)
                    <tr>
                        <td class="px-4 py-8"><a href="{{ route('stock.stocks' ,['storage_address_id' => $sla->storage_address_id ]) }}" class="hover:text-blue-400">{{ $sla->location_name }}</a></td>
                        <td class="px-4 py-8">{{ $sla->address }}</td>
                        <td class="px-4 py-8"><a href="" class="hover:text-blue-400">{{ $sla->count }}</a></td>
                        <td class="px-4 py-8">{{ \Carbon\Carbon::parse($sla->created_at)->format('Y/m/d') }}</td>

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