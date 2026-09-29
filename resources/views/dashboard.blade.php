<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel CRM Ganadero | Cow App</title>

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
            --cow-slate-800: #1e293b;
            --cow-slate-700: #334155;
            --cow-slate-600: #475569;
            --cow-slate-100: #f1f5f9;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--cow-slate-700);
            background-color: #f4f7f4;
            min-height: 100vh;
            margin: 0;
        }

        .cow-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .cow-logo-badge {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(4, 120, 87, 0.3);
        }

        .cow-hero-banner {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            border-radius: 20px;
            color: #ffffff;
            padding: 2.25rem 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.25);
        }

        .cow-hero-banner::after {
            content: "";
            position: absolute;
            right: -20px;
            bottom: -40px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .cow-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem 1.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            height: 100%;
        }

        .cow-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px -4px rgba(6, 78, 59, 0.1);
            border-color: #cbd5e1;
        }

        .cow-kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .cow-action-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            font-weight: 600;
            color: var(--cow-slate-800);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
            width: 100%;
        }

        .cow-action-btn:hover {
            background-color: var(--cow-green-subtle);
            border-color: var(--cow-green-light);
            color: var(--cow-green-dark);
            transform: translateY(-2px);
        }

        .cow-table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            padding: 0.85rem 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .cow-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .cow-badge-available {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
        }

        .cow-badge-deal {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
        }

        .cow-badge-auction {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
        }
    </style>
