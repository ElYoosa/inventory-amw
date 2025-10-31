@extends('layouts.app')

@section('content')
<h4>Tambah Kategori</h4>
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Nama Kategori</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection