<?php

namespace App\Http\Controllers;

use App\Models\Zakat;
use Illuminate\Http\Request;

class ZakatController extends Controller
{
    // Menampilkan semua zakat
    public function index()
    {
        return Zakat::all();
    }

    // Menyimpan data zakat baru
    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'category_name' => 'required|string|max:255',
                'amount' => 'required|numeric',
                'distribution' => 'required|numeric',
            ]);
    
            $zakat = Zakat::create($validatedData);
            return response()->json($zakat, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Menampilkan data zakat berdasarkan ID
    public function show($id)
    {
        return Zakat::findOrFail($id);
    }

    // Memperbarui data zakat
    public function update(Request $request, $id)
    {
        $zakat = Zakat::findOrFail($id);
        $zakat->update($request->all());
        return response()->json($zakat);
    }

    // Menghapus data zakat
    public function destroy($id)
    {
        Zakat::destroy($id);
        return response()->json(null, 204);
    }
}
