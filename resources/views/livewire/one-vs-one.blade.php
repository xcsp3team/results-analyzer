<div class="relative">
    <h2 class="h2">One to one comparison</h2>
    @if($solver_x != null)
        <button wire:click="close" class="absolute right-5 top-2 text-gray-400">@svg('hugeicons-cancel-circle')</button>
        <h3 class="h3">Scatter plot</h3>
        <div style="width: 600px; margin: 0 auto;">
            <div
                wire:key="scatter-chart"
                wire:ignore x-data="{
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
            if (this.chart) this.chart.destroy();

            const isDecade = (val) => {
                if (!val || val <= 0) return false;
                const log = Math.log10(val);
                return Math.abs(log - Math.round(log)) < 0.01;
            };

            this.chart = new ApexCharts(this.$refs.scatter, {
                chart: {
                    type: 'scatter',
                    height: 600,
                    width: 600,
                    toolbar: { show: false }, zoom: { enabled: false, allowMouseWheelZoom: false }
                },
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
                markers: { size: 5 },
                grid: { xaxis: { lines: { show: true } } },
                xaxis: {
                    title: { text: '{{$name_x}}' },
                    logarithmic: true,
                    type: 'numeric',
                    min: 1,
                    max: {{ $filters->time_limit }},
                    tickAmount: Math.round(Math.log10({{ $filters->time_limit }})),
                    labels: {
                        formatter: (val) => isDecade(val) ? Math.round(val).toLocaleString() : ''
                    }
                },
                yaxis: {
                    title: { text: '{{$name_y}}' },
                    type: 'numeric',
                    logarithmic: true,
                    min: 1,
                    max: {{ $filters->time_limit }},
                    tickAmount: Math.round(Math.log10({{ $filters->time_limit }})),
                    labels: {
                        formatter: (val) => isDecade(val) ? Math.round(val).toLocaleString() : ''
                    }
                },
                legend: { show: true, position: 'top' },
                zoom: { enabled: true, type: 'xy' },
                series: [
                    ...@js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $scatter)),
                    { name: 'Diagonale', type: 'line', data: [[0, 0], [{{ $filters->time_limit }}, {{ $filters->time_limit }}]] }
                ]
            });
            this.chart.render();

            Livewire.on('scatter-updated', (event) => {
                this.chart.updateOptions({ series: event.series, xaxis: { numeric: event.xaxis }, ...this.themeOptions()
 });
            });
             window.addEventListener('theme-changed', () => {
            this.chart.updateOptions(this.themeOptions());
        });
        }
    }" x-init="init()">
                <div x-ref="scatter"></div>
            </div>
        </div>

        <h3 class="h3">Comparison per constraints</h3>

        <div wire:key="families-chart"
             wire:ignore x-data="{
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
                if (this.chart) this.chart.destroy();
                this.chart = new ApexCharts(this.$refs.constraints, {
                    chart: { type: 'bar', height: '400px', stacked: true,  toolbar: { show: false }},
                    plotOptions: { bar: { horizontal: false } },
                    stroke: { width: 1 },
                    tooltip: { shared: true, intersect: false },
                    markers: { size: 2 },
                    legend: { show: true, position: 'bottom' },
                    series: @js(array_values(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data, 'group' => $s->group], $per_constraints))),
                    xaxis: { categories: @js($selected_constraints) }
                });
                this.chart.render();

        Livewire.on('per_constraints-updated', (event) => {
            this.chart.updateOptions({
                series: event.series,
                xaxis: { categories: event.selected_constraints },
                ...this.themeOptions()

            }, false, true); // redrawPaths = true pour forcer le recalcul des groupes
        });
        window.addEventListener('theme-changed', () => {
            this.chart.updateOptions(this.themeOptions());
        });

    }
}">
            <div x-ref="constraints"></div>
        </div>


        <h3 class="h3">Comparison per families</h3>

        <div wire:key="families-chart"
             wire:ignore x-data="{
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
                if (this.chart) this.chart.destroy();
                this.chart = new ApexCharts(this.$refs.families, {
                    chart: { type: 'bar', height: '400px', stacked: true, toolbar: { show: false },  },
                    plotOptions: { bar: { horizontal: false } },
                    stroke: { width: 1 },
                    tooltip: { shared: true, intersect: false },
                    markers: { size: 2 },
                    legend: { show: true, position: 'bottom' },
                    series: @js(array_values(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data, 'group' => $s->group], $per_families))),
                    xaxis: { categories: @js($selected_families) }
                });
                this.chart.render();

                Livewire.on('per_families-updated', (event) => {
                    this.chart.updateOptions({ series: [], xaxis: { categories: event.selected_families },  ...this.themeOptions() }, false, false);
                    this.chart.updateOptions({ series: event.series }, false, false);
                });
                  window.addEventListener('theme-changed', () => {
            this.chart.updateOptions(this.themeOptions());
        });
            }
        }">
            <div x-ref="families"></div>
        </div>
    @else
        <form wire:submit="set_solvers">
            <div class="mx-auto w-1/2 flex justify-around items-center mb-4">
                <select wire:model="solver_x"
                        class="dark:border-gray-400 block w-72 px-3 py-1.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-xs focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                    <option></option>
                    @foreach($evaluation->solvers as $solver)
                        <option value="{{$solver->id}}">{{$solver->name ." " . $solver->version}}</option>
                    @endforeach
                </select>
                <div class="dark:text-gray-400 mx-2 ">Versus</div>
                <select wire:model="solver_y"
                        class="dark:border-gray-400 block w-72 px-3 py-1.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-xs focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                    <option></option>
                    @foreach($evaluation->solvers as $solver)
                        <option value="{{$solver->id}}">{{$solver->name ." " . $solver->version}}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-center">
                <button class="button-blue rounded-sm" type="submit">Compare</button>
            </div>
        </form>
    @endif
</div>
