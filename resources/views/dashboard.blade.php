<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel CRM | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('partials.cowapp-stitch-theme')
    <style>
        body{background:#F1F1F1;color:#020219;min-height:100vh}
        .panel-sidebar{width:250px;position:fixed;inset:0 auto 0 0;overflow-y:auto;background:#fff;border-right:1px solid #E2E4E8;padding:1.25rem}
        .panel-brand{display:flex;align-items:center;gap:.75rem;color:#020219;text-decoration:none;font-size:1.4rem;font-weight:800;margin-bottom:1.5rem}
        .panel-nav{display:grid;gap:.3rem}.panel-nav a{display:flex;align-items:center;gap:.7rem;padding:.7rem;border-radius:10px;color:#4C4639;text-decoration:none;font-weight:600;font-size:.9rem}
        .panel-nav a:hover,.panel-nav a[aria-current]{background:#E6F4E6;color:#0C820C}
        .panel-caption{font-size:.7rem;letter-spacing:.08em;text-transform:uppercase;color:#6B7280;margin:1.25rem .7rem .5rem;font-weight:700}
        .panel-main{margin-left:250px;padding:2rem;max-width:1700px}
        .metric-value{font-size:2rem;font-weight:800;letter-spacing:-.04em;font-variant-numeric:tabular-nums}
        .metric-icon{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:#E6F4E6;color:#0C820C;font-size:1.2rem}
        .panel-account{max-width:100%;overflow-wrap:anywhere}.table th{font-size:.75rem;text-transform:uppercase;color:#6B7280;white-space:nowrap}
        .panel-nav a:focus-visible,.btn:focus-visible{outline:3px solid #0C820C;outline-offset:3px}
        @media(max-width:991.98px){.panel-sidebar{position:static;width:auto;padding:1rem;border-right:0;border-bottom:1px solid #E2E4E8}.panel-brand{margin-bottom:.75rem}.panel-nav{display:flex;flex-wrap:wrap}.panel-caption{margin-top:.75rem}.panel-main{margin-left:0;padding:1.25rem 1rem}.metric-value{font-size:1.6rem}}
    </style>
</head>
<body>
<a class="visually-hidden-focusable" href="#panel-content">Ir al contenido</a>
<aside class="panel-sidebar" aria-label="Navegación principal">
    <a class="panel-brand" href="{{ route('dashboard') }}">@include('partials.cowapp-logo', ['logoSize' => 80])<span><small class="d-block text-secondary" style="font-size:.65rem;letter-spacing:.1em">CRM GANADERO</small></span></a>
    <nav class="panel-nav" aria-label="Gestión comercial">
        <a href="{{ route('dashboard') }}" aria-current="page"><i class="bi bi-grid" aria-hidden="true"></i>Panel</a>
        @can('permission', 'clients.manage')<a href="{{ route('admin.clients.index') }}"><i class="bi bi-people" aria-hidden="true"></i>Clientes</a>@endcan
        @can('permission', 'leads.manage')<a href="{{ route('admin.leads.index') }}"><i class="bi bi-person-plus" aria-hidden="true"></i>Prospectos</a>@endcan
        @can('permission', 'sales-pipeline.manage')<a href="{{ route('admin.sales-pipeline.index') }}"><i class="bi bi-kanban" aria-hidden="true"></i>Oportunidades</a>@endcan
        <a href="{{ route('admin.livestock-batches.index') }}"><i class="bi bi-boxes" aria-hidden="true"></i>Lotes de ganado</a>
        <a href="{{ route('admin.livestock-categories.index') }}"><i class="bi bi-tags" aria-hidden="true"></i>Categorías</a>
        @include('partials.workspace-links',['extendedOnly'=>true])
    </nav>
    @if($user->hasPermissionTo('content.manage') || $user->hasPermissionTo('mail-settings.manage') || $user->isPlatformAdmin())
        <div class="panel-caption">Administración</div>
        <nav class="panel-nav" aria-label="Administración">
            @can('permission', 'content.manage')
                <a href="{{ route('admin.media.index') }}"><i class="bi bi-images" aria-hidden="true"></i>Multimedia</a>
                <a href="{{ route('admin.news.index') }}"><i class="bi bi-newspaper" aria-hidden="true"></i>Noticias</a>
            @endcan
            @can('viewAny', \App\Models\MailSetting::class)<a href="{{ route('admin.settings.mail.edit') }}"><i class="bi bi-envelope-gear" aria-hidden="true"></i>Correo SMTP</a>@endcan
            @can('viewAny', \App\Models\Role::class)<a href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock" aria-hidden="true"></i>Roles y permisos</a>@endcan
        </nav>
    @endif
</aside>
<main class="panel-main" id="panel-content">
    <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div><div class="small text-secondary mb-1">CowApp / Panel</div><h1 class="h2 fw-bold mb-1">Resumen de tu cuenta</h1><p class="text-secondary mb-0">Hola, {{ $user->name }}. Consulta tu actividad comercial y tu inventario.</p></div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a class="btn btn-outline-success" href="{{ route('home') }}">Sitio público</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-dark"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Cerrar sesión</button></form>
        </div>
    </header>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif

    @if($metrics['clients'] !== null || $metrics['leads'] !== null || $metrics['open_opportunities'] !== null)
        <section aria-labelledby="commercial-title" class="mb-4">
            <h2 id="commercial-title" class="h5 fw-bold mb-3">Gestión comercial</h2>
            <div class="row g-3">
                @if($metrics['clients'] !== null)
                    <div class="col-12 col-sm-6 col-xl-3"><article class="cow-card-surface p-4 h-100"><div class="d-flex justify-content-between align-items-center mb-2"><h3 class="h6 text-secondary mb-0">Clientes registrados</h3><span class="metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span></div><div class="metric-value">{{ number_format($metrics['clients']) }}</div><a href="{{ route('admin.clients.index') }}" class="small text-success">Gestionar clientes</a></article></div>
                @endif
                @if($metrics['leads'] !== null)
                    <div class="col-12 col-sm-6 col-xl-3"><article class="cow-card-surface p-4 h-100"><div class="d-flex justify-content-between align-items-center mb-2"><h3 class="h6 text-secondary mb-0">Prospectos registrados</h3><span class="metric-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></span></div><div class="metric-value">{{ number_format($metrics['leads']) }}</div><p class="small text-secondary mb-1">{{ number_format($metrics['new_leads']) }} nuevos</p><a href="{{ route('admin.leads.index') }}" class="small text-success">Gestionar prospectos</a></article></div>
                @endif
                @if($metrics['open_opportunities'] !== null)
                    <div class="col-12 col-sm-6 col-xl-3"><article class="cow-card-surface p-4 h-100"><div class="d-flex justify-content-between align-items-center mb-2"><h3 class="h6 text-secondary mb-0">Oportunidades abiertas</h3><span class="metric-icon"><i class="bi bi-kanban" aria-hidden="true"></i></span></div><div class="metric-value">{{ number_format($metrics['open_opportunities']) }}</div><p class="small text-secondary mb-1">{{ number_format($metrics['won_opportunities']) }} ganadas</p><a href="{{ route('admin.sales-pipeline.index') }}" class="small text-success">Ver oportunidades</a></article></div>
                    <div class="col-12 col-sm-6 col-xl-3"><article class="cow-card-surface p-4 h-100"><h3 class="h6 text-secondary mb-3">Valor estimado abierto</h3><div class="fs-4 fw-bold cow-tabular">$ {{ number_format($metrics['open_pipeline_value'], 2, ',', '.') }}</div><p class="small text-secondary mb-0">Estimación comercial; no representa ventas cobradas.</p></article></div>
                @endif
            </div>
        </section>
    @endif

    <section aria-labelledby="inventory-title" class="mb-4">
        <h2 id="inventory-title" class="h5 fw-bold mb-3">Inventario de tu cuenta</h2>
        <div class="row g-3">
            <div class="col-6 col-xl-3"><article class="cow-card-surface p-3 p-md-4 h-100"><h3 class="h6 text-secondary">Cabezas activas</h3><div class="metric-value">{{ number_format($metrics['active_heads']) }}</div></article></div>
            <div class="col-6 col-xl-3"><article class="cow-card-surface p-3 p-md-4 h-100"><h3 class="h6 text-secondary">Lotes activos</h3><div class="metric-value">{{ number_format($metrics['active_batches']) }}</div></article></div>
            <div class="col-6 col-xl-3"><article class="cow-card-surface p-3 p-md-4 h-100"><h3 class="h6 text-secondary">Peso promedio ponderado</h3><div class="fs-4 fw-bold cow-tabular">{{ $metrics['weighted_average_weight'] === null ? 'Sin datos' : number_format($metrics['weighted_average_weight'], 2, '.', ',').' kg' }}</div><p class="small text-secondary mb-0">Lotes activos con peso registrado.</p></article></div>
            <div class="col-6 col-xl-3"><article class="cow-card-surface p-3 p-md-4 h-100"><h3 class="h6 text-secondary">Categorías con inventario</h3><div class="metric-value">{{ number_format($metrics['categories_with_inventory']) }}</div></article></div>
        </div>
    </section>

    <div class="row g-4">
        <section class="col-12 col-xl-8" aria-labelledby="recent-title">
            <div class="cow-card-surface h-100 overflow-hidden">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom"><h2 id="recent-title" class="h5 fw-bold mb-0">Lotes recientes</h2><a class="btn btn-sm btn-outline-success" href="{{ route('admin.livestock-batches.index') }}">Ver inventario</a></div>
                @if($recentBatches->isEmpty())
                    <div class="text-center p-4 p-md-5"><i class="bi bi-boxes fs-1 text-success" aria-hidden="true"></i><p class="text-secondary mt-3">Aún no hay lotes en tu inventario.</p><a class="btn btn-success" href="{{ route('admin.livestock-batches.index') }}">Registrar el primer lote</a></div>
                @else
                    <div class="table-responsive"><table class="table align-middle mb-0"><caption class="visually-hidden">Últimos seis lotes registrados en tu cuenta</caption><thead class="table-light"><tr><th class="ps-4">Lote</th><th>Categoría</th><th>Cabezas</th><th>Estado</th></tr></thead><tbody>
                        @foreach($recentBatches as $batch)
                            <tr><td class="ps-4 py-3"><a class="fw-semibold text-success" href="{{ route('admin.livestock-batches.edit', $batch) }}">{{ $batch->code }}</a><div class="small text-secondary">{{ $batch->farm_name ?: 'Finca no especificada' }}</div></td><td>{{ $batch->category->name }}</td><td class="cow-tabular">{{ number_format($batch->head_count) }}</td><td><span class="badge badge-{{ $batch->status }}">{{ ['active' => 'Activo', 'sold' => 'Vendido', 'inactive' => 'Inactivo'][$batch->status] }}</span></td></tr>
                        @endforeach
                    </tbody></table></div>
                @endif
            </div>
        </section>
        <section class="col-12 col-xl-4" aria-labelledby="categories-title">
            <div class="cow-card-surface p-4 h-100">
                <h2 id="categories-title" class="h5 fw-bold mb-3">Inventario por categoría</h2>
                @forelse($categoryBreakdown as $category)
                    <div class="d-flex justify-content-between gap-3 py-2 border-bottom"><span>{{ $category['name'] }}</span><span class="text-secondary text-nowrap cow-tabular">{{ number_format($category['head_count']) }} cabezas</span></div>
                @empty
                    <p class="text-secondary">Todavía no hay categorías registradas.</p>
                @endforelse
                <p class="small text-secondary mt-3">Hasta seis categorías de tu cuenta. Se cuentan únicamente cabezas de lotes activos.</p>
                <a href="{{ route('admin.livestock-categories.index') }}" class="small text-success">Gestionar categorías</a>
                <hr>
                <div class="d-flex justify-content-between small mb-2"><span>Lotes vendidos</span><strong>{{ number_format($statusCounts['sold']) }}</strong></div>
                <div class="d-flex justify-content-between small"><span>Lotes inactivos</span><strong>{{ number_format($statusCounts['inactive']) }}</strong></div>
            </div>
        </section>
    </div>
    <footer class="panel-account small text-secondary mt-4">Sesión de {{ $user->name }} · Los datos mostrados pertenecen a tu cuenta.</footer>
</main>
</body>
</html>
