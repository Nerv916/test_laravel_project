<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Merek</th>
            <th>Qty Masuk</th>
            <th>Qty Keluar</th>
            <th>Stok Sisa</th>
            <th>Harga Beli</th>
            <th>PPN</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stockData as $stock)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $stock->nama_barang }}</td>
                <td>{{ $stock->merek }}</td>
                <td>{{ $stock->total_in }}</td>
                <td>{{ $stock->total_out }}</td>
                <td>{{ $stock->stok_sisa }}</td>
                <td>{{ number_format($stock->harga_beli, 0, ',', '.') }}</td>
                <td>{{ $stock->ppn }}%</td>
                <td>{{ \Carbon\Carbon::parse($stock->tanggal)->format('d M, Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
