<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends AdminBaseController
{
    /**
     * Show the login form for the admin user.
     *
     * This method renders the login page where the admin can enter their credentials.
     *
     * @return \Illuminate\View\View The login page view.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Authenticate the admin user and log them in.
     *
     * This method validates the incoming login request, calls the login service 
     * to authenticate the admin user, and redirects to the dashboard if successful.
     * In case of a failure, it redirects back to the login form with an error message.
     *
     * @param \Illuminate\Http\Request $request The incoming login request containing the email and password.
     * @return \Illuminate\Http\RedirectResponse Redirects to the dashboard or back to the login page with a message.
     */
    public function login(Request $request)
    {
        // Validate the email and password fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Call the login service to authenticate the admin user
        $result = $this->login_service->loginAdminUser($request);

        if ($result['success']) {
            // Flash a success message and redirect to the dashboard
            $request->session()->flash('success', $result['message']);
            return redirect()->route('dashboard');
        }

        // Flash an error message and redirect back to the login page
        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Log out the currently authenticated admin user.
     *
     * This method logs out the authenticated admin user, clears the session, 
     * and redirects to the login page with a success message.
     *
     * @return \Illuminate\Http\RedirectResponse Redirects to the login page with a success message.
     */
    public function logoutAdmin()
    {
        // Retrieve the authenticated admin user
        $admin = Auth::guard('admin')->user();

        // Log out the admin user
        Auth::guard('admin')->logout();

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
