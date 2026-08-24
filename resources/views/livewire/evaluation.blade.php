<div>
    <aside id="top-bar-sidebar"
           class="fixed top-18 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0"
           aria-label="Sidebar">
        <div class="h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">
            <livewire:filtering wire:key="{{Str::random()}}" :filters=$filters :evaluation=$evaluation
            />
            <livewire:selected-solvers wire:key="{{Str::random()}}"
                                       :selected_solvers=$selected_solvers
                                       :evaluation=$evaluation
            />
        </div>
    </aside>
    <div class="p-4 sm:ml-64 mt-14 px-4 mx-auto max-w-8xl lg:px-4 pt-16">
        <div class="p-4 mb-4">
            <h3 class=" m-2 text-2xl font-bold text-heading">Ranking of solvers</h3>
            <h4 class="m-2 text-1xl font-bold text-heading">Number of selected instances: {{$nb_benchmarks}}</h4>
            <livewire:summary :filters=$filters :evaluation=$evaluation :selected_solvers=$selected_solvers
            />
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
                    <input wire:change="change_filtering" type="text"
                           class="block w-full max-w-96 ps-9 pe-3 py-2 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body"
                           placeholder="Instance name">
                </div>
            </div>


            </h4>

            <livewire:detailed :filters=$filters :evaluation=$evaluation :selected_solvers=$selected_solvers
            />

        </div>
    </div>
</div>
