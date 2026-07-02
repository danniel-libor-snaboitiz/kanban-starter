<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Post a comment on a card.
     *
     * (Mention parsing + notification fan-out is layered on in a later step.)
     */
    public function store(Request $request, Card $card): RedirectResponse
    {
        abort_unless($card->column->board->user_id === Auth::id(), 403);

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $card->comments()->create([
            'user_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        return redirect()->route('cards.show', $card);
    }
}
