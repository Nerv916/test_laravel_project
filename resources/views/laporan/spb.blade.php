<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pesanan Barang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; font-size: 16px; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table, .table th, .table td { border: 1px solid black; }
        .table th, .table td { padding: 5px; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">SURAT PESANAN BARANG</div>
    
    <table width="100%">
        <tr>
            <td>
                <strong>Dari :</strong><br>
                PT. PILAR CITRA SEJATI<br>
                KOMPLEK GRIYA BANDUNG ASRI 2 BLOK M5 NO. 27B, KEL. CIPAGALO, KEC. BOJONGSOANG, KAB. BANDUNG, JAWA BARAT<br>
                Email: pilarcitrasejatibdg@gmail.com<br>
                Tlp: 087819854086<br>
                NPWP: 03.352.119.6-444.000
            </td>
            <td>
                <strong>Kepada Yth :</strong><br>
                PT. INTI HASIL MEDICATAMA<br>
                JL. Suniajra No. 139-141 Kav. A3 Ruko, Dulatip Bandung, Jawa Barat 40181
            </td>
        </tr>
    </table>

    <br>

    <table width="100%">
        <tr>
            <td>No. SPO: {{ $preorder->no_spo }}</td>
            <td class="text-right">Tanggal: {{ \Carbon\Carbon::parse($preorder->order_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td colspan="2">Pengiriman ke: {{ $preorder->alamat_pengiriman }}</td>
        </tr>
    </table>

    <br>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kebutuhan</th>
                <th>Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($preorder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>

    <div class="text-right">
        <strong>Pemesan</strong><br><br><br>
        <u>Wulan, Amd. Farm</u><br>
        SRTTK : 19990529/SRTTK_32/2020/211369
    </div>

    <br>

    <div class="text-right">
        Tanggal Cetak: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
