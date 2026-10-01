<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes ganaderos | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('partials.cowapp-stitch-theme')
    <style>
        .client-table th { color:#6B7280; font-size:.75rem; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap }
        .client-form-card { scroll-margin-top: 1.5rem }
    </style>
</head>
<body>
<nav class="navbar cow-navbar bg-white border-bottom">
    <div class="container-fluid px-lg-5 py-2">
        <a class="navbar-brand fw-bold text-decoration-none" href="{{ route('dashboard') }}" style="color:#5A4507"><i class="bi bi-people me-2" style="color:#0C820C"></i>CowApp <span class="text-secondary fw-normal">/ Clientes</span></a>
        <div class="d-flex gap-2"><a href="{{ route('dashboard') }}" class="btn btn-outline-success rounded-3"><i class="bi bi-grid me-1"></i>Panel</a><a href="#client-form" class="btn btn-success rounded-3"><i class="bi bi-person-plus me-1"></i>Nuevo cliente</a></div>
    </div>
</nav>

<main class="container-fluid px-lg-4 px-xl-5 py-4 py-lg-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><span class="badge rounded-pill text-bg-success-subtle border border-success-subtle mb-2">RELACIONES COMERCIALES</span><h1 class="h2 fw-bold mb-2">Clientes ganaderos</h1><p class="text-secondary mb-0">Organiza los datos de contacto de compradores, proveedores y aliados de tu operación.</p></div>
        <div class="cow-card-surface px-3 py-2 small text-secondary"><i class="bi bi-lock text-success me-1"></i>Los registros pertenecen a tu cuenta.</div>
    </div>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><div class="fw-semibold mb-1">Revisa los campos indicados:</div><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section id="client-form" class="cow-card-surface client-form-card p-3 p-md-4 mb-4">
        <div class="mb-3"><h2 class="h5 fw-bold mb-1">{{ $editingClient ? 'Editar cliente' : 'Registrar cliente' }}</h2><p class="small text-secondary mb-0">El nombre es obligatorio; los datos de identificación y contacto son opcionales.</p></div>
        <form method="POST" action="{{ $editingClient ? route('admin.clients.update', $editingClient) : route('admin.clients.store') }}">
            @csrf @if($editingClient) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12 col-md-6 col-xl-4"><label for="name" class="form-label fw-semibold">Nombre o razón social <span class="text-danger">*</span></label><input id="name" name="name" value="{{ old('name', $editingClient?->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="160" autocomplete="organization" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="client_type" class="form-label fw-semibold">Tipo de cliente</label><select id="client_type" name="client_type" class="form-select @error('client_type') is-invalid @enderror" required><option value="individual" @selected(old('client_type', $editingClient?->client_type ?? 'individual') === 'individual')>Persona</option><option value="business" @selected(old('client_type', $editingClient?->client_type) === 'business')>Empresa / organización</option></select>@error('client_type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="contact_person" class="form-label fw-semibold">Persona de contacto</label><input id="contact_person" name="contact_person" value="{{ old('contact_person', $editingClient?->contact_person) }}" class="form-control @error('contact_person') is-invalid @enderror" maxlength="160" autocomplete="name">@error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="document_number" class="form-label fw-semibold">Documento / NIT</label><input id="document_number" name="document_number" value="{{ old('document_number', $editingClient?->document_number) }}" class="form-control @error('document_number') is-invalid @enderror" maxlength="40" autocomplete="off">@error('document_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="email" class="form-label fw-semibold">Correo electrónico</label><input id="email" type="email" name="email" value="{{ old('email', $editingClient?->email) }}" class="form-control @error('email') is-invalid @enderror" maxlength="255" autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="phone" class="form-label fw-semibold">Teléfono</label><input id="phone" type="tel" name="phone" value="{{ old('phone', $editingClient?->phone) }}" class="form-control @error('phone') is-invalid @enderror" maxlength="40" autocomplete="tel">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="municipality" class="form-label fw-semibold">Municipio</label><input id="municipality" name="municipality" value="{{ old('municipality', $editingClient?->municipality) }}" class="form-control @error('municipality') is-invalid @enderror" maxlength="100" autocomplete="address-level2">@error('municipality')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="department" class="form-label fw-semibold">Departamento</label><input id="department" name="department" value="{{ old('department', $editingClient?->department) }}" class="form-control @error('department') is-invalid @enderror" maxlength="100" autocomplete="address-level1">@error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6 col-xl-4"><label for="status" class="form-label fw-semibold">Estado</label><select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required><option value="active" @selected(old('status', $editingClient?->status ?? 'active') === 'active')>Activo</option><option value="inactive" @selected(old('status', $editingClient?->status) === 'inactive')>Inactivo</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label for="address" class="form-label fw-semibold">Dirección</label><input id="address" name="address" value="{{ old('address', $editingClient?->address) }}" class="form-control @error('address') is-invalid @enderror" maxlength="255" autocomplete="street-address">@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label for="notes" class="form-label fw-semibold">Notas internas</label><textarea id="notes" name="notes" rows="3" maxlength="3000" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $editingClient?->notes) }}</textarea>@error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 d-flex flex-wrap gap-2"><button type="submit" class="btn btn-success rounded-3 px-4"><i class="bi bi-check2 me-1"></i>{{ $editingClient ? 'Guardar cambios' : 'Registrar cliente' }}</button>@if($editingClient)<a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary rounded-3">Cancelar</a>@endif</div>
            </div>
        </form>
    </section>

    <section class="cow-card-surface overflow-hidden">
        <div class="p-3 p-md-4 border-bottom"><div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3"><div><h2 class="h5 fw-bold mb-1">Cartera de clientes</h2><p class="small text-secondary mb-0">{{ number_format($clients->total()) }} registros de tu cuenta.</p></div>
            <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-2"><div class="col-12 col-sm-auto"><label for="q" class="visually-hidden">Buscar cliente</label><input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Nombre, correo, teléfono…" maxlength="100"></div><div class="col-8 col-sm-auto"><label for="filter-status" class="visually-hidden">Filtrar por estado</label><select id="filter-status" name="status" class="form-select"><option value="">Todos los estados</option><option value="active" @selected(($filters['status'] ?? '') === 'active')>Activos</option><option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactivos</option></select></div><div class="col-4 col-sm-auto d-grid"><button class="btn btn-outline-success rounded-3" type="submit">Filtrar</button></div></form>
        </div></div>
        <div class="table-responsive"><table class="table table-hover client-table align-middle mb-0"><thead class="table-light"><tr><th>Cliente</th><th>Contacto</th><th>Ubicación</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
            @forelse($clients as $client)
                <tr><td><div class="fw-semibold text-dark">{{ $client->name }}</div><div class="small text-secondary">{{ $client->client_type === 'business' ? 'Empresa / organización' : 'Persona' }}{{ $client->document_number ? ' · '.$client->document_number : '' }}</div></td><td><div>{{ $client->contact_person ?: '—' }}</div><div class="small text-secondary">{{ $client->email ?: $client->phone ?: 'Sin datos de contacto' }}</div></td><td>{{ collect([$client->municipality, $client->department])->filter()->join(', ') ?: '—' }}</td><td><span class="badge rounded-pill {{ $client->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $client->status === 'active' ? 'Activo' : 'Inactivo' }}</span></td><td class="text-end text-nowrap"><a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-outline-success rounded-3">Editar</a><form method="POST" action="{{ route('admin.clients.destroy', $client) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este cliente?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger rounded-3">Eliminar</button></form></td></tr>
            @empty<tr><td colspan="5" class="text-center text-secondary py-5"><i class="bi bi-person-plus fs-2 d-block text-success mb-2"></i>{{ ($filters['q'] ?? null) || ($filters['status'] ?? null) ? 'No hay clientes que coincidan con la búsqueda.' : 'Todavía no tienes clientes registrados.' }}</td></tr>@endforelse
        </tbody></table></div>
        @if($clients->hasPages())<div class="p-3 p-md-4">{{ $clients->links() }}</div>@endif
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
