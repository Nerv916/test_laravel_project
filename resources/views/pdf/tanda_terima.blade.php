<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Penyerahan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        table.footer {
            width: 100%;
            border: none;
            border-spacing: 0;
            margin-top: 20px;
        }

        table.footer td {
            border: none;
            padding: 10px;
            text-align: center;
        }



        th {
            background-color: yellow;
        }
    </style>
</head>

<body>
    <div class="pilar">
        <h2>PT.Pilar Citra Sejati</h2><br>
        KOMPLEK GRIYA BANDUNG ASRI 2 BLOK M5 NO. 27B, KEL. CIPAGALO, KEC. BOJONGSOANG, KAB. BANDUNG, JAWA BARAT
        <br>
        Email: pilarcitrasejatibdg@gmail.com <br>
        Tlp: 087819854086 <br>
        NPWP: 03.352.119.6-444.000
    </div>
    <h3 style="text-align: center;">SURAT PENYERAHAN BARANG</h3>
    <p>No: SPB/PCS/2024</p>
    <p><b>Diserahkan Ke:</b> {{ $approved->name_cust ?? 'Tidak Diketahui' }}</p>
    <p><b>Alamat:</b> {{ $costumer->alamat ?? 'Tidak Diketahui' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Nie</th>
                <th>No. Batch</th>
                <th>Exp. Date</th>
                <th>Satuan</th>
                <th>Banyak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($approved->items as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->produk->merek ?? 'Tidak Diketahui' }}</td>
                    <td>{{ $item->produk->nie ?? '-' }}</td>
                    <td>{{ $stockMovements[$item->produk_id]->first()->no_batch ?? '-' }}</td>
                    <td>{{ $stockMovements[$item->produk_id]->first()->exp_date ?? '-' }}</td>
                    <td>{{ $item->produk->satuan->name ?? '-' }}</td>
                    <td>{{ $stockMovements[$item->produk_id]->sum('quantity') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <table class="footer" width="100%">
        <tr>
            <td style="text-align: center;">
                <b>Yang Menerima</b> <br><br><br><br> (__________________)
            </td>
            <td style="text-align: center;">
                <b>Yang Menyerahkan</b> <br><br><br><br> <b>{{ $user->name ?? 'Tidak Diketahui' }}</b>
            </td>
        </tr>
    </table>
</body>

</html>