</head>
<body>

    <!-- Barra de Navegación Cow App CRM -->
    <nav class="navbar navbar-expand-lg cow-navbar sticky-top py-2">
        <div class="container-fluid px-lg-5">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <div class="cow-logo-badge">
                    <i class="bi bi-tag-fill"></i>
                </div>
                <div>
                    <span class="fw-bold text-dark fs-5" style="letter-spacing: -0.5px;">Cow App</span>
                    <span class="badge bg-success-subtle text-success fw-bold ms-1" style="font-size: 0.68rem; letter-spacing: 0.4px;">CRM GANADERO</span>
                </div>
            </a>

            <!-- Toggle mobile -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#cowNavbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu items -->
            <div class="collapse navbar-collapse" id="cowNavbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold text-success" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-fill me-1"></i> Panel General
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ route('admin.media.index') }}">
                            <i class="bi bi-images me-1"></i> Biblioteca Multimedia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ route('admin.news.index') }}">
                            <i class="bi bi-newspaper me-1"></i> Módulo Noticias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ url('/') }}" target="_blank">
                            <i class="bi bi-globe me-1"></i> Sitio Público & Contacto
                        </a>
                    </li>
                </ul>

                <!-- Usuario y Cierre de Sesión -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <div class="d-flex align-items-center gap-2 text-end">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.95rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="d-none d-sm-block text-start">
                            <div class="fw-bold small text-dark leading-tight">{{ auth()->user()->name ?? 'Usuario Ganadero' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Productor / Administrador</div>
                        </div>
                    </div>

                    <!-- Botón Logout Formal con Formulario y CSRF -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 rounded-3 px-3 py-1 fw-semibold" title="Cerrar sesión segura">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-none d-md-inline">Salir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal del CRM Ganadero -->
    <main class="container-fluid px-lg-5 py-4">

        <!-- Banner de Bienvenida -->
        <div class="cow-hero-banner mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-20 rounded-pill px-3 py-1 small fw-semibold mb-2">
                        <i class="bi bi-circle-fill text-warning" style="font-size: 0.6rem;"></i> Sistema CRM Activo & Sincronizado
                    </div>
                    <h2 class="fw-bold mb-2">¡Bienvenido a Cow App, {{ auth()->user()->name ?? 'Ganadero' }}!</h2>
                    <p class="text-white-50 mb-0">
                        Control integral de lotes bovinos, inventario de cabezas, pesajes y comercialización directa con compradores certificados.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <span class="badge bg-white text-dark p-2 px-3 rounded-3 shadow-sm">
                        <i class="bi bi-calendar-event text-success me-1"></i> {{ date('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Fila de Métricas / KPIs del CRM Ganadero -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="cow-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold">TOTAL CABEZAS</span>
                        <div class="cow-kpi-icon bg-success-subtle text-success">
                            <i class="bi bi-tag-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">348</h3>
                    <div class="small text-success fw-semibold">
                        <i class="bi bi-arrow-up-right"></i> +24 este mes (Brahman & Angus)
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="cow-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold">LOTES EN VENTA</span>
                        <div class="cow-kpi-icon bg-warning-subtle text-warning">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">14</h3>
                    <div class="small text-muted fw-semibold">
                        4 en subasta activa | 10 venta directa
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="cow-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold">FACTURACIÓN MENSUAL</span>
                        <div class="cow-kpi-icon bg-primary-subtle text-primary">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">$194,800</h3>
                    <div class="small text-success fw-semibold">
                        <i class="bi bi-graph-up-arrow"></i> +18.2% vs periodo anterior
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="cow-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold">CLIENTES COMPRADORES</span>
                        <div class="cow-kpi-icon bg-info-subtle text-info">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">52</h3>
                    <div class="small text-muted fw-semibold">
                        8 negociaciones abiertas
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila de Acciones Rápidas del CRM y Módulos -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <a href="{{ route('admin.media.index') }}" class="cow-action-btn">
                    <div class="bg-success text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-images fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">Biblioteca Multimedia</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Carga segura de archivos</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.news.index') }}" class="cow-action-btn">
                    <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-newspaper fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">Módulo de Noticias</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Publicar y asociar medios</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('/') }}#contacto" target="_blank" class="cow-action-btn">
                    <div class="bg-warning text-dark rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-envelope-check-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">Formulario Contacto</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Envío SMTP y Mailable</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('/') }}" target="_blank" class="cow-action-btn">
                    <div class="bg-info text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-globe fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">Ver Sitio Web Público</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Noticias e imágenes en vivo</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Sección de Dos Columnas: Catálogo de Lotes & Ficha de Cuenta -->
        <div class="row g-4">
            
            <!-- Columna Izquierda: Lotes de Ganado Activos -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Lotes Bovinos en Comercialización</h5>
                            <small class="text-muted">Inventario activo disponible para venta y subasta</small>
                        </div>
                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">
                            14 Lotes Registrados
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table cow-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Identificador / Raza</th>
                                    <th>Cabezas</th>
                                    <th>Peso Promedio</th>
                                    <th>Precio Sugerido</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-success text-white p-2 rounded-3 small fw-bold">
                                                #LOT-101
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">Brahman Rojo Puro</div>
                                                <small class="text-muted">Hacienda El Roble • Machos ceba</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">28</td>
                                    <td>465 kg / animal</td>
                                    <td class="fw-bold text-success">$3.85 / kg</td>
                                    <td>
                                        <span class="cow-badge-available">Disponible</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3">Ver Ficha</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-success text-white p-2 rounded-3 small fw-bold">
                                                #LOT-102
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">Black Angus Certificado</div>
                                                <small class="text-muted">Genética de Exportación</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">16</td>
                                    <td>520 kg / animal</td>
                                    <td class="fw-bold text-success">$4.30 / kg</td>
                                    <td>
                                        <span class="cow-badge-deal">En Negociación</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3">Ver Ficha</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-success text-white p-2 rounded-3 small fw-bold">
                                                #LOT-103
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">Gyr Lechero Mestizo</div>
                                                <small class="text-muted">Hembras primerizas preñadas</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">12</td>
                                    <td>410 kg / animal</td>
                                    <td class="fw-bold text-success">$3.95 / kg</td>
                                    <td>
                                        <span class="cow-badge-auction">Subasta Abierta</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3">Ver Ficha</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-success text-white p-2 rounded-3 small fw-bold">
                                                #LOT-104
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">Nelore Blanco de Cría</div>
                                                <small class="text-muted">Lote rústico adaptado</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">35</td>
                                    <td>380 kg / animal</td>
                                    <td class="fw-bold text-success">$3.50 / kg</td>
                                    <td>
                                        <span class="cow-badge-available">Disponible</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3">Ver Ficha</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Información de la Cuenta y Seguridad Laravel -->
            <div class="col-xl-4">
                
                <!-- Tarjeta de Perfil Ganadero -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge-fill text-success fs-5"></i> Datos de la Ganadería / Usuario
                    </h6>

                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px; font-size: 1.25rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ auth()->user()->name }}</h6>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                            <div>
                                <span class="badge bg-success-subtle text-success fw-semibold" style="font-size: 0.7rem;">
                                    Productor Ganadero Verificado
                                </span>
                            </div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0 py-2 border-light-subtle">
                            <span class="text-muted">Fecha de ingreso:</span>
                            <span class="fw-semibold text-dark">{{ auth()->user()->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-2 border-light-subtle">
                            <span class="text-muted">Estado de cuenta:</span>
                            <span class="badge bg-success text-white fw-semibold">Activo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-2 border-light-subtle">
                            <span class="text-muted">Nivel de CRM:</span>
                            <span class="fw-semibold text-dark">Cow App Pro Ganadero</span>
                        </li>
                    </ul>
                </div>

                <!-- Tarjeta de Protección y Seguridad -->
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color: #ecfdf5; border: 1px solid #a7f3d0 !important;">
                    <div class="d-flex align-items-center gap-2 mb-2 text-success">
                        <i class="bi bi-shield-check fs-4"></i>
                        <h6 class="fw-bold mb-0">Sesión Segura y Protegida</h6>
                    </div>
                    <p class="small text-muted mb-0" style="font-size: 0.82rem; line-height: 1.45;">
                        Esta vista se encuentra resguardada bajo autenticación <code>auth</code> de Laravel. Tus datos de inventario ganadero y ventas están protegidos contra accesos no autorizados.
                    </p>
                </div>

            </div>

        </div>

    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
