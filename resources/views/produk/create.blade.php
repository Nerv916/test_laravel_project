<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Produk / Create
            </h2>
            <a href="{{ route('produk.index') }}"
                class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Form --}}
                    <form action="{{ route('produk.store') }}" method="POST">
                        @csrf

                        {{-- Nama Produk --}}
                        <label for="nama_barang" class="text-lg font-medium">Nama Produk</label>
                        <div class="my-3">
                            <select name="barang_id" id="barang_id"
                                class="border border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                required>
                                <option value="">Pilih Produk</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}"
                                        {{ old('barang') == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barang_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Merek --}}
                        <label for="merek" class="text-lg font-medium">Merek</label>
                        <div class="my-3">
                            <input value="{{ old('merek') }}" type="text" name="merek"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2" placeholder="Merek">
                            @error('merek')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <label for="kategori_id" class="text-lg font-medium">Kategori</label>
                        <div class="my-3">
                            <select name="kategori_id" id="kategori_id"
                                class="border border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Satuan --}}
                        <label for="satuan_id" class="text-lg font-medium">Satuan</label>
                        <div class="my-3">
                            <select name="satuan_id" id="satuan_id"
                                class="border border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                required>
                                <option value="">Pilih Satuan</option>
                                @foreach ($satuans as $satuan)
                                    <option value="{{ $satuan->id }}"
                                        {{ old('satuan_id') == $satuan->id ? 'selected' : '' }}>
                                        {{ $satuan->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Pabrikasi (Supplier) --}}
                        <label for="supplier_id" class="text-lg font-medium">Pabrikasi (Supplier)</label>
                        <div class="my-3">
                            <select name="supplier_id" id="supplier_id"
                                class="border border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                required>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIE --}}
                        <label for="nie" class="text-lg font-medium">NIE</label>
                        <div class="my-3">
                            <input value="{{ old('nie') }}" type="text" name="nie"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2" placeholder="NIE">
                            @error('nie')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                            class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
