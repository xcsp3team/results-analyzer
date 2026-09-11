<div>
    <h3 class="h3">{{$benchmark->name}}</h3>

    <div wire:ignore x-data="{
    chart: null,
    isDark() {
            return localStorage.theme === 'dark';
        },
         themeOptions() {
            return {
                theme: { mode: this.isDark() ? 'dark' : 'light' },
                chart: { foreColor: this.isDark() ? '#e5e7eb' : '#1f2937' },
                grid: { borderColor: this.isDark() ? '#374151' : '#e5e7eb' },
                tooltip: { theme: this.isDark() ? 'dark' : 'light' },
            };
        },
    init() {
        this.chart = new ApexCharts(this.$refs.evolution, {
            chart: { type: 'line',toolbar: { show: false },  },
            tooltip: { shared: true, intersect: false },
            stroke: {
    width: 1,
    curve: 'smooth',
  },
            markers: { size: 1 },
            legend: { show: true, position: 'top' },
            xaxis: {tickAmount: 5},
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series)),
        });
        this.chart.render();

    }
}">
        <div x-ref="evolution" style="min-width: 800px; min-height: 600px;"></div>
    </div>
</div>
