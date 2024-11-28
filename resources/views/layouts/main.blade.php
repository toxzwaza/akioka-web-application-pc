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

        <a href="{{ route('movie2') }}" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('movie*') ? 'font-bold text-gray-900' : ''}}">
          <span class="material-symbols-outlined">
            live_tv
          </span>
          動画視聴</a>

        <!-- 図番棚卸 -->
        <a href="{{ route('calc.product') }}" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('calc.product*') ? 'font-bold text-gray-900' : ''}}">
          <span class="mr-1 material-symbols-outlined">
            list_alt
          </span>
          製品棚卸</a>

        <!-- FAX設定 -->
        <a href="{{ route('fax') }}" class="mr-5 hover:text-gray-900 flex justify-center  {{ Route::is('fax*') ? 'font-bold text-gray-900' : ''}}">
          <span class="mr-1 material-symbols-outlined">
            description
          </span>
          FAX振り分け設定</a>


        <!-- サイネージシステム -->
        <a href="{{ route('signage.home') }}" class="mr-5 hover:text-gray-900 flex justify-center  {{ Route::is('signage*') ? 'font-bold text-gray-900' : ''}}">
          <span class="mr-1 material-symbols-outlined">
            description
          </span>
          サイネージ
        </a>
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
    <a href="{{ route('stock.retained.stocks')  }}" class="flex mx-2 px-8 hover:text-gray-900 {{ Route::is('stock.retained.stocks') ? 'text-blue-500 font-bold' : '' }}">
      <span class="text-gray-500 mr-1 material-symbols-outlined">
        link
      </span>
      滞留品
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
  <nav class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700" aria-label="Breadcrumb">
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
      @if(Route::is('stock.edit.stocks'))
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
      @elseif(Route::is('stock.edit.stocks'))
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


  <footer class="text-gray-600 body-font">


  </footer>




</body>

</html>