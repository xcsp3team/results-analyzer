<nav class="bg-blue-300 fixed w-full z-20 top-0 start-0 border-b border-default">
    <div class="ml-6 mr-6 flex flex-wrap items-center justify-between mx-auto p-4">
        <h1 class="text-xl flex items-center">
            @php
                /*<span wire:click="$dispatch('toggle_sidebar')" class="w-6 h-6 mr-2">
                    @if($display_sidebar)
                        @svg('heroicon-o-arrow-left-start-on-rectangle')
                    @else
                        @svg('heroicon-o-arrow-right-start-on-rectangle')
                    @endif
                </span>
                */
            @endphp
            <a href="{{route("home")}}" class="text-base font-medium text-xl">Result Analyzer</a>
        </h1>
        <div class="text-xl font-medium">{{$title}}</div>
        <div class="hidden w-full md:block md:w-auto" id="navbar-solid">
            <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-default rounded-base bg-neutral-secondary-soft md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-neutral-secondary-soft">

                <li>
                    <button id="theme-toggle" type="button"
                            class="text-blue dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </div>

</nav>
