<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_real_account_scoped_inventory_metrics(): void
    {
        $user = User::factory()->create(['name' => 'Ana Ganadera']);
        $category = $user->livestockCategories()->create(['name' => 'Brahman', 'active' => true]);
        $user->livestockBatches()->create([
            'livestock_category_id' => $category->id,
            'code' => 'OWN-LOT-1',
            'head_count' => 10,
            'average_weight_kg' => 400,
            'farm_name' => 'Finca propia',
            'status' => 'active',
        ]);
        $user->livestockBatches()->create([
            'livestock_category_id' => $category->id,
            'code' => 'OWN-LOT-2',
            'head_count' => 3,
            'average_weight_kg' => 300,
            'status' => 'sold',
        ]);

        $otherUser = User::factory()->create();
        $otherCategory = $otherUser->livestockCategories()->create(['name' => 'Angus', 'active' => true]);
        $otherUser->livestockBatches()->create([
            'livestock_category_id' => $otherCategory->id,
            'code' => 'FOREIGN-LOT',
            'head_count' => 999,
            'average_weight_kg' => 800,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ana Ganadera')
            ->assertSee('10')
            ->assertSee('400.00 kg')
            ->assertSee('OWN-LOT-1')
            ->assertDontSee('FOREIGN-LOT')
            ->assertDontSee('999');
    }

    public function test_dashboard_renders_an_empty_inventory_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Aún no hay lotes en tu inventario.')
            ->assertSee('Registrar el primer lote');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
