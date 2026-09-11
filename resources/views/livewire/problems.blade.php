<div>
    <h4 class="h3">Filtering problems</h4>
    <div>
        <form wire:submit="save">

            <div class="flex justify-center mb-4">
                <div class="mt-3 inline-flex rounded-xs shadow-xs -space-x-px" role="group">
                    <button type="button"
                            wire:click="select('all')"
                            class="{{count($selected_families) < count($all_families)   ? "button-blue" : "button-gray"}} rounded-s-xs">
                        All
                    </button>
                    <button type="button"
                            wire:click="select('none')"
                            class="{{count($selected_families) > 0  ? "button-blue" : "button-gray"}} rounded-e-xs">
                        None
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-4">

                @foreach($all_families as $family)
                    <div class="flex items-center m-4">
                        <input type="checkbox"
                               id="{{$family}}"
                               wire:model.live="selected_families"
                               value="{{$family}}"
                               class="checkbox">
                        <label for="{{$family}}" class="select-none ms-2 text-heading">
                            {{ $family }}
                        </label>
                    </div>
                @endforeach


            </div>
            <div class="flex justify-end">
                <button type="submit"
                        class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-xs text-sm px-4 py-1.5 focus:outline-none mr-2">
                    Save
                </button>
                <button type="button"
                        wire:click="$dispatch('closeModal')"
                        class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-xs text-sm px-4 py-1.5 focus:outline-none">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
