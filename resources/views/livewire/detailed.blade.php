<div>
    <livewire:table wire:key="{{Str::random()}}" :table="$detailed_results" :header="$header_results"
                    :sort_field="0" :sort_direction="'desc'"/>
</div>
