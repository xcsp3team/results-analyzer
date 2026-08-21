<div>
    <div class="flex items-center  flex-col">
        <div>Filters</div>
        <div class="mt-3 inline-flex rounded-xs shadow-xs -space-x-px" role="group">
            <button type="button"
                    class="text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-3 focus:ring-neutral-tertiary-soft font-medium leading-5 rounded-sm text-sm px-3 py-2 focus:outline-none">
                Problems
            </button>
            <button type="button"
                    class="text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-3 focus:ring-neutral-tertiary-soft font-medium leading-5 rounded-e-sm text-sm px-3 py-2 focus:outline-none">
                Constraints
            </button>
        </div>

        <div class="mt-3 inline-flex rounded-base shadow-xs -space-x-px" role="group">
            <button type="button"
                    class="text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-3 focus:ring-neutral-tertiary-soft font-medium leading-5 rounded-sm text-sm px-3 py-2 focus:outline-none">
                SAT
            </button>
            <button type="button"
                    class="text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-3 focus:ring-neutral-tertiary-soft font-medium leading-5 rounded-e-sm text-sm px-3 py-2 focus:outline-none">
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
            <input type="text" wire:change='change("time_limit",$event.target.value)'
                   value={{$filters->time_limit}} class="bg-neutral-secondary-medium border border-default-medium
                   text-heading text-sm rounded-sm focus:ring-brand focus:border-brand block w-full px-3 py-2
                   shadow-xs"
            placeholder="Expression" required />
        </div>
    </div>

    <div class="border-t border-default pt-4 mt-4">
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
