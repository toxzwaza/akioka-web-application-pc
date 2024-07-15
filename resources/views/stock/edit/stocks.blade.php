@extends('layouts.main')
@section('content')
<div>
    <section class="text-gray-600 body-font">


        <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-start justify-center">
            <div class="lg:max-w-lg lg:w-2/5 md:w-3/5 mb-10 md:mb-0 px-8 py-24">
                <h2 class="text-xl text-center mb-8 font-bold">{{ $stock->name }}</h2>

                <img class="my-4 w-full object-cover object-center rounded" alt="hero" src="{{ $stock->img_path && strpos($stock->img_path, 'https://') !== false ? $stock->img_path : 'http://monokanri-app.local/' . $stock->img_path }}">


                <div>
                    <table class="table-auto w-full text-left whitespace-no-wrap">
                        <thead>
                            <tr>

                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">格納場所</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">アドレス</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">個数</th>
                                <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm"></th>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach($stock_storages as $stock_storage)
                            <tr class="border-b border-gray-200 my-4 ">
                                <form action="{{ route('stock.stock_storage.update') }}" method="post">
                                @csrf
                                
                                    <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                                    <input type="hidden" name="stock_storage_id" value="{{ $stock_storage->stock_storage_id }}">
                                    
                                    <td class="px-4 py-8 text-sm text-gray-900">
                                        {{ $stock_storage->location_name }}
                                    </td>
                                    <td class="px-4 py-8 text-sm text-gray-900">
                                        <select name="storage_address_id" id="" class="w-24 text-center shadow-md border border-spacing-1 px-2 py-2 rounded-md bg-gray-50">
                                            @foreach($storage_addresses as $storage_address)
                                                <option {{ $storage_address->address == $stock_storage->address ? 'selected' : '' }} value="{{ $storage_address->id }}">{{ $storage_address->address }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </td>
                                    <td class="px-4 py-8 text-sm text-gray-900">
                                        <input name="quantity"  class="w-8 text-center shadow-md border border-spacing-1 px-2 py-2 bg-gray-50 rounded-md" type="text" value="{{ $stock_storage->quantity }}">
                                    </td>

                                    <td class="px-4 py-8 text-lg text-gray-400 w-16">

                                        <button class="w-16 text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded">更新</button>

                                    </td>

                                </form>






                            </tr>
                            @endforeach


                        </tbody>
                    </table>

                </div>
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
                    <form action = "{{ route('stock.store.stocks') }}" method="post" class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">
                        @csrf
                        <div>
                            <label for="first-name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ID*</label>

                            <input name="stock_id" class="pointer-events-none w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"  value="{{ $stock->id }}" />
                        </div>

                        <div>
                            <label for="stock_no" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">在庫no</label>
                            <input name="last-name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->stock_no }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="company" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">品名</label>
                            <input name="name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->name }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">JANコード</label>
                            <input name="jan_code" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->jan_code }}" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">品番</label>
                            <input name="s_name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->s_name }}" />
                        </div>

                        <div class="">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ファイル選択</label>
                            <input name="upload_file" type="file"  class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->img_path}}" />
                        </div>
                        <div class="">
                        
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">画像パス</label>
                            <input name="img_path" id="img_path_input"  class="pointer-events-none w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->img_path}}" />

                            <p id="img_config_change" class="text-sm mt-4 underline decoration-1 pb-1 text-indigo-300">インターネット上の画像を設定</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">購買URL</label>
                            <input name="url" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->url}}" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">EC購買用識別番号</label>
                            <input name="purchase_identification_number" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->purchase_identification_number}}" />
                        </div>
                        <div class="container w-full flex items-center justify-between sm:col-span-2">
                            <div class="w-1/4 pr-
                            4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">単位(一つ)</label>
                                <input name="solo_unit" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->solo_unit}}" />
                            </div>
                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">単位(まとめ)</label>
                                <input name="org_unit" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->org_unit}}" />
                            </div>
                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ひとまとまり数量</label>
                                <input name="quantity_per_org" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->quantity_per_org}}" />
                            </div>

                        </div>

                        <div class="container w-full flex items-center justify-between sm:col-span-2">
                            <div class="w-1/2 pr-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">備品カテゴリ</label>
                                <select name="classification_id" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option value="0">未選択</option>

                                    @foreach($classifications as $class)
                                    <option {{ $class->id == $stock->classification_id ? "selected" : ''}} value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-1/2 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">配送先</label>
                                <input name="deli_location" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->deli_location}}" />

                            </div>
                            <div class="w-1/2 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">主な使用先</label>
                                <select name="process_code" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option value="0">未選択</option>

                                    @foreach($processes as $process)
                                    <option {{ $process->id == $stock->process_code ? "selected" : ''}} value="{{ $process->id }}">{{ $process->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">メモ</label>
                            <textarea name="memo" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" rows="4">{{ $stock->memo }}</textarea>
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

<script>
    const img_config_change = document.querySelector('#img_config_change');
    const img_path_input = document.querySelector('#img_path_input');

    img_config_change.addEventListener('click', (el)=>{
        img_path_input.classList.toggle('pointer-events-none');
        img_path_input.classList.toggle('bg-gray-200');
    });
</script>



@endsection