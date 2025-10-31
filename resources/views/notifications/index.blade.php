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
                        <option value="done" {{ $status === 'done' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </form>

                {{-- Tombol Tandai Semua Dibaca --}}
                <form action="{{ route('notifications.markAllRead') }}" method="POST"
                    onsubmit="return confirm('Tandai semua notifikasi baru sebagai dibaca?');">
                    @csrf
                    <button class="btn btn-sm btn-outline-success">
                        <i data-lucide="check-circle"></i> Tandai Semua Dibaca
                    </button>
                </form>
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
                            <th>Pesan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notif)
                            <tr class="{{ $notif->status === 'new' ? 'table-warning' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $notif->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $notif->item->name ?? '-' }}</td>
                                <td>{{ $notif->message }}</td>
                                <td>
                                    @if ($notif->status === 'new')
                                        <span class="badge bg-warning text-dark">Baru</span>
                                    @elseif ($notif->status === 'read')
                                        <span class="badge bg-secondary">Dibaca</span>
                                    @elseif ($notif->status === 'done')
                                        <span class="badge bg-success">Selesai</span>
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
