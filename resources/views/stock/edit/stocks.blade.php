@extends('layouts.main')
@section('content')

<div>
    <section class="text-gray-600 body-font">


        <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-start justify-center">
            <div class="lg:max-w-lg lg:w-4/5 md:w-3/5 mb-10 md:mb-0 px-8 py-24">
                <h2 class="text-xl text-center mb-8 font-bold">{{ $stock->name }}</h2>

                <img class="my-4 w-full object-cover object-center rounded" alt="hero" src="{{ $stock->img_path && strpos($stock->img_path, 'https://') !== false ? $stock->img_path : 'http://monokanri-app.local/' . $stock->img_path }}">

                @if(count($stock_storages) != 0)
                <!-- 格納アドレスが一つ以上存在する場合 -->

                <div>
                    <table class="table-auto w-full text-left whitespace-no-wrap">
                        <thead>
                            <tr>
                                <th class="bg-gray-100"></th>
                                <th class="w-32 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">場所</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">アドレス</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">個数</th>
                                <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm"></th>
                                <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm"></th>

                            </tr>
                        </thead>

                        <tbody>

                            @foreach($stock_storages as $stock_storage)

                            <tr class="border-b border-gray-200 my-4 ">
                                <form id="update_form" action="{{ route('stock.stock_storage.update') }}" method="post">
                                    @csrf

                                    <input type="hidden" name="stock_id" value="{{ $stock->id }}" id="stock_id">
                                    <input type="hidden" name="stock_storage_id" value="{{ $stock_storage->stock_storage_id }}">
                                    <td>
                                        <input class="edit_check" type="radio" name="" id="" value="{{ $stock_storage->stock_storage_id }}">
                                    </td>

                                    <td class="w-32 px-4 py-8 text-sm text-gray-900">
                                        {{ $stock_storage->location_name }}
                                    </td>
                                    <td class="px-4 py-8 text-sm text-gray-900">

                                        <input id="storage_address_id" list="storage_address_list" name="storage_address_id" type="number" class="w-24 text-center shadow-md border border-spacing-1 px-2 py-2 rounded-md bg-gray-50" value="" placeholder="{{ $stock_storage->address }}">
                                        <datalist id="storage_address_list">
                                            @foreach($storage_addresses as $storage_address)
                                            <option class="{{ $storage_address->address == $stock_storage->address ? 'selected' : '' }}" value="{{ $storage_address->id }}">{{ $storage_address->address }}</option>
                                            @endforeach
                                        </datalist>
                                    </td>
                                    <td class="px-4 py-8 text-sm text-gray-900">
                                        <input name="quantity" class="w-8 text-center shadow-md border border-spacing-1 px-2 py-2 bg-gray-50 rounded-md" type="text" value="{{ $stock_storage->quantity }}">
                                    </td>

                                    <td class="px-4 py-8 text-lg text-gray-400 w-16">

                                        <button id="update_button" class="w-16 text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-1 rounded">更新</button>

                                    </td>
                                    <td class="px-4 py-8 text-lg text-gray-400 w-16">

                                        <a href="{{ route('stock.stock_storage.delete', ['stock_storage_id' => $stock_storage->stock_storage_id ]) }}" class="text-center block w-16 text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-1 rounded">削除</a>

                                    </td>

                                </form>


                            </tr>
                            @endforeach




                        </tbody>
                    </table>

                    <details class="mt-4">
                        <summary>格納先から変更したい場合はこちらをクリックしてください。</summary>
                        <form action="{{ route('stock.stock_storage.update') }}" method="post">
                            @csrf
                            <p class="my-2 font-semibold">変更後格納場所</p>
                            <p class="my-2 text-sm text-red-400">変更場所を指定する前に、上記の格納先一覧から変更したいデータにチェックを入れてください。</p>
                            <input type="hidden" name="method" value="change">
                            <input type="hidden" id="stock_storage_id" name="stock_storage_id" value="">

                            <select name="" id="location_select" class="mr-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                @foreach($locations as $location)

                                <option {{ $stock_storages[0]->location_name == $location->name ? 'selected' : '' }} value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <select name="storage_address_id" id="address_select" class="mr-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">

                            </select>

                            <input class="border bg-white hover:bg-blue-400 hover:text-white text-blue-700 font-bold py-2 px-4 rounded" type="submit" value="変更">
                        </form>
                    </details>
                    <script>
                        const location_select = document.querySelector('#location_select');
                        const address_select = document.querySelector('#address_select');

                        location_select.addEventListener('change', (el) => {
                            const location_id = el.target.value;
                            console.log(location_id);

                            axios.get('/api/getAddress', {
                                    params: {
                                        location_id: location_id
                                    }
                                })
                                .then(response => {
                                    console.log(response.data);

                                    if (response.data) {
                                        address_select.innerHTML = ''; // 前回の選択肢をクリア
                                        response.data.forEach((address) => {
                                            const newOption = document.createElement('option');
                                            newOption.textContent = address.address;
                                            newOption.value = address.id;
                                            address_select.appendChild(newOption);
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        });
                    </script>

                </div>

                @else
                <!-- 格納先が存在しない場合 -->
                <p>格納先がありません。<br>以下から設定することができます。</p>
                <div>
                    <table class="table-auto w-full text-left whitespace-no-wrap">
                        <thead>
                            <tr>

                                <th class="w-32 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">場所</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">アドレス</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm">個数</th>
                                <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm"></th>
                                <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 text-sm"></th>

                            </tr>
                        </thead>

                        <tbody>


                            <tr class="border-b border-gray-200 my-4 ">
                                <form id="update_form" action="{{ route('stock.stock_storage.create') }}" method="post">
                                    @csrf

                                    <input type="hidden" name="stock_id" value="{{ $stock->id }}">


                                    <td class="px-4 py-8 text-sm text-gray-900">
                                        <!-- location -->
                                        <select name="location_id" id="location_select" class="w-24 text-center shadow-md border border-spacing-1 px-2 py-2 rounded-md bg-gray-50">
                                            @foreach($locations as $location)
                                            <option class="text-left" value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>

                                    </td>
                                    <td class="px-4 py-8 text-sm text-gray-900">
                                        <!-- storage_address -->
                                        <input id="storage_address_id" list="address_select" name="storage_address_id" type="number" class="w-24 text-center shadow-md border border-spacing-1 px-2 py-2 rounded-md bg-gray-50" value="" placeholder="">
                                        <datalist id="address_select">
                                            <!-- 初期オプション  宿直室 -->

                                            @foreach($storage_addresses as $storage_address)
                                            <option value="{{ $storage_address->id }}">{{ $storage_address->address }}</option>
                                            @endforeach


                                        </datalist>
                                    </td>
                                    <td class="px-4 py-8 text-sm text-gray-900">
                                        <!-- 数量 -->
                                        <input name="quantity" class="w-8 text-center shadow-md border border-spacing-1 px-2 py-2 bg-gray-50 rounded-md" type="text" value="" placeholder="0">
                                    </td>

                                    <td class="px-4 py-8 text-lg text-gray-400 w-16">

                                        <button id="create_button" class="w-16 text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-1 rounded">登録</button>

                                    </td>
                                    <td class="px-4 py-8 text-lg text-gray-400 w-16">
                                    </td>

                                </form>


                            </tr>




                        </tbody>
                    </table>



                </div>
                <script>
                    const location_select = document.querySelector('#location_select');
                    const address_select = document.querySelector('#address_select');

                    location_select.addEventListener('change', (el) => {
                        const location_id = el.target.value;
                        console.log(location_id);

                        axios.get('/api/getAddress', {
                                params: {
                                    location_id: location_id
                                }
                            })
                            .then(response => {
                                console.log(response.data);

                                if (response.data) {
                                    address_select.innerHTML = ''; // 前回の選択肢をクリア
                                    response.data.forEach((address) => {
                                        const newOption = document.createElement('option');
                                        newOption.textContent = address.address;
                                        newOption.value = address.id;
                                        address_select.appendChild(newOption);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error(error);
                            });
                    });
                </script>

                @endif

                <a href="{{ route('stock.order', ['stock_id' => $stock->id ]) }}"><button  class="mt-16 bg-transparent hover:bg-red-500 text-red-700 hover:text-white py-4 px-8 border border-red-500 hover:border-transparent rounded text-md font-semibold">発注登録はこちら！</button></a>
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
                    <form action="{{ route('stock.store.stocks') }}" method="post" class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2" enctype="multipart/form-data">
                        @csrf


                        <div>
                            <label for="first-name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ID*</label>

                            <input name="stock_id" class="pointer-events-none w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->id }}" />
                        </div>

                        <div>
                            <label for="stock_no" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">在庫no</label>
                            <input name="stock_no" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->stock_no }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="company" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">品名</label>
                            <input name="name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->name }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">JANコード</label>
                            <input id="jan_code" name="jan_code" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->jan_code }}" />

                            <button id="not_jan_code" class="mt-2 bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500  text-xs hover:border-transparent rounded">JANなし</button>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">品番</label>
                            <input name="s_name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $stock->s_name }}" />
                        </div>

                        <!-- <div class="">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ファイル選択</label>
                            <input name="upload_file" type="file" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->img_path}}" />
                        </div> -->



                        <div class="">

                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">画像パス</label>
                            <input name="img_path" id="img_path_input" class="pointer-events-none w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->img_path}}" />

                            <p id="img_config_change" class="text-sm mt-4 underline decoration-1 pb-1 text-indigo-300">インターネット上の画像を設定</p>
                        </div>

                        <div class="">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">購買URL</label>
                            <input name="url" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->url}}" />
                            @if($stock->url != null)
                            <p class="mt-2"><a class="text-sm mt-2 text-indigo-300 underline decoration-1" href="{{$stock->url}}" target="blank">購買URLをチェックする</a></p>
                            @endif
                        </div>
                        <div class="">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">価格</label>
                            <input name="price" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{$stock->price}}" />

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

                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">基本発注単位</label>

                                <select name="main_unit_flg" id="" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option {{ $stock->main_unit_flg === 0 ? 'selected' : '' }} value="0">単品発注</option>
                                    <option {{ $stock->main_unit_flg === 1 ? 'selected' : '' }} value="1">まとめて発注</option>
                                </select>
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

                        <div>
                            <label for="first-name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ステータス*</label>

                            <select name="del_flg" id="" class="w-full font-semibold rounded border bg-gray-50 px-3 py-2 text-gray-400 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                <option class="px-2 py-1 font-semibold text-green-400" {{ $stock->del_flg == 0 ? 'selected' : ''}} value="0">表示</option>
                                <option class="px-2 py-1 font-semibold text-red-400" {{ $stock->del_flg == 1 ? 'selected' : ''}} value="1">非表示</option>
                            </select>
                        </div>
                        <!-- <div>
                            <label for="first-name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">管理ステータス*</label>

                            <select name="not_stock_flg" id="" class="w-full font-semibold rounded border bg-gray-50 px-3 py-2 text-gray-400 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                <option class="px-2 py-1 font-semibold text-green-400" {{ $stock->not_stock_flg == 0 ? 'selected' : ''}} value="0">対象</option>
                                <option class="px-2 py-1 font-semibold text-red-400" {{ $stock->not_stock_flg == 1 ? 'selected' : ''}} value="1">非対象</option>
                            </select>
                            <p class="text-xs mt-2">※非対象に設定すると在庫管理システムの検索項目とされません。</p>
                        </div> -->






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
        <hr>
        <div class="flex justify-between">
            <div class="bg-white py-6 sm:py-8 lg:py-1 w-1/2 border-r-4 border-dotted border-gray-100">
                <div class="container px-5 py-24 mx-auto">
                    <!-- text - start -->
                    <div class="mb-10 md:mb-16">
                        <h2 class="text-center sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">取引先追加
                            <span class="font-bold text-gray-400 ml-4 material-symbols-outlined">
                                arrow_forward
                            </span>
                        </h2>

                        <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">
                            取引先情報と取引先ごとのリードタイムを設定することができます。
                        </p>
                    </div>
                    <!-- text - end -->

                    <!-- form - start -->
                    <p class="text-sm mb-2 text-red-400">得意先名 / 得意先電話番号 / 得意先郵便番号から検索されます。(※最大20件)</p>

                    <form class="w-2/3 flex items-center relative" action="{{ route('stock.stocks') }}" method="get">

                        <input type="hidden" name="storage_address_id" value="{{ request('storage_address_id') ?? '' }}">
                        <input id="search_supplier_keyword" name="keyword" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="得意先検索" />

                        <button id="search_supplier_button" class="absolute right-2 ml-2 flex items-center">
                            <span class="material-symbols-outlined">search</span>
                        </button>
                    </form>

                    <div class="lg:w-full w-full mx-auto overflow-auto mt-4">
                        <table class="table-auto w-full text-left whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">取引先名</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">電話</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">FAX</th>

                                    <th class="w-24 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                                </tr>
                            </thead>
                            <tbody id="add_supplier_tbody">


                            </tbody>
                        </table>
                    </div>

                </div>
            </div>



            <section class="text-gray-600 body-font w-1/2">
                <div class="container px-5 py-24 mx-auto">
                    <div class="flex flex-col text-center w-full mb-40">
                        <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">登録済み 得意先一覧</h1>
                        <p class="lg:w-2/3 mx-auto leading-relaxed text-base">2024/07/22 取引先の編集は未実装です。</p>
                    </div>
                    <div class="lg:w-full w-full mx-auto overflow-auto">
                        <table class="table-auto w-full text-left whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">取引先名</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">電話</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">FAX</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">リードタイム</th>
                                    <th class="w-5/1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br">メモ</th>
                                    <th class="w-24 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                                    <th class="w-24 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($stock_suppliers as $stock_supplier)

                                <tr>
                                    <form action="{{ route('stock.store.stock_suppliers') }}" method="post">
                                        @csrf

                                        <input type="hidden" name="stock_supplier_id" value="{{ $stock_supplier->stock_supplier_id }}">

                                        <td class="w-1/5 px-4 py-8 text-sm text-gray-900">{{ $stock_supplier->name }}</td>
                                        <td class="px-4 py-8 text-sm text-gray-900">{{ $stock_supplier->tel }}</td>
                                        <td class="px-4 py-8 text-sm text-gray-900">{{ $stock_supplier->fax }}</td>
                                        <td class="px-4 py-8 text-sm text-gray-900"><input class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" type="text" value="{{ $stock_supplier->lead_time }}" name="lead_time" id=""></td>
                                        <td class="w-1/5 px-4 py-8 text-sm text-gray-900">
                                            <input class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" type="text" value="{{ $stock_supplier->stock_supplier_memo }}" name="memo" id="">
                                        </td>

                                        <td class="text-center w-16 px-4 py-8 text-sm text-gray-900">
                                            <button>
                                                <span class="px-8 border-r-2  text-green-400 material-symbols-outlined">
                                                    update
                                                </span>

                                            </button>

                                        </td>
                                    </form>

                                    <td class="text-center w-16 px-4 py-8 text-sm text-gray-900">
                                        <a id="stock_supplier_delete" href="{{ route('stock.delete.stock_suppliers', ['stock_supplier_id' => $stock_supplier->stock_supplier_id ]) }}">
                                            <span class="text-red-500 material-symbols-outlined">
                                                delete
                                            </span>
                                        </a>

                                    </td>


                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                </div>
            </section>


        </div>
    </section>


