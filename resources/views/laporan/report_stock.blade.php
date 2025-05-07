<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Stok barang') }}
            </h2>
            {{-- <div>
                <a href="{{ route('laporan.report_stock_in') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
            text-white">Stock In</a>
                <a href="{{ route('laporan.report_stock_out') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
            text-white">Stock Out</a>
            
            </div> --}}
            <a href="{{ route('laporan.stock_export') }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Export Excel
            </a>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-message></x-message>
            <form method="GET" class="mb-4 flex gap-4 items-center">
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                    class="border px-4 py-2 rounded-md text-sm w-64" placeholder="Cari nama barang...">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                    Cari
                </button>
            </form>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">No</th>
                        <th class="px-6 py-3 text-left" width="60">Nama Barang</th>
                        <th class="px-6 py-3 text-left" width="60">Merek</th>
                        <th class="px-6 py-3 text-left" width="60">PO Cust</th>
                        <th class="px-6 py-3 text-left" width="60">Stok in</th>
                        <th class="px-6 py-3 text-left" width="60">Stock out</th>
                        <th class="px-6 py-3 text-left" width="60">Sisa</th>
                        <th class="px-6 py-3 text-left" width="60">Harga Beli</th>
                        <th class="px-6 py-3 text-left" width="60">PPN</th>
                        <th class="px-6 py-3 text-left" width="60">Tanggal</th>
                        <th class="px-6 py-3 text-left" width="60">Keterangan Order</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($stockData->isNotEmpty())
                        @foreach ($stockData as $stock)
                            <tr class="border-b">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">{{ $stock->nama_barang }}</td>
                                <td class="px-6 py-4">{{ $stock->merek }}</td>
                                <td class="px-6 py-4">{{ $stock->approved_qty }}</td>
                                <td class="px-6 py-4">{{ $stock->total_in }}</td>

                                <td class="px-6 py-4">{{ $stock->total_out }}</td>
                                <td class="px-6 py-4">{{ $stock->stok_sisa }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($stock->harga_beli, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">{{ $stock->ppn ?? 0 }}%</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($stock->tanggal)->format('d M, Y') }}
                                <td>
                                    @if ($stock->is_lanjutan)
                                        <span class="text-sm text-blue-600 font-semibold">Order Lanjutan</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                </td>
                            </tr>
                        @endforeach

                    @endif
                </tbody>
            </table>
            <div class="my-3">
                {{-- {{ $permissions->links() }} --}}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteSatuan(id) {
                if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: '{{ route('satuan.destroy') }}',
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
