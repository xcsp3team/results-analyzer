<nav class="bg-blue-300 fixed w-full z-20 top-0 start-0 border-b border-default">
    <div class="ml-6 mr-6 flex flex-wrap items-center justify-between mx-auto p-4">
        <h1 class="text-xl flex justify-around items-center">
            <a title="Home" href="{{route("home")}}" class="text-base font-medium text-xl">Result Analyzer</a>
        </h1>
        <div class="text-xl font-medium">{{$title}}</div>
        <div class="hidden w-full md:block md:w-auto" id="navbar-solid">
            <ul class="font-medium flex items-center flex-col p-4 md:p-0 mt-4 border md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 ">
                <li title="Table view" class="hover:text-gray-500" x-on:click="$dispatch('view', {view: 1})">
                    @svg('hugeicons-table')
                </li>
                <li title="One versus One view" class="hover:text-gray-500" x-on:click="$dispatch('view', {view: 3})">
                    @svg('hugeicons-versus')
                </li>
                <li title="Families radar view" class="hover:text-gray-500" x-on:click="$dispatch('view', {view: 2})">
                    @svg('hugeicons-chart-radar')
                </li>
                <li title="Help me!" class="hover:text-gray-500" x-on:click="$dispatch('view', {view: 4})">
                    @svg('hugeicons-help-circle')
                </li>
                <button type="button" x-data="{ theme: Theme.get() }"
                        @click="theme = theme === 'light' ? 'dark' : 'light'; Theme.set(theme);">
                    <li x-show="theme == 'light'" class="hover:text-gray-500">
                        @svg('hugeicons-moon-02')
                    </li>
                    <li x-show="theme == 'dark'" class="hover:text-gray-500">
                        @svg('hugeicons-sun-03')
                    </li>
                </button>
            </ul>
        </div>
    </div>

</nav>
