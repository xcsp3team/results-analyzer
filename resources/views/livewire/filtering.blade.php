<div>
    <div class="flex items-center  flex-col">
        <div>Filters</div>
        <div class="mt-3 inline-flex rounded-xs shadow-xs -space-x-px" role="group">
            <button type="button"
                    wire:click="$dispatch('openModal',
                    {
                    component: 'problems',
                    arguments: { all_families: {{json_encode($all_families)}}, selected_families: {{json_encode($filters->families)}} }
                    })"
                    class="{{count($filters->families) < count($all_families) ? "button-blue" : "button-gray"}} rounded-s-xs">
                Problems
            </button>
            <button type="button"
                    class="text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-3 focus:ring-neutral-tertiary-soft font-medium leading-5 rounded-e-sm text-sm px-3 py-2 focus:outline-none">
                Constraints
            </button>
        </div>

        <div class="mt-3 inline-flex rounded-base shadow-xs -space-x-px" role="group">
            <button
                wire:click="$dispatch('filters_change', { field: 'status', value: 'SAT'})"
                type="button"
                class="{{$filters->status == "SAT"  ? "button-blue" : "button-gray"}} rounded-s-xs">
                SAT
            </button>
            <button type="button"
                    wire:click="$dispatch('filters_change', { field: 'status', value: 'UNSAT'})"
                    class="{{$filters->status == "UNSAT"  ? "button-blue" : "button-gray"}} rounded-e-xs">
                UNSAT
            </button>
        </div>
        <div class="mt-3">
            <label class="text-xs  px-1">Expression</label>
            <input type="text"
                   class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-sm focus:ring-brand focus:border-brand block w-full px-3 py-1 shadow-xs placeholder:text-body"
                   placeholder="Expression" required/>
        </div>

        <div class="mt-3">
            <label class="text-xs  px-1 ">Time limit</label>
            <input type="text"
                   wire:change="$dispatch('filters_change', { field: 'time_limit', value: $event.target.value })"
                   value="{{$filters->time_limit}}"
                   class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-sm block
                   w-full px-3 py-2"
                   placeholder="Expression" required/>
        </div>


        <button
            wire:click="$dispatch('initialize_filters')"
            type="button"
            class="mt-3 button-blue rounded-xs">
            Reset filters
        </button>
    </div>

    <div class="border-t border-default pt-4 mt-4">
        <div class="mb-3 flex flex-col items-center">
            <h4>Solvers</h4>

            <div class="mt-3 inline-flex rounded-xs shadow-xs -space-x-px" role="group">
                <button type="button"
                        wire:click="$dispatch('all-solvers')"
                        {{count($selected_solvers) == count($evaluation->solvers2)  ? "disabled" :""}}
                        class="{{count($selected_solvers) != count($evaluation->solvers2)  ? "button-blue" : "button-gray"}} rounded-s-xs">
                    All
                </button>
                <button
                    wire:click="$dispatch('none-solvers')"
                    type="button"
                    {{count($selected_solvers) == 0  ? "disabled" : ""}}
                    class="{{count($selected_solvers) > 0  ? "button-blue" : "button-gray"}} rounded-e-xs">
                    None
                </button>
            </div>
        </div>
        @foreach($evaluation->solvers2 as $solver)
            <div
                wire:key="Str::random()"
                wire:click="toggle_selected_solver({{$solver->id}})"
                class="{{ isset($selected_solvers[$solver->id]) ? "bg-neutral-tertiary" : "" }}  px-2 py-1.5 text-body  hover:bg-neutral-tertiary  group">
                {{$solver->name . " " . $solver->version}}
            </div>
        @endforeach
    </div>
</div>
