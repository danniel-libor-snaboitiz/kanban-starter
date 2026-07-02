<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => 'Write training slides',
            'description' => 'First draft of the Kanban board module.',
            'status' => 'todo',
        ]);

        $response->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Write training slides',
            'status' => 'todo',
        ]);
    }
}
