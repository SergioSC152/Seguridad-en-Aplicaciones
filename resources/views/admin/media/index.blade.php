<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca Multimedia | Panel de Administración</title>

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
            background-color: #f4f7f4;
            min-height: 100vh;
        }
        .cow-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .media-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .media-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.12);
            border-color: #cbd5e1;
        }
        .media-card-img-wrapper {
            height: 190px;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #f1f5f9;
        }
        .media-card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .media-card:hover .media-card-img-wrapper img {
            transform: scale(1.05);
        }
        .media-card-body {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg cow-navbar sticky-top py-2">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <div class="bg-success text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-images fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold text-dark fs-5">Biblioteca Multimedia</span>
                    <span class="badge bg-success-subtle text-success ms-1">Módulo Seguro</span>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Panel General
                </a>
                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                    <i class="bi bi-newspaper me-1"></i> Módulo Noticias
                </a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container-fluid px-lg-5 py-4">

        <!-- Mensajes Flash -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Se encontraron errores de validación:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            
            <!-- Columna Izquierda: Formulario de Carga Segura (Paso 5.5) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 80px;">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-cloud-arrow-up-fill text-success"></i> Subir Nuevo Archivo
                    </h5>
                    <p class="text-muted small mb-4">
                        Valida tipo de archivo en servidor (JPG, JPEG, PNG, WEBP, GIF) y tamaño máximo de 5MB.
                    </p>

                    <!-- Formulario de Carga (Paso 5.5) -->
                    <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Nombre del archivo</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Ej: Foto Lote Brahman" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Archivo (Imagen)</label>
                            <input type="file" name="file" class="form-control rounded-3" accept="image/*" required>
                            <div class="form-text text-muted small">
                                Formatos: JPG, PNG, WEBP, GIF (Máx. 5 MB)
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-upload"></i> Subir archivo
                        </button>
                    </form>

                    <!-- Caja informativa de seguridad -->
                    <div class="mt-4 p-3 bg-light rounded-3 small">
                        <div class="fw-bold text-success mb-1">
                            <i class="bi bi-shield-lock-fill"></i> Control de Seguridad
                        </div>
                        <p class="text-muted mb-0" style="font-size: 0.78rem;">
                            El archivo se guarda en <code>storage/app/public/media</code> con un nombre hash aleatorio para evitar colisiones y ejecución maliciosa, y se expone de forma controlada mediante <code>Storage::url()</code>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Galería y Registro de Metadatos (Pasos 5.8, 5.9, 5.10) -->
            <div class="col-lg-8">
                
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-grid-fill text-success me-2"></i> Galería Multimedia del Dashboard
                    </h5>
                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                        {{ $media->total() }} Recursos en Storage
                    </span>
                </div>

                @if($media->isEmpty())
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <div class="text-muted mb-3">
                            <i class="bi bi-images" style="font-size: 3.5rem;"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No hay archivos multimedia subidos</h5>
                        <p class="text-muted small">Utiliza el formulario de la izquierda para cargar tu primera imagen de forma segura.</p>
                    </div>
                @else
                    <!-- Galería del Dashboard (Paso 5.10) -->
                    <div class="row g-3 mb-4">
                        @foreach($media as $item)
                            <div class="col-md-6 col-xl-4">
                                <article class="media-card">
                                    <div class="media-card-img-wrapper">
                                        <!-- Visualización de imagen (Paso 5.9) -->
                                        <img src="{{ Storage::url($item->path) }}" alt="{{ $item->name }}" loading="lazy">
                                    </div>
                                    <div class="media-card-body">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $item->name }}">{{ $item->name }}</h6>
                                        <p class="text-muted small mb-2">
                                            <span class="badge bg-light text-dark border">{{ $item->mime_type ?? 'image/jpeg' }}</span>
                                            <span class="badge bg-light text-dark border">{{ number_format(($item->size ?? 0) / 1024, 1) }} KB</span>
                                        </p>
                                        <div class="small text-muted mb-3 text-truncate" style="font-size: 0.75rem;" title="{{ $item->path }}">
                                            <code>{{ $item->path }}</code>
                                        </div>

                                        <div class="mt-auto d-flex align-items-center gap-1">
                                            <a href="{{ Storage::url($item->path) }}" target="_blank" class="btn btn-sm btn-outline-secondary flex-grow-1 rounded-3" style="font-size: 0.78rem;">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Ver archivo
                                            </a>
                                            <!-- Botón Modal Reemplazar (Paso 5.13) -->
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#replaceModal{{ $item->id }}" title="Reemplazar archivo">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <!-- Botón Eliminar -->
                                            <form action="{{ route('admin.media.destroy', $item) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('¿Desea eliminar este archivo y su registro físico?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar archivo">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>

                                <!-- Modal Reemplazar Archivo (Paso 5.13) -->
                                <div class="modal fade" id="replaceModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <form action="{{ route('admin.media.update', $item) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-arrow-repeat text-primary me-1"></i> Reemplazar Archivo Multimedia #{{ $item->id }}
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center mb-3">
                                                        <img src="{{ Storage::url($item->path) }}" class="rounded-3 border" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                                        <div class="small text-muted mt-1">Archivo actual</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Nombre del recurso</label>
                                                        <input type="text" name="name" class="form-control rounded-3" value="{{ $item->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Nuevo archivo (Opcional para sustitución física)</label>
                                                        <input type="file" name="file" class="form-control rounded-3" accept="image/*">
                                                        <div class="form-text small">Al seleccionar un nuevo archivo, el anterior se eliminará del disco para evitar residuos.</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-3">Guardar y Reemplazar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mb-4">
                        {{ $media->links() }}
                    </div>

                    <!-- Tabla de Metadatos (Paso 5.8 / Evidencia 5) -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-table text-success me-2"></i> Tabla de Metadatos en Base de Datos (<code>media</code>)
                            </h6>
                            <small class="text-muted">Registro persistente de rutas, MIME y tamaño en bytes</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>id</th>
                                        <th>name</th>
                                        <th>path</th>
                                        <th>mime_type</th>
                                        <th>size (bytes)</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($media as $m)
                                        <tr>
                                            <td class="fw-bold">{{ $m->id }}</td>
                                            <td>{{ $m->name }}</td>
                                            <td><code>{{ $m->path }}</code></td>
                                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $m->mime_type }}</span></td>
                                            <td>{{ number_format($m->size ?? 0) }} B</td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success">Activo</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                @endif

            </div>

        </div>

    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
