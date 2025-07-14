<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\User;


class DashboardController extends AdminBaseController
{
    public function index()
    {
        $current_sales = order::sum('total');
        $previous_sales = order::where('created_at', '<', now()->subMonth())->sum('total'); // Last month's sales
        $last_month_sales = order::whereMonth('created_at', now()->subMonth()->month)->sum('total');

        // Calculate the percentage growth
        if ($previous_sales > 0) {
            $growth_percentage = (($current_sales - $previous_sales) / $previous_sales) * 100;
        } else {
            $growth_percentage = 0; // If no sales in the previous period
        }

        // Calculate percentage difference
        if ($last_month_sales > 0) {
            $percentage_since_last_month = (($current_sales - $last_month_sales) / $last_month_sales) * 100;
        } else {
            $percentage_since_last_month = 0;
        }

        // Total number of users now
        $current_users = User::count(); 
        $previous_month_users = User::where('created_at', '<', now()->subMonth())->count();

        // Calculate the percentage growth
        if ($previous_month_users > 0) {
            $growth_percentage_user = (($current_users - $previous_month_users) / $previous_month_users) * 100;
        } else {
            $growth_percentage_user = 0;
        }

        $last_month_users = User::whereMonth('created_at', now()->subMonth()->month)->count();

        // Calculate percentage difference
        if ($last_month_users > 0) {
            $percentage_since_last_month_user = (($current_users - $last_month_users) / $last_month_users) * 100;
        } else {
            $percentage_since_last_month_user = 0;
        }


        return view('admin.dashboard.index',compact('current_sales','growth_percentage','percentage_since_last_month','current_users','growth_percentage_user','percentage_since_last_month_user'));
    }
}
