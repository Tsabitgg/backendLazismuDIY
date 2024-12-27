<?php

namespace App\Http\Controllers;

use App\Helpers\JWT;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {

        $transactions = Transaction::with(['campaign', 'zakat', 'infak', 'wakaf'])->paginate(20);

        return response()->json($transactions);
    }

    public function getTransactionsByCategory($category)
    {
        $transactions = Transaction::where('category', $category)
            ->with(['campaign', 'zakat', 'infak', 'wakaf'])->paginate(20);

        return response()->json($transactions);
    }

    public function getTransactionsByCampaignId($campaignId)
    {
        $transactions = Transaction::where('campaign_id', $campaignId)
            ->with(['campaign', 'zakat', 'infak', 'wakaf'])->paginate(10);

        return response()->json($transactions);
    }

    public function getUserTransactions(Request $request)
    {
        // Ambil token dari header Authorization
        $authHeader = $request->header('Authorization');
        $userId = null;

        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $key = env('JWT_SECRET', 'your-secret-key');

            try {
                $decoded = JWT::decode($token, $key, ['HS256']);
                $userId = $decoded->sub; // Ambil user ID dari payload token
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid token'], 401);
            }
        }

        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401);
        }

        // Ambil transaksi berdasarkan user_id
        $transactions = Transaction::where('user_id', $userId)->get();

        return response()->json($transactions, 200);
    }

    public function getTransactionSummary(Request $request)
    {
        // Ambil token dari header Authorization
        $authHeader = $request->header('Authorization');
        $userId = null;

        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $key = env('JWT_SECRET', 'your-secret-key');

            try {
                $decoded = JWT::decode($token, $key, ['HS256']);
                $userId = $decoded->sub; // Ambil user ID dari payload token
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid token'], 401);
            }
        }

        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401);
        }

        // Hitung total transaksi per kategori
        $totalCampaign = Transaction::where('user_id', $userId)
            ->where('category', 'campaign')
            ->sum('transaction_amount');

        $totalZakat = Transaction::where('user_id', $userId)
            ->where('category', 'zakat')
            ->sum('transaction_amount');

        $totalInfak = Transaction::where('user_id', $userId)
            ->where('category', 'infak')
            ->sum('transaction_amount');

        $totalWakaf = Transaction::where('user_id', $userId)
            ->where('category', 'wakaf')
            ->sum('transaction_amount');

        // Hitung total keseluruhan transaksi
        $totalAll = Transaction::where('user_id', $userId)->sum('transaction_amount');

        return response()->json([
            'total_campaign' => $totalCampaign,
            'total_zakat' => $totalZakat,
            'total_infak' => $totalInfak,
            'total_wakaf' => $totalWakaf,
            'total_all' => $totalAll,
        ], 200);
    }

    public function totalForIct()
    {
        $total = Transaction::sum('for_ict');

        return response()->json([
            'total_for_ict' => $total
        ]);
    }

}
