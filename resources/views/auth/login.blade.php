<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | Cow App - CRM Ganadero</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS v5.3.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --cow-green-dark: #064e3b;
            --cow-green-primary: #047857;
            --cow-green-light: #10b981;
            --cow-green-subtle: #ecfdf5;
            --cow-green-border: #a7f3d0;
            --cow-amber: #d97706;
            --cow-slate-900: #0f172a;
            --cow-slate-700: #334155;
            --cow-slate-600: #475569;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--cow-slate-700);
            background-color: #f1f5f2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
                radial-gradient(circle at 10% 15%, rgba(4, 120, 87, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 90% 85%, rgba(217, 119, 6, 0.07) 0%, transparent 45%),
                #f1f5f2;
            padding: 1.5rem 0.75rem;
            margin: 0;
        }

        .cow-auth-card {
            width: 100%;
            max-width: 980px;
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 25px 50px -12px rgba(6, 78, 59, 0.15), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .cow-hero-sidebar {
            background: linear-gradient(150deg, #064e3b 0%, #032b21 100%);
            color: #ffffff;
            position: relative;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .cow-hero-sidebar::before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .cow-hero-sidebar::after {
            content: "";
            position: absolute;
            bottom: -70px;
            left: -50px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.25) 0%, transparent 70%);
            border-radius: 50%;
        }

        .cow-brand-header {
            position: relative;
            z-index: 2;
        }

        .cow-logo-badge {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #ffffff;
            box-shadow: 0 8px 16px rgba(4, 120, 87, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .cow-feature-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 1.1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .cow-feature-card:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateX(4px);
        }

        .cow-feature-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(16, 185, 129, 0.25);
            color: #34d399;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .cow-form-panel {
            padding: 3.25rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cow-input-wrapper {
            position: relative;
        }

        .cow-input-icon {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.15rem;
            pointer-events: none;
            z-index: 5;
            transition: color 0.2s;
        }

        .cow-form-input {
            width: 100%;
            padding: 0.82rem 1.1rem 0.82rem 3rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: #ffffff;
            color: var(--cow-slate-900);
        }

        .cow-form-input:focus {
            border-color: var(--cow-green-primary);
            box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.15);
            outline: none;
        }

        .cow-form-input:focus + .cow-input-icon,
        .cow-input-wrapper:focus-within .cow-input-icon {
            color: var(--cow-green-primary);
        }

        .cow-btn-toggle-pwd {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            transition: color 0.2s;
            z-index: 6;
        }

        .cow-btn-toggle-pwd:hover {
            color: var(--cow-green-primary);
        }

        .cow-submit-btn {
            background: linear-gradient(135deg, var(--cow-green-primary) 0%, var(--cow-green-dark) 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0.9rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.2px;
            box-shadow: 0 6px 18px rgba(4, 120, 87, 0.28);
            transition: all 0.25s ease;
        }

        .cow-submit-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #064e3b 100%);
            box-shadow: 0 8px 24px rgba(4, 120, 87, 0.38);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .cow-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background-color: var(--cow-green-subtle);
            color: var(--cow-green-dark);
            border: 1px solid var(--cow-green-border);
            border-radius: 9999px;
            padding: 0.3rem 0.85rem;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        @media (max-width: 991.98px) {
            .cow-hero-sidebar {
                display: none;
            }
            .cow-form-panel {
                padding: 2.25rem 1.75rem;
            }
        }
    </style>
    @include('partials.cowapp-stitch-theme')
</head>
<body>

    <div class="cow-auth-card">
        <div class="row g-0">
            <!-- Lado Izquierdo: Hero Cow App CRM Ganadero -->
            <div class="col-lg-5 cow-hero-sidebar">
                <div class="cow-brand-header">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @include('partials.cowapp-logo', ['logoSize' => 88])
                        <div>
                            <h3 class="fw-bold text-white mb-0" style="letter-spacing: -0.5px;">Cow App</h3>
                            <span class="badge bg-white text-success fw-bold px-2 py-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">CRM GANADERO</span>
                        </div>
                    </div>

                    <h4 class="fw-bold text-white mb-3" style="line-height: 1.35;">
                        Control total de comercialización y venta de ganado
                    </h4>
                    <p class="text-white-50 small mb-4">
                        Plataforma inteligente para la administración de lotes, pesaje, trazabilidad de razas y acuerdos comerciales en el sector agropecuario.
                    </p>
                </div>

                <!-- Features de la plataforma -->
                <div class="my-auto position-relative" style="z-index: 2;">
                    <div class="cow-feature-card d-flex align-items-center gap-3">
                        <div class="cow-feature-icon">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-white small">Gestión de Lotes & Razas</div>
                            <div class="text-white-50" style="font-size: 0.78rem;">Angus, Brahman, Holstein, Nelore y más</div>
                        </div>
                    </div>

                    <div class="cow-feature-card d-flex align-items-center gap-3">
                        <div class="cow-feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-white small">Monitoreo de Precios y Subastas</div>
                            <div class="text-white-50" style="font-size: 0.78rem;">Cotización por kilogramo en pie y canal</div>
                        </div>
                    </div>

                    <div class="cow-feature-card d-flex align-items-center gap-3">
                        <div class="cow-feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-white small">Trazabilidad y Certificados</div>
                            <div class="text-white-50" style="font-size: 0.78rem;">Control sanitario y guías de movilización</div>
                        </div>
                    </div>
                </div>

                <!-- Footer del Sidebar -->
                <div class="position-relative pt-3 border-top border-white border-opacity-10" style="z-index: 2;">
                    <div class="d-flex justify-content-between align-items-center text-white-50 small">
                        <span><i class="bi bi-patch-check-fill text-warning me-1"></i> Versión 1.0 CRM</span>
                        <span>AgroTech Solutions</span>
                    </div>
                </div>
            </div>

            <!-- Lado Derecho: Formulario de Login -->
            <div class="col-lg-7 cow-form-panel">
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="cow-tag">
                            <i class="bi bi-shield-lock-fill"></i> Acceso Seguro
                        </div>
                        <div class="d-lg-none d-flex align-items-center gap-2">
                            <span class="fw-bold text-success fs-5">Cow App</span>
                        </div>
                    </div>
                    <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Iniciar sesión</h2>
                    <p class="text-muted small mb-0">
                        Ingresa tus credenciales de productor o comerciante para acceder al CRM.
                    </p>
                </div>

                {{-- Mensaje de éxito tras registro u otra acción --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 py-3 px-3 d-flex align-items-center gap-2 mb-4" role="alert" style="background-color: #d1fae5; color: #065f46;">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                        <div class="small fw-semibold flex-grow-1">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                {{-- Mensajes de error de validación o credenciales --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 py-3 px-3 mb-4" role="alert" style="background-color: #fee2e2; color: #991b1b;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                            <strong class="small">No fue posible ingresar al sistema:</strong>
                        </div>
                        <ul class="mb-0 ps-4 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <!-- Campo: Correo Electrónico -->
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-dark mb-1">
                            Correo electrónico institucional / comercial
                        </label>
                        <div class="cow-input-wrapper">
                            <i class="bi bi-envelope cow-input-icon"></i>
                            <input
                                type="email"
                                class="cow-form-input @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="ganadero@tuempresa.com"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Campo: Contraseña -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label small fw-bold text-dark mb-0">
                                Contraseña
                            </label>
                        </div>
                        <div class="cow-input-wrapper">
                            <i class="bi bi-lock cow-input-icon"></i>
                            <input
                                type="password"
                                class="cow-form-input @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                placeholder="••••••••••••"
                                required
                            >
                            <button class="cow-btn-toggle-pwd" type="button" id="togglePassword" aria-label="Mostrar contraseña" aria-controls="password" aria-pressed="false" title="Mostrar contraseña">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Recordar Sesión -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} style="cursor: pointer;">
                            <label class="form-check-label small text-muted user-select-none" for="remember" style="cursor: pointer;">
                                Mantener sesión iniciada
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="small text-decoration-none fw-semibold" style="color: var(--cow-green-primary);">¿Olvidaste tu contraseña?</a>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn cow-submit-btn d-flex align-items-center justify-content-center gap-2">
                            <span>Ingresar al Panel Cow App</span>
                            <i class="bi bi-arrow-right-circle-fill"></i>
                        </button>
                    </div>
                </form>

                <!-- Pie del Formulario: Enlace a Registro -->
                <div class="text-center pt-3 border-top border-light-subtle">
                    <p class="small text-muted mb-0">
                        ¿Aún no tienes cuenta ganadera? 
                        <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: var(--cow-green-primary);">
                            Regístrate aquí <i class="bi bi-arrow-up-right small"></i>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
