<div>
    <h4>Filtering constraints</h4>
    <div>
        <form wire:submit="save">

            <div class="flex items-center mb-4">
                <input id="default-radio-1" type="radio" wire:model="forbidden" value="0"
                       class="w-4 h-4 text-neutral-primary border-default-medium bg-neutral-secondary-medium rounded-full checked:border-brand focus:ring-2 focus:outline-none focus:ring-brand-subtle border border-default appearance-none">
                <label for="default-radio-1" class="select-none ms-2 text-heading">
                    Forcing the presence of these constraints
                </label>
            </div>
            <div class="flex items-center">
                <input checked id="default-radio-2" type="radio" wire:model="forbidden" value="1"
                       class="w-4 h-4 text-neutral-primary border-default-medium bg-neutral-secondary-medium rounded-full checked:border-brand focus:ring-2 focus:outline-none focus:ring-brand-subtle border border-default appearance-none">
                <label for="default-radio-2" class="select-none ms-2  text-heading">
                    Forbidding the presence of these constraints
                </label>
            </div>

            <div class="grid grid-cols-4 gap-2 mb-4">

                @foreach($all_constraints as $constraint)
                    <div class="flex items-center m-4">
                        <input type="checkbox"
                               id="{{$constraint}}"
                               wire:model.live="selected_constraints"
                               value="{{$constraint}}"
                               class="checkbox">
                        <label for="{{$constraint}}" class="select-none ms-2 text-heading">
                            {{ $constraint }}
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
