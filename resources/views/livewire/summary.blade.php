<div class="mb-4">
    <livewire:table wire:key="{{Str::random()}}" :table=$summary :header=$header_summary :sort_field="1"
                    :sort_direction="'asc'"/>
</div>
