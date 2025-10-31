<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <strong>Stok Barang per Kategori</strong>
        <div>
            <a href="{{ route('reports.category.pdf') }}" class="btn btn-light btn-sm me-2">
                <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
            </a>
            <a href="{{ route('reports.category.excel') }}" class="btn btn-light btn-sm">
                <i class="bi bi-file-earmark-excel text-success"></i> Excel
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- 📊 Tabel Stok per Kategori --}}
        <div class="table-responsive">
            <table id="categoryTable" class="table table-bordered table-striped align-middle w-100">
                <thead class="table-info">
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Jumlah Barang</th>
                        <th>Total Stok</th>
                        <th>Minimum Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categoryReports as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->category->name ?? '-' }}</td>
                            <td>{{ $row->total_barang }}</td>
                            <td><strong class="text-success">{{ $row->total_stok }}</strong></td>
                            <td>{{ $row->total_min_stok }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <small class="text-muted d-block mt-2">
            Data menampilkan rekap stok barang berdasarkan kategori.
        </small>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (!$.fn.DataTable.isDataTable('#categoryTable')) {
                $('#categoryTable').DataTable({
                    pageLength: 10,
                    order: [
                        [1, 'asc']
                    ],
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                        paginate: {
                            previous: "Sebelumnya",
                            next: "Berikutnya"
                        }
                    }
                });
            }

            // Auto reinit ketika tab stok diklik
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr("href");
                if (target === "#stok") {
                    $('#categoryTable').DataTable().columns.adjust().draw();
                }
            });
        });
    </script>
@endpush
