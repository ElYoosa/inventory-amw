<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #dbeafe; font-weight: bold; text-align: center; }
        td { vertical-align: middle; }
    </style>
</head>
<body>

    {{-- ==================== HEADER LAPORAN ==================== --}}
    <table style="width:100%; border:none; margin-bottom:10px;">
        <tr>
            <td style="border:none; width:15%;">
                @if (file_exists(public_path('logo-amw.png')))
                    <img src="{{ public_path('logo-amw.png') }}" width="60" alt="Logo">
                @endif
            </td>
            <td style="border:none; text-align:center;">
                <h3 style="margin:0;">PT ANNUR MAKNAH WISATA</h3>
                <small>Jl. Meruya Selatan No. XX, Jakarta Barat</small><br>
                <strong>LAPORAN STOK BARANG</strong>
            </td>
            <td style="border:none; width:15%;"></td>
        </tr>
    </table>

    <p style="text-align:right; margin-top:0;">
        Dicetak: {{ $date ?? now()->translatedFormat('d F Y, H:i') }}
    </p>

    {{-- ==================== TABEL DATA BARANG ==================== --}}
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
                <tr style="background-color: {{ $i->stock <= $i->min_stock ? '#fee2e2' : '#ffffff' }};">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $i->category->name ?? '-' }}</td>
                    <td>{{ $i->name }}</td>
                    <td>{{ $i->unit }}</td>
                    <td>{{ $i->stock }}</td>
                    <td>{{ $i->min_stock }}</td>
                    <td>{{ $i->stock <= $i->min_stock ? 'Menipis' : 'Aman' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <p><i>*Catatan:</i> Baris berwarna merah muda menunjukkan stok yang menipis dan perlu segera dilakukan pengadaan ulang.</p>

</body>
</html>
