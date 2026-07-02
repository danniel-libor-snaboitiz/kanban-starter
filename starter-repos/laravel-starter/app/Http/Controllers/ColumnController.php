<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    /**
     * Add a column to a board.
     */
    public function store(Request $request, Board $board): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $board->columns()->create([
            'name' => $data['name'],
            'position' => (int) $board->columns()->max('position') + 1,
        ]);

        return redirect()->route('boards.show', $board);
    }

    /**
     * Rename or reorder a column.
     */
    public function update(Request $request, Column $column): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'integer', 'min:0'],
        ]);

        $column->update($data);

        return redirect()->route('boards.show', $column->board_id);
    }

    /**
     * Delete a column.
     */
    public function destroy(Column $column): RedirectResponse
    {
        $boardId = $column->board_id;
        $column->delete();

        return redirect()->route('boards.show', $boardId);
    }
}
