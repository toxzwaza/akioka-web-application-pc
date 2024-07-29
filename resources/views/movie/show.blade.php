@extends('layouts.main')
@section('content')
<div class="w-full flex items-start justify-between">
    <div class="w-1/5 pr-4">
        <!-- 関連動画 -->
        <p class="text-gray-800 py-4 flex items-center text-sm">
            <span class="material-symbols-outlined pr-2 text-sm">help</span>
            関連動画一覧を表示
        </p>
        <hr>
        <section class="text-gray-600 body-font">
            <div class="container">

                <div class="w-full overflow-auto">
                    <table class="table-auto w-full text-left whitespace-no-wrap">
                        <tbody>
                            @foreach($con_movies as $con_movie)
                            <tr class="text-sm">
                                <td class="py-4" style="accent-color: gray;"><input type="checkbox" name="" id=""></td>

                                <td class="py-4">{{ $con_movie->name }}</td>
                                <td class="py-4">
                                    <a href="{{ route('movie.show', ['movie_id' => $con_movie->id]) }}">
                                        <span class="material-symbols-outlined">
                                            slideshow
                                        </span>
                                    </a>

                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

        </section>
    </div>

    <div class="">
        <!-- Youtube動画画面 -->

        <div id="YTContent" class="{{ $movie->file_path }} {{ $movie->id }} video_content"></div>


        <form action="" class="flex justify-center mt-16">
            <input class="block w-4/5 sm:w-2/3 bg-gray-200 py-2 px-3 text-gray-700 border border-gray-200 rounded focus:outline-none focus:bg-white mr-4" id="memo" type="text" name="">
            <button class="border border-gray-400 rounded-md px-2 py-1 text-sm font-semibold hover:text-white hover:bg-gray-800" id="form-button" type="submit">メモ追加</button>
        </form>

        <div class="mt-8">
            <h2>{{ $movie->name }}</h2>
            <h3>{{ $movie->memo }}</h3>
            {{ dd($movie) }}
        </div>


    </div>

    <div class="memo-container w-1/5 pl-4">
        <!-- コメント一覧 -->



        @foreach($movie_memos as $memo)
        <form class="com_container pb-4 border-b border-indigo-100" method="post" action="{{ route('movie.memo.update') }}">
            @csrf
            <img src="" alt="">
            <input type="hidden" name="memo_id" value="{{ $memo->id }}">

            <div>
                <div class="flex items-center justify-between">
                    <p class="italic hover:text-indigo-500">{{ '@' . $memo->user_name }}</p>
                    <span>{{ Carbon\Carbon::parse($memo->created_at)->format('Y/m/d') }}</span>
                </div>

                <p class="underline mt-2 text-indigo-400 flex items-center font-semibold">
                    <span class="time">{{ $memo->time }}</span> ~
                </p>

                <textarea name="memo" id="" cols="30" rows="3" class="w-full text-sm  py-4  indent-2 font-serif text-gray-600 overflow-x-hidden">{{ $memo->memo }}</textarea>

                <div class="container mt-2">
                    <a href="{{ route('movie.memo.delete',['memo_id' => $memo->id ]) }}"><span class="mr-2 material-symbols-outlined text-red-400">
                            delete
                        </span>
                    </a>

                    <button>
                        <span class="material-symbols-outlined text-green-400">
                            check_circle
                        </span>

                    </button>

                </div>

            </div>
        </form>

        @endforeach

    </div>
</div>




<script>
    const video_content = document.querySelector('.video_content');
    const video_id = video_content.classList[0];
    const movie_id = video_content.classList[1];


    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/player_api";
    const firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    let player;

    function onYouTubePlayerAPIReady() {
        player = new YT.Player('YTContent', {
            width: 800, //横幅
            height: 450, //縦幅
            videoId: video_id, //動画のID。URLの"?v="以降の記述
            playerVars: {
                autoplay: 1, //自動再生
                controls: 1, //コントローラー
                rel: 0, //再生した動画と同チャンネルから関連動画が選択。
                playsinline: 1 // iOS上のHTML5 プレーヤーでインライン再生
            }
        });
    }

    function getCurrentTime() {
        if (player && player.getCurrentTime) {
            const totalSeconds = Math.floor(player.getCurrentTime());
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds - hours * 3600) / 60);
            const seconds = totalSeconds - hours * 3600 - minutes * 60;
            const formattedTime = [hours, minutes, seconds].map(v => v.toString().padStart(2, '0')).join(':');

            return formattedTime;

        }
    }

    function create_memo(created_at, memo, time, user_name) {
        // メモを追加するコンテナを取得
        const memoContainer = document.querySelector('.memo-container');

        // 新しいメモのdivを作成
        const memoDiv = document.createElement('div');
        memoDiv.className = 'memo';

        // メモヘッダーのpタグを作成
        const memoHeader = document.createElement('p');
        memoHeader.className = 'memo-header';
        memoHeader.innerHTML = `${created_at} <span class="time">${time}</span> <span>${user_name}</span>`;

        // メモテキストのpタグを作成
        const memoText = document.createElement('p');
        memoText.className = 'memo-text';
        memoText.textContent = memo;

        // メモDivにヘッダーとテキストを追加
        memoDiv.appendChild(memoHeader);
        memoDiv.appendChild(memoText);

        // コンテナに新しいメモDivを追加
        memoContainer.appendChild(memoDiv);


    }

    function create_new_memo(created_at, memo_text, time, user_name, memo_id) {
        const memo_container = document.querySelector('.memo-container');
        console.log(memo);

        const form = document.createElement('form');
        form.className = 'com_container pb-4 border-b border-indigo-100';
        form.method = 'post';
        form.action = "/movie/memo/update";
        const csrfToken = document.createElement('input');
        csrfToken.type = "hidden";
        csrfToken.name = "_token";
        csrfToken.value = "{{ csrf_token() }}";


        const img = document.createElement('img');
        img.src = "";
        img.alt = "";

        const hiddenInput = document.createElement('input');
        hiddenInput.type = "hidden"
        hiddenInput.name = "memo_id";
        hiddenInput.value = memo_id;

        const div1 = document.createElement('div');
        const div2 = document.createElement('div');
        div2.className = "flex items-center justify-between";

        const userParagraph = document.createElement('p');
        userParagraph.className = "italic hover:text-indigo-500";
        userParagraph.textContent = "@" + user_name;

        const dataSpan = document.createElement('span');
        dataSpan.textContent = created_at;

        div2.appendChild(userParagraph);
        div2.appendChild(dataSpan);

        const timeParagraph = document.createElement('p');
        timeParagraph.className = 'underline mt-2 text-indigo-400 flex items-center font-semibold';
        const timeSpan = document.createElement('span');
        timeSpan.className = 'time';
        timeSpan.textContent = time + '~';
        timeParagraph.appendChild(timeSpan);

        const textarea = document.createElement('textarea');
        textarea.name = 'memo';
        textarea.cols = '30';
        textarea.rows = '3';
        textarea.className = 'w-full text-sm py-4 indent-2 font-serif text-gray-600 overflow-x-hidden';
        textarea.textContent = memo_text;

        const div3 = document.createElement('div');
        div3.className = 'container mt-2';

        const deleteLink = document.createElement('a');
        deleteLink.href = '/movie/memo/delete/' + memo_id;
        const deleteSpan = document.createElement('span');
        deleteSpan.className = 'mr-2 material-symbols-outlined text-red-400';
        deleteSpan.textContent = 'delete';
        deleteLink.appendChild(deleteSpan);

        const button = document.createElement('button');
        const buttonSpan = document.createElement('span');
        buttonSpan.className = 'material-symbols-outlined text-green-400';
        buttonSpan.textContent = 'check_circle';
        button.appendChild(buttonSpan);

        div3.appendChild(deleteLink);
        div3.appendChild(button);

        div1.appendChild(div2);
        div1.appendChild(timeParagraph);
        div1.appendChild(textarea);
        div1.appendChild(div3);

        form.appendChild(csrfToken);
        form.appendChild(img);
        form.appendChild(hiddenInput);
        form.appendChild(div1);

        memo_container.prepend(form);

    }

    document.addEventListener('DOMContentLoaded', (event) => {
        const form_button = document.querySelector('#form-button');
        const memo = document.querySelector('#memo');

        form_button.addEventListener('click', (e) => {
            e.preventDefault();
            const time = getCurrentTime();
            console.log(time);




            if (movie_id && memo.value) {


                $.ajax({
                    url: "/AddMemo",
                    type: "get",
                    data: {
                        movie_id: movie_id,
                        memo: memo.value,
                        video_time: time
                    },
                    success: function(response) {
                        console.log(response);

                        if (response.status === 'ok') {
                            memo.value = '';
                        }
                        const created_at = new Date(response.created_at).toLocaleString('ja-JP', {
                            timeZone: 'Asia/Tokyo'
                        }).replace(/\//g, '-').replace(',', '');
                        const memo_text = response.memo;
                        const time = response.time;
                        const user_name = response.user_name;
                        const memo_id = response.memo_id;

                        // console.log(created_at, memo, time, user_name);
                        create_new_memo(created_at, memo_text, time, user_name, memo_id);
                    }
                });
            }
        });
    });

    const times = document.querySelectorAll('.time');
    times.forEach((el) => {
        el.addEventListener('click', (e) => {

            // クリックイベントの処理をここに追加
            const time_text = e.target.textContent;
            const time_array = time_text.split(':').map(Number);
            const time_seconds = (time_array[0] * 3600) + (time_array[1] * 60) + time_array[2];
            player.seekTo(time_seconds);
        });
    });
</script>
@endsection