<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <strong>Distribusi Barang per Keberangkatan</strong>
        <div>
            <a href="{{ route('reports.distribusi.pdf') }}" data-export-distribusi
                data-base="{{ route('reports.distribusi.pdf') }}" class="btn btn-light btn-sm me-2">
                <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
            </a>
            <a href="{{ route('reports.distribusi.excel') }}" data-export-distribusi
                data-base="{{ route('reports.distribusi.excel') }}" class="btn btn-light btn-sm">
                <i class="bi bi-file-earmark-excel text-success"></i> Excel
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- 🔍 Filter Form --}}
        <form id="distribusiFilterForm" class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode Grup</label>
                <select name="kode_grup" class="form-select">
                    <option value="">Semua Grup</option>
                    @foreach ($kodeGrupList as $g)
                        <option value="{{ $g }}" @selected(request('kode_grup') == $g)>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100" type="submit">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary w-100" type="button" id="resetDistribusi">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </button>
            </div>
        </form>

        {{-- 📊 Tabel Distribusi --}}
        <div class="table-responsive">
            <table id="distribusiTable" class="table table-bordered table-striped align-middle w-100">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Grup</th>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Jumlah Keluar</th>
                        <th>Penerima</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
            </table>
        </div>

        <small class="text-muted d-block mt-2">
            Auto refresh setiap 30 detik tanpa reload penuh.
        </small>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let tableDistribusi;

            function initDistribusiTable() {
                if ($.fn.DataTable.isDataTable('#distribusiTable')) {
                    tableDistribusi.ajax.reload();
                    return;
                }

                tableDistribusi = $('#distribusiTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('reports.distribusi.data') }}",
                        data: function(d) {
                            const f = $('#distribusiFilterForm')[0];
                            d.date = f.date.value;
                            d.kode_grup = f.kode_grup.value;
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
                            data: 'kode_grup'
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
                            data: 'unit'
                        },
                        {
                            data: 'qty',
                            className: 'text-end'
                        },
                        {
                            data: 'receiver'
                        },
                        {
                            data: 'note'
                        },
                    ],
                    order: [
                        [1, 'desc']
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

            // ✅ Inisialisasi langsung saat tab pertama kali tampil
            initDistribusiTable();

            // ✅ Filter manual
            $('#distribusiFilterForm').on('submit', function(e) {
                e.preventDefault();
                tableDistribusi.ajax.reload();
            });

            // ✅ Reset form
            $('#resetDistribusi').on('click', function() {
                $('#distribusiFilterForm')[0].reset();
                tableDistribusi.ajax.reload();
            });

            // ✅ Auto-refresh tiap 30 detik
            setInterval(() => tableDistribusi.ajax.reload(null, false), 30000);

            // ✅ Reinit bila tab dibuka kembali
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr("href");
                if (target === "#distribusi") initDistribusiTable();
            });

            // ✅ Sinkronkan filter saat unduh
            document.querySelectorAll('[data-export-distribusi]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = document.getElementById('distribusiFilterForm');
                    const params = new URLSearchParams(new FormData(form));
                    // buang parameter kosong supaya URL bersih
                    [...params.keys()].forEach(key => {
                        if (!params.get(key)) params.delete(key);
                    });
                    const queryString = params.toString();
                    this.href = queryString ? `${this.dataset.base}?${queryString}` : this.dataset
                        .base;
                });
            });
        });
    </script>
@endpush