</div>

<script>
    const img_config_change = document.querySelector('#img_config_change');
    const img_path_input = document.querySelector('#img_path_input');
    if (img_config_change) {

        img_config_change.addEventListener('click', (el) => {
            img_path_input.classList.toggle('pointer-events-none');
            img_path_input.classList.toggle('bg-gray-200');
        });
    }

    const edit_check = document.querySelectorAll('.edit_check');
    const stock_storage_id = document.querySelector('#stock_storage_id');
    if (edit_check) {

        edit_check.forEach((el) => {
            el.addEventListener('click', (e) => {
                console.log(e.target.value);
                stock_storage_id.value = e.target.value;
            });
        });
    }

    const update_button = document.querySelector('#update_button');
    const update_form = document.querySelector('#update_form');
    if (update_button) {

        update_button.addEventListener('click', (e) => {
            e.preventDefault();
            const storage_address_id = document.querySelector('#storage_address_id');
            const selected_list = document.querySelector('.selected');
            if (!storage_address_id.value) {
                storage_address_id.value = selected_list.value;

            }
            update_form.submit();

        });
    }

    const not_jan_code = document.querySelector('#not_jan_code');
    const jan_code = document.querySelector('#jan_code');
    if (not_jan_code) {

        not_jan_code.addEventListener('click', (el) => {
            el.preventDefault();

            jan_code.value = "None";
            jan_code.classList.toggle('bg-gray-50');
            jan_code.classList.toggle('bg-gray-200');
            jan_code.classList.toggle('pointer-events-none');
        });
    }



    // フォームのエンターキーを無効化
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
    const search_supplier_button = document.querySelector('#search_supplier_button');
    const search_supplier_keyword = document.querySelector('#search_supplier_keyword');
    const add_supplier_tbody = document.querySelector('#add_supplier_tbody');
    const stock_id = document.querySelector('#stock_id');

    search_supplier_button.addEventListener('click', (e) => {
        e.preventDefault();
        console.log(search_supplier_keyword.value);

        if (search_supplier_keyword.value == '') {
            alert('検索キーワードを入力してください。');
        }

        axios.get('/api/getSuppliers', {
                params: {
                    keyword: search_supplier_keyword.value
                }
            })
            .then(function(response) {
                add_supplier_tbody.textContent = "";
                const suppliers = response.data;
                suppliers.forEach((supplier) => {
                    console.log(supplier);

                    const newRow = createRow(supplier.name, supplier.tel, supplier.fax, stock_id.value, supplier.id);
                    add_supplier_tbody.appendChild(newRow);
                });
            })
            .catch(function(error) {
                console.log(error);
            })


    });


    function createRow(name, tel, fax, stock_id, supplier_id) {
        const newRow = document.createElement('tr');

        const nameCell = document.createElement('td');
        nameCell.className = 'px-4 py-8 text-sm text-gray-900';
        nameCell.textContent = name;
        newRow.appendChild(nameCell);

        const telCell = document.createElement('td');
        telCell.className = 'px-4 py-8 text-sm text-gray-900';
        telCell.textContent = tel;
        newRow.appendChild(telCell);

        const faxCell = document.createElement('td');
        faxCell.className = 'px-4 py-8 text-sm text-gray-900';
        faxCell.textContent = fax;
        newRow.appendChild(faxCell);

        const actionCell = document.createElement('td');
        actionCell.className = 'w-16 px-4 py-8 text-sm text-gray-900';
        const actionAtag = document.createElement('a');
        actionAtag.href = `/stock/stocks/add_supplier?stock_id=${stock_id}&supplier_id=${supplier_id}`;
        const actionSpan = document.createElement('span');
        actionSpan.className = 'material-symbols-outlined';
        actionSpan.textContent = 'add_circle';

        actionAtag.appendChild(actionSpan);
        actionCell.appendChild(actionAtag);
        newRow.appendChild(actionCell);

        return newRow;
    }

    // 物品得意先情報削除ボタン
    const stock_supplier_delete = document.querySelector('#stock_supplier_delete');
    stock_supplier_delete.addEventListener('click', (e) => {
        e.preventDefault();

        if(confirm('物品得意先情報を削除してもよろしいですか？')){
            window.location.href = stock_supplier_delete.getAttribute('href');
        }
    });
</script>



@endsection