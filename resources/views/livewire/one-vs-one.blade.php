<div>
    <h2 class="text-3xl font-bold mb-8">One to one comparison</h2>
    @if($solver_x != null)
        <h3 class="h3">Scatter plot</h3>
        <div wire:ignore x-data="{
            chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.scatter, {
            chart: { type: 'scatter', height: '400px' },
           tooltip: {
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
            if (w.config.series[seriesIndex].name === 'Diagonale') {
                return '';
            }
            var point = w.config.series[seriesIndex].data[dataPointIndex];
            var x = point[0];
            var y = point[1];
            var name = point[2];

            return '<div style=\'padding:8px 12px;\'>' +
                '<div style=\'font-weight:600;margin-bottom:4px; background-color:#ddd;border-bottom: 1px solid black; \'>' + name + '</div>' +
        '<div>{{$name_x}}: ' + x + '</div>' +
        '<div>{{$name_y}}: ' + y + '</div>' +
        '</div>';
            }
        },
        markers: { size: 5   },
        grid: {
            xaxis: {
                lines: {
                    show: true
                }
            }
        },
        xaxis: {
            title: {text: '{{$name_x}}'},
            logarithmic: true,
            type: 'numeric',
            min: 1,

            max : {{$filters->time_limit}}
        },
        yaxis: {
            title: {text: '{{$name_y}}'},
            type: 'numeric',
            logarithmic: true,
            min: 1,

            max : {{$filters->time_limit}}
        },
        legend: { show: true, position: 'top' },
        zoom: {
            enabled: true,
            type: 'xy',
        },
        series: [...@js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $scatter)),
        {
            name: 'Diagonale',
            type: 'line',
            data: [[0, 0], [{{$filters->time_limit}}, {{$filters->time_limit}}]]
        }
        ]
    });
this.chart.render();

Livewire.on('scatter-updated', (event) => {
this.chart.updateOptions({
series: event.series,
xaxis: { numeric: event.xaxis }
});
});
}
}">
            <div x-ref="scatter"></div>

            <h3 class="h3">Comparison per constraints</h3>
            <div x-ref="constraints"></div>

            <h3 class="h3">Comparison per families</h3>

            <div wire:ignore x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.families, {
            chart: { type: 'bar', height: '400px', stacked: true },
            plotOptions: {
                bar: {
                    horizontal: false,
                },
            },
            stroke: { width: 1 },
            tooltip: { shared: true, intersect: false },
            markers: { size: 2 },
            legend: { show: true, position: 'bottom' },
            series: @js(array_values(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data, "group" => $s->group], $per_families))),
            xaxis: {
                categories: @js($selected_families)
            }
        });
        this.chart.render();

        Livewire.on('per_families-updated', (event) => {
            this.chart.updateOptions({
                series: event.series,
                xaxis: { categories: event.selected_families }
            });
        });
    }
}">

                <div x-ref="families"></div>
            </div>
            @else
                <form wire:submit="set_solvers">
                    <div class="mx-auto w-1/2 flex justify-around items-center mb-4">
                        <select wire:model="solver_x"
                                class="block w-72 px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                            <option></option>
                            @foreach($evaluation->solvers2 as $solver)
                                <option value="{{$solver->id}}">{{$solver->name ." " . $solver->version}}</option>
                            @endforeach
                        </select>
                        <div>Versus</div>
                        <select wire:model="solver_y"
                                class="block w-72 px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                            <option></option>
                            @foreach($evaluation->solvers2 as $solver)
                                <option value="{{$solver->id}}">{{$solver->name ." " . $solver->version}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-center">
                        <button class="button-blue rounded-sm" type="submit">Compare
                        </button>
                    </div>
                </form>
        </div>
    @endif


</div>
