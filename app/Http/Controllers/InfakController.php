<?php

namespace App\Http\Controllers;

use App\Models\Infak;
use Illuminate\Http\Request;

class InfakController extends Controller
{
    // Menampilkan semua infak
    public function index()
    {
        return Infak::all();
    }

    // Menyimpan data infak baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'distribution' => 'required|numeric',
        ]);

        $infak = Infak::create($validatedData);
        return response()->json($infak, 201);
    }

    // Menampilkan data infak berdasarkan ID
    public function show($id)
    {
        return Infak::findOrFail($id);
    }

    // Memperbarui data infak
    public function update(Request $request, $id)
    {
        $infak = Infak::findOrFail($id);
        $infak->update($request->all());
        return response()->json($infak);
    }

    // Menghapus data infak
    public function destroy($id)
    {
        Infak::destroy($id);
        return response()->json(null, 204);
    }
}

