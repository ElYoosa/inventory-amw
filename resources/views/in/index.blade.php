@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Transaksi Masuk</h4>
        <a href="{{ route('in-transactions.create') }}" class="btn btn-primary">+ Tambah Transaksi Masuk</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Qty</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $trx)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $trx->date }}</td>
                    <td>{{ $trx->item->name }}</td>
                    <td>{{ $trx->qty }}</td>
                    <td>{{ $trx->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transactions->links('vendor.pagination.custom') }}
@endsection
