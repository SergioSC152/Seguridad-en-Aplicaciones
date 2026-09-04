<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>

    <!-- Vite Assets (Guía Sección 7.5 y 8.7) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap CSS v5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-lock-fill text-primary"></i>
                Panel Principal
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold small text-dark">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="text-muted small" style="font-size: 0.8rem;">{{ auth()->user()->email ?? '' }}</div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1">¡Bienvenido, {{ auth()->user()->name ?? 'Usuario' }}!</h3>
                                <p class="text-muted mb-0">Has iniciado sesión satisfactoriamente en el sistema.</p>
                            </div>
                        </div>

                        <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-shield-check fs-4"></i>
                            <div>
                                <strong>Ruta protegida:</strong> Esta vista se encuentra resguardada por el middleware de autenticación <code>auth</code>.
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <h5 class="fw-semibold mb-3">Información de la Cuenta</h5>
                            <ul class="list-group list-group-flush rounded-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Nombre:</span>
                                    <span class="fw-medium text-dark">{{ auth()->user()->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Correo electrónico:</span>
                                    <span class="fw-medium text-dark">{{ auth()->user()->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Fecha de registro:</span>
                                    <span class="fw-medium text-dark">{{ auth()->user()->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
