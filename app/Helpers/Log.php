<?php

namespace App\Helpers;

use App\Models\Log;

class Logger
{
    public static function log($feature, $requestData, $responseData = null, $errorMessage = null)
    {
        Log::create([
            'feature' => $feature,
            'request_data' => $requestData ? json_encode($requestData) : null,
            'response_data' => $responseData ? json_encode($responseData) : null,
            'error_message' => $errorMessage,
        ]);
    }
}
