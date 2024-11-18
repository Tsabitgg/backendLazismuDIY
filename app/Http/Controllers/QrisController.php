<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Helpers\JWT;

class QrisController extends Controller
{
    public function generate(Request $request)
    {
        $createdTime = $request->query('createdTime');
    
        if (is_null($createdTime) || $createdTime <= 0) {
            return response()->json(['success' => false, 'message' => 'createdTime tidak valid']);
        }
    
        // Query database menggunakan Eloquent atau Query Builder
        $row = DB::table('billings')->where('created_time', $createdTime)->first();
    
        if ($row) {
            $data = [
                "accountNo" => "1030005418",
                "amount" => strval($row->billing_amount),
                "mitraCustomerId" => "DT Peduli508362",
                "transactionId" => strval($row->created_time),
                "tipeTransaksi" => "MTR-GENERATE-QRIS-DYNAMIC",
                "vano" => strval($row->va_number)
            ];
    
            $secretKey = 'TokenJWT_BMI_ICT';
            $jwtToken = JWT::encode($data, $secretKey);
    
            $url = 'http://10.99.23.111/qris/bandung_dt_peduli/server.php?token=' . urlencode($jwtToken);
    
            // Inisialisasi cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
    
            // Eksekusi permintaan cURL
            $response = curl_exec($ch);
    
            // Periksa kesalahan cURL
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                return response()->json(['success' => false, 'message' => 'cURL Error: ' . $error]);
            }
    
            // Tutup koneksi cURL
            curl_close($ch);
    
            // Decode respons
            $responseData = json_decode($response, true);
    
            if (isset($responseData['transactionDetail']['transactionQrId'])) {
                $transactionQrId = $responseData['transactionDetail']['transactionQrId'];
    
                // Update database dengan transactionQrId
                DB::table('billings')->where('created_time', $createdTime)->update(['transaction_qr_id' => $transactionQrId]);
    
                // Tambahkan transactionQrId ke dalam respons untuk ditampilkan ke klien
                $responseData['transactionQrId'] = $transactionQrId;
            } else {
                return response()->json(['success' => false, 'message' => 'Transaction QR ID tidak ditemukan dalam response']);
            }
    
            return response()->json($responseData);
        }
    
        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    
}
