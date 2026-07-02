<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KanbanFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_board_seeds_three_columns(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/boards', ['name' => 'Sprint'])->assertRedirect();

        $board = Board::first();
        $this->assertNotNull($board);
        $this->assertSame(
            ['Todo', 'Doing', 'Done'],
            $board->columns()->orderBy('position')->pluck('name')->all(),
        );
    }

    public function test_owner_can_view_board_card_and_notifications_pages(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $column = Column::factory()->for($board)->create();
        $card = Card::factory()->for($column)->create();
        Notification::factory()->create(['user_id' => $user->id, 'card_id' => $card->id]);

        $this->actingAs($user)->get("/boards/{$board->id}")->assertOk();
        $this->actingAs($user)->get("/cards/{$card->id}")->assertOk();
        $this->actingAs($user)->get('/notifications')->assertOk();
    }

    public function test_creating_a_card_in_a_column(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $column = Column::factory()->for($board)->create();

        $this->actingAs($user)
            ->post("/columns/{$column->id}/cards", ['title' => 'Write tests'])
            ->assertRedirect();

        $this->assertDatabaseHas('cards', ['column_id' => $column->id, 'title' => 'Write tests']);
    }

    public function test_moving_a_card_persists_the_new_column(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $todo = Column::factory()->for($board)->create(['name' => 'Todo']);
        $doing = Column::factory()->for($board)->create(['name' => 'Doing']);
        $card = Card::factory()->for($todo)->create();

        $this->actingAs($user)
            ->patch("/cards/{$card->id}", ['column_id' => $doing->id])
            ->assertRedirect();

        $this->assertDatabaseHas('cards', ['id' => $card->id, 'column_id' => $doing->id]);
    }

    public function test_a_card_cannot_be_moved_to_another_boards_column(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $todo = Column::factory()->for($board)->create();
        $card = Card::factory()->for($todo)->create();

        $otherBoard = Board::factory()->for($user)->create();
        $otherColumn = Column::factory()->for($otherBoard)->create();

        $this->actingAs($user)
            ->patch("/cards/{$card->id}", ['column_id' => $otherColumn->id])
            ->assertSessionHasErrors('column_id');

        $this->assertDatabaseHas('cards', ['id' => $card->id, 'column_id' => $todo->id]);
    }

    public function test_clicking_a_notification_marks_it_read(): void
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $column = Column::factory()->for($board)->create();
        $card = Card::factory()->for($column)->create();
        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'card_id' => $card->id,
        ]);

        $this->actingAs($user)
            ->patch("/notifications/{$notification->id}")
            ->assertRedirect(route('cards.show', $card));

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
