<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Transaksi') }}
            </h2>
            <div>
                <form method="GET" action="{{ route('laporan.transaksi') }}"
                    class="mb-6 flex flex-wrap items-center space-x-4">

                    <div class="flex flex-col">
                        <label for="start_date" class="text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                            class="border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex flex-col">
                        <label for="end_date" class="text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                            class="border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex flex-col">
                        <label class="invisible mb-1">Filter</label>
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 whitespace-nowrap">
                            Filter
                        </button>
                    </div>

                    <div class="flex flex-col">
                        <label class="invisible mb-1">Reset</label>
                        <a href="{{ route('laporan.transaksi') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap">
                            Reset
                        </a>
                    </div>

                    <div class="flex flex-col">
                        <label class="invisible mb-1">Export</label>
                        <a href="{{ route('laporan.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 whitespace-nowrap">
                            Export Excel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-message></x-message>


            @if (request('start_date') && request('end_date'))
                <h2 class="text-lg font-bold mb-2">Data Stok Masuk</h2>
                <table class="w-full border border-gray-300 mb-6">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Produk</th>
                            <th class="px-4 py-2 text-left">No Batch</th>
                            <th class="px-4 py-2 text-left">Exp Date</th>
                            <th class="px-4 py-2 text-left">Qty</th>
                            <th class="px-4 py-2 text-left">Harga Beli</th>
                            <th class="px-4 py-2 text-left">PPN</th>
                            <th class="px-4 py-2 text-left">Total Harga Jual</th>
                            <th class="px-4 py-2 text-left">Deskripsi</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stokIn as $item)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $item->produk->barang->nama }} - {{ $item->produk->merek }}
                                </td>
                                <td class="px-4 py-2">{{ $item->no_batch }}</td>
                                <td class="px-4 py-2">{{ $item->exp_date }}</td>
                                <td class="px-4 py-2">{{ $item->quantity }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ $item->ppn ?? 0 }}%</td>
                                <td class="px-4 py-2">Rp {{ number_format($item->total_harga_jual, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ $item->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h2 class="text-lg font-bold mb-2">Data Stok Keluar</h2>
                <table class="w-full border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Produk</th>
                            <th class="px-4 py-2 text-left">No Batch</th>
                            <th class="px-4 py-2 text-left">Exp Date</th>
                            <th class="px-4 py-2 text-left">Qty</th>
                            <th class="px-4 py-2 text-left">Harga Beli</th>
                            <th class="px-4 py-2 text-left">PPN</th>
                            <th class="px-4 py-2 text-left">Total Harga Jual</th>
                            <th class="px-4 py-2 text-left">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stokOut as $item)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $item->produk->barang->nama }} - {{ $item->produk->merek }}
                                </td>
                                <td class="px-4 py-2">{{ $item->no_batch }}</td>
                                <td class="px-4 py-2">{{ $item->exp_date }}</td>
                                <td class="px-4 py-2">{{ $item->quantity }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ $item->ppn ?? 0 }}%</td>
                                <td class="px-4 py-2">Rp {{ number_format($item->total_harga_jual, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ $item->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="my-3">
                {{-- {{ $kategori->links() }} --}}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deletePreorder(id) {
                if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: '{{ route('polaporan.destroy') }}',
                        type: 'DELETE',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        success: function(response) {
                            if (response.status) {
                                alert("Data berhasil dihapus!");
                                window.location.reload();
                            } else {
                                alert("Gagal menghapus data!");
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert("Terjadi kesalahan, coba lagi!");
                        }
                    });
                }
            }
        </script>
    </x-slot>
</x-app-layout>
