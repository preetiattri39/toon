<?php 

namespace App\Services\API;

use App\Models\Page;
use App\Models\PageContent;

class PagesService
{
    
    /**
     * Retrieve the details of a page based on the provided slug.
     *
     * @param  \Illuminate\Http\Request  $request The incoming request containing the slug parameter
     * @return array An associative array containing the success status, message, and page data (if applicable)
     */
    public function getPageDetails($request)
    {
        try {
            // Retrieve the slug from the request
            $slug = $request->slug;

            // Check if the slug is provided
            if (!$slug) {
                return [
                    'success' => false,
                    'message' => 'Slug is required.',  // Return a message if slug is missing
                ];
            }

            // Attempt to retrieve the page with the given slug, including its content
            $page = Page::where('slug', $slug)
                ->with(['pageContent'])
                ->first();

            // Check if the page was found in the database
            if (!$page) {
                return [
                    'success' => false,
                    'message' => 'Page not found.',  // Return a message if the page does not exist
                ];
            }

            // Return a success response with the retrieved page data
            return [
                'success' => true,
                'message' => 'Page retrieved successfully.',
                'data' => $page,  // Include the page data in the response
            ];

        } catch (\Exception $e) {
            // Return an error response if an exception occurs during the process
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),  // Include the exception message for debugging
            ];
        }
    }
}
?>
