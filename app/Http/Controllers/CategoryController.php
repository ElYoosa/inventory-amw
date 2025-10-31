<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $r)
    {
        try {
            $r->validate(['name' => 'required|string|max:50']);
            Category::create($r->only('name', 'description'));

            // ✅ Toast sukses
            return redirect()->route('dashboard')
                ->with('successToast', 'Kategori baru berhasil ditambahkan!');
        } catch (\Throwable $e) {
            // ❌ Toast error
            return redirect()->back()
                ->with('errorToast', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $r, Category $category)
    {
        try {
            $r->validate(['name' => 'required|string|max:50']);
            $category->update($r->only('name', 'description'));

            return redirect()->route('dashboard')
                ->with('successToast', 'Kategori berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('errorToast', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return redirect()->route('dashboard')
                ->with('successToast', 'Kategori berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('errorToast', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
