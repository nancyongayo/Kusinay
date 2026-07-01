<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KusiNay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ── KusiNay Logo Palette ──────────────────────────────────────────
           Olive Green  : #6B7A3A  (logo border / badge bg)
           Warm Cream   : #F5EDD6  (logo inner circle)
           Burnt Orange : #C4722A  (pot & heart)
           Dark Olive   : #3D4A1E  (deep text / dark accents)
        ─────────────────────────────────────────────────────────────────── */
        :root {
            --kn-green  : #6B7A3A;
            --kn-green-d: #556030;
            --kn-orange : #C4722A;
            --kn-cream  : #F5EDD6;
            --kn-dark   : #3D4A1E;
            --kn-text   : #3d3d2e;
            --kn-muted  : rgba(61,74,30,0.55);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: #fde8d8;
            color: var(--kn-text);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Subtle decorative blobs */
        body::before {
            content: '';
            position: fixed;
            top: -120px; left: -120px;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(107,122,58,0.13) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -100px; right: -100px;
            width: 360px; height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(196,114,42,0.10) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Card ─────────────────────────────────────────────────────── */
        .page-card {
            background: rgba(255, 252, 245, 0.97);
            border: 1.5px solid rgba(107,122,58,0.18);
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(61,74,30,0.13), 0 2px 8px rgba(196,114,42,0.06);
        }

        /* ── Brand badge ──────────────────────────────────────────────── */
        .brand-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
        }
        .brand-logo .logo-circle {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: var(--kn-green);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(107,122,58,0.30);
            overflow: hidden;
        }
        .brand-logo .logo-circle img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .brand-logo .logo-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--kn-green);
            letter-spacing: -0.01em;
        }
        .brand-logo .logo-name em {
            color: var(--kn-orange);
            font-style: normal;
        }
        .brand-logo .logo-tagline {
            font-size: 0.78rem;
            color: var(--kn-muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* ── Buttons ──────────────────────────────────────────────────── */
        .btn-primary {
            background-color: var(--kn-green);
            border-color: var(--kn-green);
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--kn-green-d);
            border-color: var(--kn-green-d);
        }
        .btn-outline-kn {
            color: var(--kn-green);
            border: 1.5px solid var(--kn-green);
            font-weight: 600;
        }
        .btn-outline-kn:hover {
            background: var(--kn-green);
            color: #fff;
        }
        .btn-google {
            background: #fff;
            border: 1.5px solid rgba(107,122,58,0.25);
            color: var(--kn-dark);
            font-weight: 500;
            display: flex; align-items: center; justify-content: center; gap: 0.6rem;
        }
        .btn-google:hover {
            background: var(--kn-cream);
            border-color: var(--kn-green);
        }
        .btn-google svg { flex-shrink: 0; }

        /* ── Form controls ────────────────────────────────────────────── */
        .form-control, .form-select {
            border-color: rgba(107,122,58,0.22);
            background: rgba(245,237,214,0.35);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--kn-orange);
            box-shadow: 0 0 0 0.2rem rgba(196,114,42,0.18);
            background: #fff;
        }
        .form-label { color: var(--kn-dark); font-weight: 500; font-size: 0.9rem; }

        /* ── Alerts ───────────────────────────────────────────────────── */
        .alert-kn-error {
            background: rgba(196,114,42,0.10);
            border: 1px solid rgba(196,114,42,0.30);
            color: #7a3a10;
            border-radius: 0.75rem;
        }
        .alert-kn-success {
            background: rgba(107,122,58,0.10);
            border: 1px solid rgba(107,122,58,0.30);
            color: var(--kn-dark);
            border-radius: 0.75rem;
        }

        /* ── Divider ──────────────────────────────────────────────────── */
        .divider-text {
            display: flex; align-items: center; gap: 0.75rem;
            color: var(--kn-muted); font-size: 0.85rem;
        }
        .divider-text::before, .divider-text::after {
            content: ''; flex: 1;
            height: 1px; background: rgba(107,122,58,0.18);
        }

        /* ── Links ────────────────────────────────────────────────────── */
        a.kn-link { color: var(--kn-orange); text-decoration: none; font-weight: 500; }
        a.kn-link:hover { color: var(--kn-green); text-decoration: underline; }

        /* ── Password strength bar ────────────────────────────────────── */
        .strength-bar { height: 4px; border-radius: 2px; transition: width 0.3s, background 0.3s; }

        /* ── Footer text ──────────────────────────────────────────────── */
        .footer-text { color: var(--kn-muted); font-size: 0.82rem; }
    </style>
</head>
<body>
<div class="d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="container px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
