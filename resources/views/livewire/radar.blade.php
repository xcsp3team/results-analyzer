<div>
    <h2 class="h2">Radar per families</h2>

    <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-y-2">
        @foreach($families as $name => $family)
            <div class="">
                <h3 class="text-center text-lg font-medium dark:text-gray-400 relative top-10">{{$name}}</h3>
                <div wire:ignore x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.{{$name}}, {
            chart: { type: 'radar', height: 400, size: 500,
            toolbar: {
                show: false,
            }},
            stroke: { width: 1 },
            tooltip: {intersect: false},
            markers: { size: 2 },
            series: @js([['name' => $family->name, 'data' => $family->data]]),
            xaxis: { categories: @js($solvers_name) },
        });
        this.chart.render();
        Livewire.on('radar-{{$name}}-updated', (event) => {
            this.chart.updateOptions({
                series: event.series,
                xaxis: { categories: event.xaxis }
            });
        });
    }
}">
                    <div x-ref="{{$name}}" style="width:100%;"></div>
                </div>
            </div>
        @endforeach


    </div>
</div>
