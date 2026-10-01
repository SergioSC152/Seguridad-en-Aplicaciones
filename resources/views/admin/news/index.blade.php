<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Módulo de Noticias y Publicaciones | Panel de Administración</title>

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
            background-color: #f4f7f4;
            min-height: 100vh;
        }
        .cow-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .news-thumb {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
    </style>
    @include('partials.cowapp-stitch-theme')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg cow-navbar sticky-top py-2">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-newspaper fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold text-dark fs-5">Módulo Noticias</span>
                    <span class="badge bg-primary-subtle text-primary ms-1">Reto B - Publicación</span>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Panel General
                </a>
                <a href="{{ route('admin.media.index') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                    <i class="bi bi-images me-1"></i> Multimedia
                </a>
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3">
                    <i class="bi bi-globe me-1"></i> Ver Sitio Público
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
            
            <!-- Formulario Nueva Publicación -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 80px;">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-primary"></i> Crear Noticia
                    </h5>
                    <p class="text-muted small mb-4">
                        Asocia una imagen existente de la biblioteca o sube un archivo nuevo que se registrará de forma segura.
                    </p>

                    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Título de la Noticia</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="Ej: Nueva subasta ganadera 2026" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Extracto / Resumen corto</label>
                            <input type="text" name="excerpt" class="form-control rounded-3" placeholder="Breve descripción para la portada" value="{{ old('excerpt') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Contenido de la Noticia</label>
                            <textarea name="content" class="form-control rounded-3" rows="4" placeholder="Escribe el artículo completo..." required>{{ old('content') }}</textarea>
                        </div>

                        <!-- Selección de Imagen Existente o Carga Nueva -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">
                                <i class="bi bi-link-45deg text-success"></i> Seleccionar de Multimedia existente (media_id)
                            </label>
                            <select name="media_id" class="form-select rounded-3">
                                <option value="">-- Sin imagen / Subir nueva abajo --</option>
                                @foreach($mediaList as $m)
                                    <option value="{{ $m->id }}" {{ old('media_id') == $m->id ? 'selected' : '' }}>
                                        #{{ $m->id }} - {{ $m->name }} ({{ $m->mime_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">O Subir Imagen Directamente</label>
                            <input type="file" name="file" class="form-control rounded-3" accept="image/*">
                            <div class="form-text small">JPG, PNG, WEBP, GIF (Máx. 5MB)</div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" name="published" value="1" id="publishedCheck" checked>
                            <label class="form-check-label small fw-semibold" for="publishedCheck">Publicar en el sitio web</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send-fill"></i> Guardar Publicación
                        </button>
                    </form>
                </div>
            </div>

            <!-- Listado de Noticias en el Panel -->
            <div class="col-lg-8">
                
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-collection-fill text-primary me-2"></i> Noticias Publicadas en el CMS
                    </h5>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                        {{ $news->total() }} Publicaciones
                    </span>
                </div>

                @if($news->isEmpty())
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <div class="text-muted mb-3">
                            <i class="bi bi-newspaper" style="font-size: 3.5rem;"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No hay publicaciones registradas</h5>
                        <p class="text-muted small">Crea tu primera noticia utilizando el formulario lateral.</p>
                    </div>
                @else
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Título y Contenido</th>
                                        <th>Multimedia Asociada</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($news as $item)
                                        <tr>
                                            <td>
                                                <!-- Visualización con Storage::url según paso 5.11 -->
                                                @if($item->media)
                                                    <img src="{{ Storage::url($item->media->path) }}" alt="{{ $item->title }}" class="news-thumb">
                                                @else
                                                    <div class="news-thumb bg-light d-flex align-items-center justify-content-center text-muted">
                                                        <i class="bi bi-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                                    {{ $item->excerpt ?? Str::limit($item->content, 60) }}
                                                </small>
                                                <small class="text-secondary" style="font-size: 0.72rem;">
                                                    Slug: <code>{{ $item->slug }}</code>
                                                </small>
                                            </td>
                                            <td>
                                                @if($item->media)
                                                    <span class="badge bg-success-subtle text-success">
                                                        #{{ $item->media->id }} - {{ Str::limit($item->media->name, 15) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border">Sin multimedia</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->published)
                                                    <span class="badge bg-success text-white">Publicada</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">Borrador</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <!-- Botón Editar / Reemplazar Imagen (Paso 5.13) -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#editNewsModal{{ $item->id }}" title="Editar Noticia">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <!-- Botón Eliminar -->
                                                <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('¿Desea eliminar esta publicación?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar Noticia">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Editar Noticia & Reemplazar Imagen (Paso 5.13) -->
                                        <div class="modal fade" id="editNewsModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <form action="{{ route('admin.news.update', $item) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">
                                                                <i class="bi bi-pencil-square text-primary me-1"></i> Editar Noticia #{{ $item->id }}
                                                            </h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold small">Título</label>
                                                                        <input type="text" name="title" class="form-control rounded-3" value="{{ $item->title }}" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold small">Extracto</label>
                                                                        <input type="text" name="excerpt" class="form-control rounded-3" value="{{ $item->excerpt }}">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold small">Contenido</label>
                                                                        <textarea name="content" class="form-control rounded-3" rows="4" required>{{ $item->content }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <!-- Vista previa actual -->
                                                                    <div class="p-3 bg-light rounded-3 text-center mb-3">
                                                                        <label class="form-label fw-semibold small d-block mb-2">Imagen Actual</label>
                                                                        @if($item->media)
                                                                            <img src="{{ Storage::url($item->media->path) }}" class="rounded-3 border" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                                                                            <div class="small text-muted mt-1">{{ $item->media->name }} (<code>{{ $item->media->path }}</code>)</div>
                                                                        @else
                                                                            <div class="text-muted small">No tiene imagen asociada actualmente.</div>
                                                                        @endif
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold small">Asociar otro recurso de la biblioteca</label>
                                                                        <select name="media_id" class="form-select rounded-3">
                                                                            <option value="">-- Conservar actual / Sin cambio --</option>
                                                                            @foreach($mediaList as $m)
                                                                                <option value="{{ $m->id }}" {{ $item->media_id == $m->id ? 'selected' : '' }}>
                                                                                    #{{ $m->id }} - {{ $m->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <!-- Reemplazar archivo físico (Paso 5.13) -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold small text-primary">
                                                                            <i class="bi bi-arrow-repeat"></i> Reemplazar con nuevo archivo
                                                                        </label>
                                                                        <input type="file" name="file" class="form-control rounded-3" accept="image/*">
                                                                        <div class="form-text small">
                                                                            Si seleccionas un archivo, el anterior se eliminará de Storage y se actualizarán los metadatos.
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-check form-switch mt-3">
                                                                        <input class="form-check-input" type="checkbox" role="switch" name="published" value="1" id="editPub{{ $item->id }}" {{ $item->published ? 'checked' : '' }}>
                                                                        <label class="form-check-label small fw-semibold" for="editPub{{ $item->id }}">Publicada</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill px-3">Guardar Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $news->links() }}
                    </div>
                @endif

            </div>

        </div>

    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
