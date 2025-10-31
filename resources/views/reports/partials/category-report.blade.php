<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('reports.category.pdf') }}" class="btn btn-outline-danger me-2">
        <i class="bi bi-file-earmark-pdf"></i> PDF
    </a>
    <a href="{{ route('reports.category.excel') }}" class="btn btn-outline-success">
        <i class="bi bi-file-earmark-excel"></i> Excel
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-info">
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Total Barang</th>
                    <th>Total Stok</th>
                    <th>Stok Minimum</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categoryReports as $c)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $c->category->name ?? '-' }}</td>
                        <td>{{ $c->total_barang }}</td>
                        <td class="text-success fw-bold">{{ $c->total_stok }}</td>
                        <td>{{ $c->total_min_stok }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
