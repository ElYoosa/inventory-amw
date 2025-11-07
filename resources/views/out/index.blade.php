@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Transaksi Keluar</h4>
        <a href="{{ route('out-transactions.create') }}" class="btn btn-primary">+ Tambah Transaksi Keluar</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Dibuat Pada</th>
                <th>Barang</th>
                <th>Qty</th>
                <th>Penerima</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $trx)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}</td>
                    <td>{{ optional($trx->created_at)->format('d M Y H:i') }}</td>
                    <td>{{ $trx->item->name }}</td>
                    <td>{{ $trx->qty }}</td>
                    <td>{{ $trx->receiver }}</td>
                    <td>{{ $trx->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transactions->links('vendor.pagination.custom') }}
@endsection
