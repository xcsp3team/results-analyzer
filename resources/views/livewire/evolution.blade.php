<div>
    <h3 class="h3">{{$benchmark->name}}</h3>

    <div wire:ignore x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.evolution, {
            chart: { type: 'line' },
            tooltip: { shared: true, intersect: false },
            markers: { size: 1 },
            legend: { show: true, position: 'top' },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series)),
        });
        this.chart.render();
    }
}">
        <div x-ref="evolution" style="min-width: 800px; min-height: 600px;"></div>
    </div>
</div>
