<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Post a comment on a card, then notify any mentioned users.
     */
    public function store(Request $request, Card $card): RedirectResponse
    {
        abort_unless($card->column->board->user_id === Auth::id(), 403);

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $comment = $card->comments()->create([
            'user_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        $this->notifications->notifyMentioned($comment);

        return redirect()->route('cards.show', $card);
    }
}
