@extends('layouts.main')
@section('content')
<div>
    <section class="text-gray-600 body-font">


        <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-start justify-center">


            <div class="bg-white py-6 sm:py-8 lg:py-12">
                <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
                    <!-- text - start -->
                    <div class="mb-10 md:mb-16">
                        <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">在庫追加</h2>

                        <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">
                            在庫新規追加を行います。
                        </p>
                    </div>


                    <form action="{{ route('stock.store.stocks') }}" method="post" class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2" enctype="multipart/form-data">
                        @csrf
                        <!-- <div>
                            <label for="first-name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ID*</label>

                            <input name="stock_id" class="pointer-events-none w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div> -->

                        <div>
                            <label for="stock_no" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">在庫no</label>
                            <input name="stock_no" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="company" class="mb-2 inline-block text-sm text-red-400 sm:text-base">品名*</label>
                            <input name="name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">JANコード</label>
                            <input name="jan_code" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">品番</label>
                            <input name="s_name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div>

                        <div class="">
                            <label for="subject" class="mb-2 inline-block text-sm text-red-400 sm:text-base">ファイル選択*</label>
                            <input name="upload_file" type="file" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div>
                        <div class="">

                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">画像パス</label>
                            <input name="img_path" id="img_path_input" class="pointer-events-none w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />

                            <p id="img_config_change" class="text-sm mt-4 underline decoration-1 pb-1 text-indigo-300">インターネット上の画像を設定</p>
                        </div>

                        <div class="">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">価格</label>
                            <input name="price" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />

                        </div>
                        <div class="">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">購買URL</label>
                            <input name="url" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />

                        </div>
                        <div class="sm:col-span-2">
                            <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">EC購買用識別番号</label>
                            <input name="purchase_identification_number" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div>
                        <div class="container w-full flex items-center justify-between sm:col-span-2">
                            <div class="w-1/4 pr-
                            4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">単位(一つ)</label>
                                <input name="solo_unit" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                            </div>
                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">単位(まとめ)</label>
                                <input name="org_unit" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                            </div>
                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ひとまとまり数量</label>
                                <input name="quantity_per_org" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                            </div>

                            <div class="w-1/4 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">基本発注単位</label>

                                <select name="main_unit_flg" id="" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option  value="0">単品発注</option>
                                    <option  value="1">まとめて発注</option>
                                </select>
                            </div>

                        </div>

                        <div class="container w-full flex items-center justify-between sm:col-span-2">
                            <div class="w-1/2 pr-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-red-400 sm:text-base">*備品カテゴリ</label>
                                <select name="classification_id" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option value="0">未選択</option>

                                    @foreach($classifications as $class)
                                    <option value="">{{ $class->name }}</option>
                       
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-1/2 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">配送先</label>
                                <input name="deli_location" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />

                            </div>
                            <div class="w-1/2 px-4">
                                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">主な使用先</label>
                                <select name="process_code" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                    <option value="0">未選択</option>

                                </select>
                            </div>


                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">メモ</label>
                            <textarea name="memo" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" rows="4"></textarea>
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

    img_config_change.addEventListener('click', (el) => {
        img_path_input.classList.toggle('pointer-events-none');
        img_path_input.classList.toggle('bg-gray-200');
    });

    const edit_check = document.querySelectorAll('.edit_check');
    const stock_storage_id = document.querySelector('#stock_storage_id');
    edit_check.forEach((el) => {
        el.addEventListener('click', (e) => {
            console.log(e.target.value);
            stock_storage_id.value = e.target.value;
        });
    });
</script>



@endsection