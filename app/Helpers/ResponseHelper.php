<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{

    /**
     * Generates a JSON response based on the success status of an operation.
     *
     * @param array $response The response array containing the success status and message.
     * @param int $success_status The HTTP status code to return for a successful response.
     * @param int $failed_status The HTTP status code to return for a failed response.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the outcome of the operation.
     */
    public static function jsonResponse($response, $success_status, $failed_status)
    {
        if(isset($response['success']) && $response['success']){
            return response()->json([
                'status' => 'success',
                'message' => $response['message'],
                'data' => $response['data'],
            ],$success_status);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => $response['message'],
                'data' => (object) [],
            ],$failed_status);
        }
    }


    public static function jsonListingResponse($response, $success_status, $failed_status)
    {
        if(isset($response['success']) && $response['success']){
            return response()->json([
                'status' => 'success',
                'message' => $response['message'],
                'data' => $response['data'],
            ],$success_status);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => $response['message'],
                'data' => ['data'=>[]],
            ],$failed_status);
        }
    }
}
