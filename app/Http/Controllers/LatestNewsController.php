<?php

namespace App\Http\Controllers;

use App\Models\latestNews;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class LatestNewsController extends Controller
{

    public function index($category)
    {
        $news = latestNews::where('category', $category);
        return response()->json($news);
    }

    // Create news (only category and ID)
    public function store(Request $request, $category, $id)
    {
        $request->validate([
            'latest_news_date' => 'required|date',
            'image' => 'required|image|mimes:jpg,jpeg,png',
            'description' => 'required|string',
        ]);

        // Upload image to Cloudinary
        $imagePath = Cloudinary::upload($request->file('image')->getRealPath(), [
            'folder' => 'latest_news',
        ])->getSecurePath();

        // Create a new news entry
        $news = latestNews::create([
            'latest_news_date' => $request->latest_news_date,
            'image' => $imagePath,
            'description' => $request->description,
            'category' => $category,
        ]);

        return response()->json($news, 201);
    }

    // Update news
    public function update(Request $request, $category, $id)
    {
        $news = latestNews::where('category', $category)->findOrFail($id);

        $request->validate([
            'latest_news_date' => 'sometimes|date',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png',
            'description' => 'sometimes|string',
        ]);

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'latest_news',
            ])->getSecurePath();
            $news->image = $imagePath;
        }

        // Update other fields
        $news->update($request->except('image'));

        return response()->json($news);
    }

    // Delete news by ID and category
    public function destroy($category, $id)
    {
        $news = latestNews::where('category', $category)->findOrFail($id);
        $news->delete();

        return response()->json(['message' => 'Latest news deleted successfully.']);
    }

    public function getByCategoryAndEntityId($category, $id)
    {
        // Validate category
        $validCategories = ['campaign', 'zakat', 'infak', 'wakaf'];
        if (!in_array($category, $validCategories)) {
            return response()->json(['error' => 'Invalid category'], 400);
        }

        // Build query based on category
        $column = $category . '_id';
        $latestNews = latestNews::where($column, $id)->get();

        // Check if any news is found
        if ($latestNews->isEmpty()) {
            return response()->json(['message' => 'No latest news found for this category and ID'], 404);
        }

        return response()->json($latestNews);
    }
}
