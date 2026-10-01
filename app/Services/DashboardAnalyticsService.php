<?php

namespace App\Services;

use App\Models\LivestockBatch;
use App\Models\LivestockCategory;
use App\Models\User;

class DashboardAnalyticsService
{
    /**
     * Build account-scoped dashboard metrics from the current livestock inventory.
     *
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        $batches = LivestockBatch::query()->where('user_id', $user->id);
        $activeBatches = (clone $batches)->where('status', 'active');
        $activeHeads = (int) (clone $activeBatches)->sum('head_count');

        $weightSummary = (clone $activeBatches)
            ->whereNotNull('average_weight_kg')
            ->selectRaw('SUM(head_count * average_weight_kg) / NULLIF(SUM(head_count), 0) AS weighted_average_weight')
            ->value('weighted_average_weight');

        $allCategories = LivestockCategory::query()
            ->where('user_id', $user->id)
            ->withSum(['batches as active_head_count' => fn ($query) => $query->where('status', 'active')], 'head_count')
            ->withCount(['batches as active_batch_count' => fn ($query) => $query->where('status', 'active')])
            ->orderBy('name')
            ->get()
            ->map(fn (LivestockCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'head_count' => (int) ($category->active_head_count ?? 0),
                'batch_count' => (int) $category->active_batch_count,
            ])
            ->sortByDesc('head_count')
            ->values();

        $categoriesWithInventory = $allCategories->where('head_count', '>', 0)->count();
        $categoryBreakdown = $allCategories->take(6)->values();
        $leads = $user->leads();
        $opportunities = $user->salesOpportunities();

        return [
            'metrics' => [
                'active_heads' => $activeHeads,
                'active_batches' => (clone $activeBatches)->count(),
                'weighted_average_weight' => $weightSummary === null ? null : round((float) $weightSummary, 2),
                'categories_with_inventory' => $categoriesWithInventory,
                'clients' => $user->clients()->count(),
                'leads' => (clone $leads)->count(),
                'new_leads' => (clone $leads)->where('status', 'new')->count(),
                'open_opportunities' => (clone $opportunities)->whereIn('stage', ['contact', 'visit', 'negotiation'])->count(),
                'open_pipeline_value' => (float) (clone $opportunities)->whereIn('stage', ['contact', 'visit', 'negotiation'])->sum('estimated_value'),
                'won_opportunities' => (clone $opportunities)->where('stage', 'won')->count(),
            ],
            'categoryBreakdown' => $categoryBreakdown,
            'statusCounts' => [
                'active' => (clone $batches)->where('status', 'active')->count(),
                'sold' => (clone $batches)->where('status', 'sold')->count(),
                'inactive' => (clone $batches)->where('status', 'inactive')->count(),
            ],
            'recentBatches' => (clone $batches)->with('category')->latest()->limit(6)->get(),
            'user' => $user,
        ];
    }
}
