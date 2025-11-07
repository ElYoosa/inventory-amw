<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
  public function index()
  {
    $items = Item::with("category")->orderBy("name")->paginate(10);
    return view("items.index", compact("items"));
  }

  public function create()
  {
    $categories = Category::orderBy("name")->get();
    return view("items.create", compact("categories"));
  }

  public function store(Request $r)
  {
    try {
      $r->validate([
        "category_id" => "required|exists:categories,id",
        "name" => "required|string|max:100",
        "unit" => "required|string|max:20",
        "stock" => "required|integer|min:0",
        "min_stock" => "required|integer|min:0",
      ]);

      Item::create($r->all());

      return redirect()
        ->route("items.index")
        ->with("successToast", "Barang baru berhasil ditambahkan ke inventori!");
    } catch (\Throwable $e) {
      return redirect()
        ->back()
        ->with("errorToast", "Terjadi kesalahan saat menambahkan barang: " . $e->getMessage());
    }
  }

  public function edit(Item $item)
  {
    $categories = Category::orderBy("name")->get();
    return view("items.edit", compact("item", "categories"));
  }

  public function update(Request $r, Item $item)
  {
    try {
      $r->validate([
        "category_id" => "required|exists:categories,id",
        "name" => "required|string|max:100",
        "unit" => "required|string|max:20",
        "stock" => "required|integer|min:0",
        "min_stock" => "required|integer|min:0",
      ]);

      $item->update($r->all());

      return redirect()
        ->route("items.index")
        ->with("successToast", "Data barang berhasil diperbarui!");
    } catch (\Throwable $e) {
      return redirect()
        ->back()
        ->with("errorToast", "Gagal memperbarui data barang: " . $e->getMessage());
    }
  }

  public function destroy(Item $item)
  {
    try {
      $item->delete();

      return redirect()
        ->route("items.index")
        ->with("successToast", "Barang berhasil dihapus dari inventori!");
    } catch (\Throwable $e) {
      return redirect()
        ->back()
        ->with("errorToast", "Gagal menghapus barang: " . $e->getMessage());
    }
  }
}
