<?php

namespace App\Http\Controllers;

use App\Models\Zakat;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
            $request->merge([
                'amount' => $request->input('amount', 0),
                'distribution' => $request->input('distribution', 0),
            ]);
            
            $validatedData = $request->validate([
                'category_name' => 'required|string|max:255',
                'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'amount' => 'required|numeric',
                'distribution' => 'required|numeric',
            ]);
    
                
            if ($request->hasFile('thumbnail')) {
                $uploadedFile = Cloudinary::upload($request->file('thumbnail')->getRealPath(), ['folder' => 'campaign_images']);
                $validatedData['thumbnail'] = $uploadedFile->getSecurePath();
            }
    
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
