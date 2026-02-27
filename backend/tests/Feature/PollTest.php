<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Poll;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PollTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_poll()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->postJson('/api/polls', [
            'question' => 'Best PHP Framework?',
            'options' => ['Laravel', 'Symfony', 'CodeIgniter']
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('polls', [
            'question' => 'Best PHP Framework?',
            'user_id' => $admin->id
        ]);
        $this->assertDatabaseCount('options', 3);
    }

    public function test_non_admin_cannot_create_poll()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->postJson('/api/polls', [
            'question' => 'Hacker Question?',
            'options' => ['A', 'B']
        ]);

        $response->assertStatus(403);
    }

    public function test_can_fetch_active_polls()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        // Active poll
        Poll::factory()->create(['user_id' => $admin->id, 'is_active' => true]);
        // Inactive poll
        Poll::factory()->create(['user_id' => $admin->id, 'is_active' => false]);

        $response = $this->getJson('/api/active-polls');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }

    public function test_poll_show_includes_has_voted_status()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $poll = Poll::factory()->create(['user_id' => $admin->id, 'is_active' => true]);
        $option = Option::factory()->create(['poll_id' => $poll->id]);

        // Initially should be false
        $this->getJson("/api/polls/{$poll->slug}")
             ->assertJsonPath('has_voted', false);

        // Vote
        $this->postJson("/api/polls/{$poll->slug}/vote", [
            'option_id' => $option->id,
        ]);

        // Now should be true
        $this->getJson("/api/polls/{$poll->slug}")
             ->assertJsonPath('has_voted', true);
    }

    public function test_user_can_vote()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $poll = Poll::factory()->create(['user_id' => $admin->id]);
        $option = Option::factory()->create(['poll_id' => $poll->id]);

        $response = $this->postJson("/api/polls/{$poll->slug}/vote", [
            'option_id' => $option->id,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Vote recorded successfully.']);
                 
        $this->assertDatabaseHas('votes', [
            'poll_id' => $poll->id,
            'option_id' => $option->id,
        ]);
    }

    public function test_user_cannot_vote_twice()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $poll = Poll::factory()->create(['user_id' => $admin->id]);
        $option1 = Option::factory()->create(['poll_id' => $poll->id]);
        $option2 = Option::factory()->create(['poll_id' => $poll->id]);

        // First vote
        $this->postJson("/api/polls/{$poll->slug}/vote", [
            'option_id' => $option1->id,
        ])->assertStatus(200);

        // Second vote attempt from same IP (default for test environment)
        $response = $this->postJson("/api/polls/{$poll->slug}/vote", [
            'option_id' => $option2->id,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['vote']);
    }
}
