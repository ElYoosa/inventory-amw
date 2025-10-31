@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold text-blue-700 mb-4">Tambah Transaksi Keluar</h2>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form action="{{ route('out-transactions.store') }}" method="POST">
                @csrf

                {{-- 📅 Tanggal --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="form-control"
                        required>
                </div>

                {{-- 📦 Pilih Barang --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Barang</label>
                    <select name="item_id" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} (Stok: {{ $item->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 🚍 Kode Grup Keberangkatan --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Grup Keberangkatan</label>
                    <input list="kode_grup_list" name="kode_grup" value="{{ old('kode_grup') }}" class="form-control"
                        placeholder="Pilih atau ketik kode grup..." required>
                    <datalist id="kode_grup_list">
                        @foreach (\App\Models\OutTransaction::select('kode_grup')->distinct()->whereNotNull('kode_grup')->get() as $g)
                            <option value="{{ $g->kode_grup }}">{{ $g->kode_grup }}</option>
                        @endforeach
                    </datalist>
                    <small class="text-muted">Ketik atau pilih kode grup keberangkatan (misal: 10 November)</small>
                </div>

                {{-- 🔢 Jumlah dan Penerima --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Keluar</label>
                        <input type="number" name="qty" value="{{ old('qty') }}" class="form-control" min="1"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Penerima</label>
                        <input type="text" name="receiver" value="{{ old('receiver') }}" class="form-control" required>
                    </div>
                </div>

                {{-- 📝 Catatan --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Catatan (opsional)</label>
                    <textarea name="note" rows="3" class="form-control" placeholder="Keterangan tambahan jika diperlukan">{{ old('note') }}</textarea>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                    <a href="{{ route('out-transactions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
