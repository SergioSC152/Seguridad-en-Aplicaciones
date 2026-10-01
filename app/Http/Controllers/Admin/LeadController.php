<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexLeadsRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct(private readonly LeadService $leads) {}

    public function index(IndexLeadsRequest $request): View
    {
        Gate::authorize('viewAny', Lead::class);

        return $this->leadView($request, null);
    }

    public function edit(IndexLeadsRequest $request, Lead $lead): View
    {
        Gate::authorize('update', $lead);

        return $this->leadView($request, $lead);
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $this->leads->create($request->user(), $request->validated());

        return to_route('admin.leads.index')->with('success', 'Prospecto registrado correctamente.');
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        Gate::authorize('update', $lead);
        $this->leads->update($lead, $request->validated());

        return to_route('admin.leads.index')->with('success', 'Prospecto actualizado correctamente.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        Gate::authorize('delete', $lead);
        $this->leads->delete($lead);

        return to_route('admin.leads.index')->with('success', 'Prospecto eliminado correctamente.');
    }

    private function leadView(IndexLeadsRequest $request, ?Lead $editingLead): View
    {
        $ownedLeads = $request->user()->leads();
        $monthLeads = (clone $ownedLeads)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);

        return view('admin.leads.index', [
            'leads' => $this->leads->paginateFor($request->user(), $request->validated()),
            'editingLead' => $editingLead,
            'filters' => $request->safe()->only(['q', 'source', 'status']),
            'sources' => Lead::SOURCES,
            'statuses' => Lead::STATUSES,
            'metrics' => [
                'month_total' => (clone $monthLeads)->count(),
                'month_whatsapp' => (clone $monthLeads)->where('source', 'whatsapp')->count(),
                'qualified_total' => (clone $ownedLeads)->where('status', 'qualified')->count(),
                'follow_up_due' => (clone $ownedLeads)->whereNotNull('follow_up_at')->whereDate('follow_up_at', '<=', today())->whereNotIn('status', ['disqualified'])->count(),
            ],
        ]);
    }
}
