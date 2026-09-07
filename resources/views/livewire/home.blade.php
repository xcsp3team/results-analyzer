<div class="h-screen">
    <nav class="bg-blue-300 fixed w-full z-20 top-0 start-0 border-b border-default">
        <div class="ml-6 mr-6 flex flex-wrap items-center justify-between mx-auto p-4">
            <h1 class="text-xl flex justify-around items-center">
                <a title="Home" href="{{route("home")}}" class="text-base font-medium text-xl">Result Analyzer</a>
            </h1>

            <div class="hidden w-full md:block md:w-auto" id="navbar-solid">
                <ul class="font-medium flex items-center flex-col p-4 md:p-0 mt-4 border md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 ">

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
    <div
        class="p-4  mt-14 px-4 mx-auto max-w-8xl lg:px-4 pt-16 flex justify-center flex-col items-center h-1/2">
        <h2 class="text-2xl mb-6 dark:text-gray-400">Select an evaluation</h2>
        <select wire:model="evaluation" wire:change="load"
                class="block w-1/3 px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium dark:border-gray-400 text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
            <option selected></option>
            @foreach($evaluations as $v)
                <option value="{{$v->id}}">{{$v->name}} {{$v->track}}</option>
            @endforeach
        </select>
    </div>
</div>
