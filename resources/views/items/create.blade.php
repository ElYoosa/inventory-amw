@extends('layouts.app')

@section('content')
<h4>Tambah Barang</h4>
<form action="{{ route('items.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Satuan</label>
            <input type="text" name="unit" value="pcs" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Stok Awal</label>
            <input type="number" name="stock" value="0" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Stok Minimum</label>
            <input type="number" name="min_stock" value="10" class="form-control" required>
        </div>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection