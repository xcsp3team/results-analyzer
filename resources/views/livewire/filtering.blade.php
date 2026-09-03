<div class="mb-4">
    <div class="flex items-center  flex-col">
        <div class="dark:text-gray-400">Filters</div>
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
                    wire:click="$dispatch('openModal',
                    {
                    component: 'constraints',
                    arguments: { all_constraints: {{json_encode($all_constraints)}}, selected_constraints: {{json_encode($filters->constraints)}}, forbidden: {{$filters->are_forbidden}} }
                    })"
                    class="{{count($filters->constraints) > 0 ? "button-blue" : "button-gray"}} rounded-e-xs">
                Constraints
            </button>
        </div>

        @if($filters->category == "sat")
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
        @endif

        @if($filters->category == "cop")
            <div class="mt-3 inline-flex rounded-base shadow-xs -space-x-px" role="group">
                <button
                    wire:click="$dispatch('filters_change', { field: 'status', value: 'CLOSED'})"
                    type="button"
                    class="{{$filters->status == "CLOSED"  ? "button-blue" : "button-gray"}} rounded-s-xs">
                    Closed
                </button>
                <button type="button"
                        wire:click="$dispatch('filters_change', { field: 'status', value: 'OPEN'})"
                        class="{{$filters->status == "OPEN"  ? "button-blue" : "button-gray"}} rounded-e-xs">
                    Open
                </button>
            </div>

            <div class="mt-3 inline-flex rounded-base shadow-xs -space-x-px" role="group">
                <button
                    wire:click="$dispatch('filters_change', { field: 'type', value: 'MINIMIZE'})"
                    type="button"
                    class="{{$filters->type == "MINIMIZE"  ? "button-blue" : "button-gray"}} rounded-s-xs">
                    Min
                </button>
                <button type="button"
                        wire:click="$dispatch('filters_change', { field: 'type', value: 'MAXIMIZE'})"
                        class="{{$filters->type == "MAXIMIZE"  ? "button-blue" : "button-gray"}} rounded-e-xs">
                    Max
                </button>
            </div>
        @endif

        <div class="mt-3">
            <label class="text-xs  px-1">Expression</label>
            <input type="text"
                   value="{{$filters->expression}}"
                   wire:change="$dispatch('filters_change', { field: 'expression', value: $event.target.value})"
                   class="dark:border-gray-400 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-sm focus:ring-brand focus:border-brand block w-full px-3 py-1 shadow-xs placeholder:text-body"
                   placeholder="d > 0 && v > 0 && c > 0" required/>
        </div>

        <div class="mt-3">
            <label class="text-xs  px-1 ">Time limit</label>
            <input type="text"
                   wire:change="$dispatch('filters_change', { field: 'time_limit', value: $event.target.value })"
                   value="{{$filters->time_limit}}"
                   class="dark:border-gray-400 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-sm block
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

</div>
