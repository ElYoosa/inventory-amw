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

            <div class="d-flex gap-2">
                {{-- Dropdown Filter Status --}}
                <form action="{{ route('notifications.index') }}" method="GET" class="d-flex align-items-center">
                    <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="new" {{ $status === 'new' ? 'selected' : '' }}>Baru</option>
                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Dibaca</option>
                    </select>
                </form>

                {{-- Tombol Tandai Semua Dibaca (AJAX) --}}
                <button id="btnMarkAll" class="btn btn-sm btn-outline-success">
                    <i data-lucide="check-circle"></i> Tandai Semua Dibaca
                </button>
            </div>
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
                    <tbody>
                        @foreach ($notifications as $notif)
                            <tr class="{{ $notif->status === 'new' ? 'table-warning' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($notif->notified_at)->format('d M Y') ?? $notif->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $notif->item->name ?? '-' }}</td>
                                <td>
                                    @php $t = strtolower($notif->title ?? ''); @endphp
                                    @if (str_contains($t, 'habis'))
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-exclamation-octagon-fill"></i> Habis</span>
                                    @elseif (str_contains($t, 'menipis'))
                                        <span class="badge bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Menipis</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-bell-fill"></i> Umum</span>
                                    @endif
                                </td>
                                <td>{{ $notif->message }}</td>
                                <td>
                                    @if ($notif->status === 'new')
                                        <span class="badge bg-warning text-dark">Baru</span>
                                    @else
                                        <span class="badge bg-secondary">Dibaca</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($notif->status === 'new')
                                        <button type="button" class="btn btn-sm btn-outline-success btn-mark-read" data-id="{{ $notif->id }}">
                                            <i data-lucide="check-circle"></i> Tandai Dibaca
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
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

            async function markRead(id, rowEl) {
                try {
                    const res = await fetch(`{{ url('/notifications') }}/${id}/mark-read`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (rowEl) {
                        rowEl.classList.remove('table-warning');
                        const statusCell = rowEl.querySelector('td:nth-child(5)');
                        const actionCell = rowEl.querySelector('td:nth-child(6)');
                        if (statusCell) statusCell.innerHTML = '<span class="badge bg-secondary">Dibaca</span>';
                        if (actionCell) actionCell.innerHTML = '<span class="text-muted small">-</span>';
                    }
                    if (typeof data.count !== 'undefined') updateBadge(Number(data.count));
                } catch (e) {}
            }

            document.querySelectorAll('.btn-mark-read').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const rowEl = btn.closest('tr');
                    markRead(id, rowEl);
                });
            });

            document.getElementById('btnMarkAll')?.addEventListener('click', async () => {
                try {
                    const res = await fetch('{{ route('notifications.markAllRead') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    document.querySelectorAll('table tbody tr').forEach(tr => {
                        tr.classList.remove('table-warning');
                        const statusCell = tr.querySelector('td:nth-child(5)');
                        const actionCell = tr.querySelector('td:nth-child(6)');
                        if (statusCell) statusCell.innerHTML = '<span class="badge bg-secondary">Dibaca</span>';
                        if (actionCell) actionCell.innerHTML = '<span class="text-muted small">-</span>';
                    });
                    if (typeof data.count !== 'undefined') updateBadge(Number(data.count));
                } catch (e) {}
            });

            fetchCount();
        });
    </script>
@endpush
