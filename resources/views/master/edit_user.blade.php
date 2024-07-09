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
        <form class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">ID*</label>
                <input name="name" class="pointer-events-none w-full rounded border bg-gray-200 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $user->id }}"/>
            </div>

            <div class="sm:col-span-2">
                <label for="name" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">氏名*</label>
                <input name="name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $user->name }}" />
            </div>
            <div class="sm:col-span-2">
                <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">Email*</label>
                <input name="email" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $user->email }}" placeholder="{{ $user->email ?? '設定されていません。'}}" />
            </div>

            <div class="sm:col-span-2">
                <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">パスワード*</label>
                <input name="email" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="{{ $user->password }}" />
            </div>

            <hr class="my-8">


            <div class="sm:col-span-2">
                <label for="company" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">所属部署*</label>
                <select name="company" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                    <option value="部署1">部署1</option>
                    <option value="部署2">部署2</option>
                    <option value="部署3">部署3</option>
                    <option value="部署4">部署4</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="company" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">役職*</label>
                <select name="company" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                    <option value="部署1">部署1</option>
                    <option value="部署2">部署2</option>
                    <option value="部署3">部署3</option>
                    <option value="部署4">部署4</option>
                </select>
            </div>


            <div class="sm:col-span-2">
                <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">製造工程*</label>
                <p class="text-sm mb-4 text-gray-500">製造部に所属しない場合は、選択する必要はありません。</p>
                <div class="sm:col-span-2">
                    <select name="company" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                        <option value="部署1">部署1</option>
                        <option value="部署2">部署2</option>
                        <option value="部署3">部署3</option>
                        <option value="部署4">部署4</option>
                    </select>
                </div>
            </div>

            <hr class="my-8">

            <div class="sm:col-span-2">
                <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">管理者フラグ*</label> <br>
                <input type="checkbox" name="message" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>
            <div class="sm:col-span-2">
                <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">非常勤・派遣フラグ*</label> <br>
                <input type="checkbox" name="message" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>
            <div class="sm:col-span-2">
                <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">パート社員フラグ*</label> <br>
                <input type="checkbox" name="message" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
            </div>

            <div class="sm:col-span-2">
                <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">弁当注文グループ参加フラグ*</label> <br>
                <p class="text-sm mb-4 text-gray-500">頻繁に弁当を注文する場合は、チェックを入れてください。</p>
                <input type="checkbox" name="message" class="h-4 w-4 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
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