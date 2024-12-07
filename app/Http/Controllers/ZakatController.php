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
        try {
            // Cari data infak berdasarkan ID
            $zakat = Zakat::findOrFail($id);

            // Validasi input
            $validatedData = $request->validate([
                'category_name' => 'sometimes|string|max:255',
                'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Proses thumbnail (gunakan gambar lama jika tidak diunggah)
            if ($request->hasFile('thumbnail')) {
                $uploadedFile = Cloudinary::upload($request->file('thumbnail')->getRealPath(), ['folder' => 'campaign_images']);
                $validatedData['thumbnail'] = $uploadedFile->getSecurePath();
            } else {
                // Jika thumbnail tidak diunggah, gunakan data lama
                $validatedData['thumbnail'] = $zakat->thumbnail;
            }

            // Gunakan nilai lama untuk field yang tidak ada di input
            $validatedData['category_name'] = $request->input('category_name', $zakat->category_name);

            // Perbarui data zakat
            $zakat->update($validatedData);

            return response()->json($zakat, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Menghapus data zakat
    public function destroy($id)
    {
        Zakat::destroy($id);
        return response()->json(null, 204);
    }
}
