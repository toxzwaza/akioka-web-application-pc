<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akioka管理画面</title>


  <!-- google icon -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

  <script src="https://cdn.tailwindcss.com"></script>

  <!-- chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- axios -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <!-- JavaScriptカレンダー -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build@3.4.0/event-calendar.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@event-calendar/build@3.4.0/event-calendar.min.js"></script>

</head>

<body>
  <header class="text-gray-600 body-font print_hidden">
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

        <a href="{{ route('movie') }}" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('movie*') ? 'font-bold text-gray-900' : ''}}">
          <span class="material-symbols-outlined">
            live_tv
          </span>
          動画視聴</a>
      </nav>



      @if(session('user') == null)
      <a class="flex items-center" href="{{ route('login') }}">
        <span class="material-symbols-outlined">
          account_circle
        </span>
        Login
      </a>
      @else
      <a class="flex items-center" href="{{ route('logout') }}">
        <span class="material-symbols-outlined">
          logout
        </span>
        {{ session('user.name') }}
      </a>


      @endif


    </div>



  </header>

  <!-- サブナビゲーション -->
  <nav class="flex justify-flex-start items-center bg-gray-200 p-4 overflow-x-auto print_hidden">
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
    <a href="{{ route('stock.storage_addresses.create') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.storage_addresses.create') ? 'text-red-500 font-bold' : '' }}">
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
    <a href="{{ route('stock.storage_addresses') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.storage_addresses') ? 'text-blue-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        list_alt
      </span>
      格納先一覧
    </a>

    <a href="{{ route('stock.stocks.taking') }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.storage_addresses') ? 'text-blue-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        list_alt
      </span>
      棚卸し
    </a>



    @elseif(Route::is('order*'))
    <a href="{{ route('order.consumOrders') }}" class="flex mx-2 px-8 hover:text-gray-900"><span class="material-symbols-outlined">
        assignment
      </span>消耗品発注依頼リスト
    </a>

    <a href="{{ route('order.already_requests') }}" class="opacity-30 flex mx-2 px-8 hover:text-gray-900"><span class="material-symbols-outlined">
        approval_delegation
      </span>承認依頼
    </a>

    <a href="{{ route('order.already_orders') }}" class="opacity-30 flex mx-2 px-8 hover:text-gray-900">
      <span class="material-symbols-outlined">
        assignment
      </span>
      注文書作成待ちリスト
    </a>
    <a href="#" class="opacity-30 flex mx-2 px-8 hover:text-gray-900">
      <span class="material-symbols-outlined">
        grade
      </span>
      注文待ちリスト
    </a>
    <a href="#" class="opacity-30 flex mx-2 px-8 hover:text-gray-900"><span class="material-symbols-outlined">
        orders
      </span>納品待ちリスト
    </a>


    @elseif(Route::is('lunch*'))
    <a href="{{ route('lunch.order-archive') }}" class="flex mx-2 px-8 hover:text-gray-900">弁当注文履歴</a>
    <a href="{{ route('lunch.create_description') }}" class="flex mx-2 px-8 hover:text-gray-900">備考作成</a>
    @elseif(Route::is('movie*'))
    <a href="{{ route('movie') }}" class="flex mx-2 px-8 hover:text-gray-900">動画一覧</a>
    <a href="{{ route('movie.create') }}" class="flex mx-2 px-8 hover:text-gray-900">動画追加</a>
    <a href="{{ route('movie.create.tag') }}" class="flex mx-2 px-8 hover:text-gray-900">動画タグ追加</a>

    @endif
  </nav>
  <!-- パンくずリスト -->


  <!-- Breadcrumb -->
  <nav class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 print_hidden" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
      <li class="inline-flex items-center">
        <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
          <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
          </svg>
          Home
        </a>
      </li>

      @if(Route::is('master*'))
      <li>
        <div class="flex items-center">
          <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
            基幹マスタ管理</a>
        </div>
      </li>
      @if(Route::is('master'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">TOP</span>
        </div>
      </li>
      @endif
      @elseif(Route::is('stock*'))
      <li>
        <div class="flex items-center">
          <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
            在庫管理</a>
        </div>
      </li>
      <!-- 中層 -->
      @if(Route::is('stock.show.stocks'))
      <li>
        <div class="flex items-center">
          <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <a href="{{ route('stock.stocks') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
            在庫一覧</a>
        </div>
      </li>
      @endif
      <!-- 下層部 -->
      @if(Route::is('stock'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">TOP</span>
        </div>
      </li>
      @elseif(Route::is('stock.stocks.create'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">在庫追加</span>
        </div>
      </li>
      @elseif(Route::is('stock.suppliers.create'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">取引先追加</span>
        </div>
      </li>
      @elseif(Route::is('stock.storage_addresses.create'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">格納先アドレス追加</span>
        </div>
      </li>

      @elseif(Route::is('stock.stocks'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">在庫一覧</span>
        </div>
      </li>
      @elseif(Route::is('stock.suppliers'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">取引先一覧</span>
        </div>
      </li>
      @elseif(Route::is('stock.storage_addresses'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">格納先アドレス一覧</span>
        </div>
      </li>
      @elseif(Route::is('stock.show.stocks'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">在庫編集</span>
        </div>
      </li>

      @endif
      @elseif(Route::is('order*'))
      <li>
        <div class="flex items-center">
          <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
            発注管理</a>
        </div>
      </li>
      @if(Route::is('order'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">TOP</span>
        </div>
      </li>
      @endif
      @elseif(Route::is('lunch*'))
      <li>
        <div class="flex items-center">
          <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
            弁当注文</a>
        </div>
      </li>
      @if(Route::is('lunch'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">TOP</span>
        </div>
      </li>
      @endif
      @elseif(Route::is('movie*'))
      <li>
        <div class="flex items-center">
          <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
            動画視聴</a>
        </div>
      </li>
      @if(Route::is('movie'))
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">TOP</span>
        </div>
      </li>
      @endif


      @endif



      <!-- <li aria-current="page">
        <div class="flex items-center">
          <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
          </svg>
          <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Flowbite</span>
        </div>
      </li> -->
    </ol>
  </nav>



  <!-- メッセージ -->
  @if(session('success'))
  <div class="flex items-center bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
    <span class="mr-2 material-symbols-outlined">
      check_circle
    </span>
    <p>{{ session('success') }}</p>
  </div>
  @endif

  @if(session('error'))
  <div class="flex items-center bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
    <span class="mr-2 material-symbols-outlined">
      error
    </span>

    <p>{{ session('error') }}</p>
  </div>
  @endif

  @if(session('info'))
  <div class="flex items-center bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4" role="alert">
    <span class="mr-2 material-symbols-outlined">
      info
    </span>

    <p>{{ session('info') }}</p>
  </div>

  @endif


  <main class="py-16 px-24">

    @yield('content')
  </main>


  <footer class="text-gray-600 body-font print_hidden">
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