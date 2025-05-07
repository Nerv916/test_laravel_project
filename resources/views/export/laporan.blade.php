<h3>Data Stok Masuk</h3>
<table>
    <thead>
        <tr>
            <th>Produk</th>
            <th>No Batch</th>
            <th>Exp Date</th>
            <th>Qty</th>
            <th>Harga Beli</th>
            <th>PPN</th>
            <th>Total Harga Jual</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stokIn as $item)
            <tr>
                <td>{{ $item->produk->barang->nama }} - {{ $item->produk->merek }}</td>
                <td>{{ $item->no_batch }}</td>
                <td>{{ $item->exp_date }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->harga_beli }}</td>
                <td>{{ $item->ppn ?? 0 }}%</td>
                <td>{{ $item->total_harga_jual }}</td>
                <td>{{ $item->description }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3>Data Stok Keluar</h3>
<table>
    <thead>
        <tr>
            <th>Produk</th>
            <th>No Batch</th>
            <th>Exp Date</th>
            <th>Qty</th>
            <th>Harga Beli</th>
            <th>PPN</th>
            <th>Total Harga Jual</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stokOut as $item)
            <tr>
                <td>{{ $item->produk->barang->nama }} - {{ $item->produk->merek }}</td>
                <td>{{ $item->no_batch }}</td>
                <td>{{ $item->exp_date }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->harga_beli }}</td>
                <td>{{ $item->ppn ?? 0 }}%</td>
                <td>{{ $item->total_harga_jual }}</td>
                <td>{{ $item->description }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
