<?php 

namespace App\Services\API;

// Models
use App\Models\VatRate;

class VatService
{
    /**
     * Retrieve active VAT rates, optionally filtered by country.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request object containing optional `country_id`.
     * @return array  Returns a response array indicating success status, message, and VAT rate data if found.
     */
    public function getVatRates($request)
    {   
        $country_id = $request->country_id;
        $query = VatRate::query();

        if($country_id){
            $query->where('country_id',$country_id);
        }

        $vats = $query->where('status',true)->get();

        if ($vats) {
            return [
                'success' => true,
                'message' => 'Vat rate retrieved successfully',
                'data' => $vats
            ];
        }
        return [
            'success' => false,
            'message' => 'Vat rate not found',
        ];
    }


}
?>
