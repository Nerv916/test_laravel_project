<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Penjualan') }}
            </h2>
            <div>
                @if ($approvedId)
                    <a href="{{ route('tandaTerima.pdf', $approvedId) }}"
                        class="bg-slate-500 text-white px-4 py-2 rounded-md hover:bg-slate-700">
                        Cetak PDF
                    </a>

                    <a href="{{ route('invoice.print', $approvedId) }}" target="_blank"
                        class="bg-slate-500 text-white px-4 py-2 rounded-md hover:bg-slate-700">
                        Print Faktur PDF
                    </a>
                @endif
            </div>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('stok.keluar') }}">
                @csrf
                <div class="py-12">
                    <x-message></x-message>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr class="border-b">
                                <th class="px-6 py-3 text-left">No Surat</th>
                                <th class="px-6 py-3 text-left">Customer</th>
                                <th class="px-6 py-3 text-left">Tanggal Order</th>
                                <th class="px-6 py-3 text-left">Total Harga</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        @if ($approvedOrders)
                            <tbody class="bg-white">
                                <input type="hidden" name="approved_id" value="{{ $approvedOrders->id }}">
                                <tr class="border-b">

                                    <td class="px-6 py-3 text-left">{{ $approvedOrders->no_surat }}</td>
                                    <td class="px-6 py-3 text-left">
                                        {{ $approvedOrders->name_cust ?? 'Tidak Diketahui' }}
                                    </td>
                                    <td class="px-6 py-3 text-left">{{ $approvedOrders->order_date }}</td>
                                    <td class="px-6 py-3 text-left">
                                        Rp. {{ number_format($approvedOrders->items->sum('total_harga'), 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <button type="button" onclick="toggleDetail('{{ $approvedOrders->id }}')"
                                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                            Detail Produk
                                        </button>
                                    </td>
                                </tr>
                                <tr id="detail-{{ $approvedOrders->id }}" class="hidden">
                                    <td colspan="5" class="p-4 bg-gray-100">
                                        <table class="w-full border border-gray-300">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Produk</th>
                                                    <th class="px-4 py-2 text-left">Merek</th>
                                                    <th class="px-4 py-2 text-left">No Batch</th>
                                                    <th class="px-4 py-2 text-left">Exp Date</th>
                                                    <th class="px-4 py-2 text-left">Kebutuhan</th>
                                                    <th class="px-4 py-2 text-left">Qty Out</th>
                                                    <th class="px-4 py-2 text-left">PPN</th>
                                                    <th class="px-4 py-2 text-left">Harga</th>
                                                    <th class="px-4 py-2 text-left">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($stockMovements as $movement)
                                                    <tr class="border-b" x-data="batchSearch({{ $movement->produk->id }}, '{{ $movement->approved_item_id }}', {{ $movement->total_harga_jual / $movement->quantity }}, {{ $movement->quantity }})"
                                                        x-init="init()">

                                                        <td class="px-4 py-2 whitespace-nowrap">
                                                            {{ $movement->produk->barang->nama ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            {{ $movement->produk->merek ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-2 relative">
                                                            <input type="hidden"
                                                                name="produk_id[{{ $movement->approved_item_id }}]"
                                                                value="{{ $movement->produk_id }}">

                                                            <input type="text"
                                                                name="batch[{{ $movement->approved_item_id }}]"
                                                                class="border border-gray-300 rounded px-2 py-1 w-32"
                                                                x-model="search" @input.debounce.300ms="fetchBatches"
                                                                @focus="show = true" @click.away="show = false"
                                                                x-ref="batchInput" autocomplete="off"
                                                                placeholder="No Batch" required>

                                                            <!-- Dropdown hasil search -->
                                                            <ul x-show="show && results.length > 0"
                                                                class="absolute z-50 w-72 bg-white border border-gray-300 rounded-xl mt-2 shadow-lg max-h-60 overflow-y-auto text-sm space-y-1">
                                                                <template x-for="(item, index) in results"
                                                                    :key="index">
                                                                    <li @click="selectBatch(item)"
                                                                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer flex flex-col border-b last:border-none">
                                                                        <span class="font-semibold text-gray-800"
                                                                            x-text="`Batch: ${item.no_batch}`"></span>
                                                                        <div
                                                                            class="flex justify-between text-gray-600 text-xs mt-1">
                                                                            <span
                                                                                x-text="`Exp: ${item.exp_date}`"></span>
                                                                            <span x-text="`Stok: ${item.stok}`"></span>
                                                                        </div>
                                                                        <div
                                                                            class="flex justify-between text-gray-600 text-xs">
                                                                            <span
                                                                                x-text="`Rp ${Number(item.harga_beli).toLocaleString('id-ID')}`"></span>
                                                                            <span x-text="`PPN: ${item.ppn}%`"></span>
                                                                        </div>
                                                                    </li>
                                                                </template>
                                                            </ul>

                                                            <input type="hidden"
                                                                name="harga_beli[{{ $movement->approved_item_id }}]"
                                                                x-model="harga_beli" x-ref="hargaBeliField">

                                                            <input type="hidden"
                                                                name="total_harga_beli[{{ $movement->approved_item_id }}]"
                                                                x-model="total_harga_beli">

                                                        </td>

                                                        <td class="px-4 py-2">
                                                            <input type="date"
                                                                name="exp_date[{{ $movement->approved_item_id }}]"
                                                                class="border border-gray-300 rounded px-2 py-1"
                                                                x-ref="expField" required>
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            {{ $movement->approvedItem->qty }}
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            <input type="number"
                                                                name="qty[{{ $movement->approved_item_id }}]"
                                                                x-bind:max="results.find(r => r.no_batch === search)?.stok || null"
                                                                min="1"
                                                                class="border border-gray-300 rounded px-2 py-1 w-16"
                                                                x-ref="qtyField" x-model.number="qty"
                                                                @input="updateTotal()">
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            <input type="text"
                                                                name="ppn[{{ $movement->approved_item_id }}]"
                                                                class="border border-gray-300 rounded px-2 py-1 w-16 bg-gray-100 text-right"
                                                                x-ref="ppnField" x-model="ppn" readonly>
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            <span x-text="formatRupiah(harga_jual_satuan)"></span>
                                                            <input type="hidden"
                                                                name="harga_jual_satuan[{{ $movement->approved_item_id }}]"
                                                                x-model="harga_jual_satuan">
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            Rp. <span x-text="formatRupiah(total_harga_jual)"></span>
                                                            <input type="hidden"
                                                                name="total_harga_jual[{{ $movement->approved_item_id }}]"
                                                                x-model="total_harga_jual">
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        @endif
                    </table>
                    <button type="submit"
                        class="bg-green-500 mt-4 text-white px-6 py-2 rounded-md hover:bg-green-700">
                        Simpan Stok Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

<script>
    function toggleDetail(orderId) {
        let row = document.getElementById('detail-' + orderId);
        row.classList.toggle('hidden');
    }


    function batchSearch(produkId, rowId, hargaJual, approvedQty) {
        return {
            search: '',
            results: [],
            show: false,
            harga_jual_satuan: hargaJual || 0,
            harga_beli: 0,
            total_harga_beli: 0,
            qty: 0,
            ppn: 0,
            total_harga_jual: 0,
            storageKey: `order_${rowId}_data`,
            approved_qty: approvedQty,

            init() {
                // Load saved data
                const savedData = localStorage.getItem(this.storageKey);
                if (savedData) {
                    const parsedData = JSON.parse(savedData);
                    this.search = parsedData.search || '';
                    this.qty = parsedData.qty || 0;
                    this.ppn = parsedData.ppn || this.ppn;
                    this.total_harga_jual = parsedData.total_harga_jual || 0;
                    this.harga_beli = parsedData.harga_beli || 0;
                    this.total_harga_beli = parsedData.total_harga_beli || 0;

                    if (this.$refs.qtyField) this.$refs.qtyField.value = this.qty;
                    if (this.$refs.ppnField) this.$refs.ppnField.value = this.ppn;
                    if (this.$refs.expField && parsedData.exp_date) {
                        this.$refs.expField.value = parsedData.exp_date;
                    }
                    if (this.$refs.batchInput) this.$refs.batchInput.value = this.search;
                    if (this.$refs.hargaBeliField) this.$refs.hargaBeliField.value = this.harga_beli;
                }

                this.updateTotal();
            },

            formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value).replace('Rp', 'Rp.');
            },

            updateTotal() {
                const selected = this.results.find(r => r.no_batch === this.search);
                if (selected && this.qty > selected.stok) {
                    this.qty = selected.stok;
                }

                this.total_harga_jual = this.harga_jual_satuan * this.qty;
                this.total_harga_beli = this.harga_beli * this.qty;

                this.saveToStorage();
                this.$dispatch('update-total');
            },

            fetchBatches() {
                if (this.search.length < 1) {
                    this.results = [];
                    return;
                }

                fetch(`/api/batch-search?produk_id=${produkId}&query=${this.search}`)
                    .then(res => res.json())
                    .then(data => {
                        this.results = data;
                        this.saveToStorage();
                    });
            },

            selectBatch(item) {
                this.search = item.no_batch;
                this.show = false;
                this.ppn = item.ppn ?? this.ppn;
                this.harga_beli = item.harga_beli ?? 0;

                const sisaKebutuhan = this.approved_qty;
                this.qty = Math.min(sisaKebutuhan, item.stok);

                if (this.$refs.expField) this.$refs.expField.value = item.exp_date;
                if (this.$refs.ppnField) this.$refs.ppnField.value = this.ppn;
                if (this.$refs.hargaBeliField) this.$refs.hargaBeliField.value = this.harga_beli;

                this.total_harga_beli = this.harga_beli * this.qty; // << TAMBAHAN PENTING

                this.updateTotal();
            },

            saveToStorage() {
                const dataToSave = {
                    search: this.search,
                    qty: this.qty,
                    ppn: this.ppn,
                    harga_beli: this.harga_beli,
                    total_harga_beli: this.total_harga_beli,
                    total_harga_jual: this.total_harga_jual,
                    exp_date: this.$refs.expField?.value
                };
                localStorage.setItem(this.storageKey, JSON.stringify(dataToSave));
            },

            clearStorage() {
                localStorage.removeItem(this.storageKey);
            },
        }
    }

    document.addEventListener('submit', function(e) {
        console.log('Form submitted normally');
    });

    function data() {
        return {
            formData: {
                produk_id: '',
                qty: '',
                // isi sesuai kebutuhan
            },
            async submitForm() {
                const res = await fetch('/stock-out', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData),
                    credentials: 'same-origin'
                });

                const data = await res.json();
                if (data.status === 'success') {
                    alert('Stok keluar berhasil!');
                    window.location.href = '/penjualan'; // redirect manual
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            }
        }
    }
</script>
