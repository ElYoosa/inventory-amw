@extends('layouts.app')

@section('content')
    <div id="dashboard-container" class="opacity-0 translate-y-5 transition-all duration-700 ease-out">
        {{-- 🔹 Pesan Sambutan --}}
        @if (session('welcome_message'))
            @php $msg = session('welcome_message'); @endphp
            <div class="alert text-white shadow-sm border-0 animate-fade-in mb-4"
                style="background: linear-gradient(90deg, {{ $msg['color'] }} 0%, {{ $msg['color'] }}EE 70%, #E5B80B 100%);">
                <strong>{{ $msg['icon'] }}</strong> {!! $msg['text'] !!}
            </div>
        @endif

        {{-- 🔸 Judul --}}
        <h1 class="fw-bold fs-4 text-primary mb-3">📊 Dashboard Sistem Inventory ANAMTA</h1>
        <p class="text-muted mb-4">
            Selamat datang di sistem informasi inventori perlengkapan operasional <strong>PT Annur Maknah Wisata
                (ANAMTA)</strong>.
        </p>

        {{-- 🔹 Statistik Utama --}}
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-lg h-100 text-white"
                    style="background: linear-gradient(135deg, #002B5B, #003B7A);">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">👥 Total Pengguna</h5>
                        <p class="display-6 fw-bold mt-2">{{ $userCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-lg h-100 text-white"
                    style="background: linear-gradient(135deg, #0F766E, #22C55E);">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">📦 Total Barang</h5>
                        <p class="display-6 fw-bold mt-2">{{ $totalItems ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-lg h-100 text-white"
                    style="background: linear-gradient(135deg, #CA8A04, #EAB308);">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">🕑 Log Aktivitas</h5>
                        <p class="display-6 fw-bold mt-2">{{ $activityCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Ringkasan Barang --}}
        <div class="card mt-5 border-0 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">📦 Stok Barang Menipis</div>
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
                                    <td><span class="badge bg-danger">{{ $item->stock }}</span></td>
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
    </div>

    {{-- 🔸 Efek Transisi --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const c = document.getElementById('dashboard-container');
            setTimeout(() => {
                c.classList.remove('opacity-0', 'translate-y-5');
                c.classList.add('opacity-100', 'translate-y-0');
            }, 100);
        });
    </script>

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
