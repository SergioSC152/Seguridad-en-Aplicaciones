<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLivestockBatchRequest;
use App\Http\Requests\UpdateLivestockBatchRequest;
use App\Models\LivestockBatch;
use App\Models\LivestockCategory;
use App\Services\LivestockBatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LivestockBatchController extends Controller
{
    public function __construct(private readonly LivestockBatchService $service) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', LivestockBatch::class);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:active,sold,inactive'],
        ]);

        return view('admin.livestock-batches.index', [
            'batches' => $this->service->paginateFor($request->user(), $filters),
            'categories' => LivestockCategory::query()->where('user_id', $request->user()->id)->orderBy('name')->get(),
            'filters' => $filters,
            'editingBatch' => null,
        ]);
    }

    public function store(StoreLivestockBatchRequest $request): RedirectResponse
    {
        Gate::authorize('create', LivestockBatch::class);
        $this->service->create($request->user(), $request->validated());

        return to_route('admin.livestock-batches.index')->with('success', 'Lote creado correctamente.');
    }

    public function edit(Request $request, LivestockBatch $batch): View
    {
        Gate::authorize('update', $batch);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:active,sold,inactive'],
        ]);

        return view('admin.livestock-batches.index', [
            'batches' => $this->service->paginateFor($request->user(), $filters),
            'categories' => LivestockCategory::query()->where('user_id', $request->user()->id)->orderBy('name')->get(),
            'filters' => $filters,
            'editingBatch' => $batch,
        ]);
    }

    public function update(UpdateLivestockBatchRequest $request, LivestockBatch $batch): RedirectResponse
    {
        $this->service->update($batch, $request->validated());

        return to_route('admin.livestock-batches.index')->with('success', 'Lote actualizado correctamente.');
    }

    public function destroy(LivestockBatch $batch): RedirectResponse
    {
        Gate::authorize('delete', $batch);
        $this->service->delete($batch);

        return to_route('admin.livestock-batches.index')->with('success', 'Lote eliminado correctamente.');
    }
}
