<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta</t
    itle>

    <!-- Bootstrap CSS v5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding: 1.5rem 0;
        }
        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            padding: 2.25rem;
        }
        .brand-title {
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .btn-toggle-password {
            cursor: pointer;
        }
    </style>
    @include('partials.cowapp-stitch-theme')
</head>
<body>
    <div class="auth-card">
        <div class="text-start mb-4">
            <h4 class="brand-title mb-1">Crear cuenta</h4>
            <p class="text-muted small mb-0">Complete los siguientes datos para registrarse en el sistema.</p>
        </div>

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Por favor verifique los siguientes campos:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label small fw-semibold text-dark">Nombre completo</label>
                <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Ej. Juan Pérez"
                    required
                    autofocus
                >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-dark">Correo electrónico</label>
                <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="ejemplo@dominio.com"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold text-dark">Contraseña</label>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Mínimo 8 caracteres"
                        required
                    >
                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" id="toggleRegPasswordBtn">
                        Mostrar
                    </button>
                </div>
                <div class="form-text text-muted small">
                    Debe incluir al menos 8 caracteres.
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label small fw-semibold text-dark">Confirmar contraseña</label>
                <input
                    type="password"
                    class="form-control"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Repita su contraseña"
                    required
                >
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary fw-medium py-2">
                    Registrarse
                </button>
            </div>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <p class="small text-muted mb-0">
                ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Inicia sesión</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleRegBtn = document.getElementById('toggleRegPasswordBtn');
        const regPasswordInput = document.getElementById('password');

        if (toggleRegBtn && regPasswordInput) {
            toggleRegBtn.addEventListener('click', function () {
                const isPassword = regPasswordInput.getAttribute('type') === 'password';
                regPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleRegBtn.textContent = isPassword ? 'Ocultar' : 'Mostrar';
            });
        }
    </script>
</body>
</html>
