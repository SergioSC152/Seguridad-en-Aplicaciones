<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LivestockBatchImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggested_or_custom_breed_is_created_only_for_the_owner_and_reused(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $other->livestockCategories()->create(['name' => 'Brahman', 'active' => true]);

        foreach (['LOT-A', 'LOT-B'] as $code) {
            $this->actingAs($user)->post(route('admin.livestock-batches.store'), [
                'new_category_name' => 'Brahman', 'code' => $code, 'head_count' => 2, 'status' => 'active',
            ])->assertSessionHasNoErrors()->assertRedirect(route('admin.livestock-batches.index'));
        }
        $this->assertSame(1, $user->livestockCategories()->where('name', 'Brahman')->count());
        $this->assertSame(2, $user->livestockBatches()->count());
        $this->actingAs($user)->post(route('admin.livestock-batches.store'), [
            'new_category_name' => 'Raza particular', 'code' => 'LOT-C', 'head_count' => 1, 'status' => 'active',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('livestock_categories', ['user_id' => $user->id, 'name' => 'Raza particular']);
    }

    public function test_image_url_is_saved_and_replacement_removal_and_batch_deletion_clean_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = $user->livestockCategories()->create(['name' => 'Angus', 'active' => true]);
        $data = ['livestock_category_id' => $category->id, 'code' => 'PHOTO', 'head_count' => 3, 'status' => 'active'];
        $this->actingAs($user)->post(route('admin.livestock-batches.store'), $data + [
            'image' => UploadedFile::fake()->image('first.png'),
        ])->assertSessionHasNoErrors();
        $batch = $user->livestockBatches()->firstOrFail();
        $firstPath = $batch->image_path;
        Storage::disk('public')->assertExists($firstPath);
        $this->assertSame('/storage/'.$firstPath, $batch->image_url);
        $this->get(route('admin.livestock-batches.index'))->assertOk()->assertSee($batch->image_url);

        $this->put(route('admin.livestock-batches.update', $batch), $data + [
            'image' => UploadedFile::fake()->image('second.jpg'),
        ])->assertSessionHasNoErrors();
        $secondPath = $batch->refresh()->image_path;
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);

        $this->put(route('admin.livestock-batches.update', $batch), $data)->assertSessionHasNoErrors();
        $this->assertSame($secondPath, $batch->refresh()->image_path);
        $this->put(route('admin.livestock-batches.update', $batch), $data + ['remove_image' => '1'])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($secondPath);
        $this->assertNull($batch->refresh()->image_url);

        $this->put(route('admin.livestock-batches.update', $batch), $data + [
            'image' => UploadedFile::fake()->image('third.png'),
        ])->assertSessionHasNoErrors();
        $thirdPath = $batch->refresh()->image_path;
        $this->delete(route('admin.livestock-batches.destroy', $batch))->assertRedirect();
        Storage::disk('public')->assertMissing($thirdPath);
        $this->assertDatabaseMissing('livestock_batches', ['id' => $batch->id]);
    }

    public function test_disguised_oversized_and_svg_files_are_rejected_without_creating_rows(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $data = ['new_category_name' => 'Jersey', 'code' => 'INVALID', 'head_count' => 1, 'status' => 'active'];
        $files = [
            UploadedFile::fake()->createWithContent('fake.jpg', '<?php echo "not an image";'),
            UploadedFile::fake()->image('large.png')->size(5121),
            UploadedFile::fake()->createWithContent('vector.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
        ];
        foreach ($files as $file) {
            $this->actingAs($user)->post(route('admin.livestock-batches.store'), $data + ['image' => $file])
                ->assertSessionHasErrors('image');
        }
        $this->assertDatabaseCount('livestock_batches', 0);
        $this->assertDatabaseCount('livestock_categories', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_other_user_cannot_replace_or_remove_the_image(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($owner)->post(route('admin.livestock-batches.store'), [
            'new_category_name' => 'Holstein', 'code' => 'OWNED', 'head_count' => 1,
            'status' => 'active', 'image' => UploadedFile::fake()->image('owned.png'),
        ])->assertSessionHasNoErrors();
        $batch = $owner->livestockBatches()->firstOrFail();
        $path = $batch->image_path;
        $this->actingAs($other)->put(route('admin.livestock-batches.update', $batch), [
            'remove_image' => '1', 'image' => UploadedFile::fake()->image('foreign.png'),
        ])->assertForbidden();
        $this->delete(route('admin.livestock-batches.destroy', $batch))->assertForbidden();
        Storage::disk('public')->assertExists($path);
        $this->assertSame($path, $batch->refresh()->image_path);
    }
}
