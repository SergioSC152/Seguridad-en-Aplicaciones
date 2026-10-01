<?php

namespace App\Services;

use App\Models\LivestockBatch;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LivestockBatchService
{
    public function paginateFor(User $user, array $filters = []): LengthAwarePaginator
    {
        return LivestockBatch::query()
            ->with('category')
            ->where('user_id', $user->id)
            ->when($filters['q'] ?? null, function ($query, string $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('code', 'like', "%{$term}%")
                        ->orWhere('ear_tag', 'like', "%{$term}%")
                        ->orWhere('farm_name', 'like', "%{$term}%");
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $categoryId) => $query->where('livestock_category_id', $categoryId))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }

    public function create(User $user, array $data): LivestockBatch
    {
        return DB::transaction(fn () => $user->livestockBatches()->create($data));
    }

    public function update(LivestockBatch $batch, array $data): LivestockBatch
    {
        return DB::transaction(function () use ($batch, $data) {
            $batch->update($data);

            return $batch->refresh();
        });
    }

    public function delete(LivestockBatch $batch): void
    {
        DB::transaction(fn () => $batch->delete());
    }
}
