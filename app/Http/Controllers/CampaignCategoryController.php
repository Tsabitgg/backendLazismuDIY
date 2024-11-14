<?php

namespace App\Http\Controllers;

use App\Models\CampaignCategory;
use Illuminate\Http\Request;

class CampaignCategoryController extends Controller
{
    // Menampilkan semua kategori campaign
    public function index()
    {
        return CampaignCategory::all();
    }

    // Menyimpan kategori campaign baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'campaign_category' => 'required|string|max:255',
        ]);

        $category = CampaignCategory::create($validatedData);
        return response()->json($category, 201);
    }

    // Menampilkan kategori campaign berdasarkan ID
    public function show($id)
    {
        return CampaignCategory::findOrFail($id);
    }

    // Memperbarui kategori campaign
    public function update(Request $request, $id)
    {
        $category = CampaignCategory::findOrFail($id);
        $category->update($request->all());
        return response()->json($category);
    }

    // Menghapus kategori campaign
    public function destroy($id)
    {
        CampaignCategory::destroy($id);
        return response()->json(null, 204);
    }
}
