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
                "accountNo" => "5320017203",
                "amount" => strval($row->billing_amount),
                "mitraCustomerId" => "LAZIZMU DIY274029",
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

    public function checkStatus(Request $request)
    {
        $createdTime = $request->query('createdTime');
    
        if (is_null($createdTime) || $createdTime <= 0) {
            return response()->json(['success' => false, 'message' => 'createdTime tidak valid']);
        }
    
        // Query database menggunakan Eloquent atau Query Builder
        $row = DB::table('billings')->where('created_time', $createdTime)->first();
    
        if ($row) {
            $dataCheckStatus = [
                "accountNo" => "5320017203",
                "amount" => strval($row->billing_amount),
                "merchantId" => "839853200172032",
                "mitraCustomerId" => "LAZIZMU DIY274029",
                "transactionId" => strval($row->created_time),
                "transactionQrId" => $row->transaction_qr_id,
                "tipeTransaksi" => "MTR-CHECK-STATUS"
            ];
    
            $secretKey = 'TokenJWT_BMI_ICT';
            $jwtTokenCheckStatus = JWT::encode($dataCheckStatus, $secretKey);
    
            $url = 'http://10.99.23.111/qris/bandung_dt_peduli/server.php?token=' . urlencode($jwtTokenCheckStatus);
    
            // Inisialisasi cURL
            $chCheckStatus = curl_init($url);
            curl_setopt($chCheckStatus, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chCheckStatus, CURLOPT_POST, true);
            curl_setopt($chCheckStatus, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
    
            // Eksekusi cURL
            $responseCheckStatus = curl_exec($chCheckStatus);
    
            // Periksa kesalahan cURL
            if (curl_errno($chCheckStatus)) {
                $error = curl_error($chCheckStatus);
                curl_close($chCheckStatus);
                return response()->json(['success' => false, 'message' => 'cURL Error: ' . $error]);
            }
    
            // Tutup koneksi cURL
            curl_close($chCheckStatus);
    
            // Decode respons
            $responseDataCheckStatus = json_decode($responseCheckStatus, true);
    
            return response()->json($responseDataCheckStatus);
        }
    
        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    
}
