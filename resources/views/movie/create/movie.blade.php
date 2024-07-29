@extends('layouts.main')
@section('content')
<div class="bg-white py-6 sm:py-8 lg:py-12">
    <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
        <!-- text - start -->
        <div class="mb-10 md:mb-16">
            <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">動画追加</h2>

            <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">Youtubeにアップロードした動画ファイルを追加することができます。</p>
        </div>
        <!-- text - end -->

        <!-- form - start -->
        <form class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2" method="post" action="{{ route('movie.store') }}">
            @csrf

            <div class="sm:col-span-2">
                <label for="name" class="font-semibold text-red-400 mb-2 inline-block text-sm text-gray-800 sm:text-base">ファイル名*</label>
                <input id="name" name="name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="サンプルファイル１"/>
            </div>

            <div class="sm:col-span-2">
                <label for="file_path" class="font-semibold text-red-400 mb-2 inline-block text-sm text-gray-800 sm:text-base">YoutubeID*</label>
                <input id="file_path" name="file_path" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="cfqlhC5qhLg?si=wmq_hX-aeASFzwgO"/>
            </div>

            <div class="sm:col-span-2">
                <label for="movie_tag_id" class="font-semibold text-red-400 mb-2 inline-block text-sm text-gray-800 sm:text-base">動画タグ*</label>
                <select id="movie_tag_id" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" name="movie_tag_id" id="">
                    <option value="0">選択してください。</option>

                    @foreach($movie_tags as $tag)
                    <option value="{{ $tag->tag_id }}">{{ $tag->category_name . ' >>> ' . $tag->tag_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="sm:col-span-2">
                <label for="memo" class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base">メモ</label>
                <textarea id="memo" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" name="memo" cols="30" rows="10"></textarea>
            </div>

            <div class="flex items-center justify-between sm:col-span-2">
                <button class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">Send</button>

                <span class="text-sm text-gray-500">*Required</span>
            </div>

            <p class="text-xs text-gray-400">By signing up to our newsletter you agree to our <a href="#" class="underline transition duration-100 hover:text-indigo-500 active:text-indigo-600">Privacy Policy</a>.</p>
        </form>
        <!-- form - end -->
    </div>
</div>


@endsection