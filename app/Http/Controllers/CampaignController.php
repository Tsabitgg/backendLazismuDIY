<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Log;

use App\Models\Campaign;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan kategori dari query string
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        // Query campaign dengan filter nama dan kategori jika ada
        $campaigns = Campaign::with('category')
            ->when($search, function ($query, $search) {
                $query->where('campaign_name', 'like', '%' . $search . '%');
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('campaign_category_id', $categoryId);
            })
            ->paginate(12);

        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        // dd($request);
        try {
            $validatedData = $request->validate([
                'campaign_category_id' => 'required|exists:campaign_categories,id',
                'campaign_name' => 'required|string|max:255',
                'campaign_code' => 'required|string|unique:campaigns,campaign_code',
                'campaign_thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'campaign_image_1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'campaign_image_2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'campaign_image_3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'description' => 'required',
                'location' => 'required|string|max:255',
                'target_amount' => 'required|numeric',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);
    
            if ($request->hasFile('campaign_thumbnail')) {
                $uploadedFile = Cloudinary::upload($request->file('campaign_thumbnail')->getRealPath(), ['folder' => 'campaign_images']);
                $validatedData['campaign_thumbnail'] = $uploadedFile->getSecurePath();
            }
    
            $validatedData['campaign_image_1'] = $request->hasFile('campaign_image_1')
                ? Cloudinary::upload($request->file('campaign_image_1')->getRealPath(), ['folder' => 'campaign_images'])->getSecurePath()
                : null;
    
            $validatedData['campaign_image_2'] = $request->hasFile('campaign_image_2')
                ? Cloudinary::upload($request->file('campaign_image_2')->getRealPath(), ['folder' => 'campaign_images'])->getSecurePath()
                : null;
    
            $validatedData['campaign_image_3'] = $request->hasFile('campaign_image_3')
                ? Cloudinary::upload($request->file('campaign_image_3')->getRealPath(), ['folder' => 'campaign_images'])->getSecurePath()
                : null;
 
            $validatedData['end_date'] = $validatedData['end_date'] ?? null;
            $validatedData['active'] = 1;
            $validatedData['approved'] = 1;
            $validatedData['priority'] = 0;
            $validatedData['distribution'] = 0;
            $validatedData['current_mount'] = 0;

            $campaign = Campaign::create($validatedData);
    
            return response()->json($campaign, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }    

    public function show($id)
    {
        return Campaign::with('category')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        try {
            $campaign = Campaign::findOrFail($id);
    
            // Validasi input
            $validatedData = $request->validate([
                'campaign_category_id' => 'sometimes|exists:campaign_categories,id',
                'campaign_name' => 'sometimes|string|max:255',
                'campaign_code' => 'sometimes|string|unique:campaigns,campaign_code,' . $id, // Abaikan validasi unique untuk ID ini
                'campaign_thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'campaign_image_1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'campaign_image_2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'campaign_image_3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'description' => 'sometimes|string',
                'location' => 'sometimes|string|max:255',
                'target_amount' => 'sometimes|numeric',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date',
            ]);
    
            // Proses campaign thumbnail
            if ($request->hasFile('campaign_thumbnail')) {
                $uploadedFile = Cloudinary::upload($request->file('campaign_thumbnail')->getRealPath(), ['folder' => 'campaign_images']);
                $validatedData['campaign_thumbnail'] = $uploadedFile->getSecurePath();
            } else {
                $validatedData['campaign_thumbnail'] = $campaign->campaign_thumbnail; // Gunakan data lama jika tidak diunggah
            }
    
            // Proses campaign image 1-3 (gunakan gambar lama jika tidak diunggah)
            $validatedData['campaign_image_1'] = $request->hasFile('campaign_image_1')
                ? Cloudinary::upload($request->file('campaign_image_1')->getRealPath(), ['folder' => 'campaign_images'])->getSecurePath()
                : $campaign->campaign_image_1;
    
            $validatedData['campaign_image_2'] = $request->hasFile('campaign_image_2')
                ? Cloudinary::upload($request->file('campaign_image_2')->getRealPath(), ['folder' => 'campaign_images'])->getSecurePath()
                : $campaign->campaign_image_2;
    
            $validatedData['campaign_image_3'] = $request->hasFile('campaign_image_3')
                ? Cloudinary::upload($request->file('campaign_image_3')->getRealPath(), ['folder' => 'campaign_images'])->getSecurePath()
                : $campaign->campaign_image_3;
    
            // Update data campaign
            $campaign->update($validatedData);
    
            return response()->json($campaign, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    
    public function destroy($id)
    {
        Campaign::destroy($id);
        return response()->json(null, 204);
    }

    // Set priority to true
    public function setPriorityTrue($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->priority = true;
        $campaign->save();

        return response()->json([
            'message' => 'Campaign set priority successfully!',
            'campaign' => $campaign,
        ]);
    }

    // Set priority to false
    public function setPriorityFalse($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->priority = false;
        $campaign->save();

        return response()->json([
            'message' => 'Campaign unset priority successfully!',
            'campaign' => $campaign,
        ]);
    }

    // Get all campaigns priority
    public function getPriorityCampaigns()
    {
        $campaigns = Campaign::with('category')
            ->where('priority', true)
            ->get();

        return response()->json($campaigns, 200);
    }

}
