<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LeadService
{
    /** @param array{q?:?string,source?:?string,status?:?string} $filters */
    public function paginateFor(User $user, array $filters = []): LengthAwarePaginator
    {
        return $user->leads()
            ->when($filters['q'] ?? null, function ($query, string $search): void {
                $term = '%'.$search.'%';
                $query->where(function ($matches) use ($term): void {
                    $matches->where('name', 'like', $term)
                        ->orWhere('farm_name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('livestock_interest', 'like', $term)
                        ->orWhere('municipality', 'like', $term);
                });
            })
            ->when($filters['source'] ?? null, fn ($query, string $source) => $query->where('source', $source))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderByRaw('follow_up_at IS NULL')
            ->orderBy('follow_up_at')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function create(User $user, array $data): Lead
    {
        return DB::transaction(fn () => $user->leads()->create($data));
    }

    /** @param array<string, mixed> $data */
    public function update(Lead $lead, array $data): Lead
    {
        return DB::transaction(function () use ($lead, $data): Lead {
            $lead->fill($data)->save();

            return $lead->refresh();
        });
    }

    public function delete(Lead $lead): void
    {
        DB::transaction(fn () => $lead->delete());
    }
    public function convert(Lead $lead): \App\Models\SalesOpportunity
    {
        return DB::transaction(function () use ($lead) {
            $lead=Lead::whereKey($lead->id)->lockForUpdate()->firstOrFail();
            if ($lead->converted_opportunity_id) return \App\Models\SalesOpportunity::findOrFail($lead->converted_opportunity_id);
            $client=$lead->email ? $lead->user->clients()->where('email',$lead->email)->first() : null;
            $client ??= $lead->user->clients()->create(['name'=>$lead->name,'client_type'=>'individual','email'=>$lead->email,'phone'=>$lead->phone,'municipality'=>$lead->municipality,'department'=>$lead->department,'status'=>'active']);
            $opportunity=$lead->user->salesOpportunities()->create(['client_id'=>$client->id,'title'=>'Negocio: '.$lead->name,'livestock_summary'=>$lead->livestock_interest,'head_count'=>$lead->estimated_heads,'estimated_value'=>$lead->budget ?? 0,'stage'=>'contact']);
            $lead->update(['converted_client_id'=>$client->id,'converted_opportunity_id'=>$opportunity->id,'status'=>'qualified']);
            return $opportunity;
        });
    }
}
