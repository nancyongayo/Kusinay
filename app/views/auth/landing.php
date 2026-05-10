<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KusiNay – Smart Meal Planning & Nutrition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --kn-green  : #6B7A3A;
            --kn-green-d: #556030;
            --kn-orange : #C4722A;
            --kn-cream  : #fdf0e8;
            --kn-dark   : #3D4A1E;
            --kn-muted  : rgba(61,74,30,0.55);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--kn-cream);
            color: var(--kn-dark);
            overflow-x: hidden;
        }

        /* ── Navbar ─────────────────────────────────────────────────── */
        .kn-nav {
            background: var(--kn-green);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(61,74,30,0.18);
        }
        .kn-nav .nav-brand {
            display: flex; align-items: center; gap: .6rem;
            color: var(--kn-cream);
            font-size: 1.4rem;
            font-weight: 800;
            text-decoration: none;
        }
        .kn-nav .nav-brand em { color: #f0c080; font-style: normal; }
        .kn-nav .nav-actions { display: flex; gap: .75rem; }
        .btn-nav-outline {
            border: 1.5px solid var(--kn-cream);
            color: var(--kn-cream);
            background: transparent;
            padding: .45rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            font-size: .9rem;
            transition: background .2s, color .2s;
        }
        .btn-nav-outline:hover { background: var(--kn-cream); color: var(--kn-green); }
        .btn-nav-solid {
            background: var(--kn-orange);
            color: #fff;
            border: none;
            padding: .45rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            font-size: .9rem;
            transition: background .2s;
        }
        .btn-nav-solid:hover { background: #a85e22; color: #fff; }

        /* ── Hero ───────────────────────────────────────────────────── */
        .hero {
            min-height: 88vh;
            display: flex;
            align-items: center;
            background:
                radial-gradient(ellipse at 80% 20%, rgba(196,114,42,0.12) 0%, transparent 55%),
                radial-gradient(ellipse at 10% 80%, rgba(107,122,58,0.14) 0%, transparent 50%),
                linear-gradient(160deg, #fdf7ea 0%, #f5edd6 60%, #ede0c0 100%);
            padding: 4rem 2rem;
        }
        .hero-text h1 {
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            font-weight: 900;
            line-height: 1.15;
            color: var(--kn-dark);
        }
        .hero-text h1 span { color: var(--kn-orange); }
        .hero-text p {
            font-size: 1.1rem;
            color: var(--kn-muted);
            max-width: 480px;
            line-height: 1.7;
            margin: 1.25rem 0 2rem;
        }
        .hero-cta { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-hero-primary {
            background: var(--kn-green);
            color: #fff;
            padding: .85rem 2rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: background .2s, transform .15s;
            box-shadow: 0 4px 16px rgba(107,122,58,0.25);
        }
        .btn-hero-primary:hover { background: var(--kn-green-d); transform: translateY(-2px); color: #fff; }
        .btn-hero-secondary {
            background: transparent;
            color: var(--kn-green);
            padding: .85rem 2rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            border: 2px solid var(--kn-green);
            transition: background .2s, color .2s;
        }
        .btn-hero-secondary:hover { background: var(--kn-green); color: #fff; }

        /* Hero illustration / logo */
        .hero-logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .hero-logo-circle {
            width: 360px; height: 360px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            filter: drop-shadow(0 16px 40px rgba(107,122,58,0.25));
        }
        .hero-logo-circle img {
            width: 360px; height: 360px;
            object-fit: contain;
            border-radius: 50%;
        }

        /* ── Features ───────────────────────────────────────────────── */
        .features {
            background: var(--kn-green);
            padding: 5rem 2rem;
            color: var(--kn-cream);
        }
        .features h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: .5rem;
        }
        .features .sub {
            text-align: center;
            color: rgba(245,237,214,0.7);
            margin-bottom: 3rem;
            font-size: .95rem;
        }
        .feature-card {
            background: rgba(245,237,214,0.10);
            border: 1px solid rgba(245,237,214,0.18);
            border-radius: 1rem;
            padding: 1.75rem 1.5rem;
            text-align: center;
            transition: background .2s, transform .15s;
        }
        .feature-card:hover { background: rgba(245,237,214,0.18); transform: translateY(-4px); }
        .feature-card .icon { font-size: 2.4rem; margin-bottom: .75rem; }
        .feature-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: .4rem; }
        .feature-card p { font-size: .85rem; color: rgba(245,237,214,0.75); line-height: 1.6; }

        /* ── Roles section ──────────────────────────────────────────── */
        .roles-section {
            padding: 5rem 2rem;
            background: linear-gradient(160deg, #fdf0e8 0%, #fde8d8 100%);
        }
        .roles-section h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            color: var(--kn-dark);
            margin-bottom: .5rem;
        }
        .roles-section .sub {
            text-align: center;
            color: var(--kn-muted);
            margin-bottom: 3rem;
            font-size: .95rem;
        }
        .role-pill {
            background: #fff;
            border: 2px solid rgba(107,122,58,0.15);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: border-color .2s, transform .15s;
        }
        .role-pill:hover { border-color: var(--kn-orange); transform: translateY(-3px); }
        .role-pill .icon { font-size: 2rem; margin-bottom: .5rem; }
        .role-pill h4 { font-size: .95rem; font-weight: 700; color: var(--kn-dark); margin-bottom: .3rem; }
        .role-pill p { font-size: .8rem; color: var(--kn-muted); }

        /* ── CTA Banner ─────────────────────────────────────────────── */
        .cta-banner {
            background: var(--kn-orange);
            padding: 4rem 2rem;
            text-align: center;
            color: #fff;
        }
        .cta-banner h2 { font-size: 2rem; font-weight: 800; margin-bottom: .75rem; }
        .cta-banner p { font-size: 1rem; opacity: .88; margin-bottom: 2rem; }
        .btn-cta-white {
            background: #fff;
            color: var(--kn-orange);
            padding: .85rem 2.5rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: background .2s;
        }
        .btn-cta-white:hover { background: var(--kn-cream); color: var(--kn-orange); }

        /* ── Footer ─────────────────────────────────────────────────── */
        .kn-footer {
            background: var(--kn-dark);
            color: rgba(245,237,214,0.65);
            text-align: center;
            padding: 1.5rem;
            font-size: .82rem;
        }
        .kn-footer span { color: var(--kn-orange); }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="kn-nav">
    <a href="index.php" class="nav-brand">
        <img src="public/images/logo.png" alt="KusiNay" style="width:32px;height:32px;object-fit:contain;border-radius:50%;vertical-align:middle;margin-right:6px"><span style="color:var(--kn-cream)">Kusi</span><span style="color:#f0c080">Nay</span>
    </a>
    <div class="nav-actions">
        <a href="index.php?action=login"    class="btn-nav-outline">Sign In</a>
        <a href="index.php?action=register" class="btn-nav-solid">Get Started</a>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-6 hero-text">
                <h1>Smart Nutrition<br>for Every <span>Barangay</span></h1>
                <p>
                    KusiNay empowers Barangay Nutrition Scholars to track, plan, and validate
                    community nutrition data — all in one secure, easy-to-use system.
                </p>
                <div class="hero-cta">
                    <a href="index.php?action=register" class="btn-hero-primary">Get Started — It's Free</a>
                    <a href="index.php?action=login"    class="btn-hero-secondary">Sign In</a>
                </div>
            </div>
            <div class="col-12 col-lg-6 hero-logo-wrap">
                <div class="hero-logo-circle">
                    <img src="public/images/logo.png" alt="KusiNay Logo">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features">
    <div class="container">
        <h2>Everything you need</h2>
        <p class="sub">Built for BNS programs, designed for real communities.</p>
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon">📊</div>
                    <h3>Nutrition Tracking</h3>
                    <p>Record and monitor height, weight, and nutritional status for every family.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon">✅</div>
                    <h3>Report Validation</h3>
                    <p>Nutrition Officers can review and approve encoded data with a clear workflow.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon">🔒</div>
                    <h3>Secure & Compliant</h3>
                    <p>BCRYPT passwords, AES-128 encrypted addresses, and full audit logging.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon">🗺️</div>
                    <h3>PSGC Integration</h3>
                    <p>Accurate barangay addressing using the Philippine Standard Geographic Code API.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Roles -->
<section class="roles-section">
    <div class="container">
        <h2>Who is KusiNay for?</h2>
        <p class="sub">Four roles, one unified platform.</p>
        <div class="row g-3 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="role-pill">
                    <div class="icon">🛡️</div>
                    <h4>Admin</h4>
                    <p>Manages users, monitors security logs and system activity.</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="role-pill">
                    <div class="icon">🩺</div>
                    <h4>Nutrition Officer II</h4>
                    <p>Validates and approves nutrition reports from BNS staff.</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="role-pill">
                    <div class="icon">📋</div>
                    <h4>BNS Staff</h4>
                    <p>Encodes nutrition data and meal plans for the barangay.</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="role-pill">
                    <div class="icon">🏠</div>
                    <h4>Mother / Father</h4>
                    <p>Tracks family nutrition and accesses personalized meal plans.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="cta-banner">
    <div class="container">
        <h2>Ready to nourish your community?</h2>
        <p>Join KusiNay today and make every meal count.</p>
        <a href="index.php?action=register" class="btn-cta-white">Create Your Account</a>
    </div>
</section>

<!-- Footer -->
<footer class="kn-footer">
    <p>© <?= date('Y') ?> <span>KusiNay</span> — Nourishing families with care. Barangay Nutrition Scholar Program.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
