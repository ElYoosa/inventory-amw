<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Real-Time</title>
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
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #e8f5e9;
        }
    </style>
</head>

<body>
    <div class="kop">
        <img src="{{ public_path('images/logo-amw.png') }}" alt="Logo">
        <h2>PT ANNUR MAKNAH WISATA</h2>
        <p>Jl. KH Abdullah Syafei No.50 F 12, Tebet, Jakarta Selatan 12840 | WhatsApp: (+62) 821-1515-3335</p>
        <p>Email: umroh.anamta@gmail.com</p>
    </div>

    <h4 style="text-align:center;">LAPORAN TRANSAKSI REAL-TIME</h4>
    <p style="text-align:center;">
        @if (!empty($filter['start_date']))
            Mulai: <strong>{{ \Carbon\Carbon::parse($filter['start_date'])->format('d M Y') }}</strong>
        @endif
        @if (!empty($filter['end_date']))
            — Sampai: <strong>{{ \Carbon\Carbon::parse($filter['end_date'])->format('d M Y') }}</strong>
        @endif
        @if (!empty($filter['type']))
            — Jenis: <strong>{{ $filter['type'] === 'in' ? 'Masuk' : 'Keluar' }}</strong>
        @endif
        <br>Dicetak: {{ $generated_at }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Penerima / Pengirim</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $i => $t)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ optional($t->date)->format('d M Y') ?? '-' }}</td>
                    <td>{{ $t->type === 'in' ? 'Masuk' : 'Keluar' }}</td>
                    <td>{{ $t->item->code ?? '-' }}</td>
                    <td>{{ $t->item->name ?? '-' }}</td>
                    <td>{{ $t->item->category->name ?? '-' }}</td>
                    <td>{{ $t->qty }}</td>
                    <td>
                        @if ($t->type === 'in')
                            {{ $t->sender ?? '-' }}
                        @else
                            {{ $t->receiver ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $t->note ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
