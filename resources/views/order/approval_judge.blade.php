@extends('layouts.main')
@section('content')


@if($user_id)
<div class=" mb-8">
    <p class="text-sm mb-4">承認・非承認における理由を入力して、下記のボタンを押下してください。</p>
    <textarea class="block p-2.5 w-full text-lg text-gray-800 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-100 dark:placeholder-gray-400 dark:text-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500" name="" id="" cols="30" rows="5"></textarea>

    <div class="flex items-center justify-around my-4">

        <!-- 承認・非承認ボタン -->
        <button class="approval_button bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">
            非承認
        </button>
        <button class="approval_button bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            承認
        </button>
    </div>


</div>

@else
<p class="text-lg mb-4 ">承認者一覧</p>

<div class="flex items-start">
    @foreach($approval->approval_flows as $flow)
    <a class="relative flex items-center w-40 hover:text-indigo-600" href="{{ route('order.approval.judge', ['id' => $approval->id , 'user_id' => $flow->user_id ]) }}">
        <span class="material-symbols-outlined">
            account_circle
        </span>
        <span class="animate-pulse bottom-0 right-16 absolute  w-3.5 h-3.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span>
        {{ $flow->user_name }}</a>
    @endforeach
</div>

@endif






<hr class="my-16">


<table class="table-auto w-full text-left whitespace-no-wrap">
    <tbody>
        <tr>
            <th class="w-1/5 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">件名</th>

            <td class="px-4 py-8 text-lg text-gray-900">{{ $approval->title }}</td>
        </tr>
        <tr>
            <th class="w-1/5 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">購入品</th>
            <td class="px-4 py-8 text-lg text-gray-900">{{ $approval->name }}</td>
        </tr>
        <tr>
            <th class="w-1/5 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">購入金額</th>
            <td class="px-4 py-8 text-lg text-gray-900">{{ $approval->price }}</td>
        </tr>
        <tr>
            <th class="w-1/5 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">購入目的</th>
            <td class="px-4 py-8 text-lg text-gray-900">{{ $approval->reason }}</td>
        </tr>
        <tr>
            <th class="w-1/5 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">審査状況</th>
            <td class="px-4 py-8 text-lg text-gray-900">審査中</td>
        </tr>
        <tr>
            <th class="w-24 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">作成日</th>
            <td class="px-4 py-8 text-lg text-gray-900">{{ Carbon\Carbon::parse($approval->created_at)->format('Y/m/d') }}</td>
        </tr>
        <tr>
            <th class="w-24 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">見積書</th>
            <td class="px-4 py-8 text-lg text-gray-900">
                <details>
                    <summary class="my-4 text-gray-600 font-semibold">表示切り替え</summary>

                    <img src="http://monokanri-app.local/{{ $approval->est_doc_path }}" alt="">
                </details>

            </td>
        </tr>
    </tbody>
</table>





@endsection