<div class="border-t border-default dark:border-t-gray-400 pt-4 mt-4">
    <div class="mb-3 flex flex-col items-center">
        <h4 class="dark:text-gray-400">Solvers</h4>

        <div class="mt-3 inline-flex rounded-xs shadow-xs -space-x-px" role="group">
            <button type="button"
                    wire:click="$dispatch('all-solvers')"
                    {{count($selected_solvers) == count($evaluation->solvers)  ? "disabled" :""}}
                    class="{{count($selected_solvers) != count($evaluation->solvers)  ? "button-blue" : "button-gray"}} rounded-s-xs">
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
    @foreach($evaluation->solvers as $solver)
        <div
            wire:key="{{Str::random()}}"
            wire:click="toggle_selected_solver({{$solver->id}})"
            class="{{ isset($selected_solvers[$solver->id]) ? "bg-neutral-tertiary hover:bg-white text-blue-500" : "text-gray-400" }}  px-2 py-1.5   hover:bg-neutral-tertiary  group">
            {{$solver->name . " " . $solver->version}}
        </div>
    @endforeach
</div>
