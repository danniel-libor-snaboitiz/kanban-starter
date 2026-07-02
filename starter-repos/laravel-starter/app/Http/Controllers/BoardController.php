<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BoardController extends Controller
{
    /**
     * List the current user's boards.
     */
    public function index(): View
    {
        $boards = Auth::user()->boards()->latest()->get();

        return view('boards.index', ['boards' => $boards]);
    }

    /**
     * Show the "new board" form.
     */
    public function create(): View
    {
        return view('boards.create');
    }

    /**
     * Persist a new board and seed its default columns.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $board = Auth::user()->boards()->create($data);

        foreach (['Todo', 'Doing', 'Done'] as $position => $name) {
            $board->columns()->create(['name' => $name, 'position' => $position]);
        }

        return redirect()->route('boards.show', $board);
    }

    /**
     * Show a single board with its columns and cards.
     */
    public function show(Board $board): View
    {
        $this->authorizeBoard($board);

        $board->load(['columns' => fn ($q) => $q->orderBy('position'), 'columns.cards' => fn ($q) => $q->orderBy('position')]);

        return view('boards.show', ['board' => $board]);
    }

    /**
     * Rename a board.
     */
    public function update(Request $request, Board $board): RedirectResponse
    {
        $this->authorizeBoard($board);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $board->update($data);

        return redirect()->route('boards.show', $board);
    }

    /**
     * Delete a board.
     */
    public function destroy(Board $board): RedirectResponse
    {
        $this->authorizeBoard($board);

        $board->delete();

        return redirect()->route('boards.index');
    }

    /**
     * Ensure the board belongs to the authenticated user.
     */
    protected function authorizeBoard(Board $board): void
    {
        abort_unless($board->user_id === Auth::id(), 403);
    }
}
