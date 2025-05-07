<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pesanan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 20px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .sub-header {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
            background: #8ee1e173;
            padding: 10px;
            display: block;
            font-weight: bold;
        }

        .identitas {
            width: 100%;
            border-collapse: collapse;
        }

        .identitas td {
            vertical-align: top;
            padding: 10px;
            text-align: left;
        }

        .identitas td strong {
            display: block;
            margin-bottom: 8px;
        }

        .identitas tr {
            margin-bottom: 15px;
        }

        .identitas .H_ident td {
            background: rgba(156, 154, 154, 0.139);
            padding-bottom: 10px;
            border-bottom: 2px solid white;
        }

        .identitas .no_spo td {
            background: rgba(156, 154, 154, 0.139);
            padding-top: 10px;
        }


        .items_order {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
        }

        .items_order th,
        .items_order td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .items_order th {
            background-color: #f2f2f2;
        }

        .supplier_name {
            display: block;
            margin-bottom: 15px;
        }




        /* th, */
        /* td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
        } */

        /* th {
            background: #f0f0f0;
        } */

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('storage/image/pt.png') }}" alt="Foto" width="100">
        <p>
            PT. PILAR CITRA SEJATI
        </p>
    </div>
    <div class="sub-header">SURAT PESANAN BARANG</div>

    <<table class="identitas">
        <tr class="H_ident">
            <td width="50%">
                <strong class="title">Dari :</strong> <br>
                <strong>PT. PILAR CITRA SEJATI</strong> <br>
                KOMPLEK GRIYA BANDUNG ASRI 2 BLOK M5 NO. 27B, KEL. CIPAGALO, KEC. BOJONGSOANG, KAB. BANDUNG, JAWA BARAT
                <br>
                Email: pilarcitrasejatibdg@gmail.com <br>
                Tlp: 087819854086 <br>
                NPWP: 03.352.119.6-444.000
            </td>
            <td width="50%">
                <strong class="title">Kepada Yth :</strong> <br>
                @foreach ($supplierOrders as $supplierId => $items)
                    <strong>{{ $items->first()->produk->supplier->name ?? 'Tanpa Supplier' }}</strong><br>
                    {{ $items->first()->produk->supplier->alamat ?? 'Alamat Tidak Diketahui' }}<br>
                @endforeach
            </td>
        </tr>
        <tr class="no_spo">
            <td><strong>No.</strong> <span class="highlight">{{ $approved->no_spo }}</span></td>
            <td><strong>Tanggal</strong> {{ \Carbon\Carbon::parse($approved->order_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Pengiriman ke :</strong> KOMPLEK GRIYA BANDUNG ASRI 2 BLOK M5 NO. 27B, KEL.
                CIPAGALO, KEC.
                BOJONGSOANG, KAB. BANDUNG, JAWA BARAT
            </td>
        </tr>
        </table>


        @foreach ($supplierOrders as $supplierId => $items)
            <br>
            <strong class="supplier_name">
                {{ $items->first()->produk->supplier->name ?? 'Tanpa Supplier' }}</strong>
            <table class="items_order">
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th>Nama Barang</th>
                        <th width="70">Kebutuhan</th>
                        <th width="60">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->produk->barang->nama }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->produk->satuan->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        <div class="footer">
            <strong>Pemesan</strong> <br><br><br>
            {{ $user->name ?? 'Nama Tidak Diketahui' }} <br>
            SRTTK: 19990529/SRTTK_32/2020/211369
        </div>

</body>

</html>
