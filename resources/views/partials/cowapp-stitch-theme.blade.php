<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root,
    [data-bs-theme="light"] {
        --cow-primary-earth: #5A4507;
        --cow-green-dark: #5A4507;
        --cow-green-primary: #0C820C;
        --cow-green-light: #0FA50F;
        --cow-green-subtle: #E6F4E6;
        --cow-green-border: #B6DAB6;
        --cow-green: #0C820C;
        --cow-light: #E6F4E6;
        --cow-border: #E2E4E8;
        --cow-ink: #020219;
        --cow-muted: #6B7280;
        --cow-amber-dark: #8A4700;
        --cow-amber-primary: #D37211;
        --cow-amber-light: #FFF0DD;
        --cow-slate-900: #020219;
        --cow-slate-800: #181932;
        --cow-slate-700: #34354D;
        --cow-slate-600: #4C4639;
        --cow-slate-500: #6B7280;
        --cow-slate-100: #E2E4E8;
        --cow-slate-50: #F1F1F1;
        --cow-surface: #FFFFFF;
        --cow-shadow-sm: 0 1px 3px rgba(2, 2, 25, .04), 0 1px 2px rgba(2, 2, 25, .02);
        --cow-shadow-md: 0 4px 12px -2px rgba(2, 2, 25, .08), 0 2px 6px -1px rgba(2, 2, 25, .04);
        --bs-primary-rgb: 12, 130, 12;
        --bs-secondary-rgb: 76, 70, 57;
        --bs-success-rgb: 12, 130, 12;
        --bs-warning-rgb: 211, 114, 17;
        --bs-dark-rgb: 2, 2, 25;
        --bs-body-bg: #F1F1F1;
        --bs-body-color: #020219;
        --bs-border-color: #E2E4E8;
        --bs-success-bg-subtle: #E6F4E6;
        --bs-success-border-subtle: #B6DAB6;
        --bs-success-text-emphasis: #0C820C;
        --bs-primary-bg-subtle: #F4EFD8;
        --bs-primary-border-subtle: #D9CCA0;
        --bs-primary-text-emphasis: #5A4507;
        --bs-warning-bg-subtle: #FFF0DD;
        --bs-warning-border-subtle: #F2C99E;
        --bs-warning-text-emphasis: #8A4700;
        --bs-info-rgb: 90, 69, 7;
    }

    body {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--cow-slate-900);
        background-color: #F1F1F1;
    }

    .btn-primary,
    .btn-success {
        --bs-btn-color: #fff;
        --bs-btn-bg: #0C820C;
        --bs-btn-border-color: #0C820C;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #0FA50F;
        --bs-btn-hover-border-color: #0FA50F;
        --bs-btn-active-bg: #086B08;
        --bs-btn-active-border-color: #086B08;
        --bs-btn-focus-shadow-rgb: 12, 130, 12;
    }

    .btn-outline-primary,
    .btn-outline-success {
        --bs-btn-color: #0C820C;
        --bs-btn-border-color: #0C820C;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #0C820C;
        --bs-btn-hover-border-color: #0C820C;
        --bs-btn-active-bg: #086B08;
        --bs-btn-active-border-color: #086B08;
        --bs-btn-focus-shadow-rgb: 12, 130, 12;
    }

    .cow-olive-surface { background-color: #5A4507 !important; color: #fff !important; }
    .cow-amber-surface { background-color: #D37211 !important; color: #fff !important; }
    .cow-card-surface { background: #fff; border: 1px solid #E2E4E8; box-shadow: var(--cow-shadow-sm); border-radius: 16px; }
    .cow-tabular { font-variant-numeric: tabular-nums; }
    .cow-hero,
    .cow-hero-banner,
    .cow-hero-sidebar,
    .cow-hero-panel { background: linear-gradient(135deg, #5A4507 0%, #0C820C 100%) !important; }
    .cow-submit-btn,
    .btn-cow { background: #0C820C !important; border-color: #0C820C !important; color: #fff !important; }
    .cow-submit-btn:hover,
    .btn-cow:hover { background: #0FA50F !important; border-color: #0FA50F !important; color: #fff !important; }
    .cow-logo,
    .brand-icon,
    .cow-logo-icon,
    .cow-logo-badge { background: linear-gradient(135deg, #5A4507 0%, #0C820C 100%) !important; }
    .cow-progress > span { background: linear-gradient(90deg, #0C820C, #0FA50F) !important; }
    .cow-status.status-active,
    .badge-active { background: #E6F4E6 !important; color: #0C820C !important; }
    .cow-status.status-sold,
    .badge-sold { background: #F4EFD8 !important; color: #5A4507 !important; }
    .cow-status.status-inactive,
    .badge-inactive { background: #E2E4E8 !important; color: #34354D !important; }
    .auth-card { box-shadow: var(--cow-shadow-md) !important; border-color: #E2E4E8 !important; }
    .cow-kpi-card,
    .cow-card,
    .contact-box,
    .media-card,
    .news-card { border-color: #E2E4E8 !important; box-shadow: var(--cow-shadow-sm); }
    .cow-navbar { border-color: #E2E4E8 !important; }
    .form-control:focus,
    .form-select:focus { border-color: #0C820C; box-shadow: 0 0 0 .2rem rgba(12, 130, 12, .18); }
</style>
