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
        @foreach ($reports as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}</td>
                <td>{{ $r->kode_grup ?? '-' }}</td>
                <td>{{ $r->item->name ?? '-' }}</td>
                <td>{{ $r->item->category->name ?? '-' }}</td>
                <td>{{ $r->qty }}</td>
                <td>{{ $r->receiver ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
