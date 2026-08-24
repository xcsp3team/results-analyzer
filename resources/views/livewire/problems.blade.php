<div>
    <h4>Filtering problems</h4>
    <div>
        <div class="flex flex-wrap">
            @foreach($all_families as $family)
                <div class="flex items-center mb-4">
                    <input type="checkbox" value=""
                           {{ in_array($family, $selected_families) ? "checked": "" }}
                           class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                    <label for="default-checkbox" class="select-none ms-2 text-sm font-medium text-heading">
                        {{ $family }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
