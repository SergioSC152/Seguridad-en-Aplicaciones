<?php

namespace App\Services;

use App\Models\LivestockBatch;
use App\Models\LivestockCategory;
use App\Models\User;
use App\Models\SalesOpportunity;

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
        $canManageClients = $user->hasPermissionTo('clients.manage');
        $canManageLeads = $user->hasPermissionTo('leads.manage');
        $canManageOpportunities = $user->hasPermissionTo('sales-pipeline.manage');

        return [
            'metrics' => [
                'active_heads' => $activeHeads,
                'active_batches' => (clone $activeBatches)->count(),
                'weighted_average_weight' => $weightSummary === null ? null : round((float) $weightSummary, 2),
                'categories_with_inventory' => $categoriesWithInventory,
                'clients' => $canManageClients ? $user->clients()->count() : null,
                'leads' => $canManageLeads ? (clone $leads)->count() : null,
                'new_leads' => $canManageLeads ? (clone $leads)->where('status', 'new')->count() : null,
                'open_opportunities' => $canManageOpportunities ? (clone $opportunities)->whereIn('stage', SalesOpportunity::OPEN_STAGES)->count() : null,
                'open_pipeline_value' => $canManageOpportunities ? (float) (clone $opportunities)->whereIn('stage', SalesOpportunity::OPEN_STAGES)->sum('estimated_value') : null,
                'won_opportunities' => $canManageOpportunities ? (clone $opportunities)->where('stage', 'won')->count() : null,
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
