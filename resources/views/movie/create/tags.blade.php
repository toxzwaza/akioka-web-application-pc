@extends('layouts.main')
@section('content')
<div class="flex justify-between">
    <div class="w-1/2 bg-white py-6 sm:py-8 lg:py-12">
        <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
            <!-- text - start -->
            <div class="mb-10 md:mb-16">
                <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">カテゴリー追加</h2>

                <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">動画の分類カテゴリーを作成します。</p>
            </div>
            <!-- text - end -->

            <!-- form - start -->
            <form class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">


                <div class="sm:col-span-2">
                    <label for="subject" class="mb-2 inline-block text-sm text-gray-600 sm:text-base">カテゴリー名*</label>
                    <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
                </div>
                <div class="sm:col-span-2">
                    <label for="subject" class="mb-2 inline-block text-sm sm:text-base text-gray-600">アクセントカラー*</label>
                    <select name="accent_color" id="accent_color" class="w-full rounded border bg-gray-50 px-3 py-2 outline-none transition duration-100 focus:ring">
                        <option value="">未選択</option>
                        <option value="yellow">黄色</option>
                        <option value="red">赤</option>
                        <option value="green">緑</option>
                        <option value="blue">青</option>
                        <option value="indigo">水色</option>
                        <option value="purple">紫</option>
                        <option value="pink">ピンク</option>

                    </select>
                </div>

                <div class="w-full sm:col-span-2 mb-12">
                    <p class="text-gray-400 text-xs mb-2">作成済みカテゴリ↓</p>
                    @foreach($movie_categories as $category)
                    <span class="bg-{{ $category->accent_color }}-100 text-{{ $category->accent_color }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-{{ $category->accent_color }}-400 border border-{{ $category->accent_color }}-400">{{ $category->name }}</span>
                    @endforeach

                </div>

                <div class="flex items-center justify-between sm:col-span-2">
                    <button class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">追加</button>

                    <span class="text-sm text-gray-500">*Required</span>
                </div>
            </form>
            <!-- form - end -->
        </div>
    </div>

    <div class="w-1/2 bg-white py-6 sm:py-8 lg:py-12">
        <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
            <!-- text - start -->
            <div class="mb-10 md:mb-16">
                <h2 class="mb-4 text-center text-2xl font-bold text-gray-600 md:mb-6 lg:text-3xl">タグ追加</h2>

                <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">タグを設定することで、カテゴリーからさらに詳しく分類分けすることができます。</p>
            </div>
            <!-- text - end -->

            <!-- form - start -->
            <form class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="company" class="mb-2 inline-block text-sm text-gray-600 sm:text-base">カテゴリー*</label>
                    <select name="" id="category_select" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-600 outline-none ring-indigo-300 transition duration-100 focus:ring">
                        @foreach($movie_categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="email" class="mb-2 inline-block text-sm text-gray-600 sm:text-base">タグ名*</label>
                    <input name="email" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-600 outline-none ring-indigo-300 transition duration-100 focus:ring" />
                </div>
                <div class="w-full sm:col-span-2 mb-12">
                    <p class="text-gray-400 text-xs mb-2">作成済みタグ↓</p>
                    <p id="tag_container"></p>


                </div>


                <div class="sm:col-span-2">
                    <label for="subject" class="mb-2 inline-block text-sm sm:text-base text-gray-600">アクセントカラー*</label>
                    <select name="accent_color" id="tag_accent_color" class="w-full rounded border bg-gray-50 px-3 py-2 outline-none transition duration-100 focus:ring">
                        <option value="">未選択</option>
                        <option value="yellow">黄色</option>
                        <option value="red">赤</option>
                        <option value="green">緑</option>
                        <option value="blue">青</option>
                        <option value="indigo">水色</option>
                        <option value="purple">紫</option>
                        <option value="pink">ピンク</option>

                    </select>
                </div>

                <div class="flex items-center justify-between sm:col-span-2 mt-8">
                    <button class="inline-block rounded-lg bg-white px-8 py-2 text-center text-sm font-semibold text-indigo-400 outline ring-indigo-300 transition duration-100 hover:bg-indigo-600 hover:text-white focus-visible:ring active:bg-indigo-700 md:text-base">追加</button>

                    <span class="text-sm text-gray-500">*Required</span>
                </div>

            </form>
            <!-- form - end -->
        </div>
    </div>
</div>
<script>
    const accent_color = document.querySelector('#accent_color');
    accent_color.addEventListener('change', (e) => {
        addClassColor(e.target, e.target.value)
    });
    const tag_accent_color = document.querySelector('#tag_accent_color');
    tag_accent_color.addEventListener('change', (e) => {
        addClassColor(e.target, e.target.value);
    });

    function addClassColor(target, value) {
        target.classList.add('text-' + value + '-500');
        target.classList.add('font-semibold');
        target.classList.add('ring-' + value + '-500');
    }


    const category_select = document.querySelector('#category_select');
    const tag_container = document.querySelector('#tag_container');
    category_select.addEventListener('change', (e) => {
        console.log(e.target.value);
        axios.get('/api/getMovieTags', {
                params: {
                    movie_category_id: e.target.value
                }
            })
            .then(response => {
                // console.log(response.data);
                tag_container.innerHTML = '';
                if (response.data) {
                    response.data.forEach((tag) => {
                        // console.log(address.address);
                        const newBadge = document.createElement('span');
                        newBadge.classList.add(`bg-${tag.accent_color}-100`, `text-${tag.accent_color}-600`, `text-xs`, `font-medium`, `me-2`, `px-2.5`, `py-0.5`, `rounded`, `dark:bg-${tag.accent_color}-700`, `dark:text-${tag.accent_color}-400`, `border`, `border-${tag.accent_color}-400`);
                        newBadge.textContent = tag.name;
                        tag_container.appendChild(newBadge);
                    });
                }
            })
            .catch(error => {
                console.error(error);
            });
    })
</script>

@endsection