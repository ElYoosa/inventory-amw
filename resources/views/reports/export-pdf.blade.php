<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h3 {
            text-align: center;
            color: #1a73e8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #e8f0fe;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            text-align: right;
            color: #555;
        }
    </style>
</head>

<body>
    <h3>Laporan Distribusi Barang Keberangkatan AMW</h3>
    <p><strong>Kode Grup:</strong> {{ $kodeGrup ?? 'Semua Grup' }} | <strong>Tanggal:</strong>
        {{ $tanggal ?? 'Semua Tanggal' }}</p>

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
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}</td>
                    <td>{{ $r->kode_grup ?? '-' }}</td>
                    <td>{{ $r->item->name ?? '-' }}</td>
                    <td>{{ $r->item->category->name ?? '-' }}</td>
                    <td>{{ $r->qty }}</td>
                    <td>{{ $r->receiver ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">Tidak ada data untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generated at: {{ $generatedAt }}</div>
</body>

</html>
