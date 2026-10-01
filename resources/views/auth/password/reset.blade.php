<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva contraseña | CowApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{min-height:100vh;display:grid;place-items:center;padding:1rem;background:radial-gradient(circle at 10% 10%,#dcfce7,transparent 42%),#f1f5f2;font-family:'Plus Jakarta Sans',system-ui,sans-serif}.auth-card{width:min(100%,480px);background:#fff;border:1px solid #e2e8f0;border-radius:22px;padding:clamp(1.5rem,5vw,2.5rem);box-shadow:0 22px 48px -24px #064e3b55}.brand-icon{height:48px;width:48px;display:grid;place-items:center;border-radius:14px;background:linear-gradient(135deg,#10b981,#047857);color:white;font-size:1.4rem}.form-control{min-height:48px}</style>
    @include('partials.cowapp-stitch-theme')
</head>
<body><main class="auth-card"><div class="d-flex align-items-center gap-3 mb-4"><span class="brand-icon"><i class="bi bi-key"></i></span><div><div class="fw-bold text-dark fs-5">CowApp</div><div class="small text-secondary">Protege tu cuenta</div></div></div>
    <p class="text-success text-uppercase small fw-bold mb-1">Paso 3 de 3</p><h1 class="h3 fw-bold text-dark">Crea una nueva contraseña</h1><p class="text-secondary mb-4">La contraseña debe tener al menos 8 caracteres.</p>
    @if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
    <form method="POST" action="{{ route('password.reset') }}">@csrf<label for="password" class="form-label fw-semibold">Nueva contraseña</label><input type="password" id="password" name="password" class="form-control mb-3 @error('password') is-invalid @enderror" minlength="8" autocomplete="new-password" required autofocus><label for="password_confirmation" class="form-label fw-semibold">Confirma la contraseña</label><input type="password" id="password_confirmation" name="password_confirmation" class="form-control mb-4" minlength="8" autocomplete="new-password" required><button type="submit" class="btn btn-success w-100 py-2 fw-semibold">Guardar nueva contraseña</button></form>
</main></body></html>
