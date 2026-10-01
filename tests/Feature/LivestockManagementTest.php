<?php

namespace Tests\Feature;

use App\Models\LivestockCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivestockManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_livestock_pages_require_authentication(): void
    {
        $this->get(route('admin.livestock-batches.index'))->assertRedirect(route('login'));
        $this->get(route('admin.livestock-categories.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_create_a_category_and_livestock_batch(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.livestock-categories.index'))->assertOk();
        $this->get(route('admin.livestock-batches.index'))->assertOk();

        $this->post(route('admin.livestock-categories.store'), [
            'name' => 'Brahman',
            'description' => 'Ganado de carne',
            'active' => '1',
        ])->assertRedirect(route('admin.livestock-categories.index'));

        $category = LivestockCategory::where('user_id', $user->id)->firstOrFail();
        $this->post(route('admin.livestock-batches.store'), [
            'livestock_category_id' => $category->id,
            'code' => 'LOT-100',
            'ear_tag' => 'H-88',
            'head_count' => 20,
            'average_weight_kg' => 420.50,
            'farm_name' => 'Finca Norte',
            'paddock' => 'Potrero 2',
            'status' => 'active',
        ])->assertRedirect(route('admin.livestock-batches.index'));

        $this->assertDatabaseHas('livestock_batches', [
            'user_id' => $user->id,
            'livestock_category_id' => $category->id,
            'code' => 'LOT-100',
            'head_count' => 20,
        ]);

        $this->get(route('admin.livestock-batches.index'))->assertOk()->assertSee('LOT-100');
    }

    public function test_user_cannot_access_another_users_batch_or_category(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = $owner->livestockCategories()->create(['name' => 'Nelore', 'active' => true]);
        $batch = $owner->livestockBatches()->create([
            'livestock_category_id' => $category->id,
            'code' => 'LOT-200',
            'head_count' => 5,
            'status' => 'active',
        ]);

        $this->actingAs($otherUser)
            ->get(route('admin.livestock-batches.edit', $batch))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->delete(route('admin.livestock-categories.destroy', $category))
            ->assertForbidden();
    }

    public function test_batch_must_use_a_category_owned_by_the_current_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $foreignCategory = $otherUser->livestockCategories()->create(['name' => 'Angus', 'active' => true]);

        $this->actingAs($user)->from(route('admin.livestock-batches.index'))
            ->post(route('admin.livestock-batches.store'), [
                'livestock_category_id' => $foreignCategory->id,
                'code' => 'LOT-300',
                'head_count' => 10,
                'status' => 'active',
            ])
            ->assertSessionHasErrors('livestock_category_id');

        $this->assertDatabaseMissing('livestock_batches', ['code' => 'LOT-300']);
    }

    public function test_category_with_batches_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = $user->livestockCategories()->create(['name' => 'Gyr', 'active' => true]);
        $user->livestockBatches()->create([
            'livestock_category_id' => $category->id,
            'code' => 'LOT-400',
            'head_count' => 2,
            'status' => 'active',
        ]);

        $this->actingAs($user)->delete(route('admin.livestock-categories.destroy', $category))
            ->assertRedirect(route('admin.livestock-categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('livestock_categories', ['id' => $category->id]);
    }
}
