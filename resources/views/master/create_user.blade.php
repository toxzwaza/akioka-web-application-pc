@extends('layouts.main')
@section('content')

<div class="bg-white py-6 sm:py-8 lg:py-12">
    <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
        <!-- text - start -->
        <div class="mb-10 md:mb-16">
            <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">従業員新規登録</h2>

            <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">
                従業員の新規作成を行います。
            </p>
        </div>
        <!-- text - end -->

        <!-- form - start -->
        <form action="{{ route('master.store.users') }}" method="post" class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">
            @csrf
            <div class="sm:col-span-2">
                <label for="name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">氏名*</label>
                <input name="name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>
            <div class="sm:col-span-2">
                <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">Email</label>
                <input name="email" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>

            <div class="sm:col-span-2">
                <label for="pwd" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">パスワード*</label>
                <input name="pwd" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>

            <hr class="my-8">


            <div class="sm:col-span-2">
                <label for="group_id" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">所属部署*</label>
                <select name="group_id" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                    <option selected value="">未選択</option>
                    @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="position_id" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">役職*</label>
                <select name="position_id" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                    <option selected value="">未選択</option>
                    @foreach($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="sm:col-span-2">
                <label for="process_id" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">製造工程*</label>
                <p class="text-sm mb-4 text-gray-500">製造部に所属しない場合は、選択する必要はありません。</p>
                <div class="sm:col-span-2">
                    <select name="process_id" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                        <option value="0" selected>未選択</option>

                        @foreach($processes as $process)
                        <option value="{{ $process->id }}">{{ $process->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="sm:col-span-2 my-8">

            <div class="sm:col-span-2">
                <div class="sm:col-span-2">
                    <label for="fax_folder_name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">FAX振り分けフォルダ名</label>
                    <p class="text-sm mb-4 text-gray-500">振り分ける必要がない場合、記載する必要はありません。</p>
                    <div class="sm:col-span-2">
                        <input class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" type="text" name="fax_folder_name">
                    </div>
                </div>

            </div>

            <hr class="sm:col-span-2 my-8">


            <div class="sm:col-span-2">

                <label for="is_admin" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">管理者フラグ</label> <br>
                <input type="checkbox" name="is_admin" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>
            <div class="sm:col-span-2">
                <label for="dispatch_flg" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">非常勤・派遣フラグ</label> <br>
                <input type="checkbox" name="dispatch_flg" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>
            <div class="sm:col-span-2">
                <label for="part_flg" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">パート社員フラグ</label> <br>
                <input type="checkbox" name="part_flg" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>

            <div class="sm:col-span-2">
                <label for="always_order_flg" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">弁当注文グループ参加フラグ</label> <br>
                <p class="text-sm mb-4 text-gray-500">頻繁に弁当を注文する場合は、チェックを入れてください。</p>
                <input type="checkbox" name="always_order_flg" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>

            <div class="flex items-center justify-between sm:col-span-2">
                <button class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">新期登録</button>

                <span class="text-sm text-gray-500">*必須</span>
            </div>

        </form>
        <!-- form - end -->
    </div>
</div>
@endsection