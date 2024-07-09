@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">従業員一覧</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
            <!-- <h1 class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900">Pricing</h1> -->
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Banh mi cornhole echo 従業員一覧を表示します。<br>
                こちらから、編集・削除ページへ遷移することが可能です。
            </p>
        </div>
        <div class="lg:w-4/5 w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                    <tr>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ID</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">氏名</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">部署</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">グループ</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">役職</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 text-center">人数</th>
                        <!-- <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th> -->
                    </tr>
                </thead>

                <tbody>
                    @foreach($high_users as $index => $high_user)
                    <tr class="">
                        <td class="px-4 py-3"><a href="">{{ $high_user->user_id }}</a></td>
                        <td class="px-4 py-3">{{ $high_user->user_name }}</td>
                        <td class="px-4 py-3">{{ $high_user->group_name }}</td>
                        <td class="px-4 py-3">{{ $high_user->process_name ?? 'なし' }}</td>
                        <td class="px-4 py-3">{{ $high_user->position_name }}</td>


                        @if($index === 0)
                        <td class="px-4 py-3 text-center" rowspan="{{ count($high_users) }}">{{ count($high_users) }}</td>
                        @endif
                    </tr>
                    @endforeach

                    @foreach($product_users as $index => $p_user)
                    @if($index >= 0 && $index < $product_count['電気炉'])
                    <tr class="even bg-slate-200">
                        <td class="px-4 py-3" {{  $p_user->dispatch_flg == 1 ? 'h' : ''}}"><a href="">{{ $p_user->user_id }}</a></td>
                        <td class="px-4 py-3">{{ $p_user->user_name }}</td>
                        <td class="px-4 py-3">{{ $p_user->group_name }}</td>
                        <td class="px-4 py-3">{{ $p_user->process_name }}</td>
                        <td class="px-4 py-3">{{ $p_user->position_name }}</td>
                        @if($index === 0)
                        <td class="peo_cal text-center" rowspan="{{ $product_count['電気炉'] }}">{{ $product_count['電気炉'] }}</td>
                        @endif
                        </tr>
                        @elseif($index >= $product_count['電気炉'] && $index < $product_count['電気炉'] + $product_count['生型造型']) <tr>
                            <td class="px-4 py-3" {{  $p_user->dispatch_flg == 1 ? 'h' : ''}}"><a href="">{{ $p_user->user_id }}</a></td>
                            <td class="px-4 py-3">{{ $p_user->user_name }}</td>
                            <td class="px-4 py-3">{{ $p_user->group_name }}</td>
                            <td class="px-4 py-3">{{ $p_user->process_name }}</td>
                            <td class="px-4 py-3">{{ $p_user->position_name }}</td>
                            @if($index === $product_count['電気炉'])
                            <td class="peo_cal text-centerbg-gray-200 text-center" rowspan="{{ $product_count['生型造型'] }}">{{ $product_count['生型造型'] }}</td>
                            @endif
                            </tr>
                            @elseif($index >= $product_count['電気炉'] + $product_count['生型造型'] && $index < $product_count['電気炉'] + $product_count['生型造型'] + $product_count['フラン']) <tr class="even bg-slate-200">
                                <td class="px-4 py-3" {{  $p_user->dispatch_flg == 1 ? 'h' : ''}}"><a href="">{{ $p_user->user_id }}</a></td>
                                <td class="px-4 py-3">{{ $p_user->user_name }}</td>
                                <td class="px-4 py-3">{{ $p_user->group_name }}</td>
                                <td class="px-4 py-3">{{ $p_user->process_name }}</td>
                                <td class="px-4 py-3">{{ $p_user->position_name }}</td>
                                @if($index === $product_count['電気炉'] + $product_count['生型造型'])
                                <td class="peo_cal text-center" rowspan="{{ $product_count['フラン'] }}">{{ $product_count['フラン'] }}</td>
                                @endif
                                </tr>
                                @elseif($index >= $product_count['電気炉'] + $product_count['生型造型'] + $product_count['中子'] && $index < $product_count['電気炉'] + $product_count['生型造型'] + $product_count['フラン'] + $product_count['中子']) <tr>
                                    <td class="px-4 py-3 {{  $p_user->dispatch_flg == 1 ? 'h' : ''}}"><a href="">{{ $p_user->user_id }}</a></td>
                                    <td class="px-4 py-3">{{ $p_user->user_name }}</td>
                                    <td class="px-4 py-3">{{ $p_user->group_name }}</td>
                                    <td class="px-4 py-3">{{ $p_user->process_name }}</td>
                                    <td class="px-4 py-3">{{ $p_user->position_name }}</td>
                                    @if($index === $product_count['電気炉'] + $product_count['生型造型'] + $product_count['フラン'])
                                    <td class="peo_cal text-center" rowspan="{{ $product_count['中子'] }}">{{ $product_count['中子'] }}</td>
                                    @endif
                                    </tr>
                                    @elseif($index >= $product_count['電気炉'] + $product_count['生型造型'] + $product_count['中子'] + $product_count['フラン'] && $index < $product_count['電気炉'] + $product_count['生型造型'] + $product_count['フラン'] + $product_count['中子'] + $product_count['仕上げ']) <tr class="even bg-slate-200">
                                        <td class="px-4 py-3 {{ $p_user->dispatch_flg == 1 ? 'h' : ''}}"><a href="">{{ $p_user->user_id }}</a></td>
                                        <td class="px-4 py-3">{{ $p_user->user_name }}</td>
                                        <td class="px-4 py-3">{{ $p_user->group_name }}</td>
                                        <td class="px-4 py-3">{{ $p_user->process_name }}</td>
                                        <td class="px-4 py-3">{{ $p_user->position_name }}</td>
                                        @if($index === $product_count['電気炉'] + $product_count['生型造型'] + $product_count['フラン'] + $product_count['中子'])
                                        <td class="peo_cal text-center" rowspan="{{ $product_count['仕上げ'] }}">{{ $product_count['仕上げ'] }}</td>
                                        @endif
                                        </tr>

                                        @elseif($index >= $product_count['電気炉'] + $product_count['生型造型'] + $product_count['中子'] + $product_count['フラン'] + $product_count['仕上げ'] && $index < $product_count['電気炉'] + $product_count['生型造型'] + $product_count['フラン'] + $product_count['中子'] + $product_count['仕上げ'] + $product_count['出荷検査']) <tr>
                                            <td class="px-3 py-4 {{  $p_user->dispatch_flg == 1 ? 'h' : ''}}"><a href="">{{ $p_user->user_id }}</a></td>
                                            <td class="px-4 py-3">{{ $p_user->user_name }}</td>
                                            <td class="px-4 py-3">{{ $p_user->group_name }}</td>
                                            <td class="px-4 py-3">{{ $p_user->process_name }}</td>
                                            <td class="px-4 py-3">{{ $p_user->position_name }}</td>
                                            @if($index === $product_count['電気炉'] + $product_count['生型造型'] + $product_count['フラン'] + $product_count['中子'] + $product_count['仕上げ'])
                                            <td class="peo_cal text-center" rowspan="{{ $product_count['出荷検査'] }}">{{ $product_count['出荷検査'] }}</td>
                                            @endif
                                            </tr>

                                            @endif
                                            @endforeach

                                            @foreach($office_users as $index => $o_user)
                                            @if($index >= 0 && $index < $office_count['業務部']) <tr class="even bg-slate-200">
                                                <td class="px-4 py-3"><a href="">{{ $o_user->user_id }}</a></td>
                                                <td class="px-4 py-3">{{ $o_user->user_name }}</td>
                                                <td class="px-4 py-3">{{ $o_user->group_name }}</td>
                                                <td class="px-4 py-3">{{ $o_user->process_name ?? 'なし'}}</td>
                                                <td class="px-4 py-3">{{ $o_user->position_name }}</td>

                                                @if($index === 0)
                                                <td class="peo_cal text-center" rowspan="{{ $office_count['業務部'] }}">{{ $office_count['業務部'] }}</td>
                                                @endif
                                                </tr>
                                                @elseif($index >= $office_count['業務部'] && $index < $office_count['業務部'] + $office_count['総務部']) <tr>
                                                    <td class="px-4 py-3"><a href="">{{ $o_user->user_id }}</a></td>
                                                    <td class="px-4 py-3">{{ $o_user->user_name }}</td>
                                                    <td class="px-4 py-3">{{ $o_user->group_name }}</td>
                                                    <td class="px-4 py-3">{{ $o_user->process_name ?? 'なし'}}</td>
                                                    <td class="px-4 py-3">{{ $o_user->position_name }}</td>

                                                    @if($index === $office_count['業務部'])
                                                    <td class="peo_cal text-center" rowspan="{{ $office_count['総務部'] }}">{{ $office_count['総務部'] }}</td>
                                                    @endif
                                                    </tr>
                                                    @endif
                                                    @endforeach

                                                    @foreach($tec_users as $index => $t_user)

                                                    @if($index >= 0 && $index < $tec_count['品質保証部(鋳造技術課)']) <tr class="even bg-slate-200">
                                                        <td class="px-4 py-3"><a href="">{{ $t_user->user_id }}</a></td>
                                                        <td class="px-4 py-3">{{ $t_user->user_name }}</td>
                                                        <td class="px-4 py-3">{{ $t_user->group_name }}</td>
                                                        <td class="px-4 py-3">{{ $t_user->process_name ?? 'なし'}}</td>
                                                        <td class="px-4 py-3">{{ $t_user->position_name }}</td>

                                                        @if($index === 0)
                                                        <td class="peo_cal text-center" rowspan="{{ $tec_count['品質保証部(鋳造技術課)'] }}">{{ $tec_count['品質保証部(鋳造技術課)'] }}</td>
                                                        @endif
                                                        </tr>

                                                        @elseif($index >= $tec_count['品質保証部(鋳造技術課)'] && $index < $tec_count['品質保証部(鋳造技術課)'] + $tec_count['品質保証部(品質保証課)']) <tr>
                                                            <td class="px-4 py-3"><a href="">{{ $t_user->user_id }}</a></td>
                                                            <td class="px-4 py-3">{{ $t_user->user_name }}</td>
                                                            <td class="px-4 py-3">{{ $t_user->group_name }}</td>
                                                            <td class="px-4 py-3">{{ $t_user->process_name ?? 'なし'}}</td>
                                                            <td class="px-4 py-3">{{ $t_user->position_name }}</td>

                                                            @if($index === $tec_count['品質保証部(鋳造技術課)'])
                                                            <td class="peo_cal text-center" rowspan="{{ $tec_count['品質保証部(品質保証課)'] }}">{{ $tec_count['品質保証部(品質保証課)'] }}</td>
                                                            @endif
                                                            </tr>
                                                            @elseif($index >= $tec_count['品質保証部(鋳造技術課)'] + $tec_count['品質保証部(品質保証課)'] && $index < $tec_count['品質保証部(鋳造技術課)'] + $tec_count['品質保証部(品質保証課)'] + $tec_count['製造部(設備保全・TPM課)']) <tr class="even bg-slate-200">
                                                                <td class="px-4 py-3"><a href="">{{ $high_user->user_id }}</a></td>
                                                                <td class="px-4 py-3">{{ $t_user->user_name }}</td>
                                                                <td class="px-4 py-3">{{ $t_user->group_name }}</td>
                                                                <td class="px-4 py-3">{{ $t_user->process_name ?? 'なし'}}</td>
                                                                <td class="px-4 py-3">{{ $t_user->position_name }}</td>

                                                                @if($index === $tec_count['品質保証部(鋳造技術課)'] + $tec_count['品質保証部(品質保証課)'])
                                                                <td class="peo_cal text-center" rowspan="{{ $tec_count['製造部(設備保全・TPM課)'] }}">{{ $tec_count['製造部(設備保全・TPM課)'] }}</td>
                                                                @endif
                                                                </tr>

                                                                @endif


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



@endsection