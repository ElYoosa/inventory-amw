<?php

namespace App\Http\Controllers;

use App\Models\InTransaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InTransactionController extends Controller
{
    public function index()
    {
        $transactions = InTransaction::with('item')->latest()->paginate(10);
        return view('in.index', compact('transactions'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        return view('in.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'date'    => 'required|date',
            'qty'     => 'required|integer|min:1',
            'note'    => 'nullable|string',
        ]);

        // Pastikan user terautentikasi
        $userId = Auth::check() ? Auth::user()->id : 1;

        // Simpan transaksi
        $transaction = InTransaction::create([
            'item_id' => $request->item_id,
            'user_id' => $userId,
            'date'    => $request->date,
            'qty'     => $request->qty,
            'note'    => $request->note,
        ]);

        // Tambah stok barang
        $item = Item::find($request->item_id);
        if ($item) {
            $item->increment('stock', $request->qty);
        }

        // 🔔 Toast sukses otomatis ke dashboard
        return redirect()->route('dashboard')
            ->with('successToast', 'Transaksi masuk berhasil disimpan!');
    }
}
