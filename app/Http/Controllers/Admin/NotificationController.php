<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Notifications;
use Auth;

class NotificationController extends AdminBaseController
{
    /**
     * Display a list of unread customer notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {    
        $user = Auth::guard('admin')->user();
        $role = $user->roles->first()->name;
        $notifications = DB::table('notifications')
        ->where('read_status', false)
        ->where('type', 'customer')
        ->orderBy('id', 'desc')
        ->get();
        return view('admin.notification.index',compact('notifications'));
    }

     /**
     * Show details of a specific notification.
     *
     * @param  int  $notification_id  The ID of the notification to retrieve
     * @return \Illuminate\View\View
     */
    public function notificationDetails($notification_id)
    {
        $notification = Notifications::find($notification_id);
        return view('admin.notification.details',compact('notification'));
    }

    /**
     * Mark a notification as read and redirect to the notification list.
     *
     * @param  Request  $request  The incoming request containing the notification ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markRead(Request $request)
    {
        $notificationId = $request->notification_id;
        if ($notificationId) {
            // Update the notification's read_status in the database
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update(['read_status' => true,'read_at'=>now()]);
            
            return redirect()->route('notification-list');
        }
    }



    /**
     * Mark a notification as read via AJAX and return a JSON response.
     *
     * @param  Request  $request  The incoming request containing the notification ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('id');

        if ($notificationId) {
            // Update the notification's read_status in the database
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update(['read_status' => true,'read_at'=>now()]);
            
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid notification ID']);
    }
}
