<div>

    <div class="mt-4 mb-8">
        <h4 class="mb-12"><span class="h2">Informations about families</span>
        </h4>


    </div>
    <input type="search" wire:model.live.debounce.500ms="search" class=" dark:border-gray-400 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-sm block
                   w-96 px-3 py-2 mb-2" placeholder="Search for families/constraints"/>
    <livewire:table sticky={{true}} wire:key="information" :table=$data wire:key="information-{{ md5($search) }}"
                    :header=$header_information
                    :sort_field="0"
                    :sort_direction="'desc'"/>
</div>
