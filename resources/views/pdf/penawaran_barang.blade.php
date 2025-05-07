use App\Models\ApprovedItems;
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Penawaran Harga</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2,
        h3 {
            margin: 0;
            text-align: center;
        }

        p {
            margin: 4px 0;
        }

        table.borderless-table {
            width: 100%;
            margin-top: 5px;
        }

        table.borderless-table td {
            border: none;
            padding: 2px;
        }

        table.product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.product-table th,
        table.product-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            text-align: right;
        }

        .logo {
            width: 60px;
            height: auto;
        }

        .company-name {

            font-weight: bold;
            font-size: 16px;
            vertical-align: middle;
        }

        .info-left {
            background-color: rgba(173, 164, 164, 0.135);
            width: 70%;
            font-size: 11px;
            vertical-align: top;
        }

        .info-right {
            background-color: rgba(173, 164, 164, 0.135);
            width: 30%;
            font-size: 11px;
            text-align: right;
            vertical-align: top;
        }

        .section-title {
            background-color: #d9f2ef;
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            padding: 4px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }

        .product-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-weight: bold;
        }

        .product-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .Direct {
            margin-top: 4px;
        }

        .Direct h2 {
            text-align: left;
            font-size: 10px;
        }
    </style>
</head>

<body>

    {{-- Logo dan Nama Perusahaan --}}
    <table class="borderless-table">
        <tr>
            <td style="width: 10%;">
                <img src="{{ public_path('storage/image/pt.png') }}" alt="Foto" width="75">
            </td>
            <td class="company-name">
                PT. PILAR CITRA SEJATI
            </td>
        </tr>
    </table>

    {{-- Judul Penawaran --}}
    <h3 class="section-title">PENAWARAN HARGA</h3>

    {{-- Alamat dan Kepada Yth --}}
    <table class="borderless-table">
        <tr>
            <td class="info-left">
                KOMPLEK GRYA BANDUNG ASRI 2 BLOK MS NO 25, KEL. CIPAGALO, KEC. BOJONGSOANG,<br>
                KAB. BANDUNG, JAWA BARAT<br>
                Email: pilarcitrasejati4pb@gmail.com<br>
                Tlp: 087819854088<br>
                NPWP: 03.552.119.6-444.000
            </td>
            <td class="info-right">
                <strong>Kepada Yth:</strong><br>
                {{ $preorder->customer->name ?? '-' }}
                {{ $preorder->customer->alamat ?? '-' }}
            </td>
        </tr>
    </table>

    {{-- Info SPM dan Tanggal --}}
    <p>No. SPM: {{ $preorder->no_surat }}</p>
    <p>Tanggal: {{ \Carbon\Carbon::parse($preorder->order_date)->format('d-m-Y') }}</p>
    <br>
    <p> Bersama ini kami sampaikan penawaran harga atas produk : </p>

    <table class="product-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Merek</th>
                <th>Satuan</th>
                <th>NIE</th>
                <th>Harga</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
                @php
                    $produk = $item->produk;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $produk->barang->nama ?? '-' }}</td>
                    <td>{{ $produk->merek ?? '-' }}</td>
                    <td>{{ $produk->satuan->name ?? '-' }}</td>
                    <td>{{ $produk->nie ?? '-' }}</td>
                    <td>Rp {{ number_format($item->harga_jual_satuan ?? 0, 0, ',', '.') }}</td>
                    <td>Mohon lampirkan spek dan foto produk</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <p>Demikian Surat Penawaran ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih.</p>
    <div class="Direct">
        <h2>Direktur</h2>
        <br><br>
        <h2>Srie Ninawati, SE</h2>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y') }}
    </div>

</body>

</html>
