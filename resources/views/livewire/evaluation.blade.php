<div>
    <livewire:nav-bar title="{{$title}}"
    />
    <div x-data="{ open: @entangle('display_sidebar') }">
        <aside id="top-bar-sidebar"
               x-show="open"
               x-transition:enter="transition-transform ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition-transform ease-in duration-300"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed top-15.5 left-0 z-40 w-64 h-full"
               aria-label="Sidebar">
            <div class="bg-gray-100 h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">
                <x-hugeicons-panel-left-open class="text-gray-400 absolute left-1 top-1" @click="open = false"
                                             wire:click="$set('display_sidebar', false)"/>
                <livewire:filtering wire:key="filtering" :filters="$filters" :evaluation="$evaluation"
                />

                <livewire:selected-solvers wire:key="solvers" :selected_solvers="$selected_solvers"
                                           :evaluation="$evaluation"
                />
            </div>
        </aside>

        <template x-if="!open">
            <div class="text-gray-400 fixed top-16 left-1">
                <x-hugeicons-panel-left-close @click="open = true" wire:click="$set('display_sidebar', true)"/>
            </div>
        </template>


        <div class="relative">
            <div wire:loading.class="opacity-30 pointer-events-none">
                <div :class="open ? 'ml-64' : 'ml-0'"
                     class="p-4  transition-all duration-300 ease-in-out mt-14 px-4 mx-auto  lg:px-4 pt-16">
                    @switch($view)
                        @case(1)
                            <livewire:table-view :filters="$filters" :selected_solvers="$selected_solvers"
                                                 :evaluation="$evaluation"
                            />
                            @break
                        @case(2)
                            <livewire:is :component="$radar_component" :filters="$filters"
                                         :selected_solvers="$selected_solvers"
                                         :evaluation="$evaluation"
                            />
                            @break
                        @case(3)
                            <livewire:is :component="$versus_component" :filters="$filters"
                                         :selected_solvers="$selected_solvers" :evaluation="$evaluation"
                            />
                            @break
                        @case(4)
                            <livewire:help :type="$evaluation->type"
                            />
                            @break
                    @endswitch
                </div>
            </div>
            <div wire:loading.class="flex absolute inset-0 items-center justify-center">
                <svg aria-hidden="true" class="inline w-8 h-8 text-neutral-tertiary animate-spin fill-warning"
                     viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"/>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"/>
                </svg>
            </div>
        </div>
    </div>

</div>
