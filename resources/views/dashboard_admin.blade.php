@extends('layouts.app')

@section('content')
    <div id="dashboard-admin" class="opacity-0 translate-y-3 transition-all">
        {{-- 🔹 Pesan Sambutan --}}
        @if (session('welcome_message'))
            @php $msg = session('welcome_message'); @endphp
            <div class="alert border-0 text-white shadow-sm animate-fade-in"
                style="background: linear-gradient(90deg, {{ $msg['color'] }} 0%, {{ $msg['color'] }}EE 70%, #E5B80B 100%);">
                <strong>{{ $msg['icon'] }}</strong> {!! $msg['text'] !!}
            </div>
        @endif

        {{-- 🔹 Judul Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-primary mb-1">
                    <span class="iconify" data-icon="mdi:view-dashboard-outline"></span> Dashboard Admin
                </h4>
                <p class="text-muted mb-0">Sistem Informasi Inventory Perlengkapan Operasional PT Annur Maknah Wisata</p>
            </div>
        </div>

        {{-- 🔹 Statistik Utama --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-lg text-white border-0"
                    style="background: linear-gradient(135deg, #002B5B, #003B7A);">
                    <div class="card-body">
                        <h6 class="fw-semibold">👥 Total Pengguna</h6>
                        <h2 class="fw-bold mt-2">{{ $userCount ?? 3 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg text-white border-0"
                    style="background: linear-gradient(135deg, #0F766E, #22C55E);">
                    <div class="card-body">
                        <h6 class="fw-semibold">📦 Total Barang</h6>
                        <h2 class="fw-bold mt-2">{{ $totalItems ?? 30 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg text-white border-0"
                    style="background: linear-gradient(135deg, #EAB308, #CA8A04);">
                    <div class="card-body">
                        <h6 class="fw-semibold">⬆️ Transaksi Masuk</h6>
                        <h2 class="fw-bold mt-2">{{ $inCount ?? 48 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg text-white border-0"
                    style="background: linear-gradient(135deg, #DC2626, #EF4444);">
                    <div class="card-body">
                        <h6 class="fw-semibold">⬇️ Transaksi Keluar</h6>
                        <h2 class="fw-bold mt-2">{{ $outCount ?? 42 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Grafik Aktivitas --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-primary text-white fw-semibold">
                <span class="iconify" data-icon="mdi:chart-bar"></span> Grafik Transaksi Bulanan
            </div>
            <div class="card-body">
                <canvas id="transactionChart" height="100"></canvas>
            </div>
        </div>

        {{-- 🔹 Tabel Barang Stok Menipis --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-warning fw-bold text-dark">
                <span class="iconify" data-icon="mdi:alert"></span> Barang dengan Stok Menipis
            </div>
            <div class="card-body p-0">
                @if ($lowStocks->count())
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Minimum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStocks as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $item->stock }}</span>
                                    </td>
                                    <td>{{ $item->min_stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-3 text-center text-muted">Tidak ada barang dengan stok menipis.</div>
                @endif
            </div>
        </div>

        {{-- 🔹 Tombol Cepat --}}
        <div class="d-flex gap-3">
            <a href="{{ route('activity.index') }}" class="btn btn-outline-primary">
                <span class="iconify" data-icon="mdi:history"></span> Lihat Activity Log
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-warning">
                <span class="iconify" data-icon="mdi:file-chart"></span> Lihat Laporan
            </a>
        </div>
    </div>

    {{-- 🔸 Transisi & Grafik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Transisi Halus
            const d = document.getElementById('dashboard-admin');
            setTimeout(() => d.classList.replace('opacity-0', 'opacity-100'), 100);

            // Data Grafik Dummy (bisa disesuaikan dari controller)
            const ctx = document.getElementById('transactionChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ],
                    datasets: [{
                            label: 'Transaksi Masuk',
                            backgroundColor: '#22C55E',
                            data: [12, 15, 9, 17, 20, 13, 14, 10, 19, 18, 21, 25],
                            borderRadius: 6
                        },
                        {
                            label: 'Transaksi Keluar',
                            backgroundColor: '#EF4444',
                            data: [10, 12, 7, 15, 18, 11, 12, 8, 14, 16, 19, 22],
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
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

    <style>
        .animate-fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

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
    </style>
@endsection
