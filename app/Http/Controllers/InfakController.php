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

