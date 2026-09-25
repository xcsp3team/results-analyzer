<div>
    <livewire:nav-bar title={{$title}}
    />
    <div x-data="{ open: @entangle('display_sidebar') }">
        <aside id="top-bar-sidebar"
               x-show="open"
               x-transition:enter="transition-transform ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition-transform ease-in duration-300"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed top-15.5 left-0 z-40 w-64 h-full"
               aria-label="Sidebar">
            <div class="bg-gray-100 h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">
                <x-hugeicons-panel-left-open class="text-gray-400 absolute right-2" @click="open = false" wire:click="$set('display_sidebar', false)"/>
                <livewire:filtering wire:key="{{Str::random()}}" :filters=$filters :evaluation=$evaluation />
                <livewire:selected-solvers wire:key="{{Str::random()}}" :selected_solvers=$selected_solvers :evaluation=$evaluation />
            </div>
        </aside>
    </div>
    @if($display_sidebar == false)
        <div class="text-gray-400 fixed top-16 left-1"><x-hugeicons-panel-left-close wire:click="$set('display_sidebar', true)"/> </div>
    @endif

    <div class="p-4 {{$display_sidebar == false ? "": "ml-64"}} mt-14 px-4 mx-auto max-w-8xl lg:px-4 pt-16">
        @switch($view)
            @case(1)
                <livewire:table-view :filters=$filters :selected_solvers=$selected_solvers :evaluation=$evaluation
                />
                @break
            @case(2)
                <livewire:is :component=$radar_component :filters=$filters :selected_solvers=$selected_solvers
                             :evaluation=$evaluation
                />
                @break
            @case(3)
                <livewire:is :component=$versus_component :filters=$filters :selected_solvers=$selected_solvers
                             :evaluation=$evaluation
                />
                @break
            @case(4)
                <livewire:help :type="$evaluation->type"
                />
                @break
        @endswitch
    </div>
</div>
