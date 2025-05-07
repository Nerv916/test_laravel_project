<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Produk - Order #{{ $approved->no_surat }}
            </h2>
            <div>
                <a href="{{ route('stok.report.index') }}"
                    class="bg-slate-500 hover:bg-slate-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
            </div>
        </div>
    </x-slot>
    <div class="py-6">
        <div class=" max-w-7xl mx-auto space-y-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <strong>Customer:</strong> {{ $approved->name_cust ?? '-' }} <br>
                <strong>Tanggal Order:</strong> {{ $approved->created_at->format('d M Y') }}
            </div>
            <table class="w-full border text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-2">Barang</th>
                        <th class="px-4 py-2">Merek</th>
                        <th class="px-4 py-2">Qty Approved</th>
                        <th class="px-4 py-2">Qty Masuk</th>
                        <th class="px-4 py-2">No Batch</th>
                        <th class="px-4 py-2">Exp Date</th>
                        <th class="px-4 py-2">Harga Beli</th>
                        <th class="px-4 py-2">PPN</th>
                        <th class="px-4 py-2">Total Jual</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($itemsWithStock as $data)
                        @foreach ($data->stock_movements as $stock)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $data->barang->nama ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $data->produk->merek ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $data->qty_approved }}</td>
                                <td class="px-4 py-2">{{ $stock->quantity }}</td>
                                <td class="px-4 py-2">{{ $stock->no_batch ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $stock->exp_date ?? '-' }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($stock->harga_beli ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ $stock->ppn ?? 0 }}%</td>
                                <td class="px-4 py-2">Rp
                                    {{ number_format($stock->total_harga_jual ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        @if ($data->qty_kurang > 0)
                            <tr class="border-b bg-yellow-100 text-red-600 font-semibold" x-data="{ success: false, loading: false }">
                                <td class="px-4 py-2">{{ $data->barang->nama ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $data->produk->merek ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $data->qty_approved }}</td>
                                <td class="px-4 py-2">{{ $data->qty_kurang }} (Belum Masuk)</td>
                                <td class="px-4 py-2" colspan="5">
                                    <div class="flex justify-between items-center">
                                        <span x-show="!success">Belum ada data stok masuk</span>
                                        <span x-show="success" class="text-green-600">Order lanjutan berhasil
                                            dibuat!</span>
                                        <button
                                            @click="() => buatOrderLanjutan({{ $data->produk->id }}, '{{ $approved->no_surat }}', $el, {{ $approved->id }})"
                                            x-bind:disabled="loading || success"
                                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                            Buat Order Lanjutan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
<script>
    window.buatOrderLanjutan = function(produk_id, no_surat, el, parent_id) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const row = el.closest('[x-data]');
        const alpine = Alpine.$data(row);
        alpine.loading = true;

        fetch('/order-lanjutan/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    produk_id: produk_id,
                    no_surat: no_surat,
                    parent_id: parent_id // ✅ ini sekarang dapet dari argumen, bukan {{ $approved->id }}
                })
            })
            .then(response => {
                if (!response.ok) throw new Error("Server error");
                return response.json();
            })
            .then(data => {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    alert('Gagal membuat order lanjutan: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membuat order lanjutan');
            })
            .finally(() => {
                alpine.loading = false;
            });
    };
</script>
