<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends AdminBaseController
{
    /**
     * Display the forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function forgotPasswordForm()
    {
        return view('admin.auth.forgot');
    }

    /**
     * Display the password reset form.
     *
     * @param string|null $token The password reset token (optional).
     * @return \Illuminate\View\View
     */
    public function resetPasswordForm($token = null)
    {
        return view('admin.auth.reset', compact('token'));
    }

    /**
     * Handle the submission of the forgot password form.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing the form data.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forgotPassword(Request $request)
    {
        // Validate the email input
        $request->validate([
            'email' => 'required|email',
        ]);

        // Process the forgot password request using the service
        $result = $this->forgot_password_service->forgotUserPassword($request);

        if ($result['success']) {
            // Flash success message and redirect back
            $request->session()->flash('success', $result['message']);
            return redirect()->back();
        }

        // Flash error message and redirect back
        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

    /**
     * Handle the submission of the password reset form.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing the form data.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        // Validate the password input
        $request->validate([
            'password' => 'required|min:8|confirmed', // Password must be at least 8 characters and match confirmation
            'password_confirmation' => 'required_with:password|same:password', // Password confirmation is required and must match
        ]);

        // Process the password reset request using the service
        $result = $this->forgot_password_service->resetAdminUserPassword($request);

        if ($result['success']) {
            // Flash success message and redirect to the login page
            $request->session()->flash('success', $result['message']);
            return redirect()->route('login');
        }

        // Flash error message and redirect back
        $request->session()->flash('error', $result['message']);
        return redirect()->back();
    }

}
