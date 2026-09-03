<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset
    <div
        x-data="LivewireUIModal()"
        x-on:close.stop="setShowPropertyTo(false)"
        x-on:keydown.escape.window="closeModalOnEscape()"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true"
    >
        <!-- Modal overlay : z-index inférieur, isolé -->
        <div class="fixed inset-0 z-0 bg-gray-500/75 transition-opacity"></div>

        <!-- Contenu modal : z-index supérieur, opacité propre -->
        <div class="relative z-10 flex items-end justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            <div
                class="inline-block w-full align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-4xl">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                @forelse($components as $id => $component)
                                    <div x-show.immediate="activeComponent == '{{ $id }}'" x-ref="{{ $id }}"
                                         wire:key="{{ $id }}">
                                        @livewire($component['name'], $component['arguments'], key($id))
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
