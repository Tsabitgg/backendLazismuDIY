<?php

namespace App\Http\Controllers;

use App\Models\Infak;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
        try {
            // Cari data infak berdasarkan ID
            $infak = Infak::findOrFail($id);

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
                $validatedData['thumbnail'] = $infak->thumbnail;
            }

            // Gunakan nilai lama untuk field yang tidak ada di input
            $validatedData['category_name'] = $request->input('category_name', $infak->category_name);

            // Perbarui data infak
            $infak->update($validatedData);

            return response()->json($infak, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Menghapus data infak
    public function destroy($id)
    {
        Infak::destroy($id);
        return response()->json(null, 204);
    }
}

