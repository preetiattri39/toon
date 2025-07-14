<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\VatRate;
use App\Models\Country;

class VatRateService
{

    public function storeVatRat($request)
    {
        try {
        
            // Check if there's already a VAT rate for the given country and region
            $existingVatRate = VatRate::where('country_id', $request->country_id)
                                      ->first();
    
            if ($existingVatRate) {
                return [
                    'success' => false,
                    'message' => 'A VAT rate already exists for this country.'
                ];
            }
    
            // Create a new VAT rate
            $add_vat_rate = VatRate::create([
                'country_id' => $request->country_id,
                // 'region_id' => $request->region_id,
                'vat_name' => $request->vat_name,
                'vat_rate' => $request->vat_rate,
                'description' => $request->description,
            ]);
    
            return [
                'success' => true,
                'message' => 'VAT rate stored successfully.',
                'data' => $add_vat_rate
            ];
    
        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }    

  
    public function updateVatRate($request)
    {
        try {
            // Retrieve the VAT rate to be updated
            $update_vatrate = VatRate::find($request->vat_id);
    
            if (!$update_vatrate) {
                return [
                    'success' => false,
                    'message' => 'VAT rate not found.',
                ];                
            }
    
            $existingVatRate = VatRate::where('country_id', $request->country_id)
                                      ->where('id', '!=', $request->vat_id)
                                      ->first();
    
            if ($existingVatRate) {
                return [
                    'success' => false,
                    'message' => 'A VAT rate already exists for this country.',
                ];
            }
    
            // Update the VAT rate
            $update_vatrate->update([
                'vat_name' => $request->vat_name,
                'vat_rate' => $request->vat_rate,
                'description' => $request->description,
            ]);
    
            return [
                'success' => true,
                'message' => 'VAT rate updated successfully.',
                'data' => $update_vatrate
            ];
    
        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }    

    
    public function updateStatus($request)
    {
        try {
            $vatrate = VatRate::find($request->vat_id);

            if (!$vatrate) {
                return [
                    'success' => false,
                    'message' => 'Vat rate not found.',
                ];
            }

            $status = ($vatrate->status == true) ? 0 : 1;
            $vatrate->update(['status' => $status]);

            return [
                'success' => true,
                'message' => 'Status changed successfully.',
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

}
?>
