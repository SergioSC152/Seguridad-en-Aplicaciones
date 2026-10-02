<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de lotes | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:#F1F1F1;color:#020219}.cow-nav{background:#fff;border-bottom:1px solid #E2E4E8}.cow-card{background:#fff;border:1px solid #E2E4E8;border-radius:16px}.btn-cow{background:#0C820C;color:#fff}.btn-cow:hover{background:#0FA50F;color:#fff}.form-control,.form-select{min-height:44px}.table>:not(caption)>*>*{padding:.9rem .8rem;vertical-align:middle}.badge-active{background:#E6F4E6;color:#0C820C}.badge-sold{background:#F4EFD8;color:#5A4507}.badge-inactive{background:#E2E4E8;color:#34354D}
    </style>
    @include('partials.cowapp-stitch-theme')
</head>
<body>
<nav class="navbar cow-nav py-3"><div class="container-fluid px-lg-5"><a class="navbar-brand fw-bold text-success" href="{{ route('dashboard') }}">CowApp <span class="text-secondary fw-normal fs-6">/ Gestión ganadera</span></a><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-success" href="{{ route('admin.livestock-categories.index') }}">Categorías</a><a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard') }}">Panel</a></div></div></nav>
<main class="container-fluid px-lg-5 py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4"><div><p class="text-success text-uppercase small fw-bold mb-1">Inventario ganadero</p><h1 class="h2 fw-bold mb-1">Lotes</h1><p class="text-secondary mb-0">Organiza existencias, ubicación y categoría de tu ganado.</p></div><a class="btn btn-cow px-4 py-2" href="#form-lote">{{ $editingBatch ? 'Editar lote' : '+ Registrar lote' }}</a></div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Revisa los datos:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="cow-card p-3 p-lg-4 mb-4" id="form-lote"><h2 class="h5 fw-bold mb-3">{{ $editingBatch ? 'Editar lote' : 'Nuevo lote' }}</h2>
        <form method="POST" action="{{ $editingBatch ? route('admin.livestock-batches.update', $editingBatch) : route('admin.livestock-batches.store') }}" class="row g-3" enctype="multipart/form-data">
            @csrf @if($editingBatch) @method('PUT') @endif
            <div class="col-md-4"><label for="purpose" class="form-label">Propósito</label><select id="purpose" name="purpose" class="form-select">@foreach(['cria'=>'Cría','ceba'=>'Ceba','leche'=>'Leche','genetica'=>'Genética'] as $key=>$label)<option value="{{ $key }}" @selected(old('purpose',$editingBatch?->purpose ?? 'ceba')===$key)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-4"><label for="availability" class="form-label">Disponibilidad</label><select id="availability" name="availability" class="form-select">@foreach(['available'=>'Disponible','auction'=>'En subasta','bidding'=>'Puja activa','reserved'=>'Reservado','awarded'=>'Adjudicado'] as $key=>$label)<option value="{{ $key }}" @selected(old('availability',$editingBatch?->availability ?? 'available')===$key)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-4"><label for="published" class="form-label">Catálogo público</label><select id="published" name="published" class="form-select"><option value="0" @selected(!old('published',$editingBatch?->published))>No publicar</option><option value="1" @selected(old('published',$editingBatch?->published))>Publicar</option></select></div>
            <div class="col-md-6"><label for="price_per_kg" class="form-label">Precio COP/kg</label><input id="price_per_kg" name="price_per_kg" type="number" min="0" max="1000000" step="0.01" class="form-control" value="{{ old('price_per_kg',$editingBatch?->price_per_kg) }}"></div>
            <div class="col-md-6"><label for="rfid" class="form-label">Identificación RFID</label><input id="rfid" name="rfid" maxlength="100" class="form-control" value="{{ old('rfid',$editingBatch?->rfid) }}"></div>
            @if($editingBatch)<div class="col-12"><details><summary>Historial de pesajes registrados</summary><ul>@forelse(\App\Models\LivestockMeasurement::where('livestock_batch_id',$editingBatch->id)->where('user_id',auth()->id())->latest()->limit(20)->get() as $measurement)<li>{{ $measurement->created_at }} UTC · {{ $measurement->average_weight_kg }} kg promedio · {{ $measurement->source }}</li>@empty<li>Sin mediciones adicionales.</li>@endforelse</ul></details></div>@endif
            <datalist id="known-farms">@foreach($farmNames as $farmName)<option value="{{ $farmName }}"></option>@endforeach</datalist>
            <datalist id="known-paddocks">@foreach($paddocks as $knownPaddock)<option value="{{ $knownPaddock }}"></option>@endforeach</datalist>
            <div class="col-12">
                <label for="new_category_name" class="form-label">Raza sugerida u otra categoría nueva</label>
                <input id="new_category_name" name="new_category_name" list="suggested-breeds" class="form-control @error('new_category_name') is-invalid @enderror" maxlength="100" value="{{ old('new_category_name') }}" placeholder="Selecciona una sugerencia o escribe otra">
                <datalist id="suggested-breeds">@foreach(config('cowapp.suggested_breeds', []) as $breed)<option value="{{ $breed }}"></option>@endforeach</datalist>
                <div class="form-text">Elige una categoría existente abajo o escribe aquí una nueva. Se crea en tu cuenta al guardar el lote; si ya existe, se reutiliza.</div>
                @error('new_category_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="image" class="form-label">Imagen del lote (opcional)</label>
                <input id="image" name="image" type="file" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" aria-describedby="image-help">
                <div id="image-help" class="form-text">JPG, PNG o WebP. Máximo 5 MB y 6000 × 6000 píxeles. La foto será accesible mediante su URL pública. Una nueva imagen reemplaza la anterior.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($editingBatch?->image_url)
                    <img src="{{ $editingBatch->image_url }}" alt="Imagen del lote {{ $editingBatch->code }}" class="rounded border mt-2" width="160" height="120" style="object-fit:cover">
                    <div class="form-check mt-2"><input id="remove_image" name="remove_image" type="checkbox" value="1" class="form-check-input" @checked(old('remove_image'))><label for="remove_image" class="form-check-label">Quitar la imagen actual (si subes otra, se conserva la nueva)</label></div>
                @endif
            </div>
            <div class="col-12"><a href="{{ route('admin.livestock-categories.index') }}" target="_blank" rel="noopener" class="small">Crear o administrar categorías</a><span class="form-text"> · Se abre en otra pestaña; guarda la categoría y recarga este formulario antes de introducir los datos del lote.</span></div>
            <div class="col-12 col-md-4"><label for="code" class="form-label">Código del lote <span class="text-danger">*</span></label><input id="code" name="code" class="form-control" maxlength="50" required value="{{ old('code', $editingBatch?->code) }}" autocomplete="off"><div class="form-text">Código único dentro de tu cuenta.</div></div>
            <div class="col-12 col-md-4"><label for="livestock_category_id" class="form-label">Categoría <span class="text-danger">*</span></label><select id="livestock_category_id" name="livestock_category_id" class="form-select"><option value="">Selecciona una categoría</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('new_category_name') ? '' : old('livestock_category_id', $editingBatch?->livestock_category_id) == $category->id)>{{ $category->name }}{{ $category->active ? '' : ' (inactiva)' }}</option>@endforeach</select>@if($categories->isEmpty())<div class="form-text">Puedes usar una raza sugerida arriba o <a href="{{ route('admin.livestock-categories.index') }}">crea una categoría</a>.</div>@endif</div>
            <div class="col-12 col-md-4"><label for="ear_tag" class="form-label">Hierro / identificación</label><input id="ear_tag" name="ear_tag" class="form-control" maxlength="80" value="{{ old('ear_tag', $editingBatch?->ear_tag) }}"></div>
            <div class="col-12 col-md-3"><label for="head_count" class="form-label">Cabezas <span class="text-danger">*</span></label><input id="head_count" name="head_count" type="number" min="1" max="1000000" step="1" class="form-control" required value="{{ old('head_count', $editingBatch?->head_count) }}"></div>
            <div class="col-12 col-md-3"><label for="average_weight_kg" class="form-label">Peso promedio (kg)</label><input id="average_weight_kg" name="average_weight_kg" type="number" min="0" max="999999.99" step="0.01" class="form-control" value="{{ old('average_weight_kg', $editingBatch?->average_weight_kg) }}"></div>
            <div class="col-12 col-md-3"><label for="farm_name" class="form-label">Finca / hacienda</label><input id="farm_name" name="farm_name" list="known-farms" class="form-control" maxlength="150" value="{{ old('farm_name', $editingBatch?->farm_name) }}"></div>
            <div class="col-12 col-md-3"><label for="paddock" class="form-label">Potrero</label><input id="paddock" name="paddock" list="known-paddocks" class="form-control" maxlength="100" value="{{ old('paddock', $editingBatch?->paddock) }}"></div>
            <div class="col-12 col-md-4"><label for="status" class="form-label">Estado</label><select id="status" name="status" class="form-select" required>@foreach(['active'=>'Activo','sold'=>'Vendido','inactive'=>'Inactivo'] as $value=>$label)<option value="{{ $value }}" @selected(old('status', $editingBatch?->status ?? 'active') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-12 col-md-8"><label for="notes" class="form-label">Notas</label><textarea id="notes" name="notes" class="form-control" rows="2" maxlength="5000">{{ old('notes', $editingBatch?->notes) }}</textarea></div>
            <div class="col-12 d-flex gap-2"><button class="btn btn-cow px-4" type="submit">{{ $editingBatch ? 'Guardar cambios' : 'Crear lote' }}</button>@if($editingBatch)<a class="btn btn-outline-secondary" href="{{ route('admin.livestock-batches.index') }}">Cancelar</a>@endif</div>
        </form>
    </section>

    <form method="GET" action="{{ route('admin.livestock-batches.index') }}" class="cow-card p-3 mb-3"><div class="row g-2"><div class="col-12 col-md-6"><label class="visually-hidden" for="q">Buscar lote</label><input id="q" name="q" class="form-control" placeholder="Buscar código, identificación o finca" value="{{ $filters['q'] ?? '' }}" maxlength="100"></div><div class="col-12 col-md-3"><label class="visually-hidden" for="filter-category">Categoría</label><select id="filter-category" name="category" class="form-select"><option value="">Todas las categorías</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select></div><div class="col-8 col-md-2"><label class="visually-hidden" for="filter-status">Estado</label><select id="filter-status" name="status" class="form-select"><option value="">Todos los estados</option><option value="active" @selected(($filters['status'] ?? '') === 'active')>Activo</option><option value="sold" @selected(($filters['status'] ?? '') === 'sold')>Vendido</option><option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactivo</option></select></div><div class="col-4 col-md-1 d-grid"><button class="btn btn-outline-success" type="submit">Filtrar</button></div></div></form>

    <section class="cow-card overflow-hidden"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Código / hierro</th><th>Categoría</th><th>Cabezas</th><th>Peso prom.</th><th>Finca / potrero</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
    @forelse($batches as $batch)<tr><td>@if($batch->image_url)<img src="{{ $batch->image_url }}" alt="Imagen del lote {{ $batch->code }}" width="64" height="48" class="rounded me-2" style="object-fit:cover" loading="lazy">@endif<strong>{{ $batch->code }}</strong><div class="small text-secondary">{{ $batch->ear_tag ?: 'Sin identificación' }}</div></td><td>{{ $batch->category->name }}</td><td>{{ number_format($batch->head_count) }}</td><td>{{ $batch->average_weight_kg ? number_format((float) $batch->average_weight_kg, 2).' kg' : '—' }}</td><td>{{ $batch->farm_name ?: '—' }}<div class="small text-secondary">{{ $batch->paddock ?: '' }}</div></td><td><span class="badge rounded-pill badge-{{ $batch->status }}">{{ ['active'=>'Activo','sold'=>'Vendido','inactive'=>'Inactivo'][$batch->status] }}</span></td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-success" href="{{ route('admin.livestock-batches.edit', $batch) }}">Editar</a><form class="d-inline" method="POST" action="{{ route('admin.livestock-batches.destroy', $batch) }}" onsubmit="return confirm('¿Eliminar este lote?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-secondary py-5">No hay lotes que coincidan. Registra un lote para comenzar.</td></tr>@endforelse
    </tbody></table></div><div class="p-3">{{ $batches->links('pagination::bootstrap-5') }}</div></section>
</main>
<script>
    const categorySelect = document.getElementById('livestock_category_id');
    const categoryName = document.getElementById('new_category_name');
    categoryName.addEventListener('input', function () { if (this.value.trim()) categorySelect.value = ''; });
    categorySelect.addEventListener('change', function () { if (this.value) categoryName.value = ''; });
</script>
</body>
</html>
