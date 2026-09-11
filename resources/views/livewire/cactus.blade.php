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
        const seriesData = @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series));
        const maxX = 150;
        this.chart = new ApexCharts(this.$refs.cactus, {
            chart: { type: 'line', height: '400px', toolbar: { show: false }, zoom: { enabled: false, allowMouseWheelZoom: false } },
            stroke: { width: 1 },
            tooltip: { shared: true, intersect: false },
            markers: { size: 2 },
            legend: { show: true, position: 'bottom' },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series)),
            xaxis: { type: 'numeric', tickAmount: Math.ceil({{$maxX}} / 10) * 10 / 10, min:0, max: Math.ceil({{$maxX}} / 10) * 10 }
        });
        this.chart.render();

        Livewire.on('cactus-updated', (event) => {
            const newMax = Math.max(...event.series.flatMap(s => s.data.map(p => p.x ?? p[0])));
            this.chart.updateOptions({
                series: event.series,
                xaxis: { type: 'numeric', tickAmount: Math.ceil(event.maxX / 10) * 10 / 10, min:0, max: Math.ceil(event.maxX / 10) * 10 },
                ...this.themeOptions()

            });
        });
        window.addEventListener('theme-changed', () => {
            this.chart.updateOptions(this.themeOptions());
        });
    }
}">
    <div x-ref="cactus"></div>
</div>
