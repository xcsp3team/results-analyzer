<div>
    <div class="mt-8 mb-4">
        <h4 class="mb-4"><span class="h2">Detailed results</span> <span class="font-medium mb-6 dark:text-gray-400">(solving times in seconds per solver)</span>
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
                   class=" w-full max-w-96  dark:border-gray-400 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-sm block
                   pl-8 py-2"
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
    <div x-data="{ open: false, x: 0, y: 0, content: null }" @keydown.escape.window="open = false" class="relative">
        @include("livewire.table", ['table' => $paginator_details->items(), 'header' => $header_results, 'sticky' => true, 'sort_field' => $sort_field, 'sort_direction' => $sort_direction])
        <br/><br/>
        <br/><br/>
        <div class="flex justify-center items-center">
            <div class="inline-flex rounded-md shadow-xs mr-2">
                <label for="perPage"
                       class="m-0 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-s-xs">Par
                    page</label>
                <select id="perPage"
                        class="m-0 pl-2 pr-6 py-2 text-sm  text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700   rounded-e-xs border   text-sm block p-2.5 mr-3"
                        wire:model="perPage"
                        wire:change="changePerPage">
                    <option {{$perPage==10 ? "selected" :""}}>10</option>
                    <option {{$perPage==25 ? "selected" :""}}>25</option>
                    <option {{$perPage==100 ? "selected" :""}}>100</option>
                </select>
            </div>
            {{ $paginator_details->links('livewire.paginator') }}
        </div>
        <br/><br/><br/>

        <div
            x-show="open"
            x-cloak
            x-transition
            @click.outside="open = false"
            :style="`position: fixed; left: ${x}px; top: ${y}px; transform: translate(-50%, -100%) translateY(-8px);`"
            class="z-50 rounded-lg bg-white shadow-lg ring-1 ring-black/5 text-sm"
        >

            <div>
                <div class="px-3 py-2 bg-neutral-tertiary border-b border-default rounded-t-base dark:bg-gray-400">
                    <h3 class="font-medium text-heading " x-text="content.title"></h3>
                </div>
                <div class="px-3 py-2 dark:bg-gray-300">
                    <div class="text-gray-600" x-html="content.details"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
