<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KusiNay – Smart Meal Planning & Nutrition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --kn-green  : #6B7A3A;
            --kn-green-d: #556030;
            --kn-orange : #C4722A;
            --kn-cream  : #fdf0e8;
            --kn-dark   : #3D4A1E;
            --kn-muted  : rgba(61,74,30,0.55);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-soft: 0 4px 16px rgba(107,122,58,0.15);
            --shadow-hover: 0 8px 32px rgba(107,122,58,0.25);
        }

        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: var(--kn-cream);
            color: var(--kn-dark);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Consolidated Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: var(--transition-smooth);
        }

        .animate-on-scroll.animate {
            opacity: 1;
            transform: translateY(0);
        }

        /* Consolidated Hover Effects */
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        /* ── Navbar ─────────────────────────────────────────────────── */
        .kn-nav {
            background: var(--kn-green);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-smooth);
        }

        .kn-nav.scrolled {
            padding: 0.5rem 0;
            box-shadow: var(--shadow-hover);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex; 
            align-items: center; 
            gap: 0.6rem;
            color: var(--kn-cream);
            font-size: 1.5rem;
            font-weight: 800;
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .nav-brand:hover {
            transform: scale(1.05);
            color: var(--kn-cream);
        }

        .nav-brand em { 
            color: #f0c080; 
            font-style: normal; 
        }

        .nav-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 50%;
            transition: var(--transition-smooth);
        }

        .nav-brand:hover .nav-logo {
            transform: rotate(360deg);
        }

        /* Mobile Navigation Toggle */
        .nav-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 0.5rem;
            background: transparent;
            border: none;
            z-index: 1001;
        }

        .nav-toggle span {
            width: 25px;
            height: 3px;
            background: var(--kn-cream);
            margin: 3px 0;
            transition: var(--transition-smooth);
            border-radius: 2px;
        }

        .nav-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .nav-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .nav-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        .nav-actions { 
            display: flex; 
            gap: 0.75rem;
            align-items: center;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
            margin-right: 2rem;
        }

        .nav-link {
            color: var(--kn-cream);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition-smooth);
            padding: 0.5rem 0;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: var(--kn-cream);
            transition: var(--transition-smooth);
            transform: translateX(-50%);
        }

        .nav-link:hover {
            color: var(--kn-cream);
            transform: translateY(-1px);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-nav-outline {
            border: 2px solid var(--kn-cream);
            color: var(--kn-cream);
            background: transparent;
            padding: 0.6rem 1.4rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
            white-space: nowrap;
        }
        
        .btn-nav-outline:hover { 
            background: var(--kn-cream); 
            color: var(--kn-green);
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }
        
        .btn-nav-solid {
            background: var(--kn-orange);
            color: #fff;
            border: 2px solid var(--kn-orange);
            padding: 0.6rem 1.4rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
            white-space: nowrap;
        }
        
        .btn-nav-solid:hover { 
            background: #a85e22;
            border-color: #a85e22;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .mobile-menu-links {
            display: none;
        }

        .mobile-only {
            display: none;
        }

        /* Mobile Navigation Styles */
        @media (max-width: 768px) {
            .nav-toggle {
                display: flex;
            }

            .nav-menu {
                display: none;
            }

            .mobile-menu-links {
                display: flex;
                flex-direction: column;
                gap: 0;
                width: 100%;
                margin-bottom: 1.5rem;
            }

            .mobile-only {
                display: block;
                text-align: center;
                padding: 0.75rem 0;
                font-size: 1.1rem;
                border-bottom: 1px solid rgba(245,237,214,0.2);
                margin: 0;
            }

            .mobile-only:last-child {
                border-bottom: none;
            }

            .mobile-only::after {
                display: none;
            }

            .nav-actions {
                position: fixed;
                top: 0;
                right: -100%;
                height: 100vh;
                width: 280px;
                background: var(--kn-green);
                flex-direction: column;
                justify-content: center;
                gap: 0;
                padding: 2rem;
                transition: var(--transition-smooth);
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            }

            .nav-actions.active {
                right: 0;
            }

            .btn-nav-outline,
            .btn-nav-solid {
                width: 100%;
                text-align: center;
                padding: 1rem 2rem;
                font-size: 1rem;
                margin-top: 0.5rem;
            }

            .navbar-container {
                padding: 0 1rem;
            }

            .nav-brand {
                font-size: 1.3rem;
            }

            .nav-logo {
                width: 35px;
                height: 35px;
            }
        }

        /* ── Hero ───────────────────────────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background:
                radial-gradient(ellipse at 80% 20%, rgba(196,114,42,0.12) 0%, transparent 55%),
                radial-gradient(ellipse at 10% 80%, rgba(107,122,58,0.14) 0%, transparent 50%),
                linear-gradient(160deg, #fdf7ea 0%, #f5edd6 60%, #ede0c0 100%);
            padding: 6rem 0 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="%23556030" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .hero-text {
            animation: slideInLeft 0.8s ease-out;
        }

        .hero-text h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 900;
            line-height: 1.1;
            color: var(--kn-dark);
            margin-bottom: 1.5rem;
        }
        
        .hero-text h1 span { 
            color: var(--kn-orange);
            position: relative;
        }

        .hero-text h1 span::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--kn-orange), transparent);
            border-radius: 2px;
        }
        
        .hero-text p {
            font-size: 1.2rem;
            color: var(--kn-muted);
            max-width: 520px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            font-weight: 400;
        }
        
        .hero-cta { 
            display: flex; 
            gap: 1rem; 
            flex-wrap: wrap;
            align-items: center;
        }
        
        .btn-hero-primary {
            background: var(--kn-green);
            color: #fff;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .btn-hero-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-hero-primary:hover::before {
            left: 100%;
        }
        
        .btn-hero-primary:hover { 
            background: var(--kn-green-d); 
            transform: translateY(-3px); 
            color: #fff;
            box-shadow: var(--shadow-hover);
        }
        
        .btn-hero-secondary {
            background: transparent;
            color: var(--kn-green);
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            border: 2px solid var(--kn-green);
            transition: var(--transition-smooth);
        }
        
        .btn-hero-secondary:hover { 
            background: var(--kn-green); 
            color: #fff;
            transform: translateY(-3px);
            box-shadow: var(--shadow-soft);
        }

        /* Hero illustration / logo */
        .hero-logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            animation: slideInRight 0.8s ease-out;
        }
        
        .hero-logo-circle {
            width: 400px; 
            height: 400px;
            border-radius: 50%;
            display: flex; 
            align-items: center; 
            justify-content: center;
            filter: drop-shadow(0 20px 60px rgba(107,122,58,0.25));
            animation: pulse 4s ease-in-out infinite;
            position: relative;
        }

        .hero-logo-circle::before {
            content: '';
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--kn-orange), var(--kn-green));
            z-index: -1;
            opacity: 0.1;
            animation: pulse 4s ease-in-out infinite reverse;
        }
        
        .hero-logo-circle img {
            width: 400px; 
            height: 400px;
            object-fit: contain;
            border-radius: 50%;
            transition: var(--transition-smooth);
        }

        .hero-logo-circle:hover img {
            transform: scale(1.05);
        }

        /* Responsive Hero */
        @media (max-width: 992px) {
            .hero {
                min-height: 90vh;
                padding: 4rem 0 3rem;
            }

            .hero-logo-circle {
                width: 300px;
                height: 300px;
            }

            .hero-logo-circle img {
                width: 300px;
                height: 300px;
            }

            .hero-text {
                text-align: center;
                margin-bottom: 3rem;
            }

            .hero-cta {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .hero {
                padding: 3rem 0 2rem;
            }

            .hero-container {
                padding: 0 1rem;
            }

            .hero-text p {
                font-size: 1.1rem;
            }

            .btn-hero-primary,
            .btn-hero-secondary {
                width: 100%;
                text-align: center;
                padding: 0.9rem 2rem;
                font-size: 1rem;
            }

            .hero-cta {
                flex-direction: column;
                width: 100%;
            }

            .hero-logo-circle {
                width: 250px;
                height: 250px;
            }

            .hero-logo-circle img {
                width: 250px;
                height: 250px;
            }
        }

        /* ── Features ───────────────────────────────────────────────── */
        .features {
            background: var(--kn-green);
            padding: 6rem 0;
            color: var(--kn-cream);
            position: relative;
        }

        .features::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--kn-orange), var(--kn-green), var(--kn-orange));
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .features .sub {
            text-align: center;
            color: rgba(245,237,214,0.8);
            margin-bottom: 4rem;
            font-size: 1.1rem;
            font-weight: 400;
        }
        
        .feature-card,
        .role-pill {
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before,
        .role-pill::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(245,237,214,0.1), transparent);
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .feature-card:hover::before,
        .role-pill:hover::before {
            opacity: 1;
        }

        .feature-card {
            background: rgba(245,237,214,0.08);
            border: 1px solid rgba(245,237,214,0.15);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            text-align: center;
            height: 100%;
        }
        
        .feature-card:hover { 
            background: rgba(245,237,214,0.15); 
            border-color: rgba(245,237,214,0.3);
        }

        .role-pill {
            background: #fff;
            border: 2px solid rgba(107,122,58,0.1);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            text-align: center;
            height: 100%;
        }
        
        .role-pill:hover { 
            border-color: var(--kn-orange);
        }

        .feature-card .icon,
        .role-pill .icon { 
            font-size: 2.5rem; 
            margin-bottom: 1.5rem;
            display: block;
            transition: var(--transition-smooth);
        }
        
        .feature-card h3 { 
            font-size: 1.25rem; 
            font-weight: 700; 
            margin-bottom: 1rem;
            color: var(--kn-cream);
        }
        
        .feature-card p { 
            font-size: 0.95rem; 
            color: rgba(245,237,214,0.85); 
            line-height: 1.7;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .features {
                padding: 4rem 0;
            }

            .features-container {
                padding: 0 1rem;
            }

            .features h2 {
                font-size: 2rem;
            }

            .feature-card {
                padding: 2rem 1.5rem;
                margin-bottom: 1.5rem;
            }
        }

        /* ── Roles section ──────────────────────────────────────────── */
        .roles-section {
            padding: 6rem 0;
            background: linear-gradient(160deg, #fdf0e8 0%, #fde8d8 100%);
            position: relative;
        }

        .roles-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        .roles-section h2 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--kn-dark);
            margin-bottom: 1rem;
        }
        
        .roles-section .sub {
            text-align: center;
            color: var(--kn-muted);
            margin-bottom: 4rem;
            font-size: 1.1rem;
            font-weight: 400;
        }
        
        .role-pill {
            background: #fff;
            border: 2px solid rgba(107,122,58,0.1);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: var(--transition-smooth);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .role-pill::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(196,114,42,0.05), transparent);
            opacity: 0;
            transition: var(--transition-smooth);
        }
        
        .role-pill:hover::before {
            opacity: 1;
        }
        
        .role-pill:hover { 
            border-color: var(--kn-orange); 
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }
        
        .role-pill .icon { 
            font-size: 2.5rem; 
            margin-bottom: 1.5rem;
            display: block;
            transition: var(--transition-smooth);
        }

        .role-pill:hover .icon {
            transform: scale(1.2);
        }
        
        .role-pill h4 { 
            font-size: 1.1rem; 
            font-weight: 700; 
            color: var(--kn-dark); 
            margin-bottom: 0.75rem; 
        }
        
        .role-pill p { 
            font-size: 0.9rem; 
            color: var(--kn-muted);
            line-height: 1.6;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .roles-section {
                padding: 4rem 0;
            }

            .roles-container {
                padding: 0 1rem;
            }

            .roles-section h2 {
                font-size: 2rem;
            }

            .role-pill {
                padding: 2rem 1.5rem;
                margin-bottom: 1.5rem;
            }
        }

        /* ── CTA Banner ─────────────────────────────────────────────── */
        .cta-banner {
            background: linear-gradient(135deg, var(--kn-orange) 0%, #a85e22 100%);
            padding: 5rem 0;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><defs><pattern id="dots" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="30" cy="30" r="1.5" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="60" height="60" fill="url(%23dots)"/></svg>');
            pointer-events: none;
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .cta-banner h2 { 
            font-size: 2.5rem; 
            font-weight: 800; 
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .cta-banner p { 
            font-size: 1.2rem; 
            opacity: 0.9; 
            margin-bottom: 2.5rem;
            font-weight: 400;
        }
        
        .btn-cta-white {
            background: #fff;
            color: var(--kn-orange);
            padding: 1rem 3rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: var(--transition-smooth);
            display: inline-block;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .btn-cta-white::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(196,114,42,0.1), transparent);
            transition: left 0.5s;
        }

        .btn-cta-white:hover::before {
            left: 100%;
        }
        
        .btn-cta-white:hover { 
            background: var(--kn-cream); 
            color: var(--kn-orange);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        @media (max-width: 768px) {
            .cta-banner {
                padding: 4rem 0;
            }

            .cta-container {
                padding: 0 1rem;
            }

            .cta-banner h2 {
                font-size: 2rem;
            }

            .cta-banner p {
                font-size: 1.1rem;
            }

            .btn-cta-white {
                width: 100%;
                padding: 1rem 2rem;
            }
        }

        /* ── Footer ─────────────────────────────────────────────────── */
        .kn-footer {
            background: var(--kn-dark);
            color: rgba(245,237,214,0.7);
            padding: 3rem 0 2rem;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            color: var(--kn-cream);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-section p,
        .footer-section li {
            color: rgba(245,237,214,0.8);
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section .highlight {
            color: var(--kn-orange);
            font-weight: 600;
        }

        .developer-info {
            background: rgba(245,237,214,0.05);
            border: 1px solid rgba(245,237,214,0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
        }

        .developer-info .name {
            color: var(--kn-orange);
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .developer-info .title {
            color: var(--kn-cream);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .footer-divider {
            border: none;
            height: 1px;
            background: rgba(245,237,214,0.2);
            margin: 2rem 0 1rem;
        }

        .footer-bottom {
            text-align: center;
            color: rgba(245,237,214,0.6);
            font-size: 0.85rem;
        }
        
        .footer-bottom .brand { 
            color: var(--kn-orange);
            font-weight: 600;
        }

        .footer-section .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            justify-content: center;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--kn-green);
            color: #fff;
            border-radius: 50%;
            text-decoration: none;
            font-size: 1.2rem;
            transition: var(--transition-smooth);
        }

        .social-links a:hover {
            background: var(--kn-orange);
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .footer-section .contact-info {
            margin-top: 0.75rem;
        }

        .footer-section .contact-info p {
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .kn-footer {
                padding: 2rem 0 1.5rem;
            }

            .footer-container {
                padding: 0 1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                text-align: center;
            }

            .developer-info {
                padding: 1.25rem;
            }
        }

        /* ── Scroll to Top Button ─────────────────────────────────── */
        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--kn-green);
            color: #fff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition-smooth);
            z-index: 999;
            box-shadow: var(--shadow-soft);
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background: var(--kn-green-d);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        @media (max-width: 768px) {
            .scroll-to-top {
                bottom: 1rem;
                right: 1rem;
                width: 45px;
                height: 45px;
            }
        }

        /* ── Loading State ──────────────────────────────────────────── */
        .loading {
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .loading.delay-1 { animation-delay: 0.1s; }
        .loading.delay-2 { animation-delay: 0.2s; }
        .loading.delay-3 { animation-delay: 0.3s; }
        .loading.delay-4 { animation-delay: 0.4s; }

        /* ── Accessibility ──────────────────────────────────────────── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            .scroll-to-top,
            html {
                scroll-behavior: auto;
            }
        }

        /* Focus styles for accessibility */
        .btn-nav-outline:focus,
        .btn-nav-solid:focus,
        .btn-hero-primary:focus,
        .btn-hero-secondary:focus,
        .btn-cta-white:focus,
        .scroll-to-top:focus {
            outline: 3px solid var(--kn-orange);
            outline-offset: 2px;
        }

        .nav-toggle:focus {
            outline: 2px solid var(--kn-cream);
            outline-offset: 2px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="kn-nav" id="navbar">
    <div class="navbar-container">
        <a href="index.php" class="nav-brand">
            <img src="public/images/logo.png" alt="KusiNay" class="nav-logo">
            <span>Kusi<em>Nay</em></span>
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-menu" id="navMenu">
            <a href="#hero" class="nav-link">Home</a>
            <a href="#features" class="nav-link">About</a>
            <a href="#roles" class="nav-link">Services</a>
            <a href="#cta" class="nav-link">Contact</a>
        </div>
        <div class="nav-actions" id="navActions">
            <div class="mobile-menu-links">
                <a href="#hero" class="nav-link mobile-only">Home</a>
                <a href="#features" class="nav-link mobile-only">About</a>
                <a href="#roles" class="nav-link mobile-only">Services</a>
                <a href="#cta" class="nav-link mobile-only">Contact</a>
            </div>
            <a href="index.php?action=login" class="btn-nav-outline">Sign In</a>
            <a href="index.php?action=register" class="btn-nav-solid">Get Started</a>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero" id="hero">
    <div class="hero-container">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-6 hero-text">
                <h1>Smart Nutrition<br>for Every <span>Barangay</span></h1>
                <p>
                    KusiNay empowers Barangay Nutrition Scholars to track, plan, and validate
                    community nutrition data — all in one secure, easy-to-use system.
                </p>
                <div class="hero-cta">
                    <a href="index.php?action=register" class="btn-hero-primary">Get Started — It's Free</a>
                    <a href="index.php?action=login" class="btn-hero-secondary">Sign In</a>
                </div>
            </div>
            <div class="col-12 col-lg-6 hero-logo-wrap">
                <div class="hero-logo-circle">
                    <img src="public/images/logo.png" alt="KusiNay Logo" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features" id="features">
    <div class="features-container">
        <h2 class="animate-on-scroll">Everything you need</h2>
        <p class="sub animate-on-scroll">Built for BNS programs, designed for real communities.</p>
        <div class="row g-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card animate-on-scroll hover-lift">
                    <div class="icon hover-scale">📊</div>
                    <h3>Nutrition Tracking</h3>
                    <p>Record and monitor height, weight, and nutritional status for every family member with precision.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card animate-on-scroll hover-lift">
                    <div class="icon hover-scale">✅</div>
                    <h3>Report Validation</h3>
                    <p>Nutrition Officers can review and approve encoded data with a clear, streamlined workflow.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card animate-on-scroll hover-lift">
                    <div class="icon hover-scale">👨‍👩‍👧‍👦</div>
                    <h3>Family Profiling</h3>
                    <p>Complete family health records and nutrition status tracking for comprehensive community assessment.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card animate-on-scroll hover-lift">
                    <div class="icon hover-scale">🍎</div>
                    <h3>Meal Planning</h3>
                    <p>Create nutritious meal plans tailored to family needs and dietary requirements for better health outcomes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Roles -->
<section class="roles-section" id="roles">
    <div class="roles-container">
        <h2 class="animate-on-scroll">Who is KusiNay for?</h2>
        <p class="sub animate-on-scroll">Four roles, one unified platform.</p>
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="role-pill animate-on-scroll hover-lift">
                    <div class="icon hover-scale">🛡️</div>
                    <h4>Admin</h4>
                    <p>Manages users, monitors security logs and oversees system activity across all barangays.</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="role-pill animate-on-scroll hover-lift">
                    <div class="icon hover-scale">🩺</div>
                    <h4>Nutrition Officer II</h4>
                    <p>Validates and approves nutrition reports from BNS staff with detailed oversight capabilities.</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="role-pill animate-on-scroll hover-lift">
                    <div class="icon hover-scale">📋</div>
                    <h4>BNS Staff</h4>
                    <p>Encodes nutrition data and creates comprehensive meal plans for barangay families.</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="role-pill animate-on-scroll hover-lift">
                    <div class="icon hover-scale">🏠</div>
                    <h4>Mother / Father</h4>
                    <p>Tracks family nutrition and accesses personalized meal plans and shopping recommendations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="cta-banner" id="cta">
    <div class="cta-container">
        <h2 class="animate-on-scroll">Ready to nourish your community?</h2>
        <p class="animate-on-scroll">Join KusiNay today and make every meal count for your barangay.</p>
        <a href="index.php?action=register" class="btn-cta-white animate-on-scroll">Create Your Account</a>
    </div>
</section>

<!-- Footer -->
<footer class="kn-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>About KusiNay</h4>
                <p>A nutrition management system for Barangay Nutrition Scholar programs in the Philippines.</p>
                <ul>
                    <li>• Meal planning & tracking</li>
                    <li>• Data validation workflow</li>
                    <li>• PSGC addressing</li>
                    <li>• Multi-role access</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>System Features</h4>
                <ul>
                    <li><span class="highlight">Family Profiles</span></li>
                    <li><span class="highlight">Nutrition Assessment</span></li>
                    <li><span class="highlight">Feeding Programs</span></li>
                    <li><span class="highlight">Reports & Analytics</span></li>
                </ul>
            </div>

            <div class="footer-section developer-info">
                <div class="name">Nancy Ongayo</div>
                <div class="title">BSIT Student</div>
                <div class="contact-info">
                    <p><span class="highlight">Davao Central College</span></p>
                    <p>📧 nancyongayo24@gmail.com</p>
                    <p>📱 09269749522</p>
                </div>
                <p>Capstone project focused on improving barangay nutrition programs through technology.</p>
                <p><span class="highlight">Tech:</span> PHP, MySQL, Bootstrap, JavaScript</p>
                <div class="social-links">
                    <a href="https://www.linkedin.com/in/nancy-ongayo-006497366/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Profile">
                        💼
                    </a>
                    <a href="https://www.facebook.com/insay.nanz" target="_blank" rel="noopener noreferrer" aria-label="Facebook Profile">
                        👤
                    </a>
                </div>
            </div>
        </div>

        <hr class="footer-divider">
        
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> <span class="brand">KusiNay</span>. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
    ↑
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const navToggle = document.getElementById('navToggle');
    const navActions = document.getElementById('navActions');
    const navbar = document.getElementById('navbar');
    const scrollToTopBtn = document.getElementById('scrollToTop');

    // Mobile Navigation
    navToggle.addEventListener('click', function() {
        const isActive = navActions.classList.toggle('active');
        navToggle.classList.toggle('active');
        document.body.style.overflow = isActive ? 'hidden' : '';
        this.setAttribute('aria-expanded', isActive);
    });

    // Close mobile menu on link click or outside click
    navActions.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });

    document.addEventListener('click', function(e) {
        if (!navToggle.contains(e.target) && !navActions.contains(e.target)) {
            closeMobileMenu();
        }
    });

    function closeMobileMenu() {
        navToggle.classList.remove('active');
        navActions.classList.remove('active');
        document.body.style.overflow = '';
        navToggle.setAttribute('aria-expanded', 'false');
    }

    // Scroll Effects (consolidated)
    let ticking = false;
    function handleScroll() {
        const scrollTop = window.pageYOffset;
        
        // Navbar scroll effect
        navbar.classList.toggle('scrolled', scrollTop > 50);
        
        // Scroll to top button
        scrollToTopBtn.classList.toggle('show', scrollTop > 300);
        
        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(handleScroll);
            ticking = true;
        }
    });

    // Scroll to top
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Intersection Observer for animations
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    document.querySelectorAll('.animate-on-scroll').forEach(element => {
        observer.observe(element);
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navActions.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Button ripple effect
    document.querySelectorAll('.btn-hero-primary, .btn-hero-secondary, .btn-cta-white, .btn-nav-solid, .btn-nav-outline').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            
            Object.assign(ripple.style, {
                width: size + 'px',
                height: size + 'px',
                left: (e.clientX - rect.left - size / 2) + 'px',
                top: (e.clientY - rect.top - size / 2) + 'px',
                position: 'absolute',
                borderRadius: '50%',
                background: 'rgba(255,255,255,0.3)',
                transform: 'scale(0)',
                animation: 'ripple 0.6s linear',
                pointerEvents: 'none'
            });

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Add ripple animation CSS
    if (!document.querySelector('#ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.textContent = `
            @keyframes ripple {
                to { transform: scale(4); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
});
</script>
</body>
</html>
