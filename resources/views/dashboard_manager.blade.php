@extends('layouts.app')

@section('content')
    <div id="dashboard-manager" class="opacity-0 translate-y-3 transition-all">
        {{-- 🔹 Pesan Sambutan --}}
        @if (session('welcome_message'))
            @php $msg = session('welcome_message'); @endphp
            <div class="alert border-0 text-white shadow-sm animate-fade-in mb-4"
                style="background: linear-gradient(90deg, {{ $msg['color'] }} 0%, {{ $msg['color'] }}EE 70%, #22C55E 100%);">
                <strong>{{ $msg['icon'] }}</strong> {!! $msg['text'] !!}
            </div>
        @endif

        {{-- 🔹 Judul --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-theme mb-1">📈 Dashboard Manager</h3>
                <p class="text-muted mb-0">Pantau stok dan performa transaksi inventory ANAMTA secara real-time.</p>
            </div>
        </div>

        {{-- 🔹 Statistik Cepat --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-theme border-0 text-white h-100" style="background: var(--theme-color);">
                    <div class="card-body">
                        <h6 class="fw-semibold">📦 Total Barang</h6>
                        <h2 class="fw-bold mt-2">{{ $items->count() }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-theme border-0 text-white h-100"
                    style="background: linear-gradient(135deg, var(--theme-color), #22C55E);">
                    <div class="card-body">
                        <h6 class="fw-semibold">⬆️ Transaksi Masuk</h6>
                        <h2 class="fw-bold mt-2">{{ \App\Models\InTransaction::count() }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-theme border-0 text-white h-100"
                    style="background: linear-gradient(135deg, var(--theme-color), #16A34A);">
                    <div class="card-body">
                        <h6 class="fw-semibold">⬇️ Transaksi Keluar</h6>
                        <h2 class="fw-bold mt-2">{{ \App\Models\OutTransaction::count() }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Grafik Visual Interaktif --}}
        <div class="row g-4 mb-5">
            {{-- Grafik Batang --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
                        <i class="bi bi-bar-chart-fill me-2"></i> Stok per Kategori
                    </div>
                    <div class="card-body">
                        <canvas id="stockChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            {{-- Grafik Pie --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
                        <i class="bi bi-pie-chart-fill me-2"></i> Rasio Transaksi
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- Grafik Line --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
                        <i class="bi bi-graph-up-arrow me-2"></i> Tren Distribusi Mingguan
                    </div>
                    <div class="card-body">
                        <canvas id="lineChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Notifikasi Terbaru --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header text-white fw-semibold" style="background: var(--theme-color);">
                <i class="bi bi-bell-fill me-2"></i> Notifikasi Terbaru
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($notifications as $n)
                    @php
                        $icon = $n->icon;
                        $level = $n->level; // danger, warning, info, secondary
                        $label = $n->label; // Habis/Menipis/Transaksi Masuk/Keluar/Umum
                        $color = match($level) {
                            'danger' => 'text-danger',
                            'warning' => 'text-warning',
                            'info' => 'text-info',
                            default => 'text-secondary'
                        };
                        $badgeClass = match($level) {
                            'danger' => 'bg-danger-subtle text-danger',
                            'warning' => 'bg-warning-subtle text-warning',
                            'info' => 'bg-info-subtle text-info',
                            default => 'bg-secondary-subtle text-secondary',
                        };
                    @endphp
                    <li class="list-group-item d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi {{ $icon }} me-2 {{ $color }}"></i>
                            <span class="badge {{ $badgeClass }} me-2">{{ $label }}</span>
                            {{ $n->message }}
                        </div>
                        <small class="text-muted">{{ optional($n->notified_at)->format('d M Y H:i') ?? $n->created_at->format('d M Y H:i') }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center">Tidak ada notifikasi saat ini.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- 🔸 Script Grafik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const d = document.getElementById('dashboard-manager');
            setTimeout(() => d.classList.replace('opacity-0', 'opacity-100'), 150);

            const themeColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-color').trim();

            // === Data dari Controller ===
            const categories = @json($chartCategories);
            const stockData = @json($chartStocks);
            const groupLabels = @json($groupLabels);
            const groupTotals = @json($groupTotals);
            const dateLabels = @json($dateLabels);
            const dateTotals = @json($dateTotals);

            // === Grafik Batang (Stok per Kategori) ===
            new Chart(document.getElementById('stockChart'), {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Total Stok',
                        data: stockData,
                        backgroundColor: themeColor + "bb",
                        borderColor: themeColor,
                        borderWidth: 1.2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // === Grafik Pie (Transaksi Masuk vs Keluar) ===
            new Chart(document.getElementById('pieChart'), {
                type: 'pie',
                data: {
                    labels: ['Masuk', 'Keluar'],
                    datasets: [{
                        data: [{{ \App\Models\InTransaction::count() }},
                            {{ \App\Models\OutTransaction::count() }}
                        ],
                        backgroundColor: [themeColor, '#EF4444'],
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // === Grafik Line (Tren Distribusi Mingguan) ===
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: dateLabels,
                    datasets: [{
                        label: 'Total Barang Keluar',
                        data: dateTotals,
                        borderColor: '#22C55E',
                        backgroundColor: 'rgba(34,197,94,0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

    {{-- 🔹 Animasi --}}
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
    </style>
@endsection
