@extends('layouts.app')

@section('content')
    <div id="dashboard-staff" class="opacity-0 translate-y-2 transition-all">
        {{-- ðŸ”¹ Pesan Sambutan --}}
        @if (session('welcome_message'))
            @php $msg = session('welcome_message'); @endphp
            <div class="alert border-0 text-white shadow-sm animate-fade-in mb-4"
                style="background: linear-gradient(90deg, {{ $msg['color'] }} 0%, {{ $msg['color'] }}EE 70%, #FACC15 100%);">
                <strong>{{ $msg['icon'] }}</strong> {!! $msg['text'] !!}
            </div>
        @endif

        {{-- ðŸ”¹ Judul --}}
        <div class="mb-4">
            <h3 class="fw-bold text-theme mb-1">ðŸ‘· Dashboard Staff</h3>
            <p class="text-muted mb-0">Pantau aktivitas transaksi dan status stok barang yang Anda kelola.</p>
        </div>

        {{-- ðŸ”¹ Statistik Pribadi --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-theme border-0 text-white h-100"
                    style="background: linear-gradient(135deg, var(--theme-color), #EAB308);">
                    <div class="card-body text-center">
                        <h6 class="fw-semibold">â¬†ï¸ Transaksi Masuk oleh Anda</h6>
                        <h2 class="fw-bold mt-2">{{ $inCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-theme border-0 text-white h-100"
                    style="background: linear-gradient(135deg, var(--theme-color), #F59E0B);">
                    <div class="card-body text-center">
                        <h6 class="fw-semibold">â¬‡ï¸ Transaksi Keluar oleh Anda</h6>
                        <h2 class="fw-bold mt-2">{{ $outCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- ðŸ”¹ Grafik Donat --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
                <i class="bi bi-pie-chart-fill me-2"></i> Proporsi Transaksi Anda
            </div>
            <div class="card-body">
                <canvas id="staffChart" height="250"></canvas>
            </div>
        </div>

        {{-- ðŸ”¹ Progress Bar Stok Barang --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
                <i class="bi bi-box-seam me-2"></i> Top 5 Barang dengan Stok Menipis
            </div>
            <div class="card-body">
                @forelse (($lowStocks ?? collect()) as $item)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold text-dark">{{ $item->name }}</span>
                            <small class="text-muted">{{ $item->stock }}/{{ $item->min_stock }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $percent = $item->min_stock > 0 ? min(100, ($item->stock / max(1, $item->min_stock)) * 100) : 100;
                                $color = $percent < 50 ? '#dc3545' : ($percent < 80 ? '#ffc107' : '#16a34a');
                            @endphp
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $percent }}%; background: {{ $color }};"
                                aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">Tidak ada data stok barang yang Anda kelola.</div>
                @endforelse
            </div>
        </div>

        {{-- ðŸ”¹ Notifikasi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
                <i class="bi bi-bell-fill me-2"></i> Notifikasi Terbaru
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($notifications as $n)
                    <li class="list-group-item d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-bell me-2 text-theme"></i>
                            {{ $n->message }}
                        </div>
                        <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center">Belum ada notifikasi.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ðŸ”¸ Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('dashboard-staff');
            setTimeout(() => container.classList.replace('opacity-0', 'opacity-100'), 100);

            const ctx = document.getElementById('staffChart');
            const themeColor = getComputedStyle(document.documentElement)
                .getPropertyValue('--theme-color').trim();

            const dataIn = {{ $inCount ?? 0 }};
            const dataOut = {{ $outCount ?? 0 }};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Transaksi Masuk', 'Transaksi Keluar'],
                    datasets: [{
                        data: [dataIn, dataOut],
                        backgroundColor: [themeColor, '#EF4444'],
                        hoverBackgroundColor: ['#fbbf24', themeColor],
                        borderWidth: 2,
                        cutout: '65%'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#555',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Proporsi Transaksi Masuk vs Keluar',
                            color: themeColor,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            // ðŸ”” Toast Notification
            // (toast legacy dihapus)
        });

        // ðŸ”” Aktifkan toast otomatis jika session successToast ada
        @if (false)
            const toastEl = document.getElementById('successToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                setTimeout(() => toast.show(), 600);
            }
        @endif
    </script>

    {{-- ðŸ”” Toast Notification Template --}}
    @if (false)
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="successToast" class="toast text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        âœ… {{ session('successToast') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    {{-- ðŸ”¹ Efek Animasi --}}
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        .progress {
            background-color: #f1f1f1;
            border-radius: 10px;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }
    </style>

    {{-- Transaksi Terbaru Anda --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
            <i class="bi bi-clock-history me-2"></i> Transaksi Terbaru Anda
        </div>
        <ul class="list-group list-group-flush">
            @forelse (($recentTransactions ?? collect()) as $tx)
                @php
                    $isIn = ($tx['type'] ?? 'in') === 'in';
                    $icon = $isIn ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle';
                    $iconColor = $isIn ? 'text-success' : 'text-danger';
                    try { $d = \Carbon\Carbon::parse($tx['date']); } catch (\Throwable $e) { $d = now(); }
                @endphp
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <div class="me-3">
                        <i class="bi {{ $icon }} me-2 {{ $iconColor }}"></i>
                        <strong>{{ $isIn ? 'Masuk' : 'Keluar' }}</strong>:
                        {{ $tx['item_name'] ?? '-' }}
                        <small class="text-muted">x{{ $tx['qty'] ?? 0 }}</small>
                        @if (!$isIn && !empty($tx['receiver']))
                            <small class="text-muted">â€¢ Penerima: {{ $tx['receiver'] }}</small>
                        @endif
                        @if (!empty($tx['kode_grup']))
                            <small class="text-muted">â€¢ Grup: {{ $tx['kode_grup'] }}</small>
                        @endif
                    </div>
                    <small class="text-muted">{{ $d->format('d M Y') }}</small>
                </li>
            @empty
                <li class="list-group-item text-muted text-center">Belum ada transaksi terbaru.</li>
            @endforelse
        </ul>
    </div>

    {{-- Riwayat Transaksi Anda (10 terakhir) --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
            <i class="bi bi-table me-2"></i> Riwayat Transaksi Anda
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 110px;">Tanggal</th>
                        <th style="width: 90px;">Jenis</th>
                        <th>Barang</th>
                        <th style="width: 80px;" class="text-end">Qty</th>
                        <th style="width: 160px;">Penerima</th>
                        <th style="width: 120px;">Grup</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($historyTransactions ?? collect()) as $tx)
                        @php
                            $isIn = ($tx['type'] ?? 'in') === 'in';
                            try { $d = \Carbon\Carbon::parse($tx['date']); } catch (\Throwable $e) { $d = now(); }
                        @endphp
                        <tr>
                            <td><span class="text-nowrap">{{ $d->format('d M Y') }}</span></td>
                            <td>
                                <span class="badge {{ $isIn ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                    {{ $isIn ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td>{{ $tx['item_name'] ?? '-' }}</td>
                            <td class="text-end">{{ $tx['qty'] ?? 0 }}</td>
                            <td>{{ $tx['receiver'] ?? '-' }}</td>
                            <td>{{ $tx['kode_grup'] ?? '-' }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 260px;">
                                    {{ $tx['note'] ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('in-transactions.index') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-arrow-down-circle"></i> Lihat Transaksi Masuk
            </a>
            <a href="{{ route('out-transactions.index') }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-arrow-up-circle"></i> Lihat Transaksi Keluar
            </a>
        </div>
    </div>
@endsection