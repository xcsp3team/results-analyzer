@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between px-2 py-3">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-default">
                    {{ __('pagination.previous') }}
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev"
                        class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-800">
                    {{ __('pagination.previous') }}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"
                        class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-800">
                    {{ __('pagination.next') }}
                </button>
            @else
                <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-default">
                    {{ __('pagination.next') }}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">


            <div>
                <span class="inline-flex -space-x-px rounded-md shadow-sm isolate">
                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-gray-300 dark:text-gray-600 border rounded-l-md">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd"
                                                                                               d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                                                                               clip-rule="evenodd"/></svg>
                        </span>
                    @else
                        <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev"
                                class="relative inline-flex items-center px-2 py-2 text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-l-md hover:bg-gray-50 dark:hover:bg-gray-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd"
                                                                                               d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                                                                               clip-rule="evenodd"/></svg>
                        </button>
                    @endif

                    {{-- Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span
                                class="relative inline-flex items-center px-4 py-2 text-sm text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                          class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold text-white  bg-blue-500 border border-primary-600">{{ $page }}</span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                                            class="relative inline-flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"
                                class="relative inline-flex items-center px-2 py-2 text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-r-md hover:bg-gray-50 dark:hover:bg-gray-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd"
                                                                                               d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                                                               clip-rule="evenodd"/></svg>
                        </button>
                    @else
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-gray-300 dark:text-gray-600 border rounded-r-md">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd"
                                                                                               d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                                                               clip-rule="evenodd"/></svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
