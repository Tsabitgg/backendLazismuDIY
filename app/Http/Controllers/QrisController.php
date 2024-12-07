<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Helpers\JWT;
use App\Helpers\Logger;
use Exception;
use Illuminate\Support\Str;

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
    
            $url = 'http://10.99.23.111/qris/lazizmu_diy/server.php?token=' . urlencode($jwtToken);
    
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
                Logger::log('Generate QRIS', $data, null, $error); // Log error
                return response()->json(['success' => false, 'message' => 'cURL Error: ' . $error]);
            }
    
            // Tutup koneksi cURL
            curl_close($ch);
    
            // Decode respons
            $responseData = json_decode($response, true);
            Logger::log('Generate QRIS', $data, $responseData, 'success'); // Log sukses
    
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
    
            $url = 'http://10.99.23.111/qris/lazizmu_diy/server.php?token=' . urlencode($jwtTokenCheckStatus);
    
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
                Logger::log('Check Status Qris', $dataCheckStatus, null, $error); // Log sukses
                return response()->json(['success' => false, 'message' => 'cURL Error: ' . $error]);
            }
    
            // Tutup koneksi cURL
            curl_close($chCheckStatus);
    
            // Decode respons
            $responseDataCheckStatus = json_decode($responseCheckStatus, true);
            Logger::log('Check Status QRIS', $dataCheckStatus, $responseDataCheckStatus, 'success'); // Log sukses
    
            return response()->json($responseDataCheckStatus);
        }
    
        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }

    public function pushNotification(Request $request)
{
    $token = $request->input('token');

    if (empty($token)) {
        Logger::log('Push Notification', $request->all(), null, 'Token tidak ditemukan');
        return response()->json([
            'responseCode' => '01',
            'responseMessage' => 'Token tidak ditemukan',
            'responseTimestamp' => now()
        ]);
    }

    $secretKey = 'TokenJWT_BMI_ICT';

    try {
        $decoded = JWT::decode($token, $secretKey, ['HS256']);

        $responseCode = $decoded->responseCode;
        $responseMessage = $decoded->responseMessage;
        $responseTimestamp = $decoded->responseTimestamp;
        $transactionId = $decoded->transactionId;
        $data = $decoded->data;

        if ($responseCode === '00') {
            $vano = $data->vano1;
            $amount = $data->amount;
            $accountNo = $data->accountNo;
            $transactionQrId = $data->transactionQrId;
            $description = $data->description;

            $billing = DB::table('billings')->where('transaction_qr_id', $transactionQrId)->first();

            if ($billing) {
                $campaignId = $billing->campaign_id;
                $wakafId = $billing->wakaf_id;
                $zakatId = $billing->zakat_id;
                $infakId = $billing->infak_id;

                DB::table('billings')->where('transaction_qr_id', $transactionQrId)->update(['success' => 1]);

                do {
                    $invoiceId = 'INV-' . now()->format('ymd') . strtoupper(Str::random(5));
                } while (DB::table('transactions')->where('invoice_id', $invoiceId)->exists());

                DB::table('transactions')->insert([
                    'invoice_id' => $invoiceId,
                    'donatur' => $billing->username,
                    'phone_number' => $billing->phone_number,
                    'email' => null,
                    'transaction_amount' => $amount,
                    'message' => $billing->message,
                    'transaction_date' => now(),
                    'channel' => 'ONLINE',
                    'va_number' => $vano,
                    'method' => 'QRIS',
                    'transaction_qr_id' => $transactionQrId,
                    'created_time' => $billing->created_time,
                    'category'=> $billing->category,
                    'success' => 1,
                    'campaign_id' => $campaignId ?? null,
                    'wakaf_id' => $wakafId ?? null,
                    'zakat_id' => $zakatId ?? null,
                    'infak_id' => $infakId ?? null
                ]);

                if ($campaignId) {
                    DB::table('campaigns')->where('id', $campaignId)
                        ->increment('current_amount', $billing->billing_amount);
                }

                if ($wakafId) {
                    DB::table('wakafs')->where('id', $wakafId)
                        ->increment('amount', $billing->billing_amount);
                }

                if ($zakatId) {
                    DB::table('zakats')->where('id', $zakatId)
                        ->increment('amount', $billing->billing_amount);
                }

                if ($infakId) {
                    DB::table('infaks')->where('id', $infakId)
                        ->increment('amount', $billing->billing_amount);
                }

                Logger::log('Push Notification', $request->all(), [
                    'responseCode' => '00',
                    'responseMessage' => 'TRANSACTION SUCCESS',
                    'transactionId' => $transactionId
                ]);

                return response()->json([
                    'responseCode' => '00',
                    'responseMessage' => 'TRANSACTION SUCCESS',
                    'responseTimestamp' => now(),
                    'transactionId' => $transactionId
                ]);
            }

            Logger::log('Push Notification', $request->all(), null, 'Billing data not found');
            return response()->json([
                'responseCode' => '01',
                'responseMessage' => 'Billing data not found',
                'responseTimestamp' => now()
            ]);
        } else {
            Logger::log('Push Notification', $request->all(), null, $responseMessage);
            return response()->json([
                'responseCode' => '01',
                'responseMessage' => $responseMessage,
                'responseTimestamp' => now()
            ]);
        }
    } catch (Exception $e) {
        Logger::log('Push Notification', $request->all(), null, $e->getMessage());
        return response()->json([
            'responseCode' => '01',
            'responseMessage' => 'Invalid token or data: ' . $e->getMessage(),
            'responseTimestamp' => now()
        ]);
    }
}

    
    
}
