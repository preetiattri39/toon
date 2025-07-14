<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class ProfileSettingController extends AdminBaseController
{
    /**
     * Display the profile page for the currently authenticated admin.
     *
     * This method retrieves the admin user's information and passes it
     * to the view where it can be displayed and updated.
     *
     * @param \Illuminate\Http\Request $request The request instance.
     * @return \Illuminate\View\View The profile view for the admin.
     */
    public function profile(Request $request)
    {
        // Get the currently authenticated admin user
        $admin_user = Auth::guard('admin')->user();
        $admin_user_id = $admin_user->id;

        // Retrieve admin user details from the database
        $admin_user = Admin::find($admin_user_id);

        return view('admin.auth.profile', compact('admin_user'));
    }

    /**
     * Update the admin profile information.
     *
     * This method validates the request data for updating the admin's profile
     * and calls the profile service to update the details in the database.
     * It will flash a success or error message to the session based on the result.
     *
     * @param \Illuminate\Http\Request $request The request object containing updated profile details.
     * @return \Illuminate\Http\RedirectResponse Redirects to the profile page or back with success/error message.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required',
            'gender' => 'required|in:male,female,other',
        ]);

        // Call the profile service to update the profile
        $result = $this->profile_setting_service->updateAdminProfile($request);

        // Handle the response from the service
        if ($result['success']) {
            // Flash a success message and redirect to the profile page
            $request->session()->flash('success', $result['message']);
            return redirect()->route('admin-profile');
        }

        // Flash an error message and redirect back to the profile update page
        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Display the form to change the admin's password.
     *
     * This method renders the password change form view.
     *
     * @return \Illuminate\View\View The view with the password change form.
     */
    public function changePasswordForm()
    {
        return view('admin.auth.change-password');
    }

    /**
     * Change the admin password.
     *
     * This method validates the request data for the current password and the new password,
     * then calls the profile service to update the password. It will flash a success or
     * error message to the session based on the result.
     *
     * @param \Illuminate\Http\Request $request The request object containing current and new password details.
     * @return \Illuminate\Http\RedirectResponse Redirects to the dashboard or back with success/error message.
     */
    public function changePassword(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
        ]);

        // Call the profile service to change the password
        $result = $this->profile_setting_service->changeAdminPassword($request);

        // Handle the response from the service
        if ($result['success']) {
            // Flash a success message and redirect to the dashboard
            $request->session()->flash('success', $result['message']);
            return redirect()->route('dashboard');
        }

        // Flash an error message and redirect back to the password change page
        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }
}
