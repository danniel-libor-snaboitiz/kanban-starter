<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * The current user's notification feed, unread first.
     */
    public function index(): View
    {
        $notifications = Auth::user()->notifications()
            ->with(['actor', 'card'])
            ->orderByRaw('read_at is null desc')
            ->latest()
            ->get();

        return view('notifications.index', ['notifications' => $notifications]);
    }

    /**
     * Mark a notification read and jump to its source card.
     */
    public function update(Notification $notification): RedirectResponse
    {
        $notification->update(['read_at' => now()]);

        return redirect()->route('cards.show', $notification->card_id);
    }
}
