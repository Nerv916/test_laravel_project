<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nama Barang / Tambah
            </h2>
            <a href="{{ route('barang.index') }}"
                class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('barang.store') }} " method="post">
                        @csrf
                        <label for="" class="text-lg font-medium">Nama Barang</label>
                        <div class="my-3">
                            <input value="{{ old('nama') }}" type="text" name="nama"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg" placeholder="Masukan Nama Barang">
                            @error('name')
                                <p class="text-red-400 font-medium ">{{ $message }}</p>
                            @enderror
                        </div>
                        <button class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
