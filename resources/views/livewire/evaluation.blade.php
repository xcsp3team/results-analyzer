<div>
    <livewire:nav-bar title={{$title}} :display_sidebar=$display_sidebar
    />
    <aside id=" top-bar-sidebar
    "
           class=" {{$display_sidebar == false ? "hidden": ""}} fixed top-18 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0"
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
        <div class="p-4 mb-4">
            <h3 class=" m-2 text-2xl font-bold text-heading">Ranking of solvers</h3>
            <h4 class="m-2 text-1xl font-bold text-heading">Number of selected instances: {{$nb_benchmarks}}</h4>
            <livewire:summary :filters=$filters :evaluation=$evaluation :selected_solvers=$selected_solvers
            />

            <livewire:cactus :filters=$filters :selected_solvers=$selected_solvers :evaluation=$evaluation
            />

            <livewire:detailed :filters=$filters :evaluation=$evaluation :selected_solvers=$selected_solvers
            />

        </div>
    </div>
</div>
