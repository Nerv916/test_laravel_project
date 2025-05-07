<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Tagihan</title>
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

        td.amount {
            text-align: right;
        }

        th {
            background-color: yellow;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header .logo {
            width: 120px;
        }

        .header .company-info {
            text-align: left;
            flex-grow: 1;
            margin-left: 10px;
        }

        .company-info h2 {
            margin: 0;
            color: #38a300;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .line {
            border-top: 3px solid black;
            margin-top: -10px;
        }

        .sub-header {
            text-align: left;
            margin-top: 10px;
            font-size: 12px;
        }

        .sub-header p {
            margin: 2px 0;
        }

        .sub-header p.bold {
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
        }

        .invoice-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
            text-decoration: underline;
        }

        .total-row .label {
            text-align: right;
            font-weight: bold;
        }

        .total-row .amount {
            text-align: right;
        }

        .highlight {
            background-color: yellow;
            font-weight: bold;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 60px;
        }

        .footer-table td {
            border: none;
            vertical-align: top;
            padding: 0 20px;
        }

        .footer-left {
            text-align: center;
        }

        .footer-right {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('storage/image/pt.png') }}" alt="Foto" width="75">
        <p>
        <div class="company-info">
            <h2>PT. PILAR CITRA SEJATI</h2>
            <p>Komp. Griya Bandung Asri Blok M 5 No. 27B, Bandung</p>
            <p>Telp: (022) 753 9060, Fax: (022) 753 9060</p>
        </div>
    </div>
    <div class="line"></div>


    <div class="sub-header">
        <p>Bandung, {{ date('d F Y') }}</p>
        <p>Kepada Yth.</p>
        <p class="bold">{{ $approved->name_cust }}</p>
        <p>{{ $costumer->alamat ?? '-' }}</p>
        <p>Di Tempat</p>
    </div>

    <h3 style="text-align: center;">FAKTUR TAGIHAN</h3>

    <p><b>Nomor:</b> {{ $approved->no_spo ?? 'Tidak Ada' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Volume</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Harga Setelah Pajak</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($approved->items as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->produk->merek ?? 'Tidak Diketahui' }}</td>
                    <td>{{ $item->stockout }}</td> <!-- Volume dari stockout -->
                    <td>{{ $item->produk->satuan->name ?? '-' }}</td>
                    <td>Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format($item->harga_setelah_pajak, 0, ',', '.') }}</td>
                    <td class="amount">Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="label">Total Tagihan</td>
                <td class="amount">Rp. {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="label">PPN {{ rtrim(rtrim($item->ppn, '0'), '.') }}%</td>
                <td class="amount">Rp. {{ number_format($item->ppntotal, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="label">DPP</td>
                <td class="amount">Rp. {{ number_format($item->dpp, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row highlight">
                <td colspan="6" class="label">Jumlah DPP + PPN {{ rtrim(rtrim($item->ppn, '0'), '.') }}%</td>
                <td class="amount">Rp. {{ number_format($item->totalsemua, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <p class="terbilang">Terbilang <span>{{ ucfirst(terbilang($item->subtotal)) }} </span>rupiah</p>

    <table class="footer-table">
        <tr>
            <td class="footer-left">
                <p>Penerima</p>
                <br><br><br><br>
                <p>(..................................)</p>
            </td>
            <td class="footer-right" style="text-align: center;">
                <p>Yang Menyerahkan,</p>
                <p><b>PT. Pilar Citra Sejati</b></p>
                <br>
                <p><b>Sri Ninawati, SE</b></p>
                <p>Direktur</p>
            </td>
        </tr>
    </table>


</body>

</html>
