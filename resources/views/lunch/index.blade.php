@extends('layouts.main')
@section('content')

<h1 class="text-center text-xl font-bold text-gray-800 mb-16">注文個数履歴</h1>

<p class="text-red-400 mb-4 text-right">弁当注文数 / おかずのみ注文数</p>
<div id="ec" >

</div>
<style>
    #ec{
        max-width: 60vw;
        margin: 2rem auto;
    }
</style>

<script>
    let ec = new EventCalendar(document.getElementById('ec'), {
        view: 'dayGridMonth',
        headerToolbar: {
            start: 'prev next',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek resourceTimeGridWeek,resourceTimelineWeek'
        },
        views: {
            timeGridWeek: {
                pointer: true
            },
            resourceTimeGridWeek: {
                pointer: true
            },
            resourceTimelineWeek: {
                pointer: true,
                slotMinTime: "09:00",
                slotMaxTime: "21:00",
                slotWidth: 80,
                resources: [{
                        id: 1,
                        title: "Resource A"
                    },
                    {
                        id: 2,
                        title: "Resource B"
                    },
                    {
                        id: 3,
                        title: "Resource C"
                    },
                    {
                        id: 4,
                        title: "Resource D"
                    },
                    {
                        id: 5,
                        title: "Resource E"
                    },
                    {
                        id: 6,
                        title: "Resource F"
                    },
                    {
                        id: 7,
                        title: "Resource G"
                    },
                    {
                        id: 8,
                        title: "Resource H"
                    },
                    {
                        id: 9,
                        title: "Resource I"
                    },
                    {
                        id: 10,
                        title: "Resource J"
                    },
                    {
                        id: 11,
                        title: "Resource K"
                    },
                    {
                        id: 12,
                        title: "Resource L"
                    },
                    {
                        id: 13,
                        title: "Resource M"
                    },
                    {
                        id: 14,
                        title: "Resource N"
                    },
                    {
                        id: 15,
                        title: "Resource O"
                    },
                ],
            },
        },
        datesSet: function(info){
            console.log(info.start);
            console.log(info.end);
        }

    });


    // 当月のイベントを書き換え
    axios.get('/getMonthOrders')
        .then(function(response) {
            console.log(response.data);
            const data = response.data;
            data.forEach(order => {


                ec.addEvent({
                    start: order.created_date,
                    end: order.created_date,
                    title: String(order.lunch_count) + ' / ' + String(order.side_dish_count),
                    textColor: "#000000",
                    color: "#dce1f1"
                })
            });
        })
        .catch(function(error) {
            console.log(error);
        });


    // ec.addEvent({
    //     start: "2024-08-05",
    //     end: "2024-08-05",
    //     title: "イベント1",
    //     textColor: "#000000",
    //     color: "#FFFFFF"
    // });
    const start = ec.view.currentStart;
    const finish = ec.view.currentEnd;
    console.log(start,finish);

</script>


@endsection