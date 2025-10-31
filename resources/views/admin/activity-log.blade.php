@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4 animate-fade-in">
        {{-- 🔹 Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold text-primary mb-1">📜 Riwayat Aktivitas Pengguna</h3>
                <p class="text-muted mb-0">Catatan login & logout seluruh pengguna sistem.</p>
            </div>

            {{-- 🔍 Filter --}}
            <form method="GET" class="d-flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari username..."
                    class="form-control form-control-sm border border-primary-subtle shadow-sm">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="form-control form-control-sm border border-primary-subtle shadow-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="form-control form-control-sm border border-primary-subtle shadow-sm">
                <button class="btn btn-sm text-white fw-semibold shadow-sm"
                    style="background-color: #002B5B;">Filter</button>
            </form>
        </div>

        {{-- 🔹 Tabel Log Aktivitas --}}
        <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="text-white" style="background-color: #002B5B;">
                        <tr>
                            <th>Tanggal</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Aktivitas</th>
                            <th>IP Address</th>
                            <th>Device</th>
                            <th>Browser</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-bottom hover-row">
                                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="fw-semibold text-primary">{{ $log->username }}</td>
                                <td class="text-capitalize">
                                    <span
                                        class="badge rounded-pill
                                    @if ($log->role === 'admin') bg-primary
                                    @elseif ($log->role === 'manager') bg-success
                                    @else bg-warning text-dark @endif">
                                        {{ $log->role }}
                                    </span>
                                </td>
                                <td>
                                    @if ($log->activity === 'Login')
                                        <span
                                            class="badge bg-success-subtle text-success fw-semibold px-2 py-1 rounded-3 shadow-sm">
                                            Login ✅
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-danger-subtle text-danger fw-semibold px-2 py-1 rounded-3 shadow-sm">
                                            Logout ⛔
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td>{{ $log->device ?? '-' }}</td>
                                <td>{{ $log->browser ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada aktivitas tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔹 Footer Pagination --}}
            <div class="card-footer bg-light text-center">
                {{ $logs->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    {{-- 🔸 Efek Animasi & Hover --}}
    <style>
        .hover-row:hover {
            background-color: #f8fafc !important;
            transition: background 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        .bg-success-subtle {
            background-color: #E7F6EC;
        }

        .bg-danger-subtle {
            background-color: #FCEAEA;
        }
    </style>
@endsection
