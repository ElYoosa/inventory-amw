<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Realtime</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #e8f5e9;
            font-weight: bold;
        }

        h3 {
            text-align: center;
            margin-bottom: 8px;
        }

        p {
            text-align: center;
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <h3>LAPORAN TRANSAKSI REAL-TIME</h3>

    @if (!empty($meta['start_date']) || !empty($meta['end_date']) || !empty($meta['type']) || !empty($meta['category_id']))
        <p>
            @if (!empty($meta['start_date']))
                Mulai: <strong>{{ \Carbon\Carbon::parse($meta['start_date'])->format('d M Y') }}</strong>
            @endif
            @if (!empty($meta['end_date']))
                — Sampai: <strong>{{ \Carbon\Carbon::parse($meta['end_date'])->format('d M Y') }}</strong>
            @endif
            @if (!empty($meta['type']))
                — Jenis: <strong>{{ $meta['type'] === 'in' ? 'Masuk' : 'Keluar' }}</strong>
            @endif
        </p>
    @endif

    <p>Dicetak: {{ $generated_at }}</p>

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
                    <td>{{ $t->item->id ?? '-' }}</td>
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
