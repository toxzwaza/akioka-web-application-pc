<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- google icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

</head>

<body>
    <header class="text-gray-600 body-font">
        <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
            <a href="{{ route('home') }}" class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                <img class="w-16" src="{{ asset('img/base/logo.jpg') }}" alt="">
                <span class="ml-3 text-xl">Akioka管理画面</span>
            </a>

            <nav class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400	flex flex-wrap items-center text-base justify-center">

                <a href="{{ route('master') }}" class="mr-5 hover:text-gray-900 flex justify-center"><span class="mr-1 material-symbols-outlined">
                        database
                    </span>基幹マスタ管理</a>
                <a href="{{ route('stock') }}" class="mr-5 hover:text-gray-900 flex justify-center"><span class="mr-1 material-symbols-outlined">
                        list_alt
                    </span>在庫管理</a>
                <a href="{{ route('order') }}" class="mr-5 hover:text-gray-900 flex justify-center"><span class="mr-1 material-symbols-outlined">
                        toc
                    </span>発注管理</a>
                <a href="{{ route('lunch') }}" class="mr-5 hover:text-gray-900 flex justify-center"><span class="mr-1 material-symbols-outlined">
                        restaurant
                    </span>弁当注文</a>
            </nav>


        </div>
    </header>

    <!-- サブナビゲーション -->
    <nav class="flex justify-flex-start items-center bg-gray-200 p-4 overflow-x-auto">
        @if(Route::is('master*'))
        <a href="{{ route('master.create.user') }}" class="flex mx-2 px-8 hover:text-gray-900">
            <span class="text-gray-500 mr-1 material-symbols-outlined">
                edit_square
            </span>従業員登録</a>
        <a href="{{ route('master.users') }}" class="flex mx-2 px-8 hover:text-gray-900">
            <span class="text-gray-500 mr-1 material-symbols-outlined">
                list_alt
            </span>
            全従業員参照
        </a>
        <a href="{{ route('master.users') }}" class="flex mx-2 px-8 hover:text-gray-900">
            <span class="text-gray-500 mr-1 material-symbols-outlined">
                list_alt
            </span>
            アキオカ従業員参照
        </a>
        @elseif(Route::is('stock*'))
        <a href="{{ route('master.create.user') }}" class="flex mx-2 px-8 hover:text-gray-900">
            <span class="text-gray-500 mr-1 material-symbols-outlined">
                edit_square
            </span>新規在庫追加</a>
            <a href="{{ route('master.create.user') }}" class="flex mx-2 px-8 hover:text-gray-900">
            <span class="text-gray-500 mr-1 material-symbols-outlined">
                edit_square
            </span>新規取引先追加</a>

        <a href="{{ route('stock.stocks') }}" class="flex mx-2 px-8 hover:text-gray-900">
            <span class="text-gray-500 mr-1 material-symbols-outlined">
                list_alt
            </span>
            在庫一覧
        </a>
        <a href="{{ route('stock.stock_storages') }}" class="flex mx-2 px-8 hover:text-gray-900">
            <span class="text-gray-500 mr-1 material-symbols-outlined">
                list_alt
            </span>
            格納先一覧
        </a>

        @elseif(Route::is('order*'))
        <a href="#" class="flex mx-2 px-8 hover:text-gray-900">物品承認完了リスト</a>
        <a href="#" class="flex mx-2 px-8 hover:text-gray-900">発注待ちリスト</a>
        @elseif(Route::is('lunch*'))
        <a href="#" class="flex mx-2 px-8 hover:text-gray-900">弁当注文履歴</a>
        <a href="#" class="flex mx-2 px-8 hover:text-gray-900">備考作成</a>
        @endif
    </nav>


    <main class="py-16 px-24">

        @yield('content')
    </main>




</body>

</html>