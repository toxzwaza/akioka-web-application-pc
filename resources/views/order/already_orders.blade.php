@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">注文書作成待ちリスト</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-16">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">注文書の作成を行います。<br>
                物品依頼・稟議の承認を通過したものが表示されています。。
            </p>

            <div class="mt-6 text-gray-600">
                <form class="w-1/3 flex items-center relative" action="">
                    <input name="last-name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="キーワード検索" />
                    <span class="absolute right-2 ml-2 material-symbols-outlined">search</span>
                </form>
            </div>


        </div>
        <div class="mb-4">

        </div>
        <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>


                        <th class="w-24 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">依頼者</th>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">注文品名</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">金額</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">納期</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">消化予定日</th>

                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">備考</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"></th>


                    </tr>
                </thead>

                <tbody>
                    @foreach($object_requests as $object_request)

                    <tr class="border-b border-gray-200 my-4 hover:bg-slate-200 transition ">




                        <td class="w-1/5 px-4 py-8 text-lg text-gray-900">{{ $object_request->user_name }}</td>
                        <td class="w-1/5 px-4 py-8 text-lg text-gray-900">{{ $object_request->stock_name }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $object_request->price }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $object_request->limit }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $object_request->disappear }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $object_request->memo }}</td>
                        <td class="px-4 py-8 text-lg text-gray-400">
                            <a href="">
                                <span class="text-indigo-400 material-symbols-outlined">
                                    grading
                                </span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">


        </div>


    </div>
</section>



@endsection