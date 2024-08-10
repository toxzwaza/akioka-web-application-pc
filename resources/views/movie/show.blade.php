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
        <div id="copy_link" class="w-42 flex justify-start mb-2 text-red-500">
            <span class="material-symbols-outlined mr-2">
                link
            </span>
        </div>


        <!-- Youtube動画画面 -->

        <div id="YTContent" class="{{ $movie->file_path }} {{ $movie->id }} video_content"></div>



        <form action="" class="relative mt-16">
            <div class="flex justify-center ">
                <input class="block w-4/5 sm:w-2/3 bg-gray-200 py-2 px-3 text-gray-700 border border-gray-200 rounded focus:outline-none focus:bg-white mr-4" id="memo" type="text" name="" placeholder="@ {{ session('user.name') }}">
                <button class="border border-gray-400 rounded-md px-2 py-1 text-sm font-semibold hover:text-white hover:bg-gray-800" id="form-button" type="submit">メモ追加</button>
            </div>
        </form>

        <hr class="my-16">



        <form class="mt-4 mb-8 px-8" method="post" action="{{ route('movie.update') }}">

            @csrf
            <input type="hidden" name="movie_id" value="{{ $movie->id }}">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">
                <input class="w-full" type="text" name="name" id="" value="{{ $movie->name }}">
            </h2>

            <div class="flex">
                <a class="w-24 text-center block my-4 border bg-white hover:bg-gray-400 hover:text-white {{ $movie->del_flg == 0 ? 'text-green-400' : 'text-red-400' }} font-bold py-2 px-4 rounded text-sm" href="{{ route('movie.change_status', ['movie_id' => $movie->id ]) }}">{{ $movie->del_flg == 0 ? '表示中' : '非表示中' }}</a>



                <button id="youtube_delete" class="ml-4 w-24 text-center block my-4 border bg-white hover:bg-gray-400 hover:text-white  font-bold py-2 px-4 rounded text-sm text-red-400">動画削除</button>

            </div>




            <p class="my-4 flex items-start">
                <span class="w-12 mr-1 text-sm text-gray-400">メモ：</span>
                <textarea class="w-full rounded-md border px-4 py-2 text-gray-600 text-xs" name="memo" id="" cols="30">{{ $movie->memo }}</textarea>
            </p>
            <p class="my-4">
                <span class="w-24 mr-1 text-sm text-gray-400">カテゴリ:</span>
                <span class="bg-{{ $movie->category_color }}-100 text-{{ $movie->category_color }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-white dark:text-{{ $movie->category_color }}-400 border border-{{ $movie->category_color }}-400">{{ $movie->movie_tag_category_name }}</span>
            </p>
            <p class="my-4">
                <span class="w-24 mr-1 text-sm text-gray-400"> タグ：</span>
                <select name="movie_tag_id" id="" class="bg-{{ $movie->tag_color }}-400 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-{{ $movie->tag_color }}-400 dark:text-white">

                    @foreach($movie_tags as $tag)
                    <option {{ $movie->tag_id == $tag->id ? 'selected' : ''}} value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>

            </p>
            <p class="my-4">
                <span class="w-24 mr-1 text-sm text-gray-400">録画日:</span>
                <input type="date" name="created_at" id="" value="{{ Carbon\Carbon::parse($movie->created_at)->format('Y-m-d') }}">
            </p>
            <p class="my-4">
                <span class="w-24 mr-1 text-sm text-gray-400 ">YoutubeID：</span>
                <input id="youtube_id" class="w-3/4 border rounded py-2 px-4 text-gray-500" type="text" name="file_path" id="" value="{{ $movie->file_path }}">
            </p>

            <button class="mr-4 mt-2 border bg-white hover:bg-blue-400 hover:text-white text-blue-700 font-bold py-2 px-4 rounded text-sm">更新</button>




        </form>




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

    const youtube_delete_button = document.querySelector('#youtube_delete');
    youtube_delete_button.addEventListener('click', (e) => {
        e.preventDefault();
        console.log(e);
        youtube_delete_button.textContent = "削除中...";
        youtube_delete_button.classList.toggle('text-red-400');
        youtube_delete_button.classList.toggle('text-indigo-500');
        youtube_delete_button.classList.add('pointer-events-none');
        const youtube_id = document.querySelector('#youtube_id').value;


        axios.get('http://192.168.0.142:5000/movie/youtube_delete?youtube_id=' + youtube_id)
            .then(function(response) {
                // console.log(response.data);
                if (response.data.status == "ok") {
                    youtube_delete_button.textContent = "削除済";
                    youtube_delete_button.classList.remove('bg-white');
                    youtube_delete_button.classList.add('bg-gray-200');

                    // 画面更新
                    const reload = confirm('削除が完了しました。画面を更新しますか？')
                    if (reload) {
                        location.reload();
                    }
                }
            })
            .catch(function(error) {
                console.log(error);
            });
    });

    const copy_link = document.querySelector('#copy_link');

    copy_link.addEventListener('click', () => {
        const copy_text = window.location.href;
        navigator.clipboard.writeText(copy_text)
            .then(() => {
                copy_link.classList.add('opacity-40');
                copy_link.textContent = 'Copied.';
            })
            .catch(err => {
                console.error('テキストのコピーに失敗しました: ', err);
            });
    });
</script>
@endsection