@extends('layouts.app')

@section('content')
<h4>Tambah Transaksi Masuk</h4>
<form action="{{ route('in-transactions.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Pilih Barang</label>
        <select name="item_id" class="form-select" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($items as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Jumlah Masuk</label>
        <input type="number" name="qty" class="form-control" min="1" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Catatan (opsional)</label>
        <textarea name="note" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('in-transactions.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection