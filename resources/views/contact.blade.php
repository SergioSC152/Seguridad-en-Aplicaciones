<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacto | {{ config('app.name', 'Laravel') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS v5.3.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #334155;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .contact-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
    </style>
    @include('partials.cowapp-stitch-theme')
</head>
<body>

    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="{{ url('/') }}">
                <div class="bg-success text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
                <span>{{ config('app.name', 'Cow App') }}</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-house me-1"></i> Inicio
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-success btn-sm rounded-pill px-3">
                        <i class="bi bi-grid me-1"></i> Panel CRM
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal del Formulario -->
    <main class="container my-auto py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="contact-card p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-envelope-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Formulario de Contacto</h3>
                        <p class="text-muted small">
                            Envíanos tu consulta. Validamos los datos en servidor y te notificamos vía correo SMTP profesional.
                        </p>
                    </div>

                    <!-- Mensajes de Estado -->
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

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Nombre completo</label>
                            <input type="text" name="name" class="form-control rounded-3 py-2" placeholder="Nombre" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Correo electrónico</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="Correo electrónico" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Mensaje</label>
                            <textarea name="message" class="form-control rounded-3 py-2" rows="5" placeholder="Mensaje" required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send-fill"></i> Enviar mensaje
                        </button>
                    </form>

                    <!-- Nota de Seguridad -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <small class="text-muted d-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-shield-lock-fill text-success"></i>
                            Protegido con Token CSRF y validación estricta en servidor.
                        </small>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
        <div class="container">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }} • Sistema Profesional de Correos & Multimedia
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
