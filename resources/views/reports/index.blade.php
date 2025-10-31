@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <h4 class="fw-bold text-success text-center mb-4">Laporan Inventory ANAMTA</h4>

        {{-- 🔹 Tab Navigasi --}}
        <ul class="nav nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-distribusi" data-bs-toggle="tab" data-bs-target="#distribusi"
                    type="button" role="tab">
                    Distribusi Barang (Per Keberangkatan)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-stok" data-bs-toggle="tab" data-bs-target="#stok" type="button"
                    role="tab">
                    Stok Barang (Per Kategori)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-realtime" data-bs-toggle="tab" data-bs-target="#realtime" type="button"
                    role="tab">
                    Transaksi Real-Time
                </button>
            </li>
        </ul>

        {{-- 🔹 Isi Tiap Tab --}}
        <div class="tab-content mt-4">
            {{-- TAB 1: Distribusi Barang --}}
            <div class="tab-pane fade show active" id="distribusi" role="tabpanel">
                @include('reports.partials.group-report')
            </div>

            {{-- TAB 2: Stok Barang --}}
            <div class="tab-pane fade" id="stok" role="tabpanel">
                @include('reports.partials.group-category')
            </div>

            {{-- TAB 3: Transaksi Real-Time --}}
            <div class="tab-pane fade" id="realtime" role="tabpanel">
                @include('reports.partials.report-realtime')
            </div>
        </div>
    </div>
@endsection
