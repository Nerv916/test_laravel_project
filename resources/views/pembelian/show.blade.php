<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pembelian') }}
            </h2>
            <a href="{{ route('pembelian.pdf', $approved->id) }}"
                class="bg-slate-500 text-white px-4 py-2 rounded-md hover:bg-slate-700">
                Cetak PDF
            </a>
        </div>
    </x-slot>

    <div class=" py-12">
        <div class="  max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>
            <div
                class=" max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('stok.in') }}" method="POST" onsubmit="bersihkanPenyimpanan()">
                    @csrf
                    <input type="hidden" name="approved_id" value="{{ $approved->id }}">

                    @foreach ($supplierOrders as $supplierId => $items)
                        @php
                            $supplier = $items->first()->produk->supplier ?? null;
                            $supplierName = $supplier ? $supplier->name : 'Tanpa Supplier';
                            $totalHarga = $items->sum('total_harga');
                        @endphp

                        <div class="mb-10">
                            <div class="flex justify-between items-center mb-2">
                                <h2 class="text-lg font-bold">
                                    {{ $supplierName }}
                                </h2>
                                <span class="text-gray-600">Total: Rp
                                    {{ number_format($totalHarga, 2, ',', '.') }}</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-300">
                                    <thead class="bg-gray-200">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Produk</th>
                                            <th class="px-4 py-2 text-left">Merek</th>
                                            <th class="px-4 py-2 text-left">No Batch</th>
                                            <th class="px-4 py-2 text-left">Exp D</th>
                                            <th class="px-4 py-2 text-left">Kebutuhan</th>
                                            <th class="px-4 py-2 text-left">Qty In</th>
                                            <th class="px-4 py-2 text-left">Harga Beli</th>
                                            <th class="px-4 py-2 text-left">PPN</th>
                                            <th class="px-4 py-2 text-left">Jual</th>
                                            <th class="px-4 py-2 text-left">Total Beli</th>
                                            <th class="px-4 py-2 text-left">Total Jual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr class="border-b">
                                                <td class="px-4 py-2">{{ $item->produk->barang->nama }}</td>
                                                <td class="px-4 py-2">{{ $item->produk->merek }}</td>
                                                <td class="px-4 py-2">
                                                    <input type="text" name="no_batch[{{ $item->id }}]"
                                                        class="border border-gray-300 rounded px-2 py-1"
                                                        placeholder="No Batch">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="date" name="exp_date[{{ $item->id }}]"
                                                        class="border border-gray-300 rounded px-2 py-1">
                                                </td>
                                                <td class="px-4 py-2">{{ $item->qty }}</td>
                                                <td class="px-4 py-2">
                                                    <input type="number" name="qty[{{ $item->id }}]"
                                                        class="qty-input" data-id="{{ $item->id }}"
                                                        value="{{ $item->qty }}" min="0"
                                                        onchange="hitungHargaJual({{ $item->id }})"
                                                        onkeyup="hitungHargaJual({{ $item->id }})">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="text" name="harga_beli[{{ $item->id }}]"
                                                        class="harga-beli-input border border-gray-300 rounded px-2 py-1 w-24 text-right"
                                                        data-id="{{ $item->id }}"
                                                        oninput="hitungTotalBeli({{ $item->id }})"
                                                        placeholder="Rp 0">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" name="ppn[{{ $item->id }}]"
                                                        step="0.01" data-id="{{ $item->id }}"
                                                        class="ppn-input border border-gray-300 rounded px-2 py-1 w-20 text-right"
                                                        placeholder="%">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span class="harga-jual-satuan" data-id="{{ $item->id }}"
                                                        data-value="{{ $item->harga_jual_satuan }}">
                                                        Rp {{ number_format($item->harga_jual_satuan, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span id="total-beli-{{ $item->id }}">Rp 0</span>
                                                    <input type="hidden" name="total_beli[{{ $item->id }}]"
                                                        id="total-beli-input-{{ $item->id }}" value="0">
                                                </td>


                                                <td class="px-4 py-2">
                                                    <span id="harga-jual-{{ $item->id }}">Rp 0</span>
                                                    <input type="hidden" name="harga_jual[{{ $item->id }}]"
                                                        id="harga-jual-input-{{ $item->id }}" value="0">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Konfirmasi Stok In
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function simpanDataForm() {
        const formData = {};

        document.querySelectorAll(
            'input[name^="no_batch"], input[name^="exp_date"], input[name^="qty"], input[name^="harga_beli"], input[name^="ppn"]'
        ).forEach(input => {
            formData[input.name] = input.value;
        });

        localStorage.setItem('pembelianFormData', JSON.stringify(formData));
    }

    // Fungsi untuk memuat data dari localStorage
    function muatDataForm() {
        const savedData = localStorage.getItem('pembelianFormData');
        if (savedData) {
            const formData = JSON.parse(savedData);

            Object.entries(formData).forEach(([name, value]) => {
                const input = document.querySelector(`input[name="${name}"]`);
                if (input) {
                    input.value = value;

                    // Trigger perhitungan jika input mempengaruhi harga
                    if (name.startsWith('qty') || name.startsWith('harga_beli') || name.startsWith('ppn')) {
                        const id = input.dataset.id;
                        if (id) hitungHargaJual(id);
                    }
                }
            });
        }
    }

    // Fungsi untuk menghapus data tersimpan saat form submit
    function bersihkanPenyimpanan() {
        localStorage.removeItem('pembelianFormData');
    }

    // Panggil saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        muatDataForm();

        // Simpan data saat input berubah
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                simpanDataForm();
            });
        });

        // Bersihkan saat form submit
        document.querySelector('form').addEventListener('submit', function() {
            bersihkanPenyimpanan();
        });
    });

    function hitungTotalBeli(id) {
        let qty = parseFloat(document.querySelector(`.qty-input[data-id="${id}"]`)?.value) || 0;
        let hargaBeliRaw = document.querySelector(`.harga-beli-input[data-id="${id}"]`)?.value || '0';
        let hargaBeli = parseInt(hargaBeliRaw.replace(/[^\d]/g, '')) || 0;

        let totalBeli = qty * hargaBeli;

        document.getElementById(`total-beli-${id}`).innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(totalBeli);

        document.getElementById(`total-beli-input-${id}`).value = totalBeli;
    }

    // Perbarui fungsi hitungHargaJual agar panggil hitungTotalBeli juga
    function hitungHargaJual(id) {
        let qty = parseFloat(document.querySelector(`.qty-input[data-id="${id}"]`)?.value) || 0;
        let hargaJualSatuan = parseFloat(document.querySelector(`.harga-jual-satuan[data-id="${id}"]`)?.dataset
            .value) || 0;

        let totalHargaJual = qty * hargaJualSatuan;

        document.getElementById(`harga-jual-${id}`).innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(totalHargaJual);

        document.getElementById(`harga-jual-input-${id}`).value = totalHargaJual;

        // Tambahkan perhitungan total beli juga saat qty berubah
        hitungTotalBeli(id);
    }
    // Inisialisasi perhitungan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.qty-input').forEach(input => {
            const id = input.dataset.id;
            hitungHargaJual(id); // Hitung saat pertama kali load
        });
    });

    function formatInputAsRupiah(input) {
        let angka = input.value.replace(/[^\d]/g, '');
        if (!angka) return;
        let number = parseInt(angka);
        input.value = new Intl.NumberFormat('id-ID').format(number);
    }

    document.querySelectorAll('.qty-input, .harga-beli-input, .ppn-input').forEach(input => {
        input.addEventListener('input', function() {
            hitungHargaJual(this.dataset.id);
            simpanDataForm(); // Simpan setiap perubahan
        });

        input.addEventListener('keydown', function(e) {
            if (this.classList.contains('harga-beli-input') && e.key === 'Enter') {
                e.preventDefault();
                formatInputAsRupiah(this);
                hitungHargaJual(this.dataset.id);
            }
        });

        input.addEventListener('focus', function() {
            if (this.classList.contains('harga-beli-input')) {
                let clean = this.value.replace(/[^\d]/g, '');
                this.value = clean || '';
            }
        });
    });
</script>
