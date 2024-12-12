@extends('layouts.main')
@section('content')



<section class="text-gray-600 body-font">
    <h1 class="text-center text-xl font-bold text-gray-800 mb-4">在庫格納先・アドレス追加</h1>

    <div class="container px-5 pb-8 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">在庫格納先（倉庫）の追加、保管アドレスを追加します。<br>

            </p>
            <div class="flex items-start mt-8">
                <div class="px-8 w-1/2 border-r border-gray-200 text-left">
                    <!-- 格納先作成フォーム -->
                    <h2 class="font-bold mb-8 text-left text-gray-500">格納先追加</h2>
                    <p>現在の格納先一覧は下に表示しています。</p>
                    <form class="w-full" action="{{ route('stock.locations.create') }}">
                        <input type="text" name="location_name" id="" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">

                        <button class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            追加
                        </button>
                        <!-- <span class="absolute right-2 ml-2 material-symbols-outlined">search</span> -->
                    </form>

                </div>

                <div class="px-8 w-1/2 border-r border-gray-200">
                    <!-- 格納先作成フォーム -->
                    <h2 class="font-bold mb-8 text-left text-gray-500">格納先アドレス追加</h2>
                    <form class="w-full text-left" action="{{ route('stock.storage_address.create') }}">
                        <div class="sm:col-span-2 mb-4">
                            <label for="email" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">格納先*</label><br>
                            <select name="location_id" id="location_select" class="w-1/2 rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                                <option value="0" selected>未選択</option>
                                @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="sm:col-span-2 mb-4">
                            <label for="address" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">アドレス*</label><br>
                            <input type="text" name="address" id="address" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring">
                        </div> -->

                        <p class="mt-8 mb-2 text-sm text-red-400">※「棚番 - 段数 - 列数」がアドレスとなります。<br>段数０は床面を表します。</p>
                        <div class="flex items-center justify-start">

                            <div class="sm:col-span-2 mb-4 w-1/5 mr-4">
                                <label for="address" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">棚*</label><br>
                                <input type="text" name="shelf" id="address" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="A">
                            </div>
                            <div class="sm:col-span-2 mb-4 w-1/5 mr-4">
                                <label for="address" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">段*</label><br>
                                <input type="text" name="row" id="address" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="1">
                            </div>
                            <div class="sm:col-span-2 mb-4 w-1/5 mr-4">
                                <label for="address" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">列</label><br>
                                <input type="text" name="col" id="address" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="1">
                            </div>
                            <div class="sm:col-span-2 mb-4 w-1/5 mr-4">
                                <label for="address" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">列の中の段</label><br>
                                <input type="text" name="sub_row" id="address" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="1">
                            </div>
                        </div>
                        

                        <button class="mt-4 border bg-white hover:bg-blue-400 hover:text-white text-blue-700 font-bold py-2 px-4 rounded">
                            追加
                        </button>

                        <div class="w-full mt-8">
                            <h3 class="font-bold">追加済みアドレス</h3>
                            <p class="text-sm mt-2">格納先を選択すると、以下に追加済みのアドレスが表示されます。</p>
                            <div id="badge_container" class="mt-4 flex items-center flex-wrap max-h-40 overflow-y-scroll">

                            </div>
                        </div>
                        <!-- <span class="absolute right-2 ml-2 material-symbols-outlined">search</span> -->
                    </form>

                </div>


            </div>



        </div>





    </div>

    <div class="text-center">

        <a href="{{ route('stock.storage_addresses.print') }}" class="text-red-600 font-bold underline text-2xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 8H5V5h14v3zm1-5H4a1 1 0 00-1 1v5h18V4a1 1 0 00-1-1zM6 19h12v-6H6v6zm13-8H5a1 1 0 00-1 1v7a1 1 0 001 1h14a1 1 0 001-1v-7a1 1 0 00-1-1z"/>
            </svg> アドレス用紙を印刷する場合はこちらをクリック！
        </a>
    </div>
</section>
<hr class="my-8">


<section class="text-gray-600 body-font">
    <div class="container px-5 py-4 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
            <h1 class="text-center text-xl font-bold text-gray-800 mb-4">格納先一覧</h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                格納先の名前を修正することができます。
            </p>
        </div>
        <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ID</th>
                        <th class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">格納先名</th>
                        <th class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">アドレス数</th>
                        <th class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">更新日</th>
                        <!-- <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $location)
                    <tr>
                        <td class="px-4 py-4">{{ $location->id }}</td>

                        <td class="px-4 py-4">
                            <a href="{{ route('stock.storage_addresses', ['location_id' => $location->id ]) }}">{{ $location->name }}</a>
                        </td>

                        <td class="px-4 py-4">{{ $location->address_count }}</td>
                        <td class="px-4 py-4">{{ Carbon\Carbon::parse($location->created_at)->format('Y/m/d') }}</td>
                        <!-- <td class="px-4 py-4 w-32">
                            <a class="px-4 py-2 bg-blue-500 text-sm text-white rounded-md" href="">更新</a>
                        </td> -->

                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">
            <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0">Learn More
                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                </svg>
            </a>
            <button class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">Button</button>
        </div>
    </div>
</section>

<script>
    const location_select = document.querySelector('#location_select');
    const badge_container = document.querySelector('#badge_container');

    location_select.addEventListener('change', (e) => {
        console.log(e.target.value);
        if (e.target.value) {
            axios.get('/api/getAddress', {
                    params: {
                        location_id: e.target.value
                    }
                })
                .then(response => {
                    // console.log(response.data);
                    badge_container.innerHTML = '';
                    if (response.data) {
                        response.data.forEach((address) => {
                            // console.log(address.address);
                            const newBadge = document.createElement('span');
                            newBadge.classList.add('mx-2', 'my-2', 'bg-gray-100', 'text-white', 'font-medium', 'me-2', 'px-2.5', 'py-0.5', 'rounded', 'dark:bg-gray-500', 'dark:text-white', 'border', 'border-gray-500');
                            newBadge.textContent = address.address;
                            badge_container.appendChild(newBadge);
                        });
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }
    });
</script>


@endsection