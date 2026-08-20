<div>
    <div class="p-4">

        <h3 class=" m-2 text-2xl font-bold text-heading">Ranking of solvers</h3>
        <h4 class="m-2 text-1xl font-bold text-heading">Number of selected instances:</h4>

        <livewire:table wire:key="{{Str::random()}}" :table="$summary" :header="$headerSummary" :sort_field="1" :sort_direction="'asc'"/>

        <label for="input-group-1" class="sr-only">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </div>
            <input wire:model="filter_name" wire:change="change_filtering" type="text" id="input-group-1" class="block w-full max-w-96 ps-9 pe-3 py-2 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="Instance name">
        </div>
    </div>

    <livewire:table wire:key="{{Str::random()}}" :table="$detailedResults" :header="$headerResults" :sort_field="0" :sort_direction="'desc'"/>
</div>
