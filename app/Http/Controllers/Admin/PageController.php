<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PageContent;

class PageController extends AdminBaseController
{
    /**
     * Display a list of all pages along with their content.
     *
     * This method retrieves all the pages and their associated content from the database
     * and passes them to the 'admin.page.index' view for display.
     *
     * @return \Illuminate\View\View The view displaying the list of pages
     */
    public function index()
    {   
        $pages = Page::with('pageContent')->get();
        return view('admin.page.index',compact('pages'));
    }

    /**
     * Display the page editing form for a specific page.
     *
     * This method retrieves a single page, along with its content, based on the page ID
     * and passes the page data to the 'admin.page.edit' view for editing.
     *
     * @param  int  $page_id The ID of the page to be edited
     * @return \Illuminate\View\View The view for editing the specific page
     */
    public function viewPage($page_id)
    {
        $page = Page::with('pageContent')->find($page_id);
        return view('admin.page.edit',compact('page'));
    }

    /**
     * Update the content of a page in the database.
     *
     * This method handles the form submission for updating the page's content. It validates the incoming data,
     * updates the page content in the database, and redirects the user back to the page list with a success message.
     *
     * @param  \Illuminate\Http\Request  $request The request containing the page data to be updated
     * @return \Illuminate\Http\RedirectResponse Redirects to the page list with a success message
     */
    public function updatePageContent(Request $request)
    {
        // Retrieve the page ID from the request
        $page_id = $request->page_id;

        // Validate incoming request data
        $request->validate([
            'content_title' => 'required|string|max:255',
            'page_slug' => 'nullable|string|unique:pages,slug,' . $page_id . '|max:255',
            'content' => 'required|string',
        ]);
        
        // Find the page by ID
        $page = Page::find($page_id);
    
        // Create or update the PageContent for the page
        $pageContent = PageContent::updateOrCreate(
            ['page_id' => $page->id],
            ['name' => $request->content_title, 'page_content' => $request->content]
        );
    
        // Flash a success message to the session
        $request->session()->flash('success', 'Page updated successfully');

        // Redirect to the page list
        return redirect()->route('page-list');
    }
}
