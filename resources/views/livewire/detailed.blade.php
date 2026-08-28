<div>
    <div class="mt-8 mb-4">
        <h4><span class="mt-2 mb-2 text-2xl font-bold text-heading">Detailed results</span> <span>(solving times in seconds per solver)</span>
        </h4>

        <label for="input-group-1" class="sr-only">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 inset-s-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                     width="24"
                     height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                          d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.500ms="instance_name"
                   class="block w-full max-w-96 ps-9 pe-3 py-2 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body"
                   placeholder="Search for instances">
            <button
                type="button"
                wire:click="$set('instance_name', null)"
                x-show="$wire.instance_name"
                class="absolute inset-y-0 inset-s-90 flex items-center pe-3 text-body hover:text-heading"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <livewire:table wire:key="{{Str::random()}}" :table="$detailed_results" :header="$header_results"
                    :sort_field="0" :sort_direction="'desc'"/>
</div>
