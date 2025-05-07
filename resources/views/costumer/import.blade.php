<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Customer / Import
            </h2>
            <a href="{{ route('costumer.index') }}"
                class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('costumer.storeImport') }} " method="POST" enctype="multipart/form-data">
                        @csrf

                        <label for="" class="text-lg font-medium">Import Excel</label>
                        <div class="my-3">
                            <input type="file" name="file"
                                class="border border-gray-300 rounded-md px-4 py-2 w-full" accept=".xlsx,.xls">
                        </div>

                        <button class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
