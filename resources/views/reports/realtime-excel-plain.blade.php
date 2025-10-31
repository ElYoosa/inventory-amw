<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tipe</th>
            <th>Tanggal</th>
            <th>Kode Grup</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Qty</th>
            <th>Penerima</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $i => $tx)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $tx->type ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($tx->date ?? null)->format('d M Y') ?? '-' }}</td>
                <td>{{ $tx->kode_grup ?? '-' }}</td>
                <td>{{ $tx->item->name ?? '-' }}</td>
                <td>{{ $tx->item->category->name ?? '-' }}</td>
                <td>{{ $tx->qty ?? '-' }}</td>
                <td>{{ $tx->receiver ?? '-' }}</td>
                <td>{{ $tx->note ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
