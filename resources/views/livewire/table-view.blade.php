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
