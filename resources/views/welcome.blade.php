<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Cow App CRM') }} | Plataforma Ganadera & CMS</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS v5.3.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --cow-green-dark: #064e3b;
            --cow-green-primary: #047857;
            --cow-green-light: #10b981;
            --cow-green-subtle: #ecfdf5;
            --cow-slate-900: #0f172a;
            --cow-slate-800: #1e293b;
            --cow-slate-700: #334155;
            --cow-slate-600: #475569;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--cow-slate-700);
            background-color: #f8fafc;
            min-height: 100vh;
        }
        .cow-hero {
            background: linear-gradient(135deg, #064e3b 0%, #047857 60%, #059669 100%);
            color: #ffffff;
            padding: 5rem 0 4rem;
            position: relative;
            overflow: hidden;
        }
        .cow-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .news-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(6, 78, 59, 0.15);
            border-color: #a7f3d0;
        }
        .news-img-box {
            height: 220px;
            background-color: #f1f5f9;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #f1f5f9;
        }
        .news-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .news-card:hover .news-img-box img {
            transform: scale(1.05);
        }
        .contact-box {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="{{ url('/') }}">
                <div class="bg-success text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-tag-fill fs-5"></i>
                </div>
                <span>{{ config('app.name', 'Cow App') }}</span>
                <span class="badge bg-success-subtle text-success small">CRM Ganadero</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-2">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold text-success" href="{{ url('/') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="#noticias">Noticias & Publicaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="#contacto">Contacto Directo</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                            <i class="bi bi-grid-fill me-1"></i> Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-success rounded-pill px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-success rounded-pill px-3">
                            <i class="bi bi-person-plus-fill me-1"></i> Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="cow-hero">
        <div class="container position-relative">
            <div class="row align-items-center gy-4">
                <div class="col-lg-8">
                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 fw-semibold mb-3">
                        <i class="bi bi-shield-check text-warning me-1"></i> Seguridad en Aplicaciones • Laravel
                    </span>
                    <h1 class="display-5 fw-extrabold mb-3">
                        Gestión Segura de Imágenes, Publicaciones y Correos SMTP
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Sistema integral que implementa almacenamiento validado en disco público (<code>storage:link</code>), metadatos persistentes, reutilización de archivos y envío profesional de correos.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#noticias" class="btn btn-light text-success fw-bold rounded-pill px-4 py-2 shadow-sm">
                            <i class="bi bi-newspaper me-1"></i> Ver Noticias Publicadas
                        </a>
                        <a href="#contacto" class="btn btn-outline-light rounded-pill px-4 py-2">
                            <i class="bi bi-envelope me-1"></i> Enviar Correo de Contacto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-5">

        <!-- Sección de Noticias (Paso 5.11 / Reto B) -->
        <section id="noticias" class="mb-5 pb-4">
            <div class="d-flex align-items-end justify-content-between mb-4">
                <div>
                    <span class="badge bg-success-subtle text-success fw-bold mb-1">MÓDULO DE PUBLICACIÓN</span>
                    <h2 class="fw-bold text-dark mb-0">Últimas Noticias y Artículos</h2>
                </div>
                @auth
                    <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bi bi-gear-fill me-1"></i> Gestionar Noticias
                    </a>
                @endauth
            </div>

            @php
                $publishedNews = \App\Models\News::with('media')->where('published', true)->latest()->take(6)->get();
            @endphp

            @if($publishedNews->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="text-muted mb-3">
                        <i class="bi bi-newspaper" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No hay publicaciones disponibles en este momento</h5>
                    <p class="text-muted small mb-0">Inicia sesión en el panel para crear y publicar tus primeras noticias con imágenes.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($publishedNews as $noticia)
                        <div class="col-md-6 col-lg-4">
                            <article class="news-card">
                                <!-- Mostrar la misma imagen en la página principal (Paso 5.11) -->
                                <div class="news-img-box">
                                    @if($noticia->media)
                                        <img src="{{ Storage::url($noticia->media->path) }}" alt="{{ $noticia->title }}" loading="lazy">
                                    @else
                                        <div class="text-muted small d-flex flex-column align-items-center gap-1">
                                            <i class="bi bi-image fs-3"></i>
                                            <span>Sin imagen asignada</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $noticia->created_at->format('d M, Y') }}
                                        @if($noticia->media)
                                            <span class="badge bg-light text-muted border ms-1">{{ $noticia->media->mime_type }}</span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">{{ $noticia->title }}</h5>
                                    <p class="text-muted small mb-3 flex-grow-1">
                                        {{ $noticia->excerpt ?? Str::limit($noticia->content, 120) }}
                                    </p>
                                    @if($noticia->media)
                                        <div class="pt-2 border-top">
                                            <a href="{{ Storage::url($noticia->media->path) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                                                <i class="bi bi-arrows-fullscreen me-1"></i> Ver imagen completa
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Sección Formulario de Contacto (Paso 6.5 / Reto C) -->
        <section id="contacto" class="pt-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <div class="contact-box p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <span class="badge bg-primary-subtle text-primary fw-bold mb-2">RETO C - SISTEMA DE CORREO</span>
                            <h2 class="fw-bold text-dark mb-2">Envíanos un Mensaje</h2>
                            <p class="text-muted small">
                                Completa el formulario para enviar una notificación directa al administrador y recibir un acuse de recibo en tu correo electrónico.
                            </p>
                        </div>

                        <!-- Notificaciones de éxito o error -->
                        @if(session('mail_success'))
                            <div class="alert alert-success rounded-4 d-flex align-items-center gap-2 shadow-sm border-0 mb-4" role="alert">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>{{ session('mail_success') }}</div>
                            </div>
                        @endif

                        @if(session('mail_error'))
                            <div class="alert alert-warning rounded-4 d-flex align-items-center gap-2 shadow-sm border-0 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <div>{{ session('mail_error') }}</div>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
                                <div class="fw-bold small mb-1">Por favor verifica los campos:</div>
                                <ul class="mb-0 ps-3 small">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Formulario de Contacto (Paso 6.5) -->
                        <form action="{{ route('contact.send') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark">Nombre</label>
                                    <input type="text" name="name" class="form-control rounded-3 py-2" placeholder="Nombre" value="{{ old('name') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark">Correo electrónico</label>
                                    <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="Correo electrónico" value="{{ old('email') }}" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">Mensaje</label>
                                    <textarea name="message" class="form-control rounded-3 py-2" rows="4" placeholder="Mensaje" required>{{ old('message') }}</textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-send-fill"></i> Enviar mensaje
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-5 text-center text-muted small">
        <div class="container">
            <p class="mb-1"><strong>{{ config('app.name', 'Cow App') }}</strong> • Seguridad en Aplicaciones (MEDIT)</p>
            <p class="mb-0">Laravel + PHP + MySQL + Blade + Storage + SMTP</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
