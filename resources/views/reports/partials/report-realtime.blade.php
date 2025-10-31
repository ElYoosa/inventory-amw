<div class="card mt-3 shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-clock-history"></i> Laporan Transaksi Real-Time</h6>
        <div>
            <a href="{{ route('reports.realtime.pdf', request()->query()) }}" class="btn btn-light btn-sm me-2">
                <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
            </a>
            <a href="{{ route('reports.realtime.excel', request()->query()) }}" class="btn btn-light btn-sm">
                <i class="bi bi-file-earmark-excel text-success"></i> Excel
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- 🔍 Filter Form --}}
        <form id="realtimeFilterForm" class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai</label>
                <input type="date" name="end_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <select name="type" class="form-select">
                    <option value="">Semua</option>
                    <option value="in">Masuk</option>
                    <option value="out">Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Terapkan</button>
                <button class="btn btn-secondary" type="button" id="resetRealtime"><i class="bi bi-arrow-repeat"></i>
                    Reset</button>
            </div>
        </form>

        {{-- 📊 Tabel Real-Time --}}
        <div class="table-responsive">
            <table id="realtimeTable" class="table table-striped table-hover align-middle w-100">
                <thead class="table-primary">
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
            </table>
        </div>

        <small class="text-muted d-block mt-2">
            Auto refresh tiap 30 detik tanpa reload halaman.
        </small>
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            const table = $('#realtimeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.realtime.data') }}",
                    data: d => {
                        const f = $('#realtimeFilterForm')[0];
                        d.start_date = f.start_date.value;
                        d.end_date = f.end_date.value;
                        d.type = f.type.value;
                        d.category_id = f.category_id.value;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'item_code'
                    },
                    {
                        data: 'item_name'
                    },
                    {
                        data: 'category_name'
                    },
                    {
                        data: 'qty',
                        className: 'text-end'
                    },
                    {
                        data: 'partner'
                    },
                    {
                        data: 'note'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_-_END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });

            // 🚀 Muat data pertama kali
            table.ajax.reload();

            // Filter & Reset
            $('#realtimeFilterForm').on('submit', e => {
                e.preventDefault();
                table.ajax.reload();
            });
            $('#resetRealtime').on('click', () => {
                $('#realtimeFilterForm')[0].reset();
                table.ajax.reload();
            });

            // ⏱️ Auto-refresh 30 detik tanpa flicker
            setInterval(() => table.ajax.reload(null, false), 30000);
        });
    </script>
@endpush
