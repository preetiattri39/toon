<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VatController extends APIBaseController
{
    /**
     * Retrieve VAT rates based on optional country filter.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request, possibly with `country_id`.
     * @return \Illuminate\Http\JsonResponse  The JSON response containing VAT data or an error message.
     */
    public function getVat(Request $request)
    {
        try {
            
            $response = $this->vat_service->getVatRates($request);

            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // This is optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

}
