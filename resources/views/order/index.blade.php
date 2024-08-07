@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">発注管理</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 pt-8 pb-24 mx-auto flex flex-wrap justify-center">
        <div class="flex flex-wrap justify-center w-full">

            <div class="lg:w-3/5 md:w-1/2 md:pr-10 md:py-6">

                @foreach($consumOrders as $consumOrder)
                <div class="flex relative pb-12">
                    <div class="h-full w-10 absolute inset-0 flex items-center justify-center">
                        <div class="h-full w-1 bg-gray-200 pointer-events-none"></div>
                    </div>
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-500 inline-flex items-center justify-center text-white relative z-10">
                        <span class="material-symbols-outlined">
                            orders
                        </span>
                    </div>
                    <div class="flex-grow pl-4">
                        <h2 class="font-medium title-font text-sm text-gray-900 mb-1 tracking-wider">{{ Carbon\Carbon::parse($consumOrder->created_at)->format('Y年m月d日') }}</h2>
                        <p class="leading-relaxed">
                            <span class="mr-2 font-semibold text-indigo-600">{{ $consumOrder->user_name }}</span>さんが<span class="mx-2 font-semibold text-red-400">{{ $consumOrder->stock_name }}</span>の注文を依頼しました。
                        </p>
                    </div>
                </div>
                @endforeach

            </div>
            <!-- <img class="lg:w-3/5 md:w-1/2 object-cover object-center rounded-lg md:mt-0 mt-12" src="https://dummyimage.com/1200x500" alt="step"> -->
        </div>
    </div>
</section>



@endsection