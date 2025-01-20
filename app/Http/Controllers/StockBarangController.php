<?php

namespace App\Http\Controllers;

use App\Models\StockBarang;
use Illuminate\Http\Request;

class StockBarangController extends Controller
{
    public function index()
    {
        $stockBarang = StockBarang::all();
        return view('superAdmin.stockBarang.index', compact('stockBarang'));
    }

    public function create()
    {
        return view('superAdmin.stockBarang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tipe_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
        ]);

        // Cari barang berdasarkan nama
        $existingItem = StockBarang::where('nama_barang', $validated['nama_barang'])->first();
        $existingItem = StockBarang::where('jumlah_barang', $validated['jumlah_barang'])->first();

        $existingItem = StockBarang::where('tipe_barang', $validated['tipe_barang'])
            ->where('id', '!=', 'tipe_barang')
            ->first();

        if ($existingItem) {
            return redirect()->back()->withErrors('Tipe barang sudah digunakan.');
        }

        if ($existingItem) {
            // Jika barang sudah ada, tambahkan jumlah stok
            $existingItem->jumlah_barang += $validated['jumlah_barang'];
            $existingItem->save();

            return redirect()->route('superAdmin.stockBarang.index')
                ->with('success', 'Stok barang berhasil ditambahkan.');
        } else {
            // Jika barang belum ada, buat data baru
            StockBarang::create($validated);

            return redirect()->route('superAdmin.stockBarang.index')
                ->with('success', 'Barang baru berhasil ditambahkan.');
        }
    }


    public function edit($id)
    {
        $stockBarang = StockBarang::findOrFail($id);
        return view('superAdmin.stockBarang.edit', compact('stockBarang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
            'tipe_barang' => 'required|string',
        ]);
        // dd($validated);
        $stockBarang = StockBarang::findOrFail($id);

        // Perbarui data barang
        $stockBarang->update($validated);

        return redirect()->route('superAdmin.stockBarang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }


    public function destroy($id)
    {
        // Temukan barang berdasarkan ID
        $stockBarang = StockBarang::findOrFail($id);

        // Hapus barang
        $stockBarang->delete();

        return redirect()->route('superAdmin.stockBarang.index')
            ->with('success', 'Stock Barang deleted successfully.');
    }
}
