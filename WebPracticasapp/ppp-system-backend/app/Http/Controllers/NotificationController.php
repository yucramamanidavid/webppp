<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Obtener todas las notificaciones no leídas del usuario autenticado
    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)->where('is_read', false)->get();
        return response()->json($notifications);
    }

    // Marcar una notificación como leída
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notificación marcada como leída.']);
    }
}
