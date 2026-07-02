<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CardController extends Controller
{
    /**
     * Create a card in a column.
     */
    public function store(Request $request, Column $column): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $column->cards()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'position' => (int) $column->cards()->max('position') + 1,
        ]);

        return redirect()->route('boards.show', $column->board_id);
    }

    /**
     * Show a card with its comment thread.
     */
    public function show(Card $card): View
    {
        $card->load(['column.board', 'comments.user']);

        return view('cards.show', ['card' => $card]);
    }

    /**
     * Edit a card, or move it to another column (drag/drop persistence).
     */
    public function update(Request $request, Card $card): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'column_id' => ['sometimes', 'integer', 'exists:columns,id'],
        ]);

        // A move must stay within the same board.
        if (isset($data['column_id'])) {
            $target = Column::findOrFail($data['column_id']);
            if ($target->board_id !== $card->column->board_id) {
                throw ValidationException::withMessages([
                    'column_id' => 'The target column must belong to the same board.',
                ]);
            }
        }

        $card->update($data);

        return redirect()->route('boards.show', $card->column->board_id);
    }

    /**
     * Delete a card.
     */
    public function destroy(Card $card): RedirectResponse
    {
        $boardId = $card->column->board_id;
        $card->delete();

        return redirect()->route('boards.show', $boardId);
    }
}
