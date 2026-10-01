<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categorías ganaderas | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:#F1F1F1;color:#020219}.cow-nav{background:#fff;border-bottom:1px solid #E2E4E8}.cow-card{background:#fff;border:1px solid #E2E4E8;border-radius:16px}.btn-cow{background:#0C820C;color:#fff}.btn-cow:hover{background:#0FA50F;color:#fff}.form-control{min-height:44px}</style>
    @include('partials.cowapp-stitch-theme')
</head>
<body>
<nav class="navbar cow-nav py-3"><div class="container-fluid px-lg-5"><a class="navbar-brand fw-bold text-success" href="{{ route('dashboard') }}">CowApp <span class="text-secondary fw-normal fs-6">/ Gestión ganadera</span></a><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-success" href="{{ route('admin.livestock-batches.index') }}">Lotes</a><a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard') }}">Panel</a></div></div></nav>
<main class="container-fluid px-lg-5 py-4 py-lg-5"><div class="mb-4"><p class="text-success text-uppercase small fw-bold mb-1">Inventario ganadero</p><h1 class="h2 fw-bold mb-1">Categorías de ganado</h1><p class="text-secondary mb-0">Define catálogos para clasificar los lotes de tu cuenta.</p></div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Revisa los datos:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <section class="cow-card p-3 p-lg-4 mb-4"><h2 class="h5 fw-bold mb-3">{{ $editingCategory ? 'Editar categoría' : 'Nueva categoría' }}</h2><form method="POST" action="{{ $editingCategory ? route('admin.livestock-categories.update', $editingCategory) : route('admin.livestock-categories.store') }}" class="row g-3">@csrf @if($editingCategory) @method('PUT') @endif
        <div class="col-12 col-lg-4"><label class="form-label" for="name">Nombre <span class="text-danger">*</span></label><input class="form-control" id="name" name="name" maxlength="100" required value="{{ old('name', $editingCategory?->name) }}"></div>
        <div class="col-12 col-lg-6"><label class="form-label" for="description">Descripción</label><input class="form-control" id="description" name="description" maxlength="500" value="{{ old('description', $editingCategory?->description) }}"></div>
        <div class="col-12 col-lg-2 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="active" name="active" value="1" @checked(old('active', $editingCategory?->active ?? true))><label class="form-check-label" for="active">Activa</label></div></div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-cow px-4" type="submit">{{ $editingCategory ? 'Guardar cambios' : 'Crear categoría' }}</button>@if($editingCategory)<a class="btn btn-outline-secondary" href="{{ route('admin.livestock-categories.index') }}">Cancelar</a>@endif</div>
    </form></section>
    <section class="cow-card overflow-hidden"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Categoría</th><th>Descripción</th><th>Lotes asociados</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
    @forelse($categories as $category)<tr><td class="fw-semibold">{{ $category->name }}</td><td>{{ $category->description ?: '—' }}</td><td>{{ $category->batches_count }}</td><td><span class="badge rounded-pill {{ $category->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->active ? 'Activa' : 'Inactiva' }}</span></td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-success" href="{{ route('admin.livestock-categories.edit', $category) }}">Editar</a><form class="d-inline" method="POST" action="{{ route('admin.livestock-categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" @disabled($category->batches_count > 0)>Eliminar</button></form></td></tr>@empty<tr><td colspan="5" class="text-center text-secondary py-5">Todavía no hay categorías. Crea la primera para empezar.</td></tr>@endforelse
    </tbody></table></div><div class="p-3">{{ $categories->links('pagination::bootstrap-5') }}</div></section>
</main>
</body>
</html>
