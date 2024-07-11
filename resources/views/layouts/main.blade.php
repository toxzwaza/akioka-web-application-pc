<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akioka管理画面</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- google icon -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>
  <header class="text-gray-600 body-font">
    <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
      <a href="{{ route('home') }}" class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
        <img class="w-16" src="{{ asset('img/base/logo.jpg') }}" alt="">
        <span class="ml-3 text-xl">Akioka管理画面</span>
      </a>

      <nav class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400	flex flex-wrap items-center text-base justify-center">

        <a href="{{ route('master') }}" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('master*') ? 'font-bold text-gray-900' : ''}}"><span class="mr-1 material-symbols-outlined">
            database
          </span>基幹マスタ管理</a>
        <a href="{{ route('stock') }}" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('stock*') ? 'font-bold text-gray-900' : ''}}"><span class="mr-1 material-symbols-outlined">
            list_alt
          </span>在庫管理</a>
        <a href="{{ route('order') }}" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('order*') ? 'font-bold text-gray-900' : ''}}"><span class="mr-1 material-symbols-outlined">
            toc
          </span>発注管理</a>
        <a href="{{ route('lunch') }}" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('lunch*') ? 'font-bold text-gray-900' : ''}}"><span class="mr-1 material-symbols-outlined">
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
    <a href="{{ route('master.akioka-users') }}" class="flex mx-2 px-8 hover:text-gray-900">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        list_alt
      </span>
      アキオカ従業員参照
    </a>


    @elseif(Route::is('stock*'))
    <a href="{{ route('stock.stocks.create') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.stocks.create') ? 'text-red-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        edit_square
      </span>在庫追加</a>
    <a href="{{ route('stock.suppliers.create') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.suppliers.create') ? 'text-red-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        edit_square
      </span>取引先追加</a>
    <a href="{{ route('stock.stock_storages.create') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.stock_storages.create') ? 'text-red-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        edit_square
      </span>格納先追加</a>

    <a href="{{ route('stock.stocks') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.stocks') ? 'text-blue-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        list_alt
      </span>
      在庫一覧
    </a>
    <a href="{{ route('stock.suppliers') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.suppliers') ? 'text-blue-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        list_alt
      </span>
      取引先一覧
    </a>
    <a href="{{ route('stock.stock_storages') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.stock_storages') ? 'text-blue-500 font-bold' : '' }}">
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


  <footer class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto flex md:items-center lg:items-start md:flex-row md:flex-nowrap flex-wrap flex-col">
      <div class="w-64 flex-shrink-0 md:mx-0 mx-auto text-center md:text-left">
        <a class="flex title-font font-medium items-center md:justify-start justify-center text-gray-900">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full" viewBox="0 0 24 24">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
          </svg>
          <span class="ml-3 text-xl">Tailblocks</span>
        </a>
        <p class="mt-2 text-sm text-gray-500">Air plant banjo lyft occupy retro adaptogen indego</p>
      </div>
      <div class="flex-grow flex flex-wrap md:pl-20 -mb-10 md:mt-0 mt-10 md:text-left text-center">
        <div class="lg:w-1/4 md:w-1/2 w-full px-4">
          <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">CATEGORIES</h2>
          <nav class="list-none mb-10">
            <li>
              <a class="text-gray-600 hover:text-gray-800">First Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Second Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Third Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Fourth Link</a>
            </li>
          </nav>
        </div>
        <div class="lg:w-1/4 md:w-1/2 w-full px-4">
          <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">CATEGORIES</h2>
          <nav class="list-none mb-10">
            <li>
              <a class="text-gray-600 hover:text-gray-800">First Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Second Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Third Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Fourth Link</a>
            </li>
          </nav>
        </div>
        <div class="lg:w-1/4 md:w-1/2 w-full px-4">
          <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">CATEGORIES</h2>
          <nav class="list-none mb-10">
            <li>
              <a class="text-gray-600 hover:text-gray-800">First Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Second Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Third Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Fourth Link</a>
            </li>
          </nav>
        </div>
        <div class="lg:w-1/4 md:w-1/2 w-full px-4">
          <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">CATEGORIES</h2>
          <nav class="list-none mb-10">
            <li>
              <a class="text-gray-600 hover:text-gray-800">First Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Second Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Third Link</a>
            </li>
            <li>
              <a class="text-gray-600 hover:text-gray-800">Fourth Link</a>
            </li>
          </nav>
        </div>
      </div>
    </div>
    <div class="bg-gray-100">
      <div class="container mx-auto py-4 px-5 flex flex-wrap flex-col sm:flex-row">
        <p class="text-gray-500 text-sm text-center sm:text-left">© 2020 Tailblocks —
          <a href="https://twitter.com/knyttneve" rel="noopener noreferrer" class="text-gray-600 ml-1" target="_blank">@knyttneve</a>
        </p>
        <span class="inline-flex sm:ml-auto sm:mt-0 mt-2 justify-center sm:justify-start">
          <a class="text-gray-500">
            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
              <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
            </svg>
          </a>
          <a class="ml-3 text-gray-500">
            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
              <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
            </svg>
          </a>
          <a class="ml-3 text-gray-500">
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
              <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
            </svg>
          </a>
          <a class="ml-3 text-gray-500">
            <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="0" class="w-5 h-5" viewBox="0 0 24 24">
              <path stroke="none" d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
              <circle cx="4" cy="4" r="2" stroke="none"></circle>
            </svg>
          </a>
        </span>
      </div>
    </div>
  </footer>




</body>

</html>