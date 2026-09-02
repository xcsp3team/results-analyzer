<div>
    <div class="grid grid-cols-2 gap-6">
    <div wire:ignore x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.cactus_optimum, {
            chart: { type: 'line', height: '400px' },
            stroke: { width: 1 },
            tooltip: { shared: true, intersect: false },
            markers: { size: 2 },
            legend: { show: true, position: 'bottom' },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series_optimum)),
        });
        this.chart.render();

        Livewire.on('cactus-opt-updated', (event) => {
            this.chart.updateOptions({
                series: event.series_optimum,
            });
        });
    }
}">
        <h3 class="h3">Proof-oriented vision</h3>
        <div x-ref="cactus_optimum"></div>
    </div>
        <div wire:ignore x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.cactus_search, {
            chart: { type: 'line', height: '400px' },
            stroke: { width: 1 },
            tooltip: { shared: true, intersect: false },
            markers: { size: 2 },
            legend: { show: true, position: 'bottom' },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series_search)),
        });
        this.chart.render();

        Livewire.on('cactus-search-updated', (event) => {
            this.chart.updateOptions({
                series: event.series_search,
            });
        });
    }
}">
            <h3 class="h3">Search-oriented vision</h3>
            <div x-ref="cactus_search"></div>
        </div>
    </div>
</div>
