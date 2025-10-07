<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SavingsGoal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SavingsGoalsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_can_view_savings_goals_index()
    {
        $response = $this->actingAs($this->user)->get(route('savings-goals.index'));
        $response->assertStatus(200);
        $response->assertViewIs('savings-goals.index');
    }

    public function test_user_can_create_savings_goal()
    {
        $data = [
            'name' => 'Test Goal',
            'target_amount' => 1000,
            'current_amount' => 0,
            'target_date' => now()->addMonth()->format('Y-m-d'),
            'description' => 'Test description',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)->post(route('savings-goals.store'), $data);

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertDatabaseHas('savings_goals', [
            'name' => 'Test Goal',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_view_savings_goal_show()
    {
        $goal = SavingsGoal::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('savings-goals.show', $goal));
        $response->assertStatus(200);
        $response->assertViewIs('savings-goals.show');
    }

    public function test_user_can_update_savings_goal()
    {
        $goal = SavingsGoal::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'name' => 'Updated Goal',
            'target_amount' => 2000,
            'current_amount' => 100,
            'target_date' => now()->addMonths(2)->format('Y-m-d'),
            'description' => 'Updated description',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)->put(route('savings-goals.update', $goal), $data);

        $response->assertRedirect(route('savings-goals.show', $goal));
        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'name' => 'Updated Goal',
        ]);
    }

    public function test_user_can_delete_savings_goal()
    {
        $goal = SavingsGoal::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('savings-goals.destroy', $goal));

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertDatabaseMissing('savings_goals', ['id' => $goal->id]);
    }

    public function test_user_can_add_savings_to_goal()
    {
        $goal = SavingsGoal::factory()->create([
            'user_id' => $this->user->id,
            'current_amount' => 100,
            'target_amount' => 500,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post(route('savings-goals.addSavings', $goal), [
            'amount' => 200,
        ]);

        $response->assertRedirect(route('savings-goals.show', $goal));
        $goal->refresh();
        $this->assertEquals(300, $goal->current_amount);
    }

    public function test_user_can_toggle_savings_goal_status()
    {
        $goal = SavingsGoal::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post(route('savings-goals.toggleStatus', $goal));

        $response->assertRedirect(route('savings-goals.show', $goal));
        $goal->refresh();
        $this->assertEquals('paused', $goal->status);

        // Toggle back
        $response = $this->actingAs($this->user)->post(route('savings-goals.toggleStatus', $goal));
        $goal->refresh();
        $this->assertEquals('active', $goal->status);
    }
}
