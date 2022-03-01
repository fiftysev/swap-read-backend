<?php

namespace App\Http\Helpers;
/**
 * @param $message string
 * @param $code int
 * @return \Illuminate\Http\JsonResponse
 */
class CustomJsonResponses
{
    public static function error_response(string $message, int $code = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
