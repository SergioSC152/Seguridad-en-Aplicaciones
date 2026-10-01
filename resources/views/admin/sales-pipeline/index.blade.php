<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pipeline comercial | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.cowapp-stitch-theme')
    <style>
        :root{--ink:#020219;--muted:#737783;--line:#e8e8ec;--green:#0C820C;--gold:#5A4507;--orange:#D37211;--surface:#F1F1F1}
        body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:var(--surface);color:var(--ink);min-height:100vh}
        .crm-sidebar{width:260px;background:white;border-right:1px solid var(--line);min-height:100vh;position:fixed;inset:0 auto 0 0;z-index:1020;padding:1.25rem;display:flex;flex-direction:column}
        .crm-brand{display:flex;align-items:center;gap:.75rem;text-decoration:none;color:var(--ink);font-size:1.2rem;font-weight:800;padding:.35rem .5rem 1.7rem}
        .brand-mark{display:grid;place-items:center;width:42px;height:42px;border-radius:14px;background:linear-gradient(135deg,var(--gold),var(--green));color:white}
        .nav-caption{font-size:.67rem;text-transform:uppercase;letter-spacing:.12em;color:#a0a2aa;font-weight:800;padding:0 .8rem;margin:1.1rem 0 .45rem}
        .crm-nav{display:grid;gap:.25rem}.crm-nav a{padding:.72rem .8rem;border-radius:11px;display:flex;align-items:center;gap:.75rem;color:#555966;text-decoration:none;font-size:.88rem;font-weight:650}
        .crm-nav a:hover{background:#f4f7f4;color:var(--green)}.crm-nav a.active{background:#e9f5e9;color:var(--green)}.crm-nav i{font-size:1.1rem;width:20px;text-align:center}
        .crm-user{margin-top:auto;border-top:1px solid var(--line);padding-top:1rem;display:flex;align-items:center;gap:.65rem}.avatar{width:38px;height:38px;border-radius:50%;display:grid;place-items:center;background:#e8f4e8;color:var(--green);font-weight:800}
        .crm-main{margin-left:260px;padding:1.5rem 2rem 2.5rem}.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.7rem}
        .surface-card{background:white;border:1px solid var(--line);border-radius:16px;box-shadow:0 3px 12px rgba(2,2,25,.025)}
        .metric-label{font-size:.7rem;letter-spacing:.08em;color:var(--muted);font-weight:800;text-transform:uppercase}.metric-value{font-size:1.65rem;font-weight:800;letter-spacing:-.04em}
        .stage-head{display:flex;align-items:center;justify-content:space-between;padding:1rem;border-bottom:1px solid var(--line)}.stage-title{font-size:.82rem;font-weight:800}.stage-count{font-size:.72rem;background:#f0f1f3;color:#565a65;border-radius:99px;padding:.2rem .55rem;font-weight:750}
        .stage-column{min-height:220px}.opportunity-card{border:1px solid var(--line);border-radius:13px;padding:1rem;background:white}.opportunity-card:hover{border-color:#b9d8b9;box-shadow:0 5px 16px rgba(2,2,25,.06)}
        .pipeline-board{display:grid;grid-template-columns:repeat(5,minmax(220px,1fr));gap:.8rem;overflow-x:auto;padding-bottom:.4rem}.pipeline-lane{min-width:220px;background:#f7f7f8;border:1px solid var(--line);border-radius:15px;overflow:hidden}.lane-body{padding:.65rem;display:grid;align-content:start;gap:.65rem;min-height:180px}
        .stage-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--green);margin-right:.4rem}.stage-visit .stage-dot{background:#1689bd}.stage-negotiation .stage-dot{background:var(--orange)}.stage-won .stage-dot{background:var(--green)}.stage-lost .stage-dot{background:#858895}
        .form-label{font-size:.8rem;font-weight:700}.form-control,.form-select{border-color:#dedfe4;border-radius:10px;padding:.65rem .8rem}.btn{border-radius:10px;font-weight:700}.text-muted{color:var(--muted)!important}
        @media(max-width:991.98px){.crm-sidebar{position:static;width:auto;min-height:0;padding:.65rem 1rem;border-right:0;border-bottom:1px solid var(--line)}.crm-brand{padding:.35rem}.crm-nav{display:flex;overflow:auto}.crm-nav a{white-space:nowrap}.crm-nav .nav-caption{display:none}.crm-user{display:none}.crm-main{margin-left:0;padding:1.25rem 1rem 2rem}.pipeline-board{grid-template-columns:repeat(5,minmax(245px,1fr))}}
    </style>
</head>
<body>
<aside class="crm-sidebar" aria-label="Navegación principal">
    <a class="crm-brand" href="{{ route('dashboard') }}"><span class="brand-mark"><i class="bi bi-cow"></i></span><span>CowApp<small class="d-block text-muted" style="font-size:.62rem;letter-spacing:.12em">CRM GANADERO</small></span></a>
    <div class="nav-caption">Workspace</div><nav class="crm-nav">
        <a href="{{ route('dashboard') }}"><i class="bi bi-grid"></i>Panel</a>
        @can('permission','clients.manage')<a href="{{ route('admin.clients.index') }}"><i class="bi bi-people"></i>Clientes</a>@endcan
        @can('permission','leads.manage')<a href="{{ route('admin.leads.index') }}"><i class="bi bi-person-plus"></i>Leads y prospectos</a>@endcan
        <a class="active" aria-current="page" href="{{ route('admin.sales-pipeline.index') }}"><i class="bi bi-kanban"></i>Pipeline comercial</a>
        <a href="{{ route('admin.livestock-batches.index') }}"><i class="bi bi-boxes"></i>Lotes de ganado</a>
        <a href="{{ route('admin.livestock-categories.index') }}"><i class="bi bi-tags"></i>Categorías</a>
        @can('permission','content.manage')<a href="{{ route('admin.media.index') }}"><i class="bi bi-images"></i>Multimedia</a><a href="{{ route('admin.news.index') }}"><i class="bi bi-newspaper"></i>Noticias</a>@endcan
        @can('viewAny',\App\Models\MailSetting::class)<a href="{{ route('admin.settings.mail.edit') }}"><i class="bi bi-envelope-gear"></i>Correo SMTP</a>@endcan
        @can('viewAny',\App\Models\Role::class)<a href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock"></i>Roles y permisos</a>@endcan
    </nav>
    <div class="crm-user"><span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name,0,1)) }}</span><div class="overflow-hidden"><div class="fw-bold small text-truncate">{{ auth()->user()->name }}</div><div class="text-muted" style="font-size:.72rem">Cuenta ganadera</div></div><form class="ms-auto" method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-light" aria-label="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></button></form></div>
</aside>

<main class="crm-main">
    <header class="topbar"><div><div class="text-muted small mb-1">CRM <span class="mx-1">/</span> Oportunidades</div><h1 class="h3 fw-bold mb-0">Pipeline comercial</h1></div><a class="btn btn-success" href="#form-oportunidad"><i class="bi bi-plus-lg me-1"></i>Nueva oportunidad</a></header>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Revisa los datos del formulario.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="row g-3 mb-4" aria-label="Resumen comercial">
        <div class="col-6 col-xl-3"><article class="surface-card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between mb-3"><span class="metric-label">En curso</span><i class="bi bi-arrow-repeat text-success"></i></div><div class="metric-value">{{ number_format($metrics['open_count']) }}</div><div class="small text-muted">Oportunidades activas</div></article></div>
        <div class="col-6 col-xl-3"><article class="surface-card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between mb-3"><span class="metric-label">Valor abierto</span><i class="bi bi-currency-dollar" style="color:#0C820C"></i></div><div class="metric-value fs-4">${{ number_format((float)$metrics['open_value'],0,',','.') }}</div><div class="small text-muted">Estimado de oportunidades activas</div></article></div>
        <div class="col-6 col-xl-3"><article class="surface-card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between mb-3"><span class="metric-label">Ganadas</span><i class="bi bi-check-circle text-success"></i></div><div class="metric-value">{{ number_format($metrics['won_count']) }}</div><div class="small text-muted">Negocios cerrados con éxito</div></article></div>
        <div class="col-6 col-xl-3"><article class="surface-card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between mb-3"><span class="metric-label">Perdidas</span><i class="bi bi-dash-circle text-secondary"></i></div><div class="metric-value">{{ number_format($metrics['lost_count']) }}</div><div class="small text-muted">Negocios cerrados sin venta</div></article></div>
    </section>

    <form method="GET" action="{{ route('admin.sales-pipeline.index') }}" class="surface-card p-3 mb-4"><div class="row g-2 align-items-end"><div class="col-12 col-lg-5"><label class="form-label" for="q">Buscar</label><input class="form-control" id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Oportunidad, ganado o cliente"></div><div class="col-6 col-lg-3"><label class="form-label" for="filter-stage">Etapa</label><select class="form-select" id="filter-stage" name="stage"><option value="">Todas las etapas</option>@foreach($stages as $key=>$label)<option value="{{ $key }}" @selected(($filters['stage'] ?? '')===$key)>{{ $label }}</option>@endforeach</select></div><div class="col-6 col-lg-2"><label class="form-label" for="filter-client">Cliente</label><select class="form-select" id="filter-client" name="client_id"><option value="">Todos</option>@foreach($clients as $client)<option value="{{ $client->id }}" @selected((string)($filters['client_id'] ?? '')===(string)$client->id)>{{ $client->name }}</option>@endforeach</select></div><div class="col-12 col-lg-2 d-flex gap-2"><button class="btn btn-dark flex-fill"><i class="bi bi-funnel me-1"></i>Filtrar</button><a class="btn btn-light border" href="{{ route('admin.sales-pipeline.index') }}" aria-label="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div></div></form>

    <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="h5 fw-bold mb-1">Oportunidades</h2><p class="small text-muted mb-0">{{ $opportunities->total() }} registros en tu cuenta</p></div></div>
    <section class="pipeline-board mb-4" aria-label="Tablero de oportunidades por etapa">
        @foreach($stages as $stage=>$label)
            <section class="pipeline-lane stage-{{ $stage }}"><header class="stage-head"><span class="stage-title"><span class="stage-dot"></span>{{ $label }}</span><span class="stage-count">{{ ($groups->get($stage) ?? collect())->count() }}</span></header><div class="lane-body">
                @forelse($groups->get($stage, collect()) as $opportunity)
                    <article class="opportunity-card"><div class="d-flex justify-content-between gap-2"><h3 class="h6 fw-bold mb-1">{{ $opportunity->title }}</h3><span class="dropdown"><button class="btn btn-sm p-0 text-muted" data-bs-toggle="dropdown" aria-label="Acciones"><i class="bi bi-three-dots"></i></button><span class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="{{ route('admin.sales-pipeline.edit',$opportunity) }}">Editar</a><form method="POST" action="{{ route('admin.sales-pipeline.destroy',$opportunity) }}" onsubmit="return confirm('¿Eliminar esta oportunidad?')">@csrf @method('DELETE')<button class="dropdown-item text-danger">Eliminar</button></form></span></span></div>
                        <div class="small text-muted mb-3"><i class="bi bi-person me-1"></i>{{ $opportunity->client->name }}</div><div class="d-flex justify-content-between align-items-center border-top pt-2 small"><span class="fw-bold">${{ number_format((float)$opportunity->estimated_value,0,',','.') }}</span><span class="text-muted">{{ $opportunity->head_count ? number_format($opportunity->head_count).' cabezas' : 'Sin cantidad' }}</span></div>
                        @if($opportunity->livestock_summary)<div class="small text-muted mt-2"><i class="bi bi-cow me-1"></i>{{ $opportunity->livestock_summary }}</div>@endif
                        <div class="small text-muted mt-2"><i class="bi bi-calendar3 me-1"></i>{{ $opportunity->expected_close_date?->format('d/m/Y') ?? 'Sin fecha estimada' }}</div>
                    </article>
                @empty<div class="small text-muted text-center py-4">No hay oportunidades en esta etapa.</div>@endforelse
            </div></section>
        @endforeach
    </section>
    <div class="d-flex justify-content-end mb-4">{{ $opportunities->links() }}</div>

    <section class="surface-card p-3 p-lg-4" id="form-oportunidad"><div class="d-flex justify-content-between align-items-start mb-3"><div><h2 class="h5 fw-bold mb-1">{{ $editingOpportunity ? 'Editar oportunidad' : 'Registrar oportunidad' }}</h2><p class="small text-muted mb-0">Asocia el seguimiento comercial con uno de tus clientes.</p></div>@if($editingOpportunity)<a class="btn btn-sm btn-light border" href="{{ route('admin.sales-pipeline.index') }}">Cancelar</a>@endif</div>
        @if($editingOpportunity)<form method="POST" action="{{ route('admin.sales-pipeline.update',$editingOpportunity) }}">@csrf @method('PUT')@else<form method="POST" action="{{ route('admin.sales-pipeline.store') }}">@csrf @endif
            <div class="row g-3"><div class="col-12 col-md-6"><label class="form-label" for="title">Nombre de la oportunidad <span class="text-danger">*</span></label><input class="form-control" id="title" name="title" required maxlength="180" value="{{ old('title',$editingOpportunity?->title) }}">@error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6"><label class="form-label" for="client_id">Cliente <span class="text-danger">*</span></label><select class="form-select" id="client_id" name="client_id" required><option value="">Selecciona un cliente</option>@foreach($clients as $client)<option value="{{ $client->id }}" @selected((string)old('client_id',$editingOpportunity?->client_id)===(string)$client->id)>{{ $client->name }}</option>@endforeach</select>@error('client_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6"><label class="form-label" for="livestock_summary">Tipo de ganado / resumen</label><input class="form-control" id="livestock_summary" name="livestock_summary" maxlength="200" value="{{ old('livestock_summary',$editingOpportunity?->livestock_summary) }}" placeholder="Ej. novillos cebú"></div>
                <div class="col-6 col-md-3"><label class="form-label" for="head_count">Cabezas</label><input class="form-control" type="number" min="1" max="1000000" id="head_count" name="head_count" value="{{ old('head_count',$editingOpportunity?->head_count) }}"></div>
                <div class="col-6 col-md-3"><label class="form-label" for="estimated_value">Valor estimado (COP) <span class="text-danger">*</span></label><input class="form-control" type="number" min="0" step="0.01" id="estimated_value" name="estimated_value" required value="{{ old('estimated_value',$editingOpportunity?->estimated_value ?? '0.00') }}"></div>
                <div class="col-12 col-md-6"><label class="form-label" for="stage">Etapa <span class="text-danger">*</span></label><select class="form-select" id="stage" name="stage" required>@foreach($stages as $key=>$label)<option value="{{ $key }}" @selected(old('stage',$editingOpportunity?->stage ?? 'contact')===$key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-12 col-md-6"><label class="form-label" for="expected_close_date">Fecha estimada de cierre</label><input class="form-control" type="date" id="expected_close_date" name="expected_close_date" value="{{ old('expected_close_date',$editingOpportunity?->expected_close_date?->format('Y-m-d')) }}"></div>
                <div class="col-12"><label class="form-label" for="notes">Notas de seguimiento</label><textarea class="form-control" id="notes" name="notes" rows="3" maxlength="3000">{{ old('notes',$editingOpportunity?->notes) }}</textarea></div>
                <div class="col-12 d-flex justify-content-end"><button class="btn btn-success px-4"><i class="bi bi-check2 me-1"></i>{{ $editingOpportunity ? 'Guardar cambios' : 'Crear oportunidad' }}</button></div>
            </div>
        </form>
    </section>
    <footer class="small text-muted mt-4">Las oportunidades y métricas mostradas pertenecen a tu cuenta.</footer>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
