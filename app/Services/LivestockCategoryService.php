<?php

namespace App\Services;

use App\Models\LivestockCategory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LivestockCategoryService
{
    public function paginateFor(User $user): LengthAwarePaginator
    {
        return LivestockCategory::query()
            ->withCount('batches')
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->paginate(12);
    }

    public function create(User $user, array $data): LivestockCategory
    {
        return DB::transaction(fn () => $user->livestockCategories()->create($data));
    }

    public function update(LivestockCategory $category, array $data): LivestockCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);

            return $category->refresh();
        });
    }

    public function delete(LivestockCategory $category): void
    {
        DB::transaction(fn () => $category->delete());
    }
}
