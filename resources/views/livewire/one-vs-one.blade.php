<div>

    @if($solver_x != null)
        <div wire:ignore x-data="{
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.scatter, {
            chart: { type: 'scatter', height: '400px' },
            tooltip: { shared: false, intersect: true },
            markers: { size: 5 },
            xaxis: {
                tickAmount: 0,
               },
            legend: { show: true, position: 'bottom' },
            zoom: {
                enabled: true,
                type: 'xy',
            },
            series: @js(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $series)),
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
                <button class="button-blue rounded-sm" type="submit">Submit
                </button>
            </div>
        </form>
</div>
@endif


</div>
