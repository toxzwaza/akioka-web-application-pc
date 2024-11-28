@extends('layouts.main')
@section('content')

<div>
    <section class="text-gray-600 body-font">


        <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-start justify-center">
            <div class="lg:max-w-lg lg:w-4/5 md:w-3/5 mb-10 md:mb-0 px-8 py-24">
                <h2 class="text-xl text-center mb-8 font-bold">{{ $stock->name }}</h2>

                <img class="my-4 w-full object-cover object-center rounded" alt="hero" src="{{ $stock->img_path && strpos($stock->img_path, 'https://') !== false ? $stock->img_path : 'http://monokanri-app.local/' . $stock->img_path }}">


            </div>






            <div class="bg-white py-6 sm:py-8 lg:py-12">
                <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
                    <!-- text - start -->
                    <div class="mb-10 md:mb-16">
                        <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">発注登録</h2>

                        <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">
                            在庫編集を行います。
                        </p>
                    </div>
                    <!-- text - end -->

                    <!-- form - start -->
                    <form action="{{ route('stock.order.store') }}" method="post" class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="stock_id" value="{{ $stock->id }}">


                        <div>
                            <label for="quantity" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">数量</label>
                            <input name="quantity" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" value="" />
                        </div>

                        






                        <div class="flex items-center justify-between sm:col-span-2">
                            <button class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">登録</button>

                            <span class="text-sm text-gray-500">*必須</span>
                        </div>


                    </form>
                    <!-- form - end -->
                </div>
            </div>

        </div>
        <hr>

    </section>


</div>

<script>
    const img_config_change = document.querySelector('#img_config_change');
    const img_path_input = document.querySelector('#img_path_input');
    if (img_config_change) {

        img_config_change.addEventListener('click', (el) => {
            img_path_input.classList.toggle('pointer-events-none');
            img_path_input.classList.toggle('bg-gray-200');
        });
    }

    const edit_check = document.querySelectorAll('.edit_check');
    const stock_storage_id = document.querySelector('#stock_storage_id');
    if (edit_check) {

        edit_check.forEach((el) => {
            el.addEventListener('click', (e) => {
                console.log(e.target.value);
                stock_storage_id.value = e.target.value;
            });
        });
    }

    const update_button = document.querySelector('#update_button');
    const update_form = document.querySelector('#update_form');
    if (update_button) {

        update_button.addEventListener('click', (e) => {
            e.preventDefault();
            const storage_address_id = document.querySelector('#storage_address_id');
            const selected_list = document.querySelector('.selected');
            if (!storage_address_id.value) {
                storage_address_id.value = selected_list.value;

            }
            update_form.submit();

        });
    }

    const not_jan_code = document.querySelector('#not_jan_code');
    const jan_code = document.querySelector('#jan_code');
    if (not_jan_code) {

        not_jan_code.addEventListener('click', (el) => {
            el.preventDefault();

            jan_code.value = "None";
            jan_code.classList.toggle('bg-gray-50');
            jan_code.classList.toggle('bg-gray-200');
            jan_code.classList.toggle('pointer-events-none');
        });
    }



    // フォームのエンターキーを無効化
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
    const search_supplier_button = document.querySelector('#search_supplier_button');
    const search_supplier_keyword = document.querySelector('#search_supplier_keyword');
    const add_supplier_tbody = document.querySelector('#add_supplier_tbody');
    const stock_id = document.querySelector('#stock_id');

    search_supplier_button.addEventListener('click', (e) => {
        e.preventDefault();
        console.log(search_supplier_keyword.value);

        if (search_supplier_keyword.value == '') {
            alert('検索キーワードを入力してください。');
        }

        axios.get('/api/getSuppliers', {
                params: {
                    keyword: search_supplier_keyword.value
                }
            })
            .then(function(response) {
                add_supplier_tbody.textContent = "";
                const suppliers = response.data;
                suppliers.forEach((supplier) => {
                    console.log(supplier);

                    const newRow = createRow(supplier.name, supplier.tel, supplier.fax, stock_id.value, supplier.id);
                    add_supplier_tbody.appendChild(newRow);
                });
            })
            .catch(function(error) {
                console.log(error);
            })


    });


    function createRow(name, tel, fax, stock_id, supplier_id) {
        const newRow = document.createElement('tr');

        const nameCell = document.createElement('td');
        nameCell.className = 'px-4 py-8 text-sm text-gray-900';
        nameCell.textContent = name;
        newRow.appendChild(nameCell);

        const telCell = document.createElement('td');
        telCell.className = 'px-4 py-8 text-sm text-gray-900';
        telCell.textContent = tel;
        newRow.appendChild(telCell);

        const faxCell = document.createElement('td');
        faxCell.className = 'px-4 py-8 text-sm text-gray-900';
        faxCell.textContent = fax;
        newRow.appendChild(faxCell);

        const actionCell = document.createElement('td');
        actionCell.className = 'w-16 px-4 py-8 text-sm text-gray-900';
        const actionAtag = document.createElement('a');
        actionAtag.href = `/stock/stocks/add_supplier?stock_id=${stock_id}&supplier_id=${supplier_id}`;
        const actionSpan = document.createElement('span');
        actionSpan.className = 'material-symbols-outlined';
        actionSpan.textContent = 'add_circle';

        actionAtag.appendChild(actionSpan);
        actionCell.appendChild(actionAtag);
        newRow.appendChild(actionCell);

        return newRow;
    }

    // 物品得意先情報削除ボタン
    const stock_supplier_delete = document.querySelector('#stock_supplier_delete');
    stock_supplier_delete.addEventListener('click', (e) => {
        e.preventDefault();

        if(confirm('物品得意先情報を削除してもよろしいですか？')){
            window.location.href = stock_supplier_delete.getAttribute('href');
        }
    });
</script>



@endsection