<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexSalesOpportunitiesRequest;
use App\Http\Requests\StoreSalesOpportunityRequest;
use App\Http\Requests\UpdateSalesOpportunityRequest;
use App\Models\SalesOpportunity;
use App\Services\SalesOpportunityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SalesOpportunityController extends Controller
{
    public function __construct(private readonly SalesOpportunityService $opportunities) {}

    public function index(IndexSalesOpportunitiesRequest $request): View
    {
        Gate::authorize('viewAny', SalesOpportunity::class);

        return $this->pipelineView($request, null);
    }

    public function edit(IndexSalesOpportunitiesRequest $request, SalesOpportunity $opportunity): View
    {
        Gate::authorize('update', $opportunity);

        return $this->pipelineView($request, $opportunity);
    }

    public function store(StoreSalesOpportunityRequest $request): RedirectResponse
    {
        $this->opportunities->create($request->user(), $request->validated());

        return to_route('admin.sales-pipeline.index')->with('success', 'Oportunidad registrada correctamente.');
    }

    public function update(UpdateSalesOpportunityRequest $request, SalesOpportunity $opportunity): RedirectResponse
    {
        Gate::authorize('update', $opportunity);
        $this->opportunities->update($opportunity, $request->validated());

        return to_route('admin.sales-pipeline.index')->with('success', 'Oportunidad actualizada correctamente.');
    }

    public function destroy(SalesOpportunity $opportunity): RedirectResponse
    {
        Gate::authorize('delete', $opportunity);
        $this->opportunities->delete($opportunity);

        return to_route('admin.sales-pipeline.index')->with('success', 'Oportunidad eliminada correctamente.');
    }

    private function pipelineView(IndexSalesOpportunitiesRequest $request, ?SalesOpportunity $editing): View
    {
        $opportunities = $this->opportunities->paginateFor($request->user(), $request->validated());
        $groups = $opportunities->getCollection()->groupBy('stage');

        return view('admin.sales-pipeline.index', [
            'opportunities' => $opportunities,
            'groups' => $groups,
            'editingOpportunity' => $editing,
            'clients' => $request->user()->clients()->orderBy('name')->get(['id', 'name']),
            'filters' => $request->safe()->only(['q', 'stage', 'client_id']),
            'stages' => SalesOpportunity::STAGES,
            'metrics' => [
                'open_count' => $request->user()->salesOpportunities()->whereIn('stage', SalesOpportunity::OPEN_STAGES)->count(),
                'open_value' => $request->user()->salesOpportunities()->whereIn('stage', SalesOpportunity::OPEN_STAGES)->sum('estimated_value'),
                'won_count' => $request->user()->salesOpportunities()->where('stage', 'won')->count(),
                'lost_count' => $request->user()->salesOpportunities()->where('stage', 'lost')->count(),
            ],
        ]);
    }
}
