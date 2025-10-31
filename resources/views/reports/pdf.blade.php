<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi Barang</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
            padding: 10px 0 8px 0;
        }

        .kop img {
            float: left;
            width: 70px;
            margin-left: 20px;
            margin-right: 10px;
        }

        .kop h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop p {
            margin: 2px 0;
            font-size: 11.5px;
        }

        .kop .contact {
            font-size: 10.5px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #e8f5e9;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="kop">
        <img src="{{ public_path('images/logo-amw.png') }}" alt="Logo">
        <h2>PT ANNUR MAKNAH WISATA</h2>
        <p>Jl. KH Abdullah Syafei No.50 F12, RT.12/RW.9, Bukit Duri, Tebet, Jakarta Selatan 12840</p>
        <p class="contact">
            WhatsApp: (+62) 821-1515-3335 | Email: umroh.anamta@gmail.com
        </p>
    </div>

    <h4 style="text-align:center;">LAPORAN DISTRIBUSI BARANG</h4>
    <p style="text-align:center;">
        @if ($filter['kode_grup'])
            Kode Grup: <strong>{{ $filter['kode_grup'] }}</strong> |
        @endif
        @if ($filter['date'])
            Tanggal: <strong>{{ \Carbon\Carbon::parse($filter['date'])->format('d M Y') }}</strong>
        @endif
        <br>Dicetak: {{ $generated_at }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode Grup</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Penerima</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $r->date->format('d M Y') }}</td>
                    <td>{{ $r->kode_grup ?? '-' }}</td>
                    <td>{{ $r->item->name ?? '-' }}</td>
                    <td>{{ $r->item->category->name ?? '-' }}</td>
                    <td>{{ $r->qty }}</td>
                    <td>{{ $r->receiver }}</td>
                    <td>{{ $r->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
