<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnershipAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build an owned board -> column -> card chain for the given user.
     *
     * @return array{0: Board, 1: Column, 2: Card}
     */
    private function ownedChain(User $owner): array
    {
        $board = Board::factory()->for($owner)->create();
        $column = Column::factory()->for($board)->create();
        $card = Card::factory()->for($column)->create();

        return [$board, $column, $card];
    }

    public function test_a_user_cannot_rename_another_users_board(): void
    {
        $owner = User::factory()->create();
        [$board] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->patch("/boards/{$board->id}", ['name' => 'Hijacked'])
            ->assertForbidden();

        $this->assertDatabaseHas('boards', ['id' => $board->id, 'name' => $board->name]);
    }

    public function test_a_user_cannot_delete_another_users_board(): void
    {
        $owner = User::factory()->create();
        [$board] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->delete("/boards/{$board->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('boards', ['id' => $board->id]);
    }

    public function test_a_user_cannot_add_a_column_to_another_users_board(): void
    {
        $owner = User::factory()->create();
        [$board] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->post("/boards/{$board->id}/columns", ['name' => 'Sneaky'])
            ->assertForbidden();
    }

    public function test_a_user_cannot_update_another_users_column(): void
    {
        $owner = User::factory()->create();
        [, $column] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->patch("/columns/{$column->id}", ['name' => 'Sneaky'])
            ->assertForbidden();
    }

    public function test_a_user_cannot_create_a_card_in_another_users_column(): void
    {
        $owner = User::factory()->create();
        [, $column] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->post("/columns/{$column->id}/cards", ['title' => 'Sneaky card'])
            ->assertForbidden();
    }

    public function test_a_user_cannot_view_another_users_card(): void
    {
        $owner = User::factory()->create();
        [, , $card] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->get("/cards/{$card->id}")
            ->assertForbidden();
    }

    public function test_a_user_cannot_update_another_users_card(): void
    {
        $owner = User::factory()->create();
        [, , $card] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->patch("/cards/{$card->id}", ['title' => 'Hijacked'])
            ->assertForbidden();
    }

    public function test_a_user_cannot_delete_another_users_card(): void
    {
        $owner = User::factory()->create();
        [, , $card] = $this->ownedChain($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->delete("/cards/{$card->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('cards', ['id' => $card->id]);
    }

    public function test_the_owner_can_still_rename_their_own_board(): void
    {
        $owner = User::factory()->create();
        [$board] = $this->ownedChain($owner);

        $this->actingAs($owner)
            ->patch("/boards/{$board->id}", ['name' => 'Renamed'])
            ->assertRedirect();

        $this->assertDatabaseHas('boards', ['id' => $board->id, 'name' => 'Renamed']);
    }
}
