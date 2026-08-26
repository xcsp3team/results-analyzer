<div>
    <div id="cactus"></div>

    <script>
        var options = {
            chart: {
                type: 'line',
                height: '400px'
            },
            stroke: {
                width: 1
            },
            tooltip: {
                shared: true,
                intersect: false,
            },
            markers: {
                size: 2,
            },
            legend: {
                show: true,
                position: 'bottom'
            },
            series: [
                    @foreach($series as $s)
                {
                    name: '{{$s->name}}',
                    data: {{json_encode($s->data)}}
                },
                @endforeach
            ],
            xaxis: {
                numeric: {{ json_encode($xaxis) }}
            }
        }
        var chart = new ApexCharts(document.querySelector('#cactus'), options)
        chart.render()
    </script>
</div>
