<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Đánh dấu 1 thông báo là đã đọc và chuyển hướng đến URL đích (nếu có)
     */
    public function danhDauDaDoc(Request $request, string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $targetUrl = $notification->data['url'] ?? null;
        if ($targetUrl) {
            return redirect($targetUrl);
        }

        return back()->with('status', 'Đã đánh dấu thông báo là đã đọc.');
    }

    /**
     * Đánh dấu tất cả thông báo của người dùng hiện tại là đã đọc
     */
    public function danhDauTatCaDaDoc()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('status', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
