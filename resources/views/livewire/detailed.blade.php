<div>
    <div class="mt-8 mb-4">
        <h4 class="mb-4"><span class="h2">Detailed results</span>
        </h4>

        
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
                    <option {{$perPage==25 ? "selected" :""}}>25</option>
                    <option {{$perPage==50 ? "selected" :""}}>50</option>
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
            @click.outside="open=false"
            :style="`position: fixed; left: ${x}px; top: ${y}px; transform: translate(-50%, -100%) translateY(-8px);`"
            class="z-50 rounded-sm bg-white shadow-lg ring-1 ring-black/5 text-sm"
        >

            <div x-if="content">
                <div class="px-3 py-2 bg-neutral-tertiary border-b border-default rounded-t-sm dark:bg-gray-400">
                    <h3 class="font-medium text-heading " x-text="content.title"></h3>
                </div>
                <div class="px-3 py-2 dark:bg-gray-300">
                    <div class="text-gray-600" x-html="content.details"></div>
                </div>
            </div>
        </div>
    </div>
</div>
