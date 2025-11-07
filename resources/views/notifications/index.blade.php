@extends('layouts.app')

@section('content')
    <div class="container-fluid p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-success mb-1">
                    <i data-lucide="bell"></i> Notifikasi Barang
                </h4>
                <p class="text-muted mb-0">Pantau pemberitahuan terbaru dari sistem inventory.</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                {{-- Dropdown Filter Status & Jenis --}}
                <form action="{{ route('notifications.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="new" {{ $status === 'new' ? 'selected' : '' }}>Baru</option>
                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Dibaca</option>
                    </select>
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="stock_empty" {{ ($type ?? '') === 'stock_empty' ? 'selected' : '' }}>Habis</option>
                        <option value="stock_low" {{ ($type ?? '') === 'stock_low' ? 'selected' : '' }}>Menipis</option>
                        <option value="in_transaction" {{ ($type ?? '') === 'in_transaction' ? 'selected' : '' }}>Transaksi Masuk</option>
                        <option value="out_transaction" {{ ($type ?? '') === 'out_transaction' ? 'selected' : '' }}>Transaksi Keluar</option>
                        <option value="general" {{ ($type ?? '') === 'general' ? 'selected' : '' }}>Umum</option>
                    </select>
                </form>

                {{-- Tombol Tandai Semua Dibaca (AJAX) --}}
                <button id="btnMarkAll" class="btn btn-sm btn-outline-success">
                    <i data-lucide="check-circle"></i> Tandai Semua Dibaca
                </button>
                <button id="btnDeleteRead" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i> Hapus Dibaca
                </button>
                <button id="btnDeleteAll" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash3"></i> Hapus Semua
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">Terakhir disegarkan: <span id="lastRefreshed">-</span></small>
        </div>
        @if ($notifications->count())
            <div class="table-responsive shadow-sm rounded-3 overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Jenis</th>
                            <th>Pesan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="notifRows">
                        @include('notifications._rows', ['notifications' => $notifications])
                    </tbody>
            </table>
        </div>

        <div class="mt-4">
                <div id="notifPagination">{{ $notifications->links('vendor.pagination.simple-bootstrap-5') }}</div>
        </div>
        @else
            <div class="alert alert-info text-center shadow-sm">
                <i data-lucide="inbox"></i> Tidak ada notifikasi yang ditemukan.
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const badge = document.getElementById('notifBadge');
            const rowsEl = document.getElementById('notifRows');
            const pagEl = document.getElementById('notifPagination');
            const form = document.querySelector('form[action="{{ route('notifications.index') }}"]');
            const lastRef = document.getElementById('lastRefreshed');

            function updateBadge(count) {
                if (!badge) return;
                badge.textContent = count;
                if (count > 0) badge.classList.remove('d-none'); else badge.classList.add('d-none');
            }

            async function fetchCount() {
                try {
                    const res = await fetch('{{ route('notifications.count') }}', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    updateBadge(Number(data.count || 0));
                } catch (e) {}
            }

            function setRefreshedNow() {
                if (!lastRef) return;
                try {
                    lastRef.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
                } catch { lastRef.textContent = new Date().toLocaleTimeString(); }
            }

            function currentQuery() {
                const params = new URLSearchParams(new FormData(form));
                const urlPage = new URLSearchParams(window.location.search).get('page');
                if (urlPage) params.set('page', urlPage);
                return params.toString();
            }

            async function refreshListIfNeeded(force = false) {
                try {
                    const qs = currentQuery();
                    const url = `{{ route('notifications.list') }}?${qs}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (force || (rowsEl && data.rows)) {
                        rowsEl.innerHTML = data.rows;
                    }
                    if (pagEl && data.pagination) {
                        pagEl.innerHTML = data.pagination;
                    }
                    if (typeof data.count_new !== 'undefined') updateBadge(Number(data.count_new));
                    bindRowActions();
                    bindPagination();
                    setRefreshedNow();
                } catch (e) {}
            }

            async function markRead(id, rowEl) {
                try {
                    const res = await fetch(`{{ url('/notifications') }}/${id}/mark-read`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    await refreshListIfNeeded(true);
                    if (typeof data.count !== 'undefined') updateBadge(Number(data.count));
                    if (window.showToast) showToast('success', 'Notifikasi ditandai sebagai dibaca');
                } catch (e) {}
            }

            async function deleteNotif(id) {
                try {
                    const res = await fetch(`{{ url('/notifications') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    await refreshListIfNeeded(true);
                    if (typeof data.count !== 'undefined') updateBadge(Number(data.count));
                    if (window.showToast) showToast('success', 'Notifikasi dihapus');
                } catch (e) {}
            }

            function bindRowActions() {
                document.querySelectorAll('.btn-mark-read').forEach(btn => {
                    btn.onclick = () => {
                        const id = btn.getAttribute('data-id');
                        const rowEl = btn.closest('tr');
                        markRead(id, rowEl);
                    };
                });
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.onclick = () => {
                        const id = btn.getAttribute('data-id');
                        if (confirm('Hapus notifikasi ini?')) deleteNotif(id);
                    };
                });
            }

            function getPageFromHref(href) {
                try {
                    const u = new URL(href, window.location.origin);
                    return u.searchParams.get('page');
                } catch { return null; }
            }

            function buildQueryWithPage(page) {
                const params = new URLSearchParams(new FormData(form));
                if (page) params.set('page', page); else params.delete('page');
                return params.toString();
            }

            async function loadPageByHref(href) {
                const page = getPageFromHref(href);
                const qs = buildQueryWithPage(page);
                const url = `{{ route('notifications.list') }}?${qs}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (rowsEl && data.rows) rowsEl.innerHTML = data.rows;
                if (pagEl && data.pagination) pagEl.innerHTML = data.pagination;
                if (typeof data.count_new !== 'undefined') updateBadge(Number(data.count_new));
                // Update URL agar bisa di-refresh/back-forward
                const newUrl = `${window.location.pathname}?${qs}`;
                window.history.pushState({ page }, '', newUrl);
                bindRowActions();
                bindPagination();
                setRefreshedNow();
            }

            function bindPagination() {
                if (!pagEl) return;
                // Delegate clicks on pagination links
                pagEl.querySelectorAll('a.page-link, .pagination a').forEach(a => {
                    a.onclick = (ev) => {
                        ev.preventDefault();
                        const href = a.getAttribute('href');
                        if (href) loadPageByHref(href);
                    };
                });
            }

            bindRowActions();
            bindPagination();

            // Intercept filter form changes to load via AJAX
            form?.addEventListener('submit', (ev) => { ev.preventDefault(); });
            form?.addEventListener('change', async () => {
                const params = new URLSearchParams(new FormData(form));
                params.delete('page'); // reset ke halaman 1 saat filter berubah
                const url = `{{ route('notifications.list') }}?${params.toString()}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (rowsEl && data.rows) rowsEl.innerHTML = data.rows;
                if (pagEl && data.pagination) pagEl.innerHTML = data.pagination;
                if (typeof data.count_new !== 'undefined') updateBadge(Number(data.count_new));
                // Update URL tanpa reload
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
                bindRowActions();
                bindPagination();
                setRefreshedNow();
            });

            // Handle back/forward navigation
            window.addEventListener('popstate', () => {
                refreshListIfNeeded(true);
            });

            document.getElementById('btnMarkAll')?.addEventListener('click', async () => {
                try {
                    const res = await fetch('{{ route('notifications.markAllRead') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    await refreshListIfNeeded(true);
                    if (typeof data.count !== 'undefined') updateBadge(Number(data.count));
                    if (window.showToast && data.message) showToast('success', data.message);
                } catch (e) {}
            });

            document.getElementById('btnDeleteRead')?.addEventListener('click', async () => {
                if (!confirm('Hapus semua notifikasi yang sudah dibaca?')) return;
                try {
                    const res = await fetch('{{ route('notifications.destroyRead') }}', {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    await refreshListIfNeeded(true);
                    if (typeof data.count !== 'undefined') updateBadge(Number(data.count));
                    if (window.showToast) showToast('success', `Berhasil hapus ${data.deleted ?? 0} notifikasi dibaca`);
                } catch (e) {}
            });

            document.getElementById('btnDeleteAll')?.addEventListener('click', async () => {
                if (!confirm('Hapus semua notifikasi?')) return;
                try {
                    const res = await fetch('{{ route('notifications.destroyAll') }}', {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    await refreshListIfNeeded(true);
                    if (typeof data.count !== 'undefined') updateBadge(Number(data.count));
                    if (window.showToast) showToast('success', 'Semua notifikasi dihapus');
                } catch (e) {}
            });

            fetchCount();
            setRefreshedNow();
            // Auto-refresh ringan: badge tiap 10s, tabel tiap 30s
            setInterval(fetchCount, 10000);
            setInterval(() => refreshListIfNeeded(false), 30000);
        });
    </script>
@endpush
