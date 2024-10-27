<?php

namespace App\Http\Controllers;

use App\Models\Wakaf;
use Illuminate\Http\Request;

class WakafController extends Controller
{
    // Menampilkan semua wakaf
    public function index()
    {
        return Wakaf::all();
    }

    // Menyimpan data wakaf baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'distribution' => 'required|numeric'
        ]);

        $wakaf = Wakaf::create($validatedData);
        return response()->json($wakaf, 201);
    }

    // Menampilkan data wakaf berdasarkan ID
    public function show($id)
    {
        return Wakaf::findOrFail($id);
    }

    // Memperbarui data wakaf
    public function update(Request $request, $id)
    {
        $wakaf = Wakaf::findOrFail($id);
        $wakaf->update($request->all());
        return response()->json($wakaf);
    }

    // Menghapus data wakaf
    public function destroy($id)
    {
        Wakaf::destroy($id);
        return response()->json(null, 204);
    }
}
