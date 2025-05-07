<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-50">
        <tr class="border-b">
            <th class="px-6 py-3 text-left">Nama Produk</th>
            <th class="px-6 py-3 text-left">Supplier</th>
            <th class="px-6 py-3 text-left">Stok Saat Ini</th>
        </tr>
    </thead>
    <tbody class="bg-white">
        @foreach ($produks as $produk)
            <tr class="border-b">
                <td class="px-6 py-3">{{ $produk->merek }}</td>
                <td class="px-6 py-3">{{ $produk->supplier->name ?? '-' }}</td>
                <td class="px-6 py-3">{{ $produk->stok }}</td> <!-- Menampilkan stok terbaru -->
            </tr>
        @endforeach
    </tbody>
</table>
