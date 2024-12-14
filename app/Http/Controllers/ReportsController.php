<?php

namespace App\Http\Controllers;

use App\Models\Reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:2048', // Maksimal 2MB
        ]);

        Log::info('Request received: ', $request->all());
        Log::info('Uploaded file: ', ['file' => $request->file('file')]);

        // Simpan file PDF ke storage
        $filePath = $request->file('file')->store('reports', 'public');

        // Simpan data ke database
        $reports = Reports::create([
            'title' => $request->title,
            'file_path' => $filePath,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'reports' => $reports,
        ], 201);
    }

    // Get all PDFs with optional month and year filter
    public function index(Request $request)
    {
        $month = $request->query('month'); // Ambil query `month`
        $year = $request->query('year');   // Ambil query `year`

        $query = Reports::query();

        // Filter by month and year if provided
        if ($month && $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        } elseif ($month) {
            $query->whereMonth('created_at', $month);
        }

        $reports = $query->get();

        return response()->json($reports);
    }
}
