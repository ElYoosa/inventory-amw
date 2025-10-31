<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang per Kategori</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
            padding-bottom: 6px;
        }

        .kop img {
            float: left;
            width: 65px;
            margin-right: 10px;
        }

        .kop h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .kop p {
            margin: 0;
            font-size: 12px;
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
        }
    </style>
</head>

<body>
    <div class="kop">
        <img src="{{ public_path('images/logo-amw.png') }}" alt="Logo">
        <h2>PT ANNUR MAKNAH WISATA</h2>
        <p>Jl. KH Abdullah Syafei No.50 F12, Bukit Duri, Tebet, Jakarta Selatan 12840</p>
        <p>WhatsApp: (+62) 821-1515-3335 | Email: umroh.anamta@gmail.com</p>
    </div>

    <h4 style="text-align:center;">LAPORAN STOK BARANG PER KATEGORI</h4>
    <p style="text-align:center;">Dicetak: {{ $generated_at }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Stok</th>
                <th>Stok Minimum</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $i->category->name ?? '-' }}</td>
                    <td>{{ $i->name }}</td>
                    <td>{{ $i->unit }}</td>
                    <td>{{ $i->stock }}</td>
                    <td>{{ $i->min_stock }}</td>
                    <td>
                        @if ($i->stock <= $i->min_stock)
                            <span style="color:red;">Menipis</span>
                        @else
                            <span style="color:green;">Aman</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
