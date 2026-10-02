<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NamesAndBatchValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_rejects_numbers_and_accepts_accented_names(): void
    {
        $data=['name'=>'Juan123','email'=>'name-check@example.com','password'=>'valid-password','password_confirmation'=>'valid-password'];
        $this->post(route('register.store'),$data)->assertSessionHasErrors('name');
        $this->postJson('/api/register',$data)->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->assertDatabaseMissing('users',['email'=>$data['email']]);
        $this->post(route('register.store'),array_replace($data,['name'=>'María José Muñoz']))
            ->assertSessionHasNoErrors()->assertRedirect(route('login'));
    }

    public function test_batch_accepts_existing_or_new_category_and_rejects_both_without_server_error(): void
    {
        $user=User::factory()->create();
        $this->actingAs($user);
        $category=$user->livestockCategories()->create(['name'=>'Brahman','active'=>true]);
        $base=['head_count'=>2,'status'=>'active'];
        $this->post(route('admin.livestock-batches.store'),$base+['code'=>'EXISTING','livestock_category_id'=>$category->id])
            ->assertSessionHasNoErrors();
        $this->post(route('admin.livestock-batches.store'),$base+['code'=>'NEW','new_category_name'=>'Nelore'])
            ->assertSessionHasNoErrors();
        $this->post(route('admin.livestock-batches.store'),$base+['code'=>'BOTH','livestock_category_id'=>$category->id,'new_category_name'=>'Otra'])
            ->assertSessionHasErrors('new_category_name');
        $this->assertDatabaseMissing('livestock_batches',['code'=>'BOTH']);
        $batch=$user->livestockBatches()->where('code','EXISTING')->firstOrFail();
        $this->put(route('admin.livestock-batches.update',$batch),$base+['code'=>'EXISTING','new_category_name'=>'Jersey'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Jersey',$batch->fresh()->category->name);
    }
}
