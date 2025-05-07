import "./bootstrap";
import Alpine from "alpinejs";
import mask from "@alpinejs/mask";
import * as XLSX from "xlsx"; // Import XLSX setelah library utama
import Chart from "chart.js/auto";

window.Chart = Chart;
window.XLSX = XLSX;
window.Alpine = Alpine;
Alpine.plugin(mask);
// ✅ Komponen Alpine untuk daftar item preorder
Alpine.data("ItemLoader", (preorderId) => ({
    items: [],
    loading: false,

    async fetchItems() {
        this.loading = true;
        try {
            const res = await fetch(`/api/preorders/${preorderId}/items`);
            const data = await res.json();
            this.items = data.items;
            this.renderItems();
        } catch (e) {
            alert("Gagal mengambil data item.");
            console.error(e);
        }
        this.loading = false;
    },

    renderItems() {
        const tbody = document.getElementById("dynamic-items");
        if (!tbody) return;

        tbody.innerHTML = "";

        this.items.forEach((item) => {
            const tr = document.createElement("tr");
            tr.setAttribute(
                "x-data",
                `combinedData($dispatch, ${JSON.stringify(item)}, ${
                    item.id
                }, '${item.status}')`
            );
            tr.setAttribute("x-init", "init()");
            tr.innerHTML = `
                <td class="px-6 py-3">${item.produk}</td>
                <td class="px-6 py-3" x-text="qty"></td>
                <td class="px-6 py-3">${item.satuan}</td>
                <td class="px-6 py-3" x-text="formatRupiah(pagu)"></td>
                <td class="px-6 py-3">${item.merek}</td>
                <td class="px-6 py-3" x-text="formatRupiah(harga)"></td>
                <td class="px-6 py-3" x-text="formatRupiah(harga_setelah_pajak)"></td>
                <td class="px-6 py-3" x-text="formatRupiah(total_harga_beli)"></td>
                <td class="px-6 py-3">
                    <input type="number" step="0.01" min="0.1" x-model.number="margin" class="border p-1 w-16 text-center" @input="$dispatch('update-total')">
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

        Alpine.initTree(tbody); // reinit Alpine untuk elemen baru
    },

    init() {
        this.fetchItems();
    },
}));
Alpine.start();

console.log("Alpine:", Alpine);
console.log("Mask Plugin:", mask);
console.log("XLSX:", XLSX); // Debug apakah XLSX berhasil diimpor
