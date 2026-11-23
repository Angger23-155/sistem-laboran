<?php

namespace App\Http\Controllers;

use App\Models\MasterTugasHarian;
use Illuminate\Http\Request;

class MasterTugasHarianController extends Controller
{
    public function index()
    {
        $masters = MasterTugasHarian::all();
        return view('master_tugas.index', compact('masters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'uraian_tugas' => 'required|string',
        ]);

        MasterTugasHarian::create($request->all());

        return back()->with('success', 'Master tugas harian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $master = MasterTugasHarian::findOrFail($id);
        $master->update($request->all());

        return back()->with('success', 'Master tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MasterTugasHarian::destroy($id);
        return back()->with('success', 'Master tugas berhasil dihapus.');
    }
}