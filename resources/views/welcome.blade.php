<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CowApp: administra lotes y categorías de ganado desde una plataforma diseñada para el trabajo ganadero.">
    <title>{{ config('app.name', 'CowApp') }} | Gestión ganadera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--earth:#5A4507;--pasture:#0C820C;--pasture-bright:#0FA50F;--ochre:#D37211;--ink:#020219;--canvas:#F1F1F1;--line:#E2E4E8}
        *{scroll-margin-top:90px}body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:var(--ink);background:var(--canvas)}
        .portal-nav{background:rgba(255,255,255,.96);border-bottom:1px solid var(--line);backdrop-filter:blur(14px)}
        .brand-mark{height:42px;width:42px;border-radius:12px;background:var(--earth);color:#fff;display:grid;place-items:center;font-size:1.25rem}
        .portal-link{font-size:.9rem;font-weight:600;color:#4b5563}.portal-link:hover,.portal-link.active{color:var(--pasture)}
        .portal-hero{position:relative;overflow:hidden;background:var(--earth);color:#fff;padding:clamp(3.5rem,8vw,7rem) 0}
        .portal-hero:before,.portal-hero:after{content:"";position:absolute;border-radius:50%;pointer-events:none}
        .portal-hero:before{width:520px;height:520px;right:-170px;top:-300px;border:1px solid rgba(255,255,255,.13);box-shadow:0 0 0 42px rgba(255,255,255,.025),0 0 0 90px rgba(255,255,255,.02)}
        .portal-hero:after{width:350px;height:350px;left:-220px;bottom:-240px;background:rgba(12,130,12,.24)}
        .hero-copy,.hero-art{position:relative;z-index:1}.hero-eyebrow{display:inline-flex;align-items:center;gap:.55rem;padding:.5rem .8rem;border:1px solid rgba(255,255,255,.2);border-radius:999px;background:rgba(255,255,255,.08);font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
        .hero-copy h1{max-width:720px;font-size:clamp(2.5rem,5vw,4.35rem);line-height:1.08;letter-spacing:-.045em;font-weight:800}.hero-copy p{max-width:590px;color:rgba(255,255,255,.76);font-size:1.08rem;line-height:1.8}
        .hero-art{min-height:330px;border-radius:24px;border:1px solid rgba(255,255,255,.18);background:linear-gradient(150deg,#f8f7f1 0%,#dce7d1 52%,#a9c492 100%);overflow:hidden;box-shadow:0 24px 60px rgba(2,2,25,.22)}
        .hero-art-top{position:absolute;top:16px;left:16px;right:16px;z-index:2;display:flex;justify-content:space-between;align-items:center;color:var(--ink);font-size:.73rem;font-weight:700}
        .hero-art-badge{background:#fff;border:1px solid var(--line);border-radius:999px;padding:.45rem .7rem;box-shadow:0 2px 8px rgba(2,2,25,.06)}
        .hero-land,.hero-land-2{position:absolute;left:-10%;width:125%;height:52%;bottom:-15%;border-radius:50% 50% 0 0/20% 20% 0 0;transform:rotate(-7deg);background:#548449}
        .hero-land-2{bottom:-28%;left:28%;background:#0C820C;transform:rotate(8deg)}
        .hero-sun{position:absolute;width:92px;height:92px;right:12%;top:22%;border-radius:50%;background:#D37211;opacity:.92}
        .hero-cow{position:absolute;z-index:1;width:78%;max-width:430px;left:10%;bottom:15%;filter:drop-shadow(0 12px 12px rgba(2,2,25,.16))}
        .section-pad{padding:clamp(3.5rem,7vw,6rem) 0}.section-kicker{color:var(--pasture);font-size:.73rem;letter-spacing:.12em;font-weight:800;text-transform:uppercase}.section-title{color:var(--ink);font-size:clamp(1.8rem,3.4vw,2.65rem);font-weight:800;letter-spacing:-.035em}
        .service-card,.news-card,.contact-card{height:100%;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 1px 3px rgba(2,2,25,.04),0 1px 2px rgba(2,2,25,.02)}
        .service-card{padding:1.5rem;transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease}.service-card:hover,.news-card:hover{transform:translateY(-3px);border-color:#c4d7bd;box-shadow:0 8px 20px rgba(2,2,25,.07)}
        .service-icon{height:48px;width:48px;border-radius:14px;background:#E6F4E6;color:var(--pasture);display:grid;place-items:center;font-size:1.3rem;margin-bottom:1rem}
        .story-band{background:#fff;border-block:1px solid var(--line)}.story-mark{height:64px;width:64px;border-radius:18px;background:var(--earth);color:#fff;display:grid;place-items:center;font-size:1.7rem;flex-shrink:0}
        .news-card{overflow:hidden;display:flex;flex-direction:column;transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease}.news-img{height:205px;background:#e9ece5;overflow:hidden}.news-img img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease}.news-card:hover .news-img img{transform:scale(1.04)}
        .news-placeholder{height:100%;display:grid;place-items:center;color:#76816f;font-size:2.3rem}.contact-section{background:#F1F1F1}.contact-card{padding:clamp(1.25rem,4vw,2.5rem)}.form-control,.form-select{min-height:46px;border-color:#D1D5DB}.form-control:focus{border-color:var(--pasture);box-shadow:0 0 0 3px rgba(12,130,12,.15)}
        .portal-footer{background:#020219;color:#fff}.footer-link{color:#c7c8d2;text-decoration:none}.footer-link:hover{color:#8ffb7b}
        @media(max-width:767.98px){.portal-hero{padding:3.5rem 0}.hero-art{min-height:270px}.hero-cow{bottom:13%}.hero-copy p{font-size:1rem}}
        @media(prefers-reduced-motion:reduce){*,*::before,*::after{scroll-behavior:auto!important;transition:none!important}}
    </style>
    @include('partials.cowapp-stitch-theme')
</head>
<body>
<nav class="navbar navbar-expand-lg portal-nav sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="{{ route('home') }}">@include('partials.cowapp-logo', ['logoSize' => 72])<span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">CRM Ganadero</span></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#portalNav" aria-controls="portalNav" aria-expanded="false" aria-label="Abrir menú"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="portalNav">
            <ul class="navbar-nav mx-auto gap-lg-2 py-3 py-lg-0"><li class="nav-item"><a class="nav-link portal-link active" href="#inicio">Inicio</a></li><li class="nav-item"><a class="nav-link portal-link" href="#servicios">Plataforma</a></li><li class="nav-item"><a class="nav-link portal-link" href="#publicaciones">Actualidad</a></li><li class="nav-item"><a class="nav-link portal-link" href="#contacto">Contacto</a></li></ul>
            <div class="d-flex gap-2">@auth<a href="{{ route('dashboard') }}" class="btn btn-success rounded-3 px-3 fw-semibold">Ir al CRM</a>@else<a href="{{ route('login') }}" class="btn btn-outline-success rounded-3 px-3 fw-semibold">Iniciar sesión</a><a href="{{ route('register') }}" class="btn btn-success rounded-3 px-3 fw-semibold">Crear cuenta</a>@endauth</div>
        </div>
    </div>
</nav>

<header class="portal-hero" id="inicio"><div class="container position-relative"><div class="row align-items-center gy-5">
    <div class="col-lg-7 hero-copy"><span class="hero-eyebrow mb-4"><i class="bi bi-leaf-fill" style="color:#8FFB7B"></i> Gestión hecha para el campo</span><h1 class="mb-4">Tu ganadería, organizada desde el primer lote.</h1><p class="mb-4">Centraliza tu inventario ganadero, clasifica tus lotes y consulta la operación desde un solo lugar. CowApp acompaña el trabajo diario con información clara y accesible.</p><div class="d-flex flex-wrap gap-3"><a href="{{ route('register') }}" class="btn btn-success btn-lg rounded-3 px-4 fw-bold">Conoce CowApp <i class="bi bi-arrow-up-right ms-1"></i></a><a href="#servicios" class="btn btn-outline-light btn-lg rounded-3 px-4">Explorar plataforma</a></div><div class="d-flex flex-wrap gap-4 mt-4 small text-white-50"><span><i class="bi bi-check-circle-fill me-2" style="color:#8FFB7B"></i>Inventario por lotes</span><span><i class="bi bi-check-circle-fill me-2" style="color:#8FFB7B"></i>Acceso seguro</span></div></div>
    <div class="col-lg-5"><div class="hero-art" role="img" aria-label="Ilustración de ganado en una pradera"><div class="hero-art-top"><span>COWAPP <span class="text-secondary fw-normal">· GESTIÓN GANADERA</span></span><span class="hero-art-badge"><i class="bi bi-circle-fill text-success me-1" style="font-size:.55rem"></i> Plataforma activa</span></div><span class="hero-sun"></span><span class="hero-land"></span><span class="hero-land-2"></span>
        <svg class="hero-cow" viewBox="0 0 520 300" fill="none" aria-hidden="true"><path d="M96 111c17-30 42-43 82-43h143c23 0 41 13 53 32l23 36 48 6c18 2 31 15 32 32l2 36c1 16-12 29-28 29h-28v36h-34v-39h-85v39h-34v-39H154v39h-35v-42c-29-12-45-35-45-63v-23c0-17 7-29 22-36Z" fill="#5A4507"/><path d="M348 103c12-22 29-32 50-32h27c12 0 22 7 27 18l14 30 27 4c11 2 19 11 19 22v29c0 10-8 18-18 18h-19v18h-28v-28l-9-20-16 9h-52l-20-34" fill="#5A4507"/><path d="M116 114c16-18 36-27 62-27h121c18 0 30 9 39 25" stroke="#F1F1F1" stroke-width="8" stroke-linecap="round"/><path d="M129 230v-57c0-22 18-40 40-40h13c22 0 40 18 40 40v57m-76-49h76" stroke="#F1F1F1" stroke-width="8" stroke-linecap="round"/><circle cx="453" cy="139" r="4" fill="#F1F1F1"/><path d="M479 118l19-13m-13 40 24 4" stroke="#5A4507" stroke-width="7" stroke-linecap="round"/></svg>
    </div></div>
</div></div></header>

<main>
    <section id="servicios" class="section-pad"><div class="container"><div class="row align-items-end g-3 mb-4"><div class="col-lg-8"><span class="section-kicker">Una plataforma para el trabajo real</span><h2 class="section-title mt-2 mb-2">Herramientas para llevar mejor el control</h2><p class="text-secondary mb-0">Organiza la información ganadera con módulos sencillos, conectados y pensados para crecer contigo.</p></div></div>
        <div class="row g-3 g-lg-4">
            <div class="col-sm-6 col-xl-3"><article class="service-card"><span class="service-icon"><i class="bi bi-collection"></i></span><h3 class="h5 fw-bold">Gestión de lotes</h3><p class="text-secondary small mb-0">Registra cantidad de cabezas, peso promedio, finca, potrero e identificación de cada lote.</p></article></div>
            <div class="col-sm-6 col-xl-3"><article class="service-card"><span class="service-icon" style="background:#F4EFD8;color:#5A4507"><i class="bi bi-tags"></i></span><h3 class="h5 fw-bold">Categorías propias</h3><p class="text-secondary small mb-0">Crea un catálogo que refleje cómo clasificas y organizas tu ganado.</p></article></div>
            <div class="col-sm-6 col-xl-3"><article class="service-card"><span class="service-icon" style="background:#FFF0DD;color:#D37211"><i class="bi bi-bar-chart-line"></i></span><h3 class="h5 fw-bold">Resumen operativo</h3><p class="text-secondary small mb-0">Consulta cabezas activas, distribución por categoría y peso promedio ponderado.</p></article></div>
            <div class="col-sm-6 col-xl-3"><article class="service-card"><span class="service-icon"><i class="bi bi-shield-check"></i></span><h3 class="h5 fw-bold">Acceso protegido</h3><p class="text-secondary small mb-0">Cada cuenta accede a sus propios registros mediante autenticación y autorización del servidor.</p></article></div>
        </div>
    </div></section>

    <section class="story-band py-5"><div class="container"><div class="row align-items-center g-4"><div class="col-auto"><div class="story-mark"><i class="bi bi-tree"></i></div></div><div class="col-lg"><span class="section-kicker">Tradición y claridad</span><h2 class="h3 fw-bold mt-1 mb-2">Tecnología útil para decisiones cotidianas.</h2><p class="text-secondary mb-0">CowApp reúne el inventario y la información de tu ganadería en una experiencia clara, desde el registro de un lote hasta el resumen general de tu operación.</p></div><div class="col-lg-auto"><a href="{{ route('register') }}" class="btn btn-success rounded-3 px-4 py-2 fw-semibold">Empezar con CowApp</a></div></div></div></section>

    <section id="publicaciones" class="section-pad"><div class="container"><div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3 mb-4"><div><span class="section-kicker">Conocimiento ganadero</span><h2 class="section-title mt-2 mb-1">Actualidad y publicaciones</h2><p class="text-secondary mb-0">Artículos compartidos por el equipo de CowApp.</p></div>@auth<a href="{{ route('admin.news.index') }}" class="btn btn-outline-success rounded-3">Administrar publicaciones <i class="bi bi-arrow-up-right ms-1"></i></a>@endauth</div>
        @if($publishedNews->isEmpty())<div class="cow-card-surface p-5 text-center"><span class="text-success fs-1"><i class="bi bi-journal-richtext"></i></span><h3 class="h5 fw-bold mt-3">Pronto encontrarás nuevas publicaciones</h3><p class="text-secondary mb-0">Estamos preparando contenido útil para tu operación ganadera.</p></div>@else<div class="row g-3 g-lg-4">@foreach($publishedNews as $noticia)<div class="col-md-6 col-lg-4"><article class="news-card"><div class="news-img">@if($noticia->media)<img src="{{ Storage::url($noticia->media->path) }}" alt="{{ $noticia->title }}" loading="lazy">@else<div class="news-placeholder"><i class="bi bi-image"></i></div>@endif</div><div class="p-4 d-flex flex-column flex-grow-1"><div class="small text-secondary mb-2"><i class="bi bi-calendar3 me-1"></i>{{ $noticia->created_at->format('d/m/Y') }}</div><h3 class="h5 fw-bold mb-2">{{ $noticia->title }}</h3><p class="small text-secondary flex-grow-1">{{ $noticia->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($noticia->content), 130) }}</p>@if($noticia->media)<a href="{{ Storage::url($noticia->media->path) }}" target="_blank" rel="noopener noreferrer" class="small fw-semibold text-success text-decoration-none">Ver imagen <i class="bi bi-arrow-up-right"></i></a>@endif</div></article></div>@endforeach</div>@endif
    </div></section>

    <section class="contact-section section-pad" id="contacto"><div class="container"><div class="row justify-content-center"><div class="col-lg-9 col-xl-8"><div class="contact-card"><div class="row g-4"><div class="col-md-5"><span class="section-kicker">Hablemos</span><h2 class="h3 fw-bold mt-2">¿Tienes preguntas sobre CowApp?</h2><p class="text-secondary">Escríbenos y el equipo se pondrá en contacto contigo.</p><div class="small text-secondary mt-4"><i class="bi bi-envelope text-success me-2"></i>Atención directa desde el formulario</div></div><div class="col-md-7">
        @if(session('mail_success'))<div class="alert alert-success" role="status">{{ session('mail_success') }}</div>@endif
        @if(session('mail_error'))<div class="alert alert-warning" role="alert">{{ session('mail_error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('contact.send') }}" method="POST">@csrf<div class="row g-3"><div class="col-sm-6"><label class="form-label small fw-semibold" for="contact-name">Nombre</label><input id="contact-name" type="text" name="name" maxlength="100" class="form-control" value="{{ old('name') }}" autocomplete="name" required></div><div class="col-sm-6"><label class="form-label small fw-semibold" for="contact-email">Correo electrónico</label><input id="contact-email" type="email" name="email" maxlength="255" class="form-control" value="{{ old('email') }}" autocomplete="email" required></div><div class="col-12"><label class="form-label small fw-semibold" for="contact-message">Mensaje</label><textarea id="contact-message" name="message" rows="4" maxlength="5000" class="form-control" required>{{ old('message') }}</textarea></div><div class="col-12"><button type="submit" class="btn btn-success rounded-3 px-4 py-2 fw-bold">Enviar mensaje <i class="bi bi-send ms-1"></i></button></div></div></form>
    </div></div></div></div></div></section>
</main>

<footer class="portal-footer py-4"><div class="container d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"><div><div class="fw-bold">CowApp</div><div class="small text-white-50">Gestión ganadera hecha para el campo.</div></div><div class="d-flex gap-3 small"><a class="footer-link" href="#servicios">Plataforma</a><a class="footer-link" href="#publicaciones">Actualidad</a><a class="footer-link" href="#contacto">Contacto</a></div><div class="small text-white-50">© {{ now()->year }} CowApp</div></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
