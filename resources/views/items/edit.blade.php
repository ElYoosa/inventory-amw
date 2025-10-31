@extends('layouts.app')

@section('content')
<h4>Edit Barang</h4>
<form action="{{ route('items.update', $item->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select" required>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $cat->id == $item->category_id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="name" value="{{ $item->name }}" class="form-control" required>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Satuan</label>
            <input type="text" name="unit" value="{{ $item->unit }}" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Stok</label>
            <input type="number" name="stock" value="{{ $item->stock }}" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Stok Minimum</label>
            <input type="number" name="min_stock" value="{{ $item->min_stock }}" class="form-control" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection