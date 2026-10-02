<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title', 'Gestión') | CowApp</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
@include('partials.cowapp-stitch-theme')
<style>body{background:#F1F1F1;color:#020219}.workspace-nav{width:260px;position:fixed;inset:0 auto 0 0;background:white;padding:1.5rem;overflow:auto;border-right:1px solid #E2E4E8}.workspace-nav a{display:block;padding:.65rem;border-radius:8px;text-decoration:none;color:#4C4639}.workspace-nav a:hover{background:#e6f4e6;color:#0C820C}.workspace-main{margin-left:260px;padding:2rem}.form-control,.form-select,.btn{min-height:44px}.card{border:1px solid #E2E4E8;border-radius:16px}.table{font-variant-numeric:tabular-nums}.table th{font-size:.75rem;text-transform:uppercase}.money{font-variant-numeric:tabular-nums}@media(max-width:991px){.workspace-nav{position:static;width:auto}.workspace-nav nav{display:flex;flex-wrap:wrap}.workspace-main{margin-left:0;padding:1rem}}</style></head><body>
<aside class="workspace-nav"><a href="{{ route('dashboard') }}">@include('partials.cowapp-logo',['logoSize'=>100])</a><nav>
<a href="{{ route('dashboard') }}">Panel</a>
@include('partials.workspace-links')
</nav><form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button class="btn btn-outline-secondary">Cerrar sesión</button></form></aside>
<main class="workspace-main"><header class="mb-4"><div class="small text-secondary">CowApp / Gestión</div><h1 class="h3 fw-bold">@yield('title')</h1></header>
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')</main>@stack('scripts')</body></html>
