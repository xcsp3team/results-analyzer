<div wire:ignore x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.cactus, {
            chart: { type: 'line', height: '400px' },
            stroke: { width: 1 },
            tooltip: { shared: true, intersect: false },
            markers: { size: 2 },
            legend: { show: true, position: 'bottom' },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series)),
            xaxis: { numeric: @js($xaxis) }
        });
        this.chart.render();

        Livewire.on('cactus-updated', (event) => {
            this.chart.updateOptions({
                series: event.series,
                xaxis: { numeric: event.xaxis }
            });
        });
    }
}">
    <div x-ref="cactus"></div>
</div>
