<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index()
    {
        $liburs = HariLibur::orderBy('tanggal', 'desc')->get();
        return view('hari_libur.index', compact('liburs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_liburs',
            'keterangan' => 'required|string|max:255',
        ]);

        HariLibur::create($request->all());
        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_liburs,tanggal,' . $id,
            'keterangan' => 'required|string|max:255',
        ]);

        HariLibur::findOrFail($id)->update($request->all());
        return back()->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        HariLibur::destroy($id);
        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}