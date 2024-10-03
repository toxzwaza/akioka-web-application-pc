@extends('layouts.main')
@section('content')
<div class="mb-16">
    <ul class="flex border-b">
        <li class="-mb-px mr-1">
            <a class="bg-white inline-block  py-2 px-4 text-blue-700 font-semibold border-l border-t border-r rounded-t" href="#">ReMacsデータインポート</a>
        </li>

        <li class="-mb-px mr-1">
            <a class="bg-white inline-block  py-2 px-4 text-blue-600 font-semibold " href="#">データ入力</a>
        </li>


    </ul>
</div>
<div>
    <div class="w-full max-w-md mx-auto">
        <form action="{{ route('calc.product.store') }}" method="post" class="bg-white shadow-md rounded px-8 pt-12 pb-12 mb-4 w-full" enctype="multipart/form-data">
            @csrf

            <div class="mb-16 w-full ">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    棚卸し対象のエクセルファイル
                </label>
                <input name="file_upload" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="file" accept=".xlsx" placeholder="Username">
            </div>


            <div class="flex items-center justify-between w-full">
                <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" type="submit" value="送信">

            </div>
        </form>

    </div>

    <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
            <div class="flex flex-col text-center w-full mb-20">
                <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">アップロード済みファイル一覧</h1>
            </div>
            <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                <table class="table-auto w-full text-left whitespace-no-wrap">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ファイル名</th>
                            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">作成日</th>
                            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">最終更新日</th>

                            <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calcProductFiles as $calcProductFile)
                        <tr>
                            <td class="px-4 py-8">{{ $calcProductFile->file_path }}</td>
                            <td class="px-4 py-8">{{ Carbon\Carbon::parse($calcProductFile->created_at)->format('Y年m月d日') }}</td>
                            <td class="px-4 py-8">{{ Carbon\Carbon::parse($calcProductFile->updated_at)->format('Y年m月d日') }}</td>


                            <td class="w-10 text-center">
                                <a class="whitespace-nowrap text-sm bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded" href="{{ route('calc.product.start', ['id' => $calcProductFile->id ]) }}">削除</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </section>

</div>



@endsection