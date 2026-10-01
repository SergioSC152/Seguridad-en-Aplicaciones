<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLivestockCategoryRequest;
use App\Http\Requests\UpdateLivestockCategoryRequest;
use App\Models\LivestockCategory;
use App\Services\LivestockCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LivestockCategoryController extends Controller
{
    public function __construct(private readonly LivestockCategoryService $service) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', LivestockCategory::class);

        return view('admin.livestock-categories.index', [
            'categories' => $this->service->paginateFor($request->user()),
            'editingCategory' => null,
        ]);
    }

    public function store(StoreLivestockCategoryRequest $request): RedirectResponse
    {
        Gate::authorize('create', LivestockCategory::class);
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);
        $this->service->create($request->user(), $data);

        return to_route('admin.livestock-categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Request $request, LivestockCategory $category): View
    {
        Gate::authorize('update', $category);

        return view('admin.livestock-categories.index', [
            'categories' => $this->service->paginateFor($request->user()),
            'editingCategory' => $category,
        ]);
    }

    public function update(UpdateLivestockCategoryRequest $request, LivestockCategory $category): RedirectResponse
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');
        $this->service->update($category, $data);

        return to_route('admin.livestock-categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(LivestockCategory $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        if ($category->batches()->exists()) {
            return to_route('admin.livestock-categories.index')
                ->with('error', 'No puedes eliminar una categoría que ya tiene lotes asociados.');
        }

        $this->service->delete($category);

        return to_route('admin.livestock-categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
