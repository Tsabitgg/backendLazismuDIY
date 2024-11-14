<?php

namespace App\Http\Controllers;

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
}
