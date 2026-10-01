<?php

namespace App\Services;

use App\Models\SalesOpportunity;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SalesOpportunityService
{
    public function paginateFor(User $user, array $filters): LengthAwarePaginator
    {
        return $user->salesOpportunities()
            ->with('client:id,name')
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($nested) use ($term): void {
                    $nested->where('title', 'like', "%{$term}%")
                        ->orWhere('livestock_summary', 'like', "%{$term}%")
                        ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($filters['stage'] ?? null, fn ($query, $stage) => $query->where('stage', $stage))
            ->when($filters['client_id'] ?? null, fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->orderByRaw('expected_close_date IS NULL')
            ->orderBy('expected_close_date')
            ->orderByDesc('updated_at')
            ->paginate(60)
            ->withQueryString();
    }

    public function create(User $user, array $data): SalesOpportunity
    {
        return $user->salesOpportunities()->create($data);
    }

    public function update(SalesOpportunity $opportunity, array $data): SalesOpportunity
    {
        $opportunity->fill($data)->save();

        return $opportunity;
    }

    public function delete(SalesOpportunity $opportunity): void
    {
        DB::transaction(fn () => $opportunity->delete());
    }
}
