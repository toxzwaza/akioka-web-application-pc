@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800">在庫管理システム操作履歴</h1>

<section class="text-gray-600 body-font">
    <div class="container px-5 pt-8 pb-24 mx-auto flex flex-wrap justify-center ">


        <div class="flex flex-wrap justify-center w-1/2">
            <div class="lg:w-3/5 md:w-1/2 md:pr-10 md:py-6">
                @foreach($operation_records as $record)
                <div class="flex relative pb-12">
                    <div class="h-full w-10 absolute inset-0 flex items-center justify-center">
                        <div class="h-full w-1 bg-gray-200 pointer-events-none"></div>
                    </div>

                    @if($record->operation_id === 2)
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-400 inline-flex items-center justify-center text-white relative z-10">
                        <span class="material-symbols-outlined">
                            remove
                        </span>
                    </div>

                    @elseif($record->operation_id === 3)
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-400 inline-flex items-center justify-center text-white relative z-10">
                        <span class="material-symbols-outlined">
                            add
                        </span>
                    </div>
                    @elseif($record->operation_id === 4)
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-400 inline-flex items-center justify-center text-white relative z-10">
                        <span class="material-symbols-outlined">
                            search
                        </span>
                    </div>
                    @elseif($record->operation_id === 5)
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-400 inline-flex items-center justify-center text-white relative z-10">
                        <span class="material-symbols-outlined">
                            calculate
                        </span>

                    </div>
                    @elseif($record->operation_id === 6)
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-300 inline-flex items-center justify-center text-white relative z-10">
                        <span class="material-symbols-outlined">
                            error
                        </span>
                    </div>
                    @endif

                    <div class="flex-grow pl-4">
                        <h2 class="font-medium title-font text-sm text-gray-900 mb-1 tracking-wider">{{ Carbon\Carbon::parse($record->created_at)->format('Y年m月d日 h時m分 ') }}</h2>
                        <p class="leading-relaxed">
                            <span class="font-semibold mr-2">{{ $record->user_name}}さん</span>
                            が<span class="font-semibold mx-1">{{ $record->stock_name}}</span>を<span class="font-semibold mx-1">{{ $record->quantity }}</span>個 <span class="font-semibold mx-1">{{ $record->operation_name }}</span>しました。
                        </p>
                    </div>
                </div>

                @endforeach


            </div>
            <!-- <img class="lg:w-3/5 md:w-1/2 object-cover object-center rounded-lg md:mt-0 mt-12" src="https://dummyimage.com/1200x500" alt="step"> -->
        </div>


        <div class="w-1/2">
            <!-- <h3>直近の取引実績</h3> -->
            <canvas id="myChart" width="300" height="300"></canvas>
        </div>
    </div>
</section>


<script>
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["{{ Carbon\Carbon::now()->subDays(5)->format('m/d') }}", "{{ Carbon\Carbon::now()->subDays(4)->format('m/d') }}", "{{ Carbon\Carbon::now()->subDays(3)->format('m/d') }}", "{{ Carbon\Carbon::now()->subDays(2)->format('m/d') }}", "{{ Carbon\Carbon::now()->subDays(1)->format('m/d') }}", "{{Carbon\Carbon::now()->format('m/d')}}"],
            datasets: [{
                label: '# of 取引数',
                data: {!! json_encode($operation_record_recent) !!},
                backgroundColor: [
                    'rgba(225, 225, 225, 0.2)',
                    'rgba(225, 225, 225, 0.8)',
                    'rgba(225, 225, 225, 0.2)',
                    'rgba(225, 225, 225, 0.8)',
                    'rgba(225, 225, 225, 0.2)',
                    'rgba(225, 225, 225, 0.8)'
                ],
                borderColor: [
                    'rgba(169,169,169,1)',
                    'rgba(169,169,169,1)',
                    'rgba(169,169,169,1)',
                    'rgba(169,169,169,1)',
                    'rgba(169,169,169,1)',
                    'rgba(169,169,169,1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
@endsection