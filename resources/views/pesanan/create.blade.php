<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $is_lanjutan ?? false ? 'Order Lanjutan' : 'Pesanan Masuk' }}
                </h2>
                @if (!empty($id))
                    <div class="bg-blue-100 text-blue-800 p-2 rounded mb-4">
                        Ini adalah <strong>Order Lanjutan</strong> dari No. PO sebelumnya = {{ $no_surat }}
                    </div>
                @endif
                @if (!empty($catatanKurang) && $catatanKurang->isNotEmpty())

                    <div class="bg-yellow-100 text-red-700 p-4 rounded-lg mb-4">
                        <h3 class="text-lg font-semibold mb-2">Catatan Barang Belum Masuk dari PO #{{ $no_surat }}
                        </h3>
                        <ul class="list-disc list-inside">
                            @foreach ($catatanKurang as $item)
                                <li>
                                    {{ $item->barang->nama ?? '-' }} -
                                    {{ $item->produk->merek ?? '-' }} |
                                    Disetujui: {{ $item->qty_approved }},
                                    Masuk: {{ $item->qty_masuk }},
                                    <strong>Sisa: {{ $item->qty_kurang }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @if ($is_lanjutan ?? false)
                <form action="{{ route('preorder.lanjutan.batal') }}" method="POST"
                    onsubmit="return confirm('Yakin batalkan order lanjutan ini?')">
                    @csrf
                    <input type="hidden" name="no_spo" value="{{ $no_spo }}">
                    <input type="hidden" name="parent_id" value="{{ $id }}">
                    <button type="submit" class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600">
                        Batal Order Lanjutan
                    </button>
                </form>
            @endif


        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('pesanan.store') }}" method="POST" x-data="produkComponent()"
                        x-init="init()" @submit.prevent="handleSubmit">
                        @csrf
                        {{-- NO ORDER --}}
                        @if ($is_lanjutan)
                            <input type="hidden" name="parent_id" value="{{ $id }}">
                            <input type="hidden" name="no_surat" value="{{ $no_surat }}">
                        @endif

                        <label class="text-lg font-medium">No Order</label>
                        <div class="my-3">
                            <input value="{{ request('no_spo') ?? ($no_spo ?? '') }}" type="text" name="no_spo"
                                id="no_spo" class="border-gray-300 shadow-sm w-1/2 rounded-lg" readonly>
                        </div>

                        {{-- CUSTOMER --}}
                        <label class="text-lg font-medium">Nama Cust</label>
                        <div class="my-3">
                         {{-- <input type="text" x-model="searchCust" placeholder="Cari Customer..." 
            class="border border-gray-300 rounded-lg px-3 py-2 w-1/2 mb-2" /> --}}

                            <select name="name_cust" id="name_cust"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg px-3 py-2" required>
                                <option value="">Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ request('cust_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NO SURAT --}}
                        <label class="text-lg font-medium">No Surat Pesanan</label>
                        <div class="my-3">
                            <input value="{{ request('no_surat') ?? old('no_surat') }}" type="text" name="no_surat"
                                id="no_surat" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                        </div>

                        <label class="text-lg font-medium">Tanggal</label>
                        <div class="my-3">
                            <input value="{{ $order_date }}" type="text" name="order_date" id="order_date"
                                class="border-gray-300 shadow-sm w-1/2 rounded-lg" readonly>
                        </div>


                        {{-- <div class="flex flex-col w-full md:w-1/2">
                            <label for="file_import" class="block text-sm font-medium text-gray-700">Import Produk dari
                                Excel</label>
                            <input type="file" id="file_import" @change="handleFileImport"
                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full" />
                        </div> --}}
                        <div>
                            <input type="file" id="excelFile" @change="importExcelFile" class="mb-4">
                            <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                                Impor Produk dari Excel
                            </button>
                        </div>

                        <!-- Tabel Produk -->
                        <div class="py-12">
                            <table border="1" class="w-full my-3">
                                <thead class="bg-gray-50">
                                    <tr class="border-b">
                                        <th class="px-6 py-3 text-left">Kategori</th>
                                        <th class="px-6 py-3 text-left">Nama Barang</th>
                                        <th class="px-6 py-3 text-left">Satuan</th>
                                        <th class="px-6 py-3 text-center">Qty</th>
                                        <th class="px-6 py-3 text-center">Pagu</th>
                                        <th class="px-6 py-3 text-center">Total</th>
                                        <th class="px-6 py-3 text-left">Merek</th>
                                        <th class="px-6 py-3 text-center">Harga</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <template x-if="products.length === 0">
                                        <tr>
                                            <td colspan="9" class="px-6 py-3 text-center text-gray-500">Belum ada
                                                produk ditambahkan</td>
                                        </tr>
                                    </template>
                                    <template x-for="(product, index) in products" :key="index">
                                        <tr class="border-b">
                                            <!-- Kategori -->
                                            <td class="px-6 py-3 text-left"
                                                x-text="product.kategori.name || product.kategori"></td>

                                            <!-- Nama Barang -->
                                            <td class="px-6 py-3 text-left" x-text="product.nama_barang"></td>

                                            <!-- Satuan -->
                                            <td class="px-6 py-3 text-left"
                                                x-text="product.satuan.name || product.satuan"></td>

                                            <!-- Qty -->
                                            <td class="px-6 py-3 text-center" x-text="product.qty"></td>

                                            <!-- Pagu -->
                                            <td class="px-6 py-3 text-center"
                                                x-text="Number(product.pagu) > 0 ? formatRupiah(product.pagu) : 'Rp 0'">
                                            </td>

                                            <!-- Total -->
                                            <td class="px-6 py-3 text-left"
                                                x-text="Number(product.total) > 0 ? formatRupiah(product.total) : 'Rp 0'">
                                            </td>

                                            <!-- Merek -->
                                            <td>
                                                <div class="max-w-[150px] overflow-hidden">
                                                    <select x-model="product.merek"
                                                        @change="updateMerek(product, $event.target.value)"
                                                        class="border border-gray-300 rounded text-sm px-1 py-0.5 h-8 w-full truncate">

                                                        <!-- Tampilkan fallback option jika merek yang diset tidak ada dalam list -->
                                                        <option
                                                            x-show="!(merekOptions[product.nama_barang?.toLowerCase()] || []).includes(product.merek)"
                                                            :value="product.merek" x-text="product.merek">
                                                        </option>

                                                        <!-- Tampilkan semua opsi dari merekOptions -->
                                                        <template
                                                            x-for="(merek, idx) in merekOptions[product.nama_barang?.toLowerCase()] || []"
                                                            :key="merek + idx">
                                                            <option :value="merek" x-text="merek"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </td>




                                            <!-- Harga editable -->
                                            <td class="px-6 py-3 text-center" x-data="{
                                                isEditing: false,
                                                tempHarga: product.harga,
                                                alertShown: false,
                                                simpanHarga() {
                                                    if (this.alertShown) return;
                                            
                                                    const hargaBaru = cleanFloat(this.tempHarga);
                                                    if (hargaBaru > product.pagu) {
                                                        this.alertShown = true;
                                                        alert('Harga tidak boleh melebihi Pagu!');
                                                    } else {
                                                        product.harga = hargaBaru;
                                                        updateTotalHarga(product);
                                                    }
                                            
                                                    this.isEditing = false;
                                                    setTimeout(() => this.alertShown = false, 100);
                                                }
                                            }">

                                                <!-- Tampilkan Harga -->
                                                <span x-show="!isEditing"
                                                    @click="isEditing = true; tempHarga = product.harga"
                                                    x-text="Number(product.harga) > 0 ? formatRupiah(product.harga) : 'Rp 0'"
                                                    :class="product.harga > product.pagu ? 'text-red-500 font-semibold' : ''"
                                                    class="cursor-pointer">
                                                </span>

                                                <!-- Input Harga saat Edit -->
                                                <input x-show="isEditing" type="text" x-model="tempHarga"
                                                    @keydown.enter.prevent="simpanHarga()" @blur="simpanHarga()"
                                                    class="border border-gray-300 rounded text-sm p-1 w-24 text-right">
                                            </td>






                                            <!-- Tombol Hapus -->
                                            <td class="px-6 py-3 flex gap-4">
                                                <button type="button"
                                                    @click="editProduct(index); $nextTick(() => { showForm = true; isEditMode = true })"
                                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                                    Edit
                                                </button>
                                                <button type="button" @click="removeProduct(index)"
                                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">Hapus</button>
                                            </td>
                                        </tr>

                                    </template>
                                </tbody>
                            </table>



                            <!-- Input Tambah Produk -->
                            <div x-data="{ showForm: false, isEditMode: false }">
                                <!-- Tombol Tambah Produk -->
                                <button type="button"
                                    @click="showForm = true; isEditMode = false; $nextTick(() => resetForm())"
                                    class="bg-slate-500 text-white px-4 py-2 rounded-md mb-4">
                                    Tambah Produk
                                </button>

                                <!-- Form (Untuk Tambah dan Edit) -->
                                <div x-show="showForm" class="bg-gray-100 p-6 rounded-lg shadow-md">
                                    <h2 class="text-lg font-bold mb-4"
                                        x-text="isEditMode ? 'Edit Produk' : 'Tambah Produk'"></h2>

                                    <div class="space-y-6">
                                        <div class="flex flex-wrap gap-4">
                                            <!-- Pilih Produk -->
                                            <div class="relative flex flex-col w-full md:w-1/2" x-data>
                                                <label for="produkSearch"
                                                    class="block text-sm font-medium text-gray-700 mb-1">
                                                    Pilih Produk
                                                </label>

                                                <!-- Input pencarian -->
                                                <input type="text" id="produkSearch" x-model="searchQuery"
                                                    @input="filterProducts" @focus="showDropdown = true"
                                                    @click.away="showDropdown = false"
                                                    placeholder="Ketik nama + merek..."
                                                    class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full" />

                                                <!-- Dropdown hasil pencarian -->
                                                <div x-show="showDropdown && filteredProdukList.length > 0"
                                                    x-transition
                                                    class="absolute top-full left-0 w-full border border-gray-300 bg-white rounded-md shadow-md mt-1 z-10 max-h-64 overflow-y-auto text-sm">
                                                    <!-- max-h & text-sm diperbesar -->

                                                    <template x-for="product in filteredProdukList.slice(0, 10)"
                                                        :key="product.id">
                                                        <div @click.prevent="selectProduct(product)"
                                                            class="p-3 hover:bg-gray-200 cursor-pointer flex justify-between">

                                                            <div>
                                                                <div class="font-semibold text-base"
                                                                    x-text="product.nama_barang"></div>
                                                                <div class="text-xs text-gray-500"
                                                                    x-text="product.merek"></div>
                                                                <div class="text-xs text-purple-500"
                                                                    x-text="product.supplier_name || '-'"></div>
                                                                <div class="text-xs text-orange-500"
                                                                    x-text="`PPN: ${product.supplier_ppn ? product.supplier_ppn * 100 : 11}%`">
                                                                </div>
                                                            </div>

                                                            <div class="text-right space-y-1">
                                                                <div class="text-xs text-gray-400"
                                                                    x-text="product.satuan?.name || ''"></div>

                                                                <div class="text-sm text-green-600 font-medium"
                                                                    x-show="product.stok_tersedia !== undefined">
                                                                    Stok: <span x-text="product.stok_tersedia"></span>
                                                                </div>

                                                                <div class="text-sm text-blue-600 font-medium"
                                                                    x-show="product.stokin_harga !== undefined">
                                                                    Harga: <span
                                                                        x-text="formatRupiah(product.stokin_harga)"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Input Manual -->
                                        <div class="flex flex-col w-full md:w-1/2">
                                            <label for="nama_barang"
                                                class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                            <input type="text" id="nama_barang" x-model="newProduct.nama_barang"
                                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full"
                                                placeholder="Masukkan nama barang" readonly />
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-4">
                                        <div class="flex flex-col w-full md:w-1/2">
                                            <label for="merek"
                                                class="block text-sm font-medium text-gray-700">Merek</label>
                                            <input type="text" id="merek" x-model="newProduct.merek"
                                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full"
                                                placeholder="Masukkan merek" readonly />
                                        </div>

                                        <div class="flex flex-col w-full md:w-1/2">
                                            <label for="kategori"
                                                class="block text-sm font-medium text-gray-700">Kategori</label>
                                            <input type="text" id="kategori" x-model="newProduct.kategori"
                                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full"
                                                readonly />
                                        </div>

                                        <div class="flex flex-col w-full md:w-1/2">
                                            <label for="satuan"
                                                class="block text-sm font-medium text-gray-700">Satuan</label>
                                            <input type="text" id="satuan" x-model="newProduct.satuan"
                                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full"
                                                readonly />
                                        </div>

                                        <div class="flex flex-col w-full md:w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">Qty</label>
                                            <input type="number" x-model="newProduct.qty"
                                                @input="updateTotalHarga()"
                                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full"
                                                placeholder="Jumlah">
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-4">
                                        <div class="flex flex-col w-full md:w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">Pagu</label>
                                            <input type="text" x-model="newProduct.paguFormatted"
                                                @input="updatePaguFormatted()"
                                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full"
                                                placeholder="Pagu">
                                        </div>

                                        <div class="flex flex-col w-full md:w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">Harga</label>
                                            <input type="text" x-model="newProduct.hargaFormatted"
                                                @input="updateHargaFormatted()"
                                                class="border-gray-300 shadow-sm rounded-lg py-2 px-3 w-full"
                                                placeholder="Harga">

                                        </div>
                                    </div>

                                    <!-- Tombol Simpan dan Batal -->
                                    <div class="flex gap-4">
                                        <button type="button" @click="saveProduct()"
                                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
                                            x-text="isEditMode ? 'Update' : 'Simpan'">
                                        </button>
                                        <button type="button" @click="resetForm()"
                                            class="bg-red-400 text-white px-4 py-2 rounded-md hover:bg-red-500 transition">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="products" x-ref="productData">
                        <button class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white" type="submit"
                            @click="$refs.productData.value = JSON.stringify(products)">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    @if ($errors->any())
        <div class="text-red-500">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

