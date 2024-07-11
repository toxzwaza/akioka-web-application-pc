@extends('layouts.main')
@section('content')
<div>
    <section class="text-gray-600 body-font">


        <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-start justify-center">
            <div class="lg:max-w-lg lg:w-2/5 md:w-3/5 mb-10 md:mb-0 px-8 py-24">
                <h2 class="text-xl text-center mb-8 font-bold">{{ $stock->name }}</h2>

                <img class="my-4 w-full object-cover object-center rounded" alt="hero" src="{{ $stock->img_path && strpos($stock->img_path, 'https://') !== false ? $stock->img_path : 'http://monokanri-app.local/' . $stock->img_path }}">
            </div>






            <div class="bg-white py-6 sm:py-8 lg:py-12">
                <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
                    <!-- text - start -->
                    <div class="mb-10 md:mb-16">
                        <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">在庫編集</h2>

                        <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">
                            在庫編集を行います。
                        </p>
                    </div>
                    <!-- text - end -->

                    <!-- form - start -->
                    <form class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">
                        <div>
                            <label for="first-name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ID*</label>

                            <input name="first-name" class="w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->id }}" />
                        </div>

                        <div>
                            <label for="last-name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">在庫no</label>
                            <input name="last-name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->stock_no }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="company" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">品名</label>
                            <input name="company" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->name }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">JANコード</label>
                            <input name="email" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->jan_code }}" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">品番</label>
                            <input name="email" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->s_name }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">画像パス</label>
                            <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->img_path}}" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">購買URL</label>
                            <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->url}}" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">EC購買用識別番号</label>
                            <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->purchase_identification_number}}" />
                        </div>
                        <div class="container w-full flex items-center justify-between sm:col-span-2">
                            <div class="w-1/4 pr-
                            4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">単位(一つ)</label>
                                <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->solo_unit}}" />
                            </div>
                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">単位(まとめ)</label>
                                <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->org_unit}}" />
                            </div>
                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ひとまとまり数量</label>
                                <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->quantity_per_org}}" />
                            </div>

                        </div>

                        <div class="container w-full flex items-center justify-between sm:col-span-2">
                            <div class="w-1/2 pr-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">備品カテゴリ</label>
                                <select name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option value="0">未選択</option>

                                    @foreach($classifications as $class)
                                    <option {{ $class->id == $stock->classification_id ? "selected" : ''}} value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-1/2 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">配送先</label>
                                <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->deli_location}}" />

                            </div>
                            <div class="w-1/2 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">主な使用先</label>
                                <select name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option value="0">未選択</option>

                                    @foreach($processes as $process)
                                    <option {{ $process->id == $stock->process_code ? "selected" : ''}} value="{{ $process->id }}">{{ $process->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">メモ</label>
                            <textarea name="message" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" rows="4">{{ $stock->memo }}</textarea>
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



        </div>
    </section>


</div>


@endsection