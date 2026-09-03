<div>
    <livewire:nav-bar title={{$title}} :display_sidebar=$display_sidebar
    />
    <aside id=" top-bar-sidebar"
           wire:show="display_sidebar"
           class=" fixed top-18 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0"
           aria-label="Sidebar">
        <div class="bg-gray-100 h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">
            <livewire:filtering wire:key="{{Str::random()}}" :filters=$filters :evaluation=$evaluation
            />
            <livewire:selected-solvers wire:key="{{Str::random()}}"
                                       :selected_solvers=$selected_solvers
                                       :evaluation=$evaluation
            />
        </div>
    </aside>
    <div class="p-4 {{$display_sidebar == false ? "": "ml-64"}} mt-14 px-4 mx-auto max-w-8xl lg:px-4 pt-16">
        @switch($view)
            @case(1)
                <livewire:table-view :filters=$filters :selected_solvers=$selected_solvers :evaluation=$evaluation
                />
                @break
            @case(2)
                <livewire:is :component=$radar_component :filters=$filters :selected_solvers=$selected_solvers :evaluation=$evaluation
                />
                @break
            @case(3)
                <livewire:one-vs-one :filters=$filters :selected_solvers=$selected_solvers :evaluation=$evaluation
                />
                @break
            @case(4)
                <livewire:help
                />
                @break
        @endswitch
    </div>
</div>