</x-app-layout>
<script>
    function produkComponent() {
        return {
            produkList: [],
            kategoriList: [],
            satuanList: [],
            supplierList: [],
            products: [],
            filteredProdukList: [],
            searchQuery: "",
            hargaFormatted: '',
            selectedProduct: null,
            produkOptions: [],
            showDropdown: false,
            merekOptions: {},
            editingIndex: null,
            searchCust: '',
            customers: @json($customers),
            newProduct: {
                produk_id: '',
                nama_barang: '',
                kategori: '',
                qty: 1,
                satuan: '',
                merek: '',
                pagu: '',
                harga: '',
                total_harga: 0
            },

            init() {
                this.fetchProducts();
                this.fetchKategoriSatuan();
                this.fetchSuppliers();
                this.fetchNamaBarang();
                this.newProduct.harga = this.formatRupiah(this.newProduct.harga);



                this.$watch('searchQuery', () => {
                    this.filterProducts();
                    this.showDropdown = this.filteredProdukList.length > 0;
                });
                console.log("ProdukList semua ID:", this.produkList.map(p => `${p.id} - ${p.merek}`));
            },
            get filteredCustomers() {
            return this.customers
                .filter(c => c.name.toLowerCase().includes(this.searchCust.toLowerCase()))
                .sort((a, b) => a.name.localeCompare(b.name));
            },


            async fetchProducts() {
                try {
                    let response = await fetch('/get-product-list');
                    this.produkList = await response.json();

                    console.log("Produk list:", this.produkList);

                    this.generateMerekOptions();


                    // Cek apakah semua punya nama_barang
                    this.produkList.forEach((p, i) => {
                        if (!p.nama_barang) {
                            console.warn(`Produk index ${i} tidak punya nama_barang!`, p);
                        }
                    });
                } catch (error) {
                    console.error('Gagal mengambil data produk:', error);
                }
            },


            async fetchNamaBarang() {
                try {
                    let response = await fetch('/get-nama-barang-list');
                    this.namaBarangList = await response.json();
                } catch (error) {
                    console.error('Gagal mengambil data nama barang:', error);
                }
            },


            async fetchKategoriSatuan() {
                try {
                    let kategoriResponse = await fetch('/get-kategori-list');
                    this.kategoriList = await kategoriResponse.json();

                    let satuanResponse = await fetch('/get-satuan-list');
                    this.satuanList = await satuanResponse.json();
                } catch (error) {
                    console.error('Gagal mengambil data kategori atau satuan:', error);
                }
            },

            async fetchSuppliers() {
                try {
                    let supplierResponse = await fetch('/get-supplier-list');
                    this.supplierList = await supplierResponse.json();
                } catch (error) {
                    console.error('Gagal mengambil data supplier:', error);
                }
            },

            async fetchLatestHarga(produkId) {
                try {
                    let response = await fetch(`/get-latest-harga/${produkId}`);
                    let data = await response.json();

                    if (data.harga) {
                        this.newProduct.harga = parseFloat(data.harga) || 0; // ANGKA BERSIH DI SINI
                        this.newProduct.hargaFormatted = this.formatRupiah(this.newProduct
                            .harga); // BUAT DISPLAY AJA
                        this.updateTotalHarga();
                    }
                } catch (error) {
                    console.error("Gagal mengambil harga terakhir:", error);
                }
            },

            filterProducts() {
                if (this.searchQuery.length > 1) {
                    this.filteredProdukList = this.produkList.filter(product =>
                        (`${product.nama_barang} ${product.merek}` || '')
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase())
                    );
                } else {
                    this.filteredProdukList = [];
                }

                this.showDropdown = this.filteredProdukList.length > 0;
            },

            async selectProduct(product) {
                if (!product.id) {
                    alert("Produk tidak valid!");
                    return;
                }

                this.searchQuery = `${product.nama_barang} - ${product.merek}`;


                let selected = this.produkList.find(p => p.id === product.id);

                if (!selected) {
                    alert("Produk tidak ditemukan!");
                    return;
                }

                this.applySelectedProduct(selected);

                setTimeout(() => {
                    this.filteredProdukList = [];
                    this.showDropdown = false;
                }, 10);
            },


            applySelectedProduct(product) {
                const clone = JSON.parse(JSON.stringify(product));
                console.log("applySelectedProduct dijalankan:", product.id, "-", product.merek);
                console.log("Merek sebelum push:", this.newProduct.merek);
                console.log("Merek dari applySelectedProduct:", product.merek);

                const harga = product.stokin_harga || 0;

                this.newProduct = {
                    produk_id: clone.id,
                    supplier_id: clone.supplier_id || null,
                    nama_barang: clone.nama_barang,
                    merek: clone.merek || (this.merekOptions[clone.nama_barang]?.[0] ?? ''),
                    kategori: clone.kategori?.name || "Tidak ditemukan",
                    satuan: clone.satuan?.name || "Tidak ditemukan",
                    harga: harga,
                    hargaFormatted: this.formatRupiah(harga),
                    qty: 1,
                    pagu: '',
                    total_harga: 0
                };

                this.searchQuery = `${clone.nama_barang} - ${clone.merek}`;
                this.filteredProdukList = [];
                this.showDropdown = false;

                console.log("Produk diset ke form:", this.newProduct);

                this.updateTotalHarga(); // auto hitung total
                this.fetchLatestHarga(clone.id);
            },



            updateMerek(product, newMerek) {
                const index = this.products.findIndex(p => p.produk_id === product.produk_id);
                if (index !== -1) {
                    this.products[index].merek = newMerek;
                    console.log(`Merek diubah: ${product.nama_barang} => ${newMerek}`);
                }
            },
            generateMerekOptions() {
                this.merekOptions = {};
                this.produkList.forEach(prod => {
                    const nama = (prod.nama_barang || '').toLowerCase();
                    if (!this.merekOptions[nama]) {
                        this.merekOptions[nama] = [];
                    }
                    if (!this.merekOptions[nama].includes(prod.merek)) {
                        this.merekOptions[nama].push(prod.merek);
                    }
                });
            },
            updateMerek(product, newMerek) {
                const index = this.products.findIndex(p => p.produk_id === product.produk_id);
                if (index !== -1) {
                    this.products[index].merek = newMerek;
                    console.log(`Merek diubah: ${product.nama_barang} => ${newMerek}`);
                }
            },



            saveProduct() {
                if (!this.newProduct.nama_barang || !this.newProduct.qty || !this.newProduct.harga) {
                    alert("Harap lengkapi data sebelum menyimpan!");
                    return;
                }

                const harga = Number(this.cleanNumber(this.newProduct.harga)) || 0;
                const pagu = Number(this.cleanNumber(this.newProduct.pagu)) || 0;

                // Cek merek dari list kalau belum ada
                if (!this.newProduct.merek && merekList.length > 0) {
                    this.newProduct.merek = merekList[0];
                }

                const produkDipilih = this.produkList.find(p => p.id === this.newProduct.produk_id);
                const stokSisa = produkDipilih?.stok_tersedia || 0;
                const qtyUser = parseInt(this.newProduct.qty) || 0;

                // Hanya alert jika qty melebihi stok, tidak mengurangi stok
                if (qtyUser > stokSisa) {
                    alert(`⚠️ Qty (${qtyUser}) melebihi stok tersedia (${stokSisa}).`);
                }

                const productData = {
                    produk_id: this.newProduct.produk_id,
                    supplier_id: this.newProduct.supplier_id,
                    nama_barang: this.newProduct.nama_barang,
                    kategori: this.newProduct.kategori?.name || this.newProduct.kategori,
                    satuan: this.newProduct.satuan?.name || this.newProduct.satuan,
                    merek: this.newProduct.merek,
                    qty: qtyUser,
                    pagu: pagu,
                    harga: harga,
                    total_harga: harga * qtyUser,
                    total: pagu * qtyUser,
                };

                // 6. Logika Edit/Tambah
                if (this.editingIndex !== null && this.editingIndex !== undefined) {
                    // Mode EDIT - timpa data lama
                    this.products[this.editingIndex] = productData;
                    console.log("Produk diupdate:", productData);
                } else {
                    // Mode TAMBAH - push data baru
                    this.products.push(productData);
                    console.log("Produk baru ditambahkan:", productData);
                }

                // 7. Reset form
                this.resetForm();
                console.log("Daftar produk terbaru:", JSON.parse(JSON.stringify(this.products)));
            },
            editProduct(index) {
                if (!this.products[index]) {
                    console.error("Product not found at index:", index);
                    return;
                }

                const {
                    produk_id,
                    supplier_id,
                    nama_barang,
                    kategori,
                    qty,
                    satuan,
                    merek,
                    pagu,
                    harga,
                    total_harga
                } = this.products[index];

                this.newProduct = {
                    produk_id,
                    supplier_id,
                    nama_barang,
                    kategori: kategori?.name || kategori,
                    qty,
                    satuan: satuan?.name || satuan,
                    merek,
                    pagu,
                    harga,
                    total_harga
                };

                // Jika menggunakan input formatted
                if (this.formatRupiah) {
                    this.newProduct = {
                        ...this.newProduct,
                        hargaFormatted: this.formatRupiah(harga),
                        paguFormatted: this.formatRupiah(pagu)
                    };
                }

                this.editingIndex = index;
                this.showForm = true;
            },



            resetForm() {
                this.newProduct = {
                    produk_id: '',
                    nama_barang: '',
                    kategori: '',
                    qty: 1,
                    satuan: '',
                    merek: '',
                    pagu: '',
                    harga: '',
                    total_harga: 0
                };
                this.editingIndex = null; // reset index edit
            },
            removeProduct(index) {
                const removedProduct = this.products[index];

                // Cari di produkList berdasarkan id
                const foundProduct = this.produkList.find(p => p.id === removedProduct.produk_id);

                // Kembalikan stoknya
                if (foundProduct) {
                    foundProduct.stok_tersedia += parseInt(removedProduct.qty || 0);
                }

                // Hapus dari daftar
                this.products.splice(index, 1);
            },


            formatRupiah(angka) {
                if (!angka) return 'Rp 0';
                return new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    minimumFractionDigits: 0
                }).format(angka);
            },

            cleanNumber(value) {
                if (!value) return 0;
                return parseFloat(
                    value.toString()
                    .replace(/[^\d,]/g, '') // hanya angka dan koma
                    .replace(/\./g, '') // hilangkan titik ribuan
                    .replace(',', '.') // ubah koma ke titik desimal
                ) || 0;
            },
            updateHargaFormatted() {
                this.newProduct.harga = this.cleanNumber(this.newProduct.hargaFormatted);
                this.newProduct.hargaFormatted = this.formatRupiah(this.newProduct.harga);
                this.updateTotalHarga();
            },
            updatePaguFormatted() {
                const cleaned = this.cleanNumber(this.newProduct.paguFormatted);
                this.newProduct.pagu = cleaned;
                this.newProduct.paguFormatted = this.formatRupiah(cleaned);
            },
            updateTotalHarga() {
                this.newProduct.total_harga = (this.newProduct.qty || 0) * (this.newProduct.harga || 0);
            },
            cleanFloat(value) {
                if (!value) return 0;
                // Hapus semua karakter kecuali angka dan titik atau koma
                let cleaned = value.toString().replace(/[^\d.,]/g, '').replace(',', '.');
                let parsed = parseFloat(cleaned);
                return isNaN(parsed) ? 0 : parsed;
            },


            importExcelFile(event) {
    const file = event.target.files[0];
    if (!file) {
        alert("Pilih file terlebih dahulu!");
        return;
    }

    const reader = new FileReader();

    reader.onload = (e) => {
        console.log("XLSX di dalam onload:", XLSX); // Debug XLSX

        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, {
            type: "array"
        });

        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet);

        // Hapus data lama sebelum mengimpor data baru
        this.products = []; // Reset data lama

        this.products = [
            ...this.products,
            ...jsonData.map(row => {
                const namaBarangExcel = row["Nama Barang"] || "";
                const qty = row.Qty || 1; // Tambahkan ini dulu
                let foundProduct = this.produkList.find(p =>
                    (p.nama_barang || "").toLowerCase() === namaBarangExcel.toLowerCase()
                );
                if (foundProduct && foundProduct.stok_tersedia >= qty) {
                    foundProduct.stok_tersedia -= qty;
                }

                
                return {
                    produk_id: foundProduct ? foundProduct.id : null,
                    nama_barang: namaBarangExcel,
                    kategori: foundProduct ? (foundProduct.kategori?.name || "Tidak ditemukan") : (row.Kategori || ""),
                    satuan: foundProduct ? (foundProduct.satuan?.name || "Tidak ditemukan") : (row.Satuan || ""),
                    merek: foundProduct ? (foundProduct.merek || "Tidak ditemukan") : (row.Merek || ""),
                    qty: qty,
                    pagu: row.Pagu || 0,
                    harga: row.Harga || 0,
                    total_harga: qty * (row.Harga || 0),
                    total: qty * (row.Pagu || 0),
                };
            })
        ];
        console.log("Produk setelah impor:", this.products);
    };
    reader.readAsArrayBuffer(file); // Menggunakan ArrayBuffer untuk kompatibilitas
},

            handleSubmit() {
                // ambil data form
                const namaCustomer = document.getElementById('name_cust').options[document.getElementById('name_cust')
                    .selectedIndex].text;
                const noSurat = document.getElementById('no_surat').value;
                const orderDate = document.getElementById('order_date').value;
                const produkList = this.products.map(p =>
                    `- ${p.nama_barang} (${p.merek}) x ${p.qty} @ ${this.formatRupiah(p.harga)}`).join('%0A');

                const pesan =
                    `Pesanan Baru:%0A
Customer: ${namaCustomer}%0A
No Surat: ${noSurat}%0A
Tanggal: ${orderDate}%0A
Produk:%0A${produkList}%0A`;

                const noWa = '6281234567890'; // ganti dengan nomor tujuan
                const waUrl = `https://wa.me/${noWa}?text=${pesan}`;

                // kirim form ke server
                this.$refs.productData.value = JSON.stringify(this.products);
                this.$el.submit(); // submit form ke Laravel

                // setelah sedikit delay, arahkan ke WhatsApp
                setTimeout(() => {
                    window.open(waUrl, '_blank');
                }, 1000);
            },

        };
    }
</script>
