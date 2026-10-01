<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roles y permisos | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('partials.cowapp-stitch-theme')
</head>
<body>
<nav class="navbar cow-navbar bg-white border-bottom">
    <div class="container-fluid px-lg-5 py-2">
        <a class="navbar-brand fw-bold text-decoration-none" href="{{ route('dashboard') }}" style="color:#5A4507"><i class="bi bi-shield-lock me-2" style="color:#0C820C"></i>CowApp <span class="text-secondary fw-normal">/ Roles y permisos</span></a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-success rounded-3"><i class="bi bi-arrow-left me-1"></i>Panel</a>
    </div>
</nav>
<main class="container-fluid px-lg-4 px-xl-5 py-4 py-lg-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><span class="badge rounded-pill text-bg-success-subtle border border-success-subtle mb-2">CONTROL DE ACCESO · RBAC</span><h1 class="h2 fw-bold mb-2">Roles y permisos</h1><p class="text-secondary mb-0">Asigna únicamente las capacidades necesarias para cada cuenta.</p></div>
        <div class="small text-secondary"><i class="bi bi-person-lock text-success me-1"></i>Gestión reservada a la cuenta raíz configurada en el servidor.</div>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-warning" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><div class="fw-semibold mb-1">No se pudo guardar:</div><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="row g-4 align-items-start">
        <section class="col-12 col-xl-4">
            <div class="cow-card-surface p-3 p-md-4">
                <h2 class="h5 fw-bold mb-1">Crear rol</h2><p class="small text-secondary mb-3">Las capacidades se comprueban siempre en el servidor.</p>
                <form method="POST" action="{{ route('admin.roles.store') }}">@csrf
                    <div class="mb-3"><label for="new-name" class="form-label fw-semibold">Nombre</label><input id="new-name" name="name" value="{{ old('name') }}" class="form-control" maxlength="80" required></div>
                    <div class="mb-3"><label for="new-description" class="form-label fw-semibold">Descripción</label><textarea id="new-description" name="description" class="form-control" rows="2" maxlength="255">{{ old('description') }}</textarea></div>
                    <fieldset><legend class="form-label fw-semibold fs-6">Permisos</legend>
                        @foreach($permissions as $permission)<div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->code }}" id="new-{{ $permission->id }}"><label class="form-check-label" for="new-{{ $permission->id }}"><span class="fw-semibold">{{ $permission->label }}</span><span class="d-block small text-secondary">{{ $permission->description }}</span></label></div>@endforeach
                    </fieldset>
                    <button type="submit" class="btn btn-success rounded-3 w-100 mt-3"><i class="bi bi-plus-lg me-1"></i>Crear rol</button>
                </form>
            </div>
        </section>

        <section class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="h5 fw-bold mb-1">Roles registrados</h2><p class="small text-secondary mb-0">{{ $roles->count() }} roles · catálogo de permisos controlado por CowApp.</p></div></div>
            <div class="row g-3">
                @forelse($roles as $role)
                    <div class="col-12"><article class="cow-card-surface p-3 p-md-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3"><div><h3 class="h6 fw-bold mb-1">{{ $role->name }}</h3><p class="small text-secondary mb-0">{{ $role->description ?: 'Sin descripción' }} · {{ $role->users_count }} cuentas asignadas</p></div><span class="badge rounded-pill text-bg-light border align-self-start">{{ $role->slug }}</span></div>
                        <form method="POST" action="{{ route('admin.roles.update', $role) }}">@csrf @method('PUT')
                            <div class="row g-3"><div class="col-md-6"><label for="name-{{ $role->id }}" class="form-label small fw-semibold">Nombre</label><input id="name-{{ $role->id }}" name="name" value="{{ $role->name }}" class="form-control" maxlength="80" required></div><div class="col-md-6"><label for="description-{{ $role->id }}" class="form-label small fw-semibold">Descripción</label><input id="description-{{ $role->id }}" name="description" value="{{ $role->description }}" class="form-control" maxlength="255"></div></div>
                            <fieldset class="mt-3"><legend class="form-label small fw-semibold">Permisos asignados</legend><div class="d-flex flex-wrap gap-3">@foreach($permissions as $permission)<div class="form-check"><input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->code }}" id="role-{{ $role->id }}-permission-{{ $permission->id }}" @checked($role->permissions->contains('code', $permission->code))><label class="form-check-label small" for="role-{{ $role->id }}-permission-{{ $permission->id }}">{{ $permission->label }}</label></div>@endforeach</div></fieldset>
                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-3"><button type="submit" class="btn btn-outline-success rounded-3"><i class="bi bi-check2 me-1"></i>Guardar cambios</button></form>
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('¿Eliminar este rol?')">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger rounded-3" @disabled($role->users_count > 0) title="{{ $role->users_count > 0 ? 'Desasigna sus cuentas antes de eliminarlo.' : 'Eliminar rol' }}"><i class="bi bi-trash me-1"></i>Eliminar</button></form>
                            </div>
                    </article></div>
                @empty
                    <div class="col-12"><div class="cow-card-surface p-5 text-center"><i class="bi bi-shield-check text-success fs-1"></i><h3 class="h6 fw-bold mt-3">Aún no hay roles</h3><p class="text-secondary mb-0">Crea un rol y asigna sus permisos de forma explícita.</p></div></div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="cow-card-surface mt-4 overflow-hidden">
        <div class="p-3 p-md-4 border-bottom"><h2 class="h5 fw-bold mb-1">Asignar roles a cuentas</h2><p class="small text-secondary mb-0">Las cuentas sin rol no reciben permisos administrativos. La cuenta raíz no puede perder su acceso de emergencia.</p></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Cuenta</th><th>Correo</th><th>Rol actual</th><th class="text-end">Acción</th></tr></thead><tbody>
            @foreach($users as $account)
                <tr><td class="fw-semibold">{{ $account->name }} @if($account->isPlatformAdmin())<span class="badge rounded-pill text-bg-success ms-1">Raíz</span>@endif</td><td class="text-secondary">{{ $account->email }}</td><td>
                    @if($account->isPlatformAdmin())<span class="badge rounded-pill text-bg-success">Administrador raíz</span>@else<form id="assign-{{ $account->id }}" method="POST" action="{{ route('admin.roles.assign', $account) }}">@csrf @method('PUT')<select name="role_id" class="form-select form-select-sm" aria-label="Rol para {{ $account->email }}"><option value="">Sin rol</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected($account->role_id === $role->id)>{{ $role->name }}</option>@endforeach</select></form>@endif
                </td><td class="text-end">@if(!$account->isPlatformAdmin())<button type="submit" form="assign-{{ $account->id }}" class="btn btn-sm btn-outline-success rounded-3">Asignar</button>@else<span class="small text-secondary">Acceso del servidor</span>@endif</td></tr>
            @endforeach
            @if($users->isEmpty())<tr><td colspan="4" class="text-center text-secondary py-4">No hay cuentas registradas.</td></tr>@endif
        </tbody></table></div>
        <div class="p-3">{{ $users->links() }}</div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
