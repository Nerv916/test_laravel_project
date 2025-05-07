<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Produk / Edit
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
                    <form action="{{ route('produk.update', $produk->id) }}" method="POST">
                        @csrf
                        <label for="nama_barang" class="text-lg font-medium">Nama Barang</label>
                        <div class="my-3">
                            <select
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                id="barang_id" name="barang_id" required>
                                <option value="">Pilih Barang</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}"
                                        {{ old('barang_id', $produk->barang_id) == $barang->id ? 'selected' : '' }}>
                                        {{ $barang->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barang_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <label for="merek" class="text-lg font-medium">Merek</label>
                        <div class="my-3">
                            <input value="{{ old('merek', $produk->merek) }}" type="text" name="merek"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                required>
                            @error('merek')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <label for="kategori_id" class="text-lg font-medium">Kategori</label>
                        <div class="my-3">
                            <select
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                id="kategori_id" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <label for="satuan_id" class="text-lg font-medium">Satuan</label>
                        <div class="my-3">
                            <select
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                id="satuan_id" name="satuan_id" required>
                                <option value="">Pilih Satuan</option>
                                @foreach ($satuans as $satuan)
                                    <option value="{{ $satuan->id }}"
                                        {{ old('satuan_id', $produk->satuan_id) == $satuan->id ? 'selected' : '' }}>
                                        {{ $satuan->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <label for="supplier_id" class="text-lg font-medium">Supplier</label>
                        <div class="my-3">
                            <select
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                                id="supplier_id" name="supplier_id" required>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $produk->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <label for="nie" class="text-lg font-medium">NIE</label>
                        <div class="my-3">
                            <input value="{{ old('nie', $produk->nie) }}" type="text" name="nie"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                            @error('nie')
                                <p class="text-red-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white hover:bg-slate-800 transition">
                            Update Produk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
