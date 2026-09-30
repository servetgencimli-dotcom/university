<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = SystemNotification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('portal.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = SystemNotification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back()->with('success', 'Bildiriş oxunmuş kimi qeyd edildi.');
    }

    public function markAllAsRead()
    {
        SystemNotification::where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return back()->with('success', 'Bütün bildirişlər oxundu.');
    }
}
