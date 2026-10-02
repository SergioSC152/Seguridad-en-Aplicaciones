<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexClientsRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(private readonly ClientService $clients) {}

    public function index(IndexClientsRequest $request): View
    {
        Gate::authorize('viewAny', Client::class);

        return view('admin.clients.index', [
            'clients' => $this->clients->paginateFor($request->user(), $request->validated()),
            'editingClient' => null,
            'filters' => $request->safe()->only(['q', 'status']),
        ]);
    }

    public function edit(IndexClientsRequest $request, Client $client): View
    {
        Gate::authorize('update', $client);

        return view('admin.clients.index', [
            'clients' => $this->clients->paginateFor($request->user(), $request->validated()),
            'editingClient' => $client,
            'filters' => $request->safe()->only(['q', 'status']),
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $this->clients->create($request->user(), $request->validated());

        return to_route('admin.clients.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        Gate::authorize('update', $client);
        $this->clients->update($client, $request->validated());

        return to_route('admin.clients.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        Gate::authorize('delete', $client);
        if (! $this->clients->delete($client)) {
            return to_route('admin.clients.index')->with('error', 'No puedes eliminar un cliente con oportunidades asociadas. Puedes marcarlo como inactivo.');
        }

        return to_route('admin.clients.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
