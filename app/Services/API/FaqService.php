<?php 

namespace App\Services\API;

use App\Models\FAQ;

class FaqService
{
    /**
     * Retrieve a list of active FAQs ordered by most recent.
     *
     * @return array{
     *     success: bool,
     *     message: string,
     *     data?: \Illuminate\Support\Collection<FAQ>
     * }
     *
     * @throws \Exception If an error occurs during database query execution.
     */
    public function faqData(): array
    {
        try {
            $faq = FAQ::where('is_active', true)
                ->orderBy('id', 'desc')
                ->get();

            return [
                'success' => true,
                'message' => 'FAQ retrieved successfully',
                'data' => $faq
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        } 
    }
}
?>