<div>
    <div class="grid grid-cols-2 gap-6">
        <div wire:ignore
             x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.cactus_optimum, {
            chart: { type: 'line', height: '400px', toolbar: { show: false }, zoom: { enabled: false, allowMouseWheelZoom: false } },
            stroke: { width: 1 },
            tooltip: { shared: true, intersect: false },
            markers: { size: 2 },
            legend: { show: true, position: 'bottom' },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series_optimum)),
            xaxis: { type: 'numeric', tickAmount: Math.ceil({{$maxX_optimum}} / 10) * 10 / 10, min:0, max: Math.ceil({{$maxX_optimum}} / 10) * 10 }

        });
        this.chart.render();

        Livewire.on('cactus-opt-updated', (event) => {
            this.chart.updateOptions({
                series: event.series_optimum,
                xaxis: { type: 'numeric', tickAmount: Math.ceil(event.maxX_optimum / 10) * 10 / 10, min:0, max: Math.ceil(event.maxX_optimum / 10) * 10 }

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
            chart: { type: 'line', height: '400px', toolbar: { show: false }, zoom: { enabled: false, allowMouseWheelZoom: false } },
            stroke: { width: 1 },
            tooltip: { shared: true, intersect: false },
            markers: { size: 2 },
            legend: { show: true, position: 'bottom' },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series_search)),
            xaxis: { type: 'numeric', tickAmount: Math.ceil({{$maxX_search}} / 10) * 10 / 10, min:0, max: Math.ceil({{$maxX_search}} / 10) * 10 }

        });
        this.chart.render();

        Livewire.on('cactus-search-updated', (event) => {
            this.chart.updateOptions({
                series: event.series_search,
                xaxis: { type: 'numeric', tickAmount: Math.ceil(event.maxX_search / 10) * 10 / 10, min:0, max: Math.ceil(event.maxX_search / 10) * 10 }

            });
        });
    }
}">
            <h3 class="h3">Search-oriented vision</h3>
            <div x-ref="cactus_search"></div>
        </div>
    </div>
</div>
