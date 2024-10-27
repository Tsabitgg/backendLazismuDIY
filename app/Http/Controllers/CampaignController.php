<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan kategori dari query string
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        // Query campaign dengan filter nama dan kategori jika ada
        $campaigns = Campaign::with('category', 'admin')
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
                'admin_id' => 'required|exists:admins,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'active' => 'required|boolean',
                'approved' => 'required|boolean',
                'distribution' => 'nullable|numeric'
            ]);

            // Upload images
            if ($request->hasFile('campaign_thumbnail')) {
                $validatedData['campaign_thumbnail'] = $request->file('campaign_thumbnail')->store('campaign_images', 'public');
            }

            $validatedData['campaign_image_1'] = $request->hasFile('campaign_image_1')
                ? $request->file('campaign_image_1')->store('campaign_images', 'public')
                : null;

            $validatedData['campaign_image_2'] = $request->hasFile('campaign_image_2')
                ? $request->file('campaign_image_2')->store('campaign_images', 'public')
                : null;

            $validatedData['campaign_image_3'] = $request->hasFile('campaign_image_3')
                ? $request->file('campaign_image_3')->store('campaign_images', 'public')
                : null;

            $campaign = Campaign::create($validatedData);
            return response()->json($campaign, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return Campaign::with('category', 'admin')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update($request->all());
        return response()->json($campaign);
    }

    public function destroy($id)
    {
        Campaign::destroy($id);
        return response()->json(null, 204);
    }
}
