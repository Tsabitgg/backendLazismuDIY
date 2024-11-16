<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Campaign;
use App\Models\Infak;
use App\Models\Wakaf;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeByCategory(Request $request, $category, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'username' => 'required_without:user_id|string|max:20', // Required jika user_id null
            'phone_number' => 'required_without:user_id|string|max:15',
            'billing_amount' => 'required|numeric',
            'message' => 'nullable|string',
        ]);

        // Ambil data entitas berdasarkan kategori
        $relatedModel = $this->getRelatedModel($category);
        if (!$relatedModel) {
            return response()->json(['error' => 'Invalid category'], 400);
        }

        $relatedEntity = $relatedModel::find($id);
        if (!$relatedEntity) {
            return response()->json(['error' => 'Entity not found'], 404);
        }

        // Tambahkan data tagihan
        $data = $validated;
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            $data['user_id'] = null;
        }

        $data['created_time'] = now()->format('His');
        $data['billing_date'] = now();
        $data["{$category}_id"] = $relatedEntity->id; // Masukkan foreign key sesuai kategori

        // Simpan data tagihan
        $billing = Billing::create($data);
        return response()->json($billing, 201);
    }

    /**
     * Menentukan model terkait berdasarkan kategori
     */
    private function getRelatedModel($category)
    {
        return match ($category) {
            'zakat' => Zakat::class,
            'infak' => Infak::class,
            'campaign' => Campaign::class,
            'wakaf' => Wakaf::class,
            default => null,
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
