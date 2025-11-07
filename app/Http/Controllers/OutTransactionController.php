<?php

namespace App\Http\Controllers;

use App\Models\OutTransaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutTransactionController extends Controller
{
  public function index()
  {
    $transactions = OutTransaction::with("item")->latest()->paginate(10);
    return view("out.index", compact("transactions"));
  }

  public function create()
  {
    $items = Item::orderBy("name")->get();
    return view("out.create", compact("items"));
  }

  public function store(Request $request)
  {
    $request->validate([
      "item_id" => "required|exists:items,id",
      "date" => "required|date",
      "qty" => "required|integer|min:1",
      "receiver" => "required|string|max:100",
      "kode_grup" => "nullable|string|max:50",
      "note" => "nullable|string",
    ]);

    // Pastikan user login
    $userId = Auth::check() ? Auth::user()->id : 1;

    // Simpan transaksi
    $transaction = OutTransaction::create([
      "item_id" => $request->item_id,
      "user_id" => $userId,
      "date" => $request->date,
      "qty" => $request->qty,
      "receiver" => $request->receiver,
      "kode_grup" => $request->kode_grup,
      "note" => $request->note,
    ]);

    // Update stok tidak dilakukan di controller.
    // Stok otomatis disesuaikan oleh TransactionObserver saat transaksi dibuat.

    // 🔔 Toast sukses otomatis ke dashboard
    return redirect()
      ->route("out-transactions.index")
      ->with("successToast", "Transaksi keluar berhasil disimpan!");
  }
}
