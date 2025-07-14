<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\FAQ;

class FaqService
{
    /**
     * Store a new FAQ entry.
     *
     * This method handles the creation of a new FAQ by accepting the question and answer from the request.
     * It attempts to store the FAQ and returns a success or error message based on the outcome.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing the FAQ data.
     * 
     * @return array An array containing the success status and a message.
     */
    public function storeFaqs($request)
    {
        try {            
            $question = $request->question;
            $answer = $request->answer;

            $faq = FAQ::create([
                'question' => $question,
                'answer' => $answer,
            ]);

            // Return a success response
            return [
                'success' => true,
                'message' => 'FAQ added successfully.',
            ];
        
        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing FAQ entry.
     *
     * This method handles updating an existing FAQ based on its ID. It checks if the FAQ exists, and if so,
     * updates its question and answer. It returns a success or error message based on the result.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing the FAQ data and ID.
     * 
     * @return array An array containing the success status and a message.
     */
    public function updateFaqsData($request)
    {
        try {            

            $faq_id = $request->faq_id;
            $question = $request->question;
            $answer = $request->answer;
    
            $faq = FAQ::find($faq_id);
    
            if(!$faq){
                return [
                    'success' => false,
                    'message' => 'Faq does not exist.',
                ];
            }
    
            $faq->update([
                'question' => $question,
                'answer' => $answer,
            ]);

            return [
                'success' => true,
                'message' => 'FAQ updated successfully.',
            ];
        
        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update the active status of an FAQ.
     *
     * This method allows toggling the `is_active` status of an FAQ entry. If the FAQ exists, it will
     * toggle its status between active and inactive.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing the FAQ ID.
     * 
     * @return array An array containing the success status and a message.
     */
    public function updateFQAStatus($request)
    {
        try {
            $faq_id = $request->faq_id;
            $faq = FAQ::find($faq_id);
        
            if (!$faq) {
                return [
                    'success' => false,
                    'message' => 'Faq not found.',
                ];
            }
            
            // Toggle the status
            $status = $faq->is_active;
            $status = ($status == true) ? 0 : 1;
            $faq->update(['is_active' => $status]);

            // Return a success response
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
