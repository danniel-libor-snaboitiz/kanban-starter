<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a demo user plus "alice" (the @mention target) and one
     * populated board, so the app is explorable right after seeding.
     */
    public function run(): void
    {
        $me = User::updateOrCreate(
            ['email' => 'me@example.com'],
            [
                'name' => 'Demo User',
                'username' => 'me',
                'password' => Hash::make('password'),
            ],
        );

        User::updateOrCreate(
            ['email' => 'alice@example.com'],
            [
                'name' => 'Alice',
                'username' => 'alice',
                'password' => Hash::make('password'),
            ],
        );

        if ($me->boards()->exists()) {
            return;
        }

        $board = Board::create(['user_id' => $me->id, 'name' => 'Sprint Board']);

        foreach (['Todo', 'Doing', 'Done'] as $i => $name) {
            $column = Column::create([
                'board_id' => $board->id,
                'name' => $name,
                'position' => $i,
            ]);

            if ($name === 'Todo') {
                Card::create([
                    'column_id' => $column->id,
                    'title' => 'Design the board view',
                    'description' => 'Lay out the three columns and the card tiles.',
                    'position' => 0,
                ]);
            }
        }
    }
}
