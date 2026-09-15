<div class="p-4 mb-4 max-h-screen">
    <h3 class=" text-2xl font-medium mb-2 dark:text-gray-400">Ranking of solvers</h3>
    <h4 class=" text-1xl font-bold mb-6 dark:text-gray-400">Number of selected instances: {{$nb_benchmarks}}</h4>


    <livewire:is :component=$summary_component :filters=$filters :evaluation=$evaluation
                 :selected_solvers=$selected_solvers wire:key={{Str::random()}}
    />

    <livewire:is :component=$cactus_component :filters=$filters :selected_solvers=$selected_solvers
                 :evaluation=$evaluation
    />

    <livewire:is :component=$details_component :filters=$filters :evaluation=$evaluation
                 :selected_solvers=$selected_solvers
    />


</div>
