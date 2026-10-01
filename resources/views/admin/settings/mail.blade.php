<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración de correo | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('partials.cowapp-stitch-theme')
</head>
<body>
<nav class="navbar cow-navbar bg-white border-bottom">
    <div class="container py-2">
        <a class="navbar-brand fw-bold text-decoration-none" href="{{ route('dashboard') }}" style="color:#5A4507"><i class="bi bi-envelope-gear me-2" style="color:#0C820C"></i>CowApp <span class="text-secondary fw-normal">/ Correo SMTP</span></a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-success rounded-3"><i class="bi bi-arrow-left me-1"></i>Panel</a>
    </div>
</nav>
<main class="container py-4 py-lg-5" style="max-width: 980px">
    <div class="mb-4">
        <span class="badge rounded-pill text-bg-success-subtle border border-success-subtle mb-2">SEGURIDAD · CORREO SALIENTE</span>
        <h1 class="h2 fw-bold mb-2">Configuración SMTP</h1>
        <p class="text-secondary mb-0">Administra el transporte de correo utilizado por CowApp. Los cambios se aplican a los correos que envíe la aplicación.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><div class="fw-semibold mb-1">Revisa los campos indicados:</div><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="cow-card-surface p-3 p-md-4">
                <h2 class="h5 fw-bold mb-1">Servidor de correo</h2>
                <p class="small text-secondary mb-4">Usa el host y las credenciales proporcionadas por tu proveedor SMTP.</p>
                <form method="POST" action="{{ route('admin.settings.mail.update') }}" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label fw-semibold" for="host">Servidor SMTP</label><input id="host" name="host" type="text" class="form-control @error('host') is-invalid @enderror" value="{{ old('host', $mailSettings?->host) }}" placeholder="smtp.ejemplo.com" autocomplete="off" required maxlength="255">@error('host')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-4"><label class="form-label fw-semibold" for="port">Puerto</label><input id="port" name="port" type="number" min="1" max="65535" class="form-control @error('port') is-invalid @enderror" value="{{ old('port', $mailSettings?->port ?? 587) }}" required>@error('port')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="username">Usuario SMTP</label><input id="username" name="username" type="text" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $mailSettings?->username) }}" autocomplete="off" maxlength="255">@error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="password">Contraseña SMTP</label><input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" value="" autocomplete="new-password" maxlength="1024" placeholder="{{ $passwordConfigured ? 'Configurada · déjala vacía para conservarla' : 'Introduce la contraseña SMTP' }}">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="encryption">Cifrado de transporte</label><select id="encryption" name="encryption" class="form-select @error('encryption') is-invalid @enderror" required><option value="tls" @selected(old('encryption', $mailSettings?->encryption ?? 'tls') === 'tls')>TLS (STARTTLS)</option><option value="ssl" @selected(old('encryption', $mailSettings?->encryption) === 'ssl')>SSL/TLS implícito</option></select>@error('encryption')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="from_address">Correo remitente</label><input id="from_address" name="from_address" type="email" class="form-control @error('from_address') is-invalid @enderror" value="{{ old('from_address', $mailSettings?->from_address) }}" autocomplete="email" maxlength="255" required>@error('from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12"><label class="form-label fw-semibold" for="from_name">Nombre remitente</label><input id="from_name" name="from_name" type="text" class="form-control @error('from_name') is-invalid @enderror" value="{{ old('from_name', $mailSettings?->from_name ?? config('app.name', 'CowApp')) }}" maxlength="255" required>@error('from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-4 pt-3 border-top">
                        <span class="small text-secondary"><i class="bi bi-lock-fill text-success me-1"></i>La contraseña se cifra en base de datos y nunca se vuelve a mostrar.</span>
                        <button type="submit" class="btn btn-success rounded-3 px-4"><i class="bi bi-shield-check me-1"></i>Guardar configuración</button>
                    </div>
                </form>
            </section>
        </div>
        <aside class="col-lg-4">
            <section class="cow-card-surface p-3 p-md-4 mb-3">
                <div class="d-flex align-items-center gap-2 mb-3"><span class="d-grid place-items-center rounded-circle bg-success-subtle text-success" style="width:40px;height:40px"><i class="bi bi-shield-lock"></i></span><h2 class="h6 fw-bold mb-0">Controles de seguridad</h2></div>
                <ul class="small text-secondary ps-3 mb-0">
                    <li class="mb-2">Acceso restringido en servidor a la cuenta configurada como administradora.</li>
                    <li class="mb-2">La aplicación falla de forma cerrada si no hay administrador configurado.</li>
                    <li class="mb-2">Protección CSRF, autorización Policy y límite de cambios por minuto.</li>
                    <li>El secreto cifrado depende de <code>APP_KEY</code>; conserva una copia segura de esa clave.</li>
                </ul>
            </section>
            <section class="cow-card-surface p-3 p-md-4">
                <h2 class="h6 fw-bold"><i class="bi bi-info-circle text-success me-2"></i>Estado</h2>
                @if($mailSettings)
                    <div class="d-flex justify-content-between align-items-center"><span class="small text-secondary">Configuración</span><span class="badge rounded-pill text-bg-success">Guardada</span></div>
                    <div class="d-flex justify-content-between align-items-center mt-2"><span class="small text-secondary">Contraseña</span><span class="badge rounded-pill {{ $passwordConfigured ? 'text-bg-success' : 'text-bg-warning' }}">{{ $passwordConfigured ? 'Cifrada' : 'Sin configurar' }}</span></div>
                    <div class="small text-secondary mt-3">{{ $mailSettings->host }}:{{ $mailSettings->port }}</div>
                @else
                    <p class="small text-secondary mb-0">Aún no existe una configuración SMTP guardada en CowApp.</p>
                @endif
            </section>
        </aside>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
