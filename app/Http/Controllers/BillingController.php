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
    public function createBilling(Request $request, $categoryType, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'username' => 'required|string|max:20',
            'phone_number' => 'required|string|max:15',
            'message' => 'nullable|string|max:255',
        ]);
    
        // Buat objek billing baru
        $billing = new Billing();
        $billing->billing_amount = $request->input('amount');
        $billing->username = $request->input('username');
        $billing->phone_number = $request->input('phone_number');
        $billing->message = $request->input('message');
        $billing->billing_date = now();
        $billing->category = $categoryType;
        $billing->method = 'ONLINE';
        $billing->success = false;

        $billing->created_time = substr((string) intval(microtime(true) * 1000), -8);
    
        // Set Virtual Account (VA) Number
        if (Auth::check()) {
            $user = Auth::user();
            $updatedVaNumber = '797755' . str_pad($user->va_number ?? rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
            $billing->va_number = (int) $updatedVaNumber;
        } else {
            $vaNumber = '797755' . rand(1000000000, 9999999999);
            $billing->va_number = (int) $vaNumber;
        }
    
        // Set tipe tagihan berdasarkan categoryType
        switch ($categoryType) {
            case 'campaign':
                $campaign = Campaign::where('id', $id)->first();
                if (!$campaign) {
                    return response()->json(['error' => 'Campaign not found with id: ' . $id], 404);
                }
                $billing->campaign_id = $campaign->id;
                break;
    
            case 'zakat':
                $zakat = Zakat::where('id', $id)->first();
                if (!$zakat) {
                    return response()->json(['error' => 'Zakat not found with id: ' . $id], 404);
                }
                $billing->zakat_id = $zakat->id;
                break;
    
            case 'infak':
                $infak = Infak::where('id', $id)->first();
                if (!$infak) {
                    return response()->json(['error' => 'Infak not found with id: ' . $id], 404);
                }
                $billing->infak_id = $infak->id;
                break;
    
            case 'wakaf':
                $wakaf = Wakaf::where('id', $id)->first();
                if (!$wakaf) {
                    return response()->json(['error' => 'Wakaf not found with id: ' . $id], 404);
                }
                $billing->wakaf_id = $wakaf->id;
                break;
    
            default:
                return response()->json(['error' => 'Invalid transaction type: ' . $categoryType], 400);
        }
    
        // Simpan data ke database
        $billing->save();
    
        return response()->json($billing, 201);
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