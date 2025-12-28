<?php

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

if (!function_exists('createNotification')) {
    function createNotification($user_id, $title, $message)
    {
        try {
            Notification::create([
                'user_id' => $user_id,
                'title' => $title,
                'msg' => $message,
            ]);
            return true;
        } catch (\Exception $e) { // More specific error handling
            Log::info("notification creation error :", $e->getMessage());
        }
    }
}

if (!function_exists('showNotifications')) {
    function showNotifications()
    {
        try {
            $user = Auth::user();
            return Notification::where('user_id', $user->id)->get();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
