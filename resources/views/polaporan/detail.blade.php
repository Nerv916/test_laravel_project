<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Laporan') }}
            </h2>
            <div>
                {{-- <a href="{{ route('preorder.print.pdf', $preorder->id) }}" target="_blank"
                    class="bg-blue-600 text-white  text-sm rounded-md px-5 py-4 hover:bg-blue-700">
                    Cetak PDF Penawaran
                </a> --}}
                <button class="bg-blue-700 text-sm rounded-md px-5 py-4
                    text-white"
                    onclick="submitPreview()">Print Penawaran</button>

                <button onclick="approvePreorder({{ $preorder->id }})"
                    class="bg-green-700 text-sm rounded-md px-5 py-4
                    text-white">
                    Proses Preorder
                </button>
                <a href="{{ route('polaporan.index') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4

                text-white">Back</a>
            </div>

        </div>
        <span id="update-status-route" data-route="{{ route('item-order.update-status', ':id') }}"></span>
    </x-slot>

    <div x-data="ItemLoader({{ $preorder->id }})" x-init="init()" class="py-12 flex justify-center">
        <div x-data="TotalHarga()" class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">

            {{-- <x-message></x-message> --}}
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left">Nomor Surat</th>
                        <th class="px-6 py-3 text-left">Nama Cust</th>
                        <th class="px-6 py-3 text-left">total</th>
                        <th class="px-6 py-3 text-left">Total Belanja</th>
                        <th class="px-6 py-3 text-left">Total Jual</th>
                        <th class="px-6 py-3 text-left" width="180">Order Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr class="border-b" x-data="TotalHarga" x-init="init()">
                        <td class="px-6 py-3 text-left">{{ $preorder->no_surat ?? 'Tidak ada nomor surat' }}</td>
                        <td class="px-6 py-3 text-left">{{ optional($preorder->customer)->name ?? 'Tidak diketahui' }}
                        </td>
                        <td class="px-6 py-3 text-left">Rp {{ number_format($preorder->total_belanja, 2) }}</td>
                        <td class="px-4 py-2" x-text="formatRupiah(total_beli)"></td>
                        <td x-text="formatRupiah(total_jual)"></td>
                        <td class="px-6 py-3 text-left">{{ $preorder->order_date ?? 'Tanggal tidak tersedia' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Tabel Item Order -->
            @if (!empty($preorder->items) && $preorder->items->isNotEmpty())
                <h3 class="text-lg font-semibold mt-4">Item Order</h3>
                <div x-data="TotalHarga()" class="flex justify-center">
                    <table class="table-auto w-auto border-collapse border border-gray-300">
                        <thead class="bg-gray-800 text-white">
                            <tr class="border-b">
                                <th class="px-6 py-3 text-left whitespace-nowrap">Nama Barang</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Qty</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Satuan</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Pagu</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Merek</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Harga</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">PPN</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">HB+Tax</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Total HB</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Margin</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Harga Jual</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Selisih</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Total Jual</th>
                                <th class="px-6 py-3 text-left whitespace-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white" id="dynamic-items">
                            @foreach ($preorder->items as $item)
                                <tr class="border-b" x-data="combinedData($dispatch, {{ json_encode([
                                    'qty' => $item->qty ?? 0,
                                    'pagu' => $item->pagu ?? 0,
                                    'harga' => $item->harga ?? 0,
                                    'harga_setelah_pajak' => $item->harga_setelah_pajak ?? 0,
                                    'margin' => $item->margin,
                                    'ppn' => $item->ppn ?? 11, // ← ini penting, ambil dari DB
                                    'total_harga_jual_satuan' => $item->harga_jual_satuan ?? 0,
                                ]) }}, {{ $item->id }}, '{{ $item->status }}')" x-init="init()">

                                    <td class="px-6 py-3 text-left ">{{ $item->produk->merek }}</td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap" x-text="qty"></td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap">{{ $item->satuan }}</td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap" x-text="formatRupiah(pagu)"></td>
                                    <td class="px-6 py-3 text-left ">{{ $item->merek }}</td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap" x-text="formatRupiah(harga)"></td>
                                    {{-- <td class="px-6 py-3 text-left whitespace-nowrap"
                                        x-text="formatRupiah(total_harga_beli)"></td> --}}
                                    <td class="px-6 py-3 text-left whitespace-nowrap"
                                        x-text="formatRupiah(harga_setelah_pajak)"></td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap"
                                        x-text="formatRupiah(total_harga_beli)"></td>


                                    <!-- Input Margin -->
                                    <td class="px-6 py-3">
                                        <input type="number" step="0.01" min="0.1" x-model.number="margin"
                                            class="border p-1 w-16 text-center" @input="$dispatch('update-total')"
                                            @change="updateMargin(margin)" />
                                    </td>

                                    <td class="px-6 py-3 text-left whitespace-nowrap"
                                        x-text="formatRupiah(harga_jual_satuan)"></td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap"
                                        :class="selisih_pagu < 0 ? 'text-red-500 font-bold' : ''"
                                        x-text="formatRupiah(selisih_pagu)">
                                    </td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap"
                                        x-text="formatRupiah(total_harga_jual)"></td>
                                    <td class="px-6 py-3 text-left whitespace-nowrap">
                                        <select x-model="status" @change="updateStatus($event.target.value)">
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </td>
                                    <template x-if="true">
                                        <div>
                                            <input type="hidden"
                                                :name="'items[{{ $item->id }}][harga_jual_satuan]'"
                                                :value="harga_jual_satuan">
                                            <input type="hidden"
                                                :name="'items[{{ $item->id }}][total_harga_jual]'"
                                                :value="total_harga_jual">
                                            <input type="hidden"
                                                :name="'items[{{ $item->id }}][harga_setelah_pajak]'"
                                                :value="harga_setelah_pajak">
                                            <input type="hidden" :name="'items[{{ $item->id }}][qty]'"
                                                :value="qty">
                                            <input type="hidden" :name="'items[{{ $item->id }}][produk_id]'"
                                                value="{{ $item->produk_id }}">
                                        </div>
                                    </template>
                                </tr>
                            @endforeach
                        </tbody>

                        <!-- Footer Total -->
                        <tfoot class="bg-gray-200 font-bold w-full">
                            {{-- <tr></td>
                            </tr> --}}
                            {{-- <tr>
                                <td colspan="8" class="text-right font-bold">Total Harga Jual:</td>
                                
                            </tr> --}}
                        </tfoot>

                    </table>
                </div>
            @endif



            <div class="my-3">
                {{-- {{ $kategori->links() }} --}}
            </div>
        </div>
    </div>

</x-app-layout>
<script>
    function submitPreview() {
        const preorderId = Alpine.store('state').preorder_id;
        const approvedItems = Alpine.store('state').products.filter(item => item.status === 'approved');
        console.log('🧾 Kirim ke server:', approvedItems);


        if (approvedItems.length === 0) {
            alert('Tidak ada item yang sudah disetujui (approved) untuk dicetak.');
            return;
        }
        approvedItems.forEach(item => {
            if (!item.harga_jual_satuan) {
                // Mengonversi margin dan harga_setelah_pajak ke tipe numerik
                const margin = parseFloat(item.margin);
                const hargaSetelahPajak = parseFloat(item.harga_setelah_pajak);

                if (margin !== 0) { // Pastikan margin tidak nol
                    item.harga_jual_satuan = hargaSetelahPajak / margin;
                } else {
                    console.warn('Margin tidak boleh nol untuk item:', item);
                }
            }
        });
        const cleanItems = approvedItems.map(item => {
            const margin = parseFloat(item.margin);
            const hargaSetelahPajak = item.harga_setelah_pajak;
            const hargaJualSatuan = margin !== 0 ? hargaSetelahPajak / margin : 0;

            return {
                produk_id: item.produk_id,
                produk: item.produk,
                qty: item.qty,
                satuan: item.satuan,
                pagu: item.pagu,
                merek: item.merek,
                harga: item.harga,
                harga_setelah_pajak: hargaSetelahPajak,
                margin: margin,
                harga_jual_satuan: hargaJualSatuan,
                status: item.status,
                total_harga_jual: hargaJualSatuan * item.qty
            };
        });



        fetch('/preview-penawaran', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    preorder_id: preorderId,
                    items: JSON.stringify(approvedItems)
                })
            })
            .then(res => res.blob())
            .then(blob => {
                const url = URL.createObjectURL(blob);
                window.open(url, '_blank');
            });
    }

    function approvePreorder(id) {
        const rows = document.querySelectorAll('#dynamic-items tr[x-data]');
        const items = [];

        rows.forEach(row => {
            const data = Alpine.$data(row);

            if (!data) return;

            items.push({
                id: data.id ?? data.item_order_id ?? null,
                produk_id: data.produk_id ?? null,
                qty: Number(data.qty) || 0,
                satuan: data.satuan ?? '',
                merek: data.merek ?? '',
                pagu: Number(data.pagu) || 0,
                ppn: Number(data.ppn) || 11,
                harga: Number(data.harga) || 0,
                harga_setelah_pajak: Number(data.harga_setelah_pajak) || 0,
                total_harga_beli: Number(data.total_harga_beli) || 0,
                margin: Number(data.margin) || 0,
                harga_jual_satuan: Number(data.harga_jual_satuan) || 0,
                selisih_pagu: Number(data.selisih_pagu) || 0,
                total_harga_jual: Number(data.total_harga_jual) || 0,
                status: data.status || 'pending',
            });
        });

        if (items.length === 0) {
            alert("Tidak ada item yang dapat diproses.");
            return;
        }

        console.log("🟨 Items sebelum submit:", items);

        fetch(`/approve-preorder/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    items
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert('Gagal: ' + data.error);
                } else {
                    alert(data.message || 'Preorder berhasil diproses!');
                    window.location.href = "{{ route('polaporan.index') }}";
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan saat memproses preorder.');
                console.error('❌ Error:', error);
            });
    }



    function formatRupiah(angka) {
        if (angka === null || angka === undefined || isNaN(angka)) return "Rp 0";
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
        }).format(angka);
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('ItemLoader', (preorderId) => ({
            items: [],
            loading: false,

            async fetchItems() {
                this.loading = true;

                try {
                    const res = await fetch(`/api/preorders/${preorderId}/items`);
                    const data = await res.json();
                    this.items = data.items;

                    Alpine.store('state', {
                        preorder_id: preorderId,
                        products: data.items
                    });

                    // Render manual pakai template
                    this.renderItems();
                } catch (e) {
                    alert('Gagal mengambil data item.');
                    console.error(e);
                }

                this.loading = false;
            },

            renderItems() {
                const tbody = document.getElementById('dynamic-items');
                tbody.innerHTML = '';

                this.items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.setAttribute('x-data',
                        `combinedData($dispatch, ${JSON.stringify(item)}, ${item.id}, '${item.status}')`
                    );
                    tr.setAttribute('x-init', 'init()');
                    tr.innerHTML = `
                        <td class="px-6 py-3">${item.produk}</td>
    <td class="px-6 py-3" x-text="qty"></td>
    <td class="px-6 py-3">${item.satuan}</td>
    <td class="px-6 py-3" x-text="formatRupiah(pagu)"></td>
    <td class="px-6 py-3">${item.merek}</td>
    <td class="px-6 py-3" x-text="formatRupiah(harga)"></td>

    <!-- 🔥 Tambahin input PPN -->
    <td class="px-6 py-3">
        <input type="number" step="0.01" min="0" max="100"
            x-model.number="ppn"
            class="border p-1 w-16 text-center"
            @input="updatePPN(ppn); $dispatch('update-total')"
        >
    </td>

    <td class="px-6 py-3" x-text="formatRupiah(harga_setelah_pajak)"></td>
    <td class="px-6 py-3" x-text="formatRupiah(total_harga_beli)"></td>
    <td class="px-6 py-3">
        <input type="number" step="0.01" min="0.1"
            x-model.number="margin"
            class="border p-1 w-16 text-center"
            @input="updateMargin(margin); $dispatch('update-total')">
    </td>
    <td class="px-6 py-3" x-text="formatRupiah(harga_jual_satuan)"></td>
    <td class="px-6 py-3" x-text="formatRupiah(selisih_pagu)" :class="selisih_pagu < 0 ? 'text-red-500 font-bold' : ''"></td>
    <td class="px-6 py-3" x-text="formatRupiah(total_harga_jual)"></td>
    <td class="px-6 py-3">
        <select x-model="status" @change="updateStatus($event.target.value)">
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </td>
                    `;
                    tbody.appendChild(tr);
                });

                Alpine.initTree(tbody); // Re-inisialisasi Alpine untuk elemen baru
            },

            init() {
                this.fetchItems();
            }
        }));

        Alpine.data('TotalHarga', () => ({
            total_beli: 0,
            total_jual: 0,

            init() {
                this.hitungTotal();
                document.addEventListener('update-total', () => {
                    console.log("📢 Event 'update-total' diterima!");
                    this.$nextTick(() => this.hitungTotal());
                });
            },

            hitungTotal() {
                let totalBeli = 0,
                    totalJual = 0;

                document.querySelectorAll('[x-data^="combinedData"]').forEach(el => {
                    let component = Alpine.$data(el);
                    if (component) {
                        console.log(" Data ditemukan:", component);
                        console.log(" total_harga_beli:", component.total_harga_beli);
                        console.log(" total_harga_jual:", component.total_harga_jual);

                        totalBeli += parseFloat(component.total_harga_beli) || 0;
                        totalJual += parseFloat(component.total_harga_jual) || 0;
                    } else {
                        console.warn(" Tidak bisa membaca data dari elemen:", el);
                    }
                });

                console.log("🔹 Total Beli:", totalBeli);
                console.log("🔹 Total Jual:", totalJual);

                this.total_beli = totalBeli;
                this.total_jual = totalJual;
            }
        }));

        Alpine.data('combinedData', ($dispatch, item, itemOrderId, initialStatus) => ({
            produk_id: item.produk_id ?? null,
            qty: parseFloat(item.qty) || 0,
            pagu: parseFloat(item.pagu) || 0,
            harga: parseFloat(item.harga) || 0,
            ppn: parseFloat(item.ppn ?? 11), // Ambil dari DB

            margin: parseFloat(item.margin ?? 0.7),
            status: initialStatus || 'pending',
            item_order_id: itemOrderId,

            get harga_setelah_pajak() {
                return this.harga + (this.harga * (this.ppn / 100));
            },

            get harga_jual_satuan() {
                return this.margin ? this.harga_setelah_pajak / this.margin : 0;
            },

            get selisih_pagu() {
                return (this.pagu - this.harga_jual_satuan) || 0;
            },

            get total_harga_beli() {
                return (this.harga_setelah_pajak * this.qty) || 0;
            },

            get total_harga_jual() {
                console.log("harga_setelah_pajak:", this.harga_setelah_pajak, "margin:", this
                    .margin);
                const margin = this.margin || 0.7;
                const total = (this.harga_setelah_pajak / margin) * this.qty || 0;
                console.log("total_harga_jual:", total, "qty:", this.qty);
                return total;
            },

            updateMargin(margin) {
                this.margin = margin;

                fetch(`/update-margin/${this.item_order_id}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: JSON.stringify({
                            margin
                        }),
                    })
                    .then(res => res.json())
                    .then(data => console.log(" Margin updated:", data))
                    .catch(err => console.error(" Error saat update margin:", err));
            },

            updatePPN(value) {
                this.ppn = value;
                // Optional: lo bisa kirim ke server juga kalau perlu simpan real-time
                console.log(` PPN diperbarui untuk item ${this.item_order_id}:`, value);
            },

            updateStatus(value) {
                this.status = value;

                fetch(`/update-item-order-status/${this.item_order_id}`, {
                        method: "PATCH",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .getAttribute("content")
                        },
                        body: JSON.stringify({
                            status: value
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $dispatch('status-updated', {
                                id: this.item_order_id,
                                status: value
                            });
                        } else {
                            alert("Terjadi kesalahan saat memperbarui status.");
                        }
                    })
                    .catch(error => {
                        console.error("❌ Error saat mengupdate status:", error);
                        alert("Gagal menghubungi server.");
                    });
            },

            init() {
                this.$watch('qty', () => this.$nextTick(() => $dispatch('update-total')));
                this.$watch('total_harga_beli', () => this.$nextTick(() => $dispatch(
                    'update-total')));
                this.$watch('total_harga_jual', () => this.$nextTick(() => $dispatch(
                    'update-total')));
                this.$watch('ppn', () => this.$nextTick(() => $dispatch('update-total')));
                this.$watch('status', (newStatus) => {
                    console.log(`🆕 Status item ${this.item_order_id} berubah:`, newStatus);
                });
            }
        }));


        Alpine.data('orderSummary', () => ({
            subtotal_harga_beli: 0,

            recalculateSubtotal() {
                this.subtotal_harga_beli = [...document.querySelectorAll(
                        '[x-data^="combinedData"]')]
                    .reduce((acc, el) => acc + (parseFloat(el.__x?.$data?.total_harga_beli) || 0),
                        0);
            },

            init() {
                this.recalculateSubtotal();
                window.addEventListener('update-total', () => this.recalculateSubtotal());
            }
        }));

    });
</script>
