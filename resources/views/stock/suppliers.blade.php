@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">取引先一覧</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-16">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">取引先一覧を表示します。<br>
                こちらから、編集・削除ページへ遷移することが可能です。
            </p>

            <div class="mt-6 text-gray-600">
                <form class="w-1/3 flex items-center relative" action="">
                    <input name="last-name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" placeholder="キーワード検索" />
                    <span class="absolute right-2 ml-2 material-symbols-outlined">search</span>
                </form>
            </div>


        </div>
        <div class="mb-4">
            {{ $suppliers->links() }}
        </div>
        <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>


                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">ID</th>
                        <th class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl">社名</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">電話番号</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">FAX</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">郵便番号</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">住所</th>

                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">適確事業者番号</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">メモ</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"></th>


                    </tr>
                </thead>

                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr class="border-b border-gray-200 my-4 hover:bg-slate-200 transition ">

                        <td class="px-8 py-8 text-lg text-gray-900">{{ $supplier->id }}</td>
                        <td class="w-1/5 px-4 py-8 text-lg text-gray-900">{{ $supplier->name }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $supplier->tel }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $supplier->fax }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $supplier->p_code }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $supplier->address }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $supplier->invoice_registration_number }}</td>
                        <td class="px-4 py-8 text-lg text-gray-900">{{ $supplier->memo }}</td>
                        <td class="px-4 py-8 text-lg text-gray-400">
                            <a href="{{ route('stock.suppliers.edit', ['supplier_id' => $supplier->id ]) }}">
                                <span class="material-symbols-outlined">edit_square</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach



                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $suppliers->links() }}
        </div>


    </div>
</section>



@endsection