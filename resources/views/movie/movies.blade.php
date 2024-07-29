@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">動画一覧</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-16">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">動画一覧を表示します。<br>
                こちらから視聴・編集ページへ遷移することが可能です。
            </p>

            <div class="mt-6 text-gray-600">
                <form class="w-1/3 flex items-center relative" action="{{ route('movie') }}">
                    <input name="text" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="キーワード検索" />
                    <span class="absolute right-2 ml-2 material-symbols-outlined">search</span>
                </form>
            </div>


        </div>
        <div class="mb-4">

        </div>
        <div class="mb-4">
            {{ $movies->links() }}
        </div>
        <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>


                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">日付</th>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">カテゴリ</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">タグ</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">ファイル名</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">備考</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"><span class="material-symbols-outlined">
                                chat_bubble
                            </span></th>

                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"></th>



                    </tr>
                </thead>

                <tbody>

                    @foreach($movies as $movie)
                    <tr class="border-b border-gray-200 my-4 hover:bg-slate-200 transition ">

                        <td class="w-1/6 px-8 py-8 text-lg text-gray-900">{{ Carbon\Carbon::parse($movie->created_at)->format('Y年m月d日') }}</td>

                        <td class="w-40 px-8 py-8 text-lg text-gray-900">
                            <a href="{{ route('movie', ['category_id' => $movie->category_id ]) }}">
                                <span class="bg-{{ $movie->category_color }}-100 text-{{ $movie->category_color }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-white dark:text-{{ $movie->category_color }}-400 border border-{{ $movie->category_color }}-400"> {{ $movie->movie_tag_category_name }}</span>
                            </a>
                        </td>

                        <td class="w-40 px-8 py-8 text-lg">
                            <a href="{{ route('movie', ['tag_id' => $movie->tag_id ]) }}">
                                <span class="bg-{{ $movie->tag_color }}-400 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-{{ $movie->tag_color }}-400 dark:text-white"> {{ $movie->movie_tag_name }}</span>
                            </a>
                            

                        </td>

                        <td class="w-1/5 px-8 py-8 text-lg text-gray-900">{{ $movie->name }}</td>

                        <td class="w-1/5  px-8 py-8 text-sm text-gray-900">{{ $movie->memo }}</td>

                        <td class="w-16 px-8 py-8 text-lg text-gray-900">{{ $movie->memos_count }}</td>

                        <td class="px-8 py-8 text-lg text-gray-900">
                            <a href="{{ route('movie.show', ['movie_id' => $movie->id ]) }}">
                                <span class="material-symbols-outlined">
                                    movie
                                </span>
                            </a>
                        </td>


                    </tr>
                    @endforeach




                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $movies->links() }}

        </div>


    </div>
</section>



@endsection