<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VatRate;
use App\Models\Country;

class VatController extends AdminBaseController
{
 
    public function index()
    {
        return view('admin.vat.index');
    }

   
    public function getVatRateData(Request $request)
    {
        $query = VatRate::query();

        // Apply custom search if present
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('vat_name', 'like', "%{$searchTerm}%")
                ->orWhere('vat_rate', 'like', "%{$searchTerm}%");
            });
        }

        // Apply status filter if present
        if ($request->has('status_filter') && $request->status_filter != '') {
            $status = $request->status_filter;
            $query->where('status', $status);
        }

        // Handle sorting
        $orderColumnIndex = $request->input('order.0.column');  // Get the column index to order by
        $orderDirection = $request->input('order.0.dir');  // Get the order direction (asc/desc)
        $columns = $request->input('columns');  // Columns data (from DataTable)

        // Map column index to actual column name (based on your DataTable definition)
        $orderColumn = $columns[$orderColumnIndex]['name'];

        // Apply sorting to query
        $query->orderBy($orderColumn, $orderDirection);

        // Pagination (server-side)
        $totalRecords = $query->count();
        $vat = $query->skip($request->start)
                    ->take($request->length)
                    ->with(['country'])
                    ->get();

        // Return DataTables format response
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, 
            'data' => $vat,
        ]);
    }

  
    public function addVatRateForm()
    {
        $countries = Country::all();
        return view('admin.vat.add',compact('countries'));
    }

   
    public function addVatRat(Request $request)
    {
        $request->validate([
            'vat_rate' => 'required',
        ]);

        $response = $this->vat_rate_service->storeVatRat($request);

        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('vat.rates.index');
        }

        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

    
    public function vatDetails($vat_id)
    {
        $countries = Country::all();
        $vat_rate = VatRate::find($vat_id);
        return view('admin.vat.edit', compact('vat_rate','countries'));
    }


    public function updateVat(Request $request)
    {
        $request->validate([
            'vat_rate' => 'required',
        ]);

        $response = $this->vat_rate_service->updateVatRate($request);

        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('vat.rates.index');
        }

        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

  
    public function vatRateDelete(Request $request, $vat_id)
    {
        $vat_rate = VatRate::find($vat_id);

        if (!$vat_rate) {
            $request->session()->flash('error', 'Failed to delete the vat rate. Please try again');
            return redirect()->back();
        }

        $vat_rate->delete();
        $request->session()->flash('success', 'The vat rate was successfully deleted.');
        return redirect()->route('vat.rates.index');
    }

  
    public function updateVatRateStatus(Request $request)
    {
        $response = $this->vat_rate_service->updateStatus($request);

        // Handle the response from the service
        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Status changed successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please check and try again.',
        ]);
    }

}
