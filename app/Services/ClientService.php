<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /** @param array{q?:?string,status?:?string} $filters */
    public function paginateFor(User $user, array $filters = []): LengthAwarePaginator
    {
        return $user->clients()
            ->withCount('salesOpportunities')
            ->when($filters['q'] ?? null, function ($query, string $search): void {
                $term = '%'.$search.'%';
                $query->where(function ($matches) use ($term): void {
                    $matches->where('name', 'like', $term)
                        ->orWhere('contact_person', 'like', $term)
                        ->orWhere('document_number', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('municipality', 'like', $term);
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function create(User $user, array $data): Client
    {
        return DB::transaction(fn () => $user->clients()->create($data));
    }

    /** @param array<string, mixed> $data */
    public function update(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data): Client {
            $client->fill($data)->save();

            return $client->refresh();
        });
    }

    public function delete(Client $client): bool
    {
        return DB::transaction(function () use ($client): bool {
            $lockedClient = Client::query()->whereKey($client->id)->lockForUpdate()->firstOrFail();

            if ($lockedClient->salesOpportunities()->exists()) {
                return false;
            }

            $lockedClient->delete();

            return true;
        });
    }
}
