@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">注文書 - 備考作成</h1>
<div class="bg-white py-6 sm:py-8 lg:py-12">
    <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
        <!-- text - start -->
        <div class="mb-10 md:mb-16">


            <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">
                当日の注文書に記載する備考を設定することができます。<br>
                注文時間[8:55]を超過した場合、反映されませんのでご注意ください。
            </p>
        </div>
        <div class="mx-auto max-w-screen-md my-8">
            <h2 class="text-center text-xl text-gray-600 mb-4">テンプレートから設定</h2>

            <div class="flex justify-around">
                <button class="template_button tmp_1 mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">ふりかけ</button>
                <button class="template_button tmp_2 mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">味噌汁</button>
                <button class="template_button tmp_3 mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">両方</button>
            </div>

            <hr class="my-12">

        </div>



        <form id="description_form" action="{{ route('lunch.store_description') }}" method="post" class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">
            @csrf
            <input id="method" type="hidden" name="method" value="">

            <div class="sm:col-span-2">
                <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">備考*</label>
                <textarea id="description" name="description" class="h-64 w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">{{ $today_lunch_description->description ?? '' }}</textarea>
            </div>

            <div class="flex items-center justify-between sm:col-span-2">
                <div class="flex justify-start">
                    <button class="mr-4 inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">作成</button>
                    <button class="delete_description inline-block rounded-lg bg-red-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-red-300 transition duration-100 hover:bg-red-600 focus-visible:ring active:bg-red-700 md:text-base">削除</button>

                </div>


                <span class="text-sm text-gray-500">*Required</span>
            </div>

            <p class="text-xs text-gray-400">By signing up to our newsletter you agree to our <a href="#" class="underline transition duration-100 hover:text-indigo-500 active:text-indigo-600">Privacy Policy</a>.</p>
        </form>
        <!-- form - end -->
    </div>
</div>
<script>
    const current_date = new Date(Date.now() + 2 * 24 * 60 * 60 * 1000).toLocaleDateString('ja-JP', {
        month: 'numeric',
        day: 'numeric',
        weekday: 'long'
    });

    const template_button = document.querySelectorAll('.template_button');
    const description = document.querySelector('#description');


    template_button.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (e.target.classList.contains('tmp_1')) {
                description.textContent = `お世話になっております。\n${ current_date }の配達の際までにふりかけを持ってきてください。\nよろしくお願いいたします。`;
            } else if (e.target.classList.contains('tmp_2')) {
                description.textContent = `お世話になっております。\n${ current_date }の配達の際までに味噌汁を持ってきてください。\nよろしくお願いいたします。`;

            } else if (e.target.classList.contains('tmp_3')) {
                description.textContent = `お世話になっております。\n${ current_date }の配達の際までにふりかけと味噌汁を持ってきてください。\nよろしくお願いいたします。`;
            }
        });
    });

    //削除機能
    const delete_description = document.querySelector('.delete_description');
    const method = document.querySelector('#method');
    const description_form = document.querySelector('#description_form');

    delete_description.addEventListener('click' , (e) => {
        e.preventDefault();
        method.value = "delete";
        description_form.submit();
    });
</script>


@endsection