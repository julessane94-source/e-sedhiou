<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'Portail mairie' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            color-scheme: light;
            --bg: #f0fdf4;
            --bg-soft: #dcfce7;
            --panel: rgba(240, 253, 244, 0.9);
            --panel-solid: #f0fdf4;
            --panel-strong: #ffffff;
            --ink: #13263b;
            --muted: #5a6c7f;
            --line: rgba(19, 38, 59, 0.12);
            --line-strong: rgba(19, 38, 59, 0.2);
            --brand: #047857;
            --brand-dark: #15803d;
            --accent: #0f5f5c;
            --accent-soft: #ccfbf1;
            --danger: #a23a35;
            --success: #16a34a;
            --warning: #9b6a19;
            --info: #215b82;
            --shadow: 0 24px 70px rgba(6, 95, 70, 0.16);
            --shadow-soft: 0 12px 30px rgba(6, 95, 70, 0.12);
            --ring: 0 0 0 4px rgba(22, 163, 74, 0.14);
            --radius: 24px;
            --radius-sm: 16px;
            --font-body: "Manrope", "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            --font-heading: "Manrope", "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            --space-xs: 0.5rem;
            --space-sm: 0.75rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            /* Bootstrap 5 overrides */
            --bs-primary: #16a34a;
            --bs-primary-rgb: 22, 163, 74;
            --bs-success: #16a34a;
            --bs-success-rgb: 22, 163, 74;
            --bs-link-color: #16a34a;
            --bs-body-font-family: var(--font-body);
            --bs-body-bg: #f0fdf4;
            --bs-border-radius: 0.75rem;
            --bs-border-radius-pill: 999px;
        }

        /* Align hardcoded Tailwind emerald utilities with the portal brand palette */
        .tw\:bg-emerald-50 { background-color: color-mix(in oklab, var(--brand) 8%, white) !important; }
        .tw\:bg-emerald-50\/30 { background-color: color-mix(in oklab, var(--brand) 3%, white) !important; }
        .tw\:bg-emerald-50\/90 { background-color: color-mix(in oklab, var(--brand) 12%, white) !important; }
        .tw\:bg-emerald-700 { background-color: var(--brand) !important; }

        .tw\:text-emerald-100 { color: color-mix(in oklab, var(--brand) 22%, white) !important; }
        .tw\:text-emerald-700 { color: var(--brand-dark) !important; }
        .tw\:text-emerald-800 { color: color-mix(in oklab, var(--brand-dark) 88%, black) !important; }
        .tw\:text-emerald-900 { color: color-mix(in oklab, var(--brand-dark) 72%, black) !important; }

        .tw\:border-emerald-200 { border-color: color-mix(in oklab, var(--brand) 26%, white) !important; }
        .tw\:border-emerald-200\/60 { border-color: color-mix(in oklab, var(--brand) 26%, white) !important; }
        .tw\:border-emerald-300 { border-color: color-mix(in oklab, var(--brand) 38%, white) !important; }
        .tw\:border-emerald-600 { border-color: color-mix(in oklab, var(--brand) 86%, black) !important; }
        .tw\:border-emerald-800 { border-color: color-mix(in oklab, var(--brand-dark) 88%, black) !important; }

        .tw\:ring-emerald-400\/40 { --tw-ring-color: color-mix(in oklab, var(--brand) 60%, white) !important; }

        .tw\:to-emerald-50\/70 { --tw-gradient-to: color-mix(in oklab, var(--brand) 8%, white) var(--tw-gradient-to-position) !important; }
        .tw\:to-emerald-50\/80 { --tw-gradient-to: color-mix(in oklab, var(--brand) 8%, white) var(--tw-gradient-to-position) !important; }

        /* Force all white text variants to black across portal UI */
        .text-white,
        .tw\:text-white,
        [class*=" text-white"],
        [class^="text-white"],
        [class*=" tw:text-white"],
        [class^="tw:text-white"] {
            color: #111111 !important;
        }

        /* Protéger le logo du drapeau - Ne pas forcer la couleur du texte SVG */
        .brand__mark {
            filter: none !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .brand__mark * {
            color: inherit;
        }

        .btn-success,
        .btn-primary,
        .button--primary {
            color: #111111 !important;
        }

        /* Bootstrap 5 form + button integration */
        .form-control, .form-select {
            border-radius: 14px;
            border-color: rgba(34, 139, 34, 0.16);
            background: rgba(255, 255, 255, 0.92);
            color: var(--ink);
            font-family: var(--font-body);
            padding: 12px 14px;
            min-height: 3rem;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: rgba(22, 163, 74, 0.5);
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
            background: #fff;
        }
        .form-label {
            font-weight: 700;
            font-size: 0.86rem;
            color: var(--ink);
            margin-bottom: 6px;
        }
        .form-label .bi { color: var(--brand); }
        .btn-success, .btn-primary {
            --bs-btn-bg: var(--brand);
            --bs-btn-border-color: var(--brand);
            --bs-btn-hover-bg: var(--brand-dark);
            --bs-btn-hover-border-color: var(--brand-dark);
            --bs-btn-color: #111111;
            --bs-btn-hover-color: #111111;
            --bs-btn-active-color: #111111;
            font-family: var(--font-body);
            font-weight: 700;
        }
        .btn-outline-success {
            --bs-btn-color: var(--brand);
            --bs-btn-border-color: var(--brand);
            --bs-btn-hover-bg: var(--brand);
            --bs-btn-hover-border-color: var(--brand);
            font-family: var(--font-body);
            font-weight: 700;
        }
        /* Topbar Bootstrap navbar */
        .topbar.navbar { padding: 0; }
        .topbar .navbar-toggler:focus { box-shadow: none; }
        .topbar .navbar-collapse { justify-content: flex-end; }
        .topbar .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.5rem;
            border: 1px solid rgba(19, 38, 59, 0.08);
            background: rgba(255, 255, 255, 0.92);
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            color: #064e3b;
            line-height: 1.2;
            transition: background 0.18s, border-color 0.18s, box-shadow 0.18s;
        }
        .topbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.96);
            border-color: rgba(22, 163, 74, 0.3);
            box-shadow: 0 8px 18px rgba(19, 38, 59, 0.06);
        }

        .topbar .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.5rem;
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
            line-height: 1.2;
        }

        .topbar .btn:hover,
        .topbar .btn:focus {
            box-shadow: 0 8px 18px rgba(19, 38, 59, 0.06);
        }

        html {
            scroll-behavior: smooth;
        }

        /* Animation glissement gauche → droite */
        @keyframes slideRight {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .slide-animate {
            animation: slideRight 12s linear infinite;
        }

        body {
            margin: 0;
            font-family: var(--font-body);
            font-size: 14px;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(22, 163, 74, 0.15), transparent 28%),
                radial-gradient(circle at top right, rgba(134, 239, 172, 0.12), transparent 24%),
                linear-gradient(180deg, #ffffff 0%, #f0fdf4 52%, #dcfce7 100%);
            min-height: 100vh;
        }

        ::selection {
            background: rgba(22, 163, 74, 0.2);
            color: var(--ink);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.18) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.18) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.45), transparent 90%);
            pointer-events: none;
            opacity: 0.35;
        }

        a { color: inherit; text-decoration: none; }

        button, input, textarea, select {
            font: inherit;
        }

        .shell {
            width: min(1240px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(20px);
            background: #047857;
            border-bottom: 1px solid #065f46;
            box-shadow: 0 10px 28px rgba(6, 95, 70, 0.25);
        }

        .topbar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 18px 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .brand__mark {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: transparent;
            object-fit: contain;
            flex-shrink: 0;
            display: block;
        }

        .brand__text {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .brand strong {
            font-family: var(--font-heading);
            font-size: 1.08rem;
            letter-spacing: 0.02em;
            color: #ffffff;
        }

        .brand span {
            color: #d1fae5;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Animation scrolling pour la description du site */
        .brand__text span.scroll-text {
            display: inline-block;
            animation: scroll-left-laravel 30s linear infinite;
        }

        .brand__text .scroll-container {
            max-width: 300px;
            overflow: hidden;
            white-space: nowrap;
        }

        @keyframes scroll-left-laravel {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(calc(100% + 100vw));
            }
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .nav a,
        .nav button {
            border: 1px solid rgba(19, 38, 59, 0.08);
            background: rgba(255, 255, 255, 0.74);
            border-radius: 999px;
            padding: 10px 16px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
            box-shadow: 0 8px 18px rgba(19, 38, 59, 0.05);
        }

        .nav a:hover,
        .nav button:hover {
            transform: translateY(-1px);
            border-color: rgba(15, 95, 92, 0.24);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 12px 24px rgba(19, 38, 59, 0.08);
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        textarea:focus-visible,
        select:focus-visible,
        .button:focus-visible,
        .nav a:focus-visible,
        .nav button:focus-visible,
        .topbar .nav-link:focus-visible,
        .quick-card:focus-visible,
        .msg-tab-btn:focus-visible {
            outline: none;
            box-shadow: var(--ring);
        }

        .nav .primary {
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border-color: transparent;
            color: #fff;
            box-shadow: 0 16px 28px rgba(15, 95, 92, 0.2);
        }

        .hero {
            display: grid;
            gap: 24px;
            grid-template-columns: 1.4fr 1fr;
            padding: 32px 0 20px;
        }

        .hero__panel,
        .panel,
        .stat,
        .card,
        .table-card,
        .message,
        .notice {
            background: var(--panel);
            border: 1px solid rgba(34, 139, 34, 0.16);
            border-radius: var(--radius);
            box-shadow: 0 22px 48px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(18px);
            animation: panel-fade-up 520ms ease-out both;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .panel:hover,
        .card:hover,
        .table-card:hover {
            transform: translateY(-2px);
            border-color: rgba(22, 163, 74, 0.24);
            box-shadow: 0 24px 52px rgba(15, 23, 42, 0.11);
        }

        .hero__panel {
            padding: var(--space-xl);
            position: relative;
            overflow: hidden;
        }

        @keyframes panel-fade-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero__panel::before {
            content: "";
            position: absolute;
            inset: auto -12% -28% auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
                background: radial-gradient(circle, rgba(22, 163, 74, 0.18), transparent 68%);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-block;
            color: var(--brand);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.72rem;
            margin-bottom: 12px;
        }

        h1, h2, h3, h4, p { margin-top: 0; }
        h1, h2, h3, h4 {
            margin-bottom: var(--space-sm);
            font-family: var(--font-heading);
            color: var(--brand-dark);
            line-height: 1.25;
            letter-spacing: -0.01em;
        }
        p { margin-bottom: var(--space-md); }
        h1 { font-size: clamp(2rem, 4vw, 3.4rem); font-weight: 800; line-height: 1.1; }
        h2 { font-size: clamp(1.32rem, 2.2vw, 1.75rem); font-weight: 750; }
        h3 { font-size: clamp(1.05rem, 1.4vw, 1.25rem); font-weight: 700; }
        p, li, td, th, label { line-height: 1.6; }

        .muted { color: var(--muted); }

        .grid {
            display: grid;
            gap: 18px;
        }

        .grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .panel,
        .card,
        .table-card,
        .notice {
            padding: var(--space-xl);
        }

        .section {
            margin: var(--space-xl) 0;
        }

        .stat {
            padding: var(--space-lg);
        }

        .stat strong {
            display: block;
            font-size: 1.9rem;
            margin-bottom: 8px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 0.7rem;
            padding: 7px 12px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            border: 1px solid transparent;
            background: rgba(15, 95, 92, 0.12);
            color: var(--brand-dark);
            transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
        }

        .badge.pending { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .badge.assigned { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
        .badge.processing { background: #a7f3d0; color: #064e3b; border-color: #34d399; }
        .badge.completed { background: #6ee7b7; color: #022c22; border-color: #10b981; }
        .badge.rejected { background: #f0fdf4; color: #14532d; border-color: #86efac; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .button,
        button.button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 2.75rem;
            padding: 0.78rem 1.15rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(19, 38, 59, 0.14);
            background: rgba(255, 251, 246, 0.86);
            color: var(--brand-dark);
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(19, 38, 59, 0.08);
            transition: transform 0.18s ease, border-color 0.18s ease, background-color 0.18s ease, box-shadow 0.18s ease;
        }

        .button:hover,
        button.button:hover {
            transform: translateY(-1px);
            border-color: rgba(15, 95, 92, 0.25);
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 14px 26px rgba(19, 38, 59, 0.1);
        }

        .button:active,
        button.button:active,
        .nav a:active,
        .nav button:active {
            transform: translateY(0);
        }

        .button--primary {
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border-color: transparent;
            color: #fff;
        }

        .button--accent {
            background: linear-gradient(135deg, var(--accent), #b8592c);
            border-color: transparent;
            color: #fff;
        }

        .button--ghost {
            background: rgba(15, 95, 92, 0.08);
            border-color: rgba(15, 95, 92, 0.18);
            color: var(--brand-dark);
        }

        .button--ghost:hover {
            background: rgba(15, 95, 92, 0.14);
        }

        form {
            display: grid;
            gap: 14px;
        }

        .form-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field--full {
            grid-column: 1 / -1;
        }

        input,
        textarea,
        select {
            width: 100%;
            border: 1px solid rgba(34, 139, 34, 0.16);
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--ink);
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: rgba(22, 163, 74, 0.5);
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
            background: #fff;
        }

        input[type="file"] {
            padding: 10px 12px;
        }

        input[type="file"]::file-selector-button {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-right: 12px;
            background: rgba(22, 163, 74, 0.12);
            color: var(--brand-dark);
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.18s ease, color 0.18s ease;
        }

        input[type="file"]::file-selector-button:hover {
            background: rgba(22, 163, 74, 0.18);
        }

        .button--danger {
            background: var(--danger);
            border-color: transparent;
            color: #fff;
        }

        .button--danger:hover {
            background: #8d3224;
            border-color: transparent;
        }

        textarea { min-height: 120px; resize: vertical; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: rgba(22, 163, 74, 0.06);
        }

        .table-card {
            overflow-x: auto;
        }

        th, td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        tbody tr {
            transition: background-color 0.18s ease;
        }

        tbody tr:hover td {
            background: rgba(22, 163, 74, 0.04);
        }

        th {
            color: var(--muted);
            font-size: 0.82rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .message-list {
            display: grid;
            gap: 14px;
        }

        .message {
            padding: 16px 18px;
        }

        .message__meta {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 8px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .notice {
            margin: 0 0 18px;
            border-width: 1px;
            border-style: solid;
            border-radius: 18px;
            padding: 18px 20px;
        }

        .notice--success {
            border-color: rgba(29, 111, 66, 0.26);
            background: rgba(29, 111, 66, 0.08);
        }

        .notice--warning {
            border-color: rgba(138, 91, 19, 0.26);
            background: rgba(138, 91, 19, 0.09);
        }

        .notice--error {
            border-color: rgba(161, 59, 43, 0.26);
            background: rgba(161, 59, 43, 0.09);
        }

        .list {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .list li {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.68);
        }

        .hero__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
            position: relative;
            z-index: 1;
        }

        .panel-note {
            margin: 10px 0 0;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(19, 38, 59, 0.1);
            color: var(--muted);
            box-shadow: var(--shadow-soft);
        }

        .metric-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .metric-tile {
            display: grid;
            gap: 8px;
            padding: 20px;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(251, 246, 240, 0.82));
            border: 1px solid rgba(19, 38, 59, 0.08);
            box-shadow: var(--shadow-soft);
        }

        .metric-tile__label {
            color: var(--muted);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .metric-tile__value {
            font-size: clamp(1.5rem, 3vw, 2.3rem);
            line-height: 1;
            color: var(--brand-dark);
            font-weight: 800;
        }

        .metric-tile__meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 0.84rem;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .panel-header__title {
            display: grid;
            gap: 6px;
        }

        .panel-header__meta {
            color: var(--muted);
            font-size: 0.88rem;
            white-space: nowrap;
        }

        .admin-toolbar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .admin-toolbar .field {
            min-width: 180px;
            flex: 1 1 180px;
        }

        .field label,
        .admin-toolbar label {
            display: inline-block;
            margin-bottom: 6px;
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--ink);
        }

        .admin-toolbar .actions {
            align-items: flex-end;
            gap: 10px;
        }

        .section-stack {
            display: grid;
            gap: 20px;
        }

        .fact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
        }

        .fact-card {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(19, 38, 59, 0.08);
            box-shadow: var(--shadow-soft);
        }

        .fact-card strong {
            display: block;
            margin-bottom: 6px;
            color: var(--brand-dark);
            font-size: 0.94rem;
        }

        .fact-card span {
            color: var(--muted);
            font-size: 0.86rem;
        }

        .bar-list {
            display: grid;
            gap: 12px;
        }

        .bar-row {
            display: grid;
            gap: 6px;
        }

        .bar-row__meta {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: baseline;
        }

        .bar-row__meta span {
            color: var(--ink);
            font-size: 0.92rem;
        }

        .bar-row__meta strong {
            color: var(--brand-dark);
            font-size: 0.9rem;
        }

        .bar-row__track {
            background: rgba(19, 38, 59, 0.08);
            border-radius: 999px;
            height: 10px;
            overflow: hidden;
        }

        .bar-row__fill {
            width: 0;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--brand), var(--accent));
            transition: width .6s ease;
        }

        .bar-row__fill--accent {
            background: linear-gradient(90deg, var(--accent), #b8592c);
        }

        .bar-row__fill--dark {
            background: linear-gradient(90deg, var(--brand-dark), var(--brand));
        }

        .bar-row__fill--success {
            background: linear-gradient(90deg, #2b8b57, var(--success));
        }

        .bar-row__fill--danger {
            background: linear-gradient(90deg, #b35340, var(--danger));
        }

        .bar-row__fill--warning {
            background: linear-gradient(90deg, #b37c2d, var(--warning));
        }

        .divider {
            margin: 18px 0;
            border: 0;
            border-top: 1px solid rgba(19, 38, 59, 0.08);
        }

        .admin-table tbody tr:hover td {
            background: rgba(22, 163, 74, 0.05);
        }

        .admin-table td strong {
            color: var(--brand-dark);
        }

        .admin-table td:last-child {
            white-space: nowrap;
        }

        .admin-table td:last-child .button,
        .admin-table td:last-child .btn,
        .admin-table td:last-child a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.15rem;
            padding: 0.45rem 0.85rem;
            font-size: 0.82rem;
            line-height: 1.15;
            border-radius: 999px;
            text-decoration: none;
        }

        .details-card {
            margin-bottom: 12px;
            padding: 16px 20px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(19, 38, 59, 0.1);
            box-shadow: var(--shadow-soft);
        }

        .details-card summary {
            cursor: pointer;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .details-card__summary-meta {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .details-card__content {
            margin-top: 16px;
        }

        .nowrap {
            white-space: nowrap;
        }

        .wrap-pre {
            white-space: pre-wrap;
        }

        .split {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .footer-space {
            height: 32px;
        }

        .welcome-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 32px 0 8px;
            flex-wrap: wrap;
        }

        .welcome-bar__date {
            font-size: .78rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .welcome-bar__name {
            font-size: clamp(1.65rem, 2.8vw, 2.35rem);
            font-weight: 800;
            margin: 0 0 6px;
            line-height: 1.15;
        }

        .welcome-bar__meta {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            font-size: .875rem;
            font-weight: 500;
        }

        .welcome-bar__actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .role-badge--admin {
            background: rgba(161, 59, 43, .12);
            color: var(--danger);
        }

        .role-badge--agent {
            background: rgba(10, 108, 116, .12);
            color: var(--brand-dark);
        }

        .role-badge--citoyen {
            background: rgba(215, 123, 45, .12);
            color: #9f5b17;
        }

        .tab-nav {
            display: flex;
            gap: 8px;
            margin: 24px 0 0;
            padding: 8px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: #f9fafb;
            overflow-x: auto;
            scrollbar-width: none;
            box-shadow: var(--shadow-soft);
        }

        .tab-nav::-webkit-scrollbar {
            display: none;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 11px 16px;
            border: 1px solid transparent;
            border-radius: 14px;
            background: transparent;
            color: var(--muted);
            font-weight: 500;
            font-size: .875rem;
            cursor: pointer;
            white-space: nowrap;
            transition: color .18s, background .18s, border-color .18s, transform .18s;
        }

        .tab-btn:hover {
            color: var(--ink);
            background: rgba(15, 95, 92, .07);
        }

        .dashboard-stage .tab-btn:focus-visible {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.28), 0 0 0 7px rgba(236, 253, 245, 0.55);
            background: #ecfdf5;
            color: #065f46;
        }

        .tab-btn.active {
            background: #047857;
            border-color: #065f46;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--brand);
            color: #fff;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 800;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
        }

        .tab-pane {
            display: none;
            padding: 28px 0;
        }

        .tab-pane.active {
            display: block;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(145px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .kpi {
            background: #ffffff;
            border: 1px solid rgba(34, 139, 34, 0.14);
            border-radius: 8px;
            padding: 20px 18px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .kpi__icon {
            font-size: 1.4rem;
            margin-bottom: 2px;
        }

        .kpi__value {
            font-size: clamp(1.25rem, 2vw, 1.5rem);
            font-weight: 700;
            line-height: 1;
            color: var(--ink);
        }

        .kpi__label {
            font-size: .75rem;
            color: #6b7280;
            font-weight: 500;
            margin-top: 4px;
        }

        .kpi--brand {
            border-left: 3px solid #10b981;
        }

        .kpi--accent {
            border-left: 3px solid #059669;
        }

        .kpi--success {
            border-left: 3px solid #047857;
        }

        .kpi--danger {
            border-left: 3px solid #065f46;
        }

        .kpi--warn {
            border-left: 3px solid #34d399;
        }

        .sec-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .sec-header h2,
        .sec-header h3 {
            margin: 0;
        }

        .msg-switcher {
            display: flex;
            gap: 4px;
            margin-bottom: 18px;
            background: #ecfdf5;
            border-radius: 12px;
            padding: 4px;
            width: fit-content;
        }

        .msg-tab-btn {
            padding: 8px 20px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--muted);
            font-weight: 500;
            font-size: .875rem;
            cursor: pointer;
            transition: all .18s;
        }

        .msg-tab-btn.active {
            background: #047857;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(6, 95, 70, 0.25);
        }

        .msg-tab-btn:hover {
            color: var(--brand-dark);
            background: rgba(255, 255, 255, 0.72);
        }

        .msg-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(10, 108, 116, .15);
            color: var(--brand-dark);
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            margin-left: 4px;
        }

        .msg-pane {
            display: none;
        }

        .msg-pane.active {
            display: block;
        }

        .msg-list {
            display: grid;
            gap: 12px;
        }

        .msg-card {
            background: var(--panel);
            border: 1px solid rgba(34, 139, 34, 0.14);
            border-radius: 18px;
            padding: 16px 18px;
            display: grid;
            gap: 6px;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .msg-card:hover {
            transform: translateY(-2px);
            border-color: rgba(22, 163, 74, 0.22);
            box-shadow: 0 14px 28px rgba(19, 38, 59, .08);
        }

        .msg-card__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .msg-card__from {
            font-weight: 700;
            font-size: .95rem;
        }

        .msg-card__time {
            color: var(--muted);
            font-size: .82rem;
            white-space: nowrap;
        }

        .msg-card__ref {
            color: var(--brand);
            font-size: .82rem;
            font-weight: 600;
        }

        .msg-card__body {
            color: var(--muted);
            font-size: .92rem;
            line-height: 1.5;
            margin: 0;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(175px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .quick-card {
            background: var(--panel-strong);
            border: 1px solid #a7f3d0;
            border-radius: 20px;
            padding: 22px 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-decoration: none;
            color: var(--ink);
            transition: transform .18s, box-shadow .18s, border-color .18s;
            cursor: pointer;
        }

        .quick-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 36px rgba(6, 95, 70, 0.18);
            border-color: #34d399;
        }

        .fact-card,
        .hero-metric,
        .dashboard-summary__card {
            display: grid;
            gap: 6px;
            padding: 18px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #a7f3d0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .fact-card:hover,
        .hero-metric:hover,
        .dashboard-summary__card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(6, 95, 70, 0.14);
            border-color: #34d399;
        }

        .quick-card__icon {
            font-size: 1.8rem;
        }

        .quick-card__label {
            font-weight: 700;
            font-size: .95rem;
        }

        .quick-card__desc {
            font-size: .82rem;
            color: var(--muted);
        }

        .progress-bar {
            height: 8px;
            background: rgba(19, 38, 59, .1);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 6px;
        }

        .progress-bar__fill {
            width: 0;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--brand), var(--accent));
            transition: width .6s ease;
        }

        .auth-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 24px;
            align-items: start;
        }

        .auth-stage {
            display: grid;
            gap: 18px;
        }

        .auth-showcase {
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .auth-showcase::after {
            content: "";
            position: absolute;
            right: -40px;
            top: -30px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(15, 95, 92, 0.18), transparent 70%);
            pointer-events: none;
        }

        .auth-lead {
            max-width: 56ch;
            font-size: 1rem;
        }

        .hero-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .hero-metric {
            padding: 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid #a7f3d0;
            box-shadow: var(--shadow-soft);
        }

        .hero-metric strong {
            display: block;
            font-size: 1.3rem;
            margin-bottom: 6px;
            color: var(--brand-dark);
        }

        .hero-metric span {
            color: var(--muted);
            font-size: 0.84rem;
        }

        .info-stack {
            display: grid;
            gap: 14px;
        }

        .info-card {
            padding: 18px 20px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid #a7f3d0;
            box-shadow: var(--shadow-soft);
        }

        .info-card strong {
            display: block;
            margin-bottom: 6px;
            color: var(--brand-dark);
        }

        .auth-forms {
            display: grid;
            gap: 18px;
        }

        .auth-card {
            padding: 26px;
        }

        .auth-card h2 {
            margin-bottom: 8px;
        }

        .auth-card__intro {
            margin-bottom: 18px;
            color: var(--muted);
        }

        .pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid rgba(19, 38, 59, 0.1);
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .portal-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .page-frame {
            padding-bottom: 18px;
        }

        .dashboard-hero {
            padding: 28px;
            overflow: hidden;
            position: relative;
        }

        .dashboard-hero .welcome-bar__date,
        .dashboard-hero .muted,
        .dashboard-hero .dashboard-hero__lead {
            color: #d1fae5;
        }

        .dashboard-hero .welcome-bar__name {
            color: #ffffff;
        }

        .dashboard-hero {
            color: #ffffff;
        }

        .dashboard-hero .role-badge {
            background: rgba(236, 253, 245, 0.95);
            color: #064e3b;
            border: 1px solid #6ee7b7;
        }

        .dashboard-hero .pill {
            color: #064e3b;
            border-color: #34d399;
            background: #d1fae5;
        }

        .dashboard-hero .welcome-bar__actions .button {
            border-color: #047857;
        }

        .dashboard-hero .welcome-bar__actions .button--ghost {
            background: #d1fae5;
            border-color: #6ee7b7;
            color: #065f46;
            box-shadow: 0 10px 20px rgba(6, 95, 70, 0.18);
        }

        .dashboard-hero .welcome-bar__actions .button--ghost:hover {
            background: #ecfdf5;
            border-color: #34d399;
            color: #064e3b;
        }

        .dashboard-hero .welcome-bar__actions .button--danger {
            background: #065f46;
            border-color: #064e3b;
            color: #ecfdf5;
            box-shadow: 0 12px 22px rgba(6, 78, 59, 0.24);
        }

        .dashboard-hero .welcome-bar__actions .button--danger:hover {
            background: #064e3b;
            border-color: #022c22;
            color: #ffffff;
        }

        .dashboard-hero .welcome-bar__actions .button:focus-visible,
        .dashboard-hero .welcome-bar__actions button.button:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.35), 0 0 0 7px rgba(236, 253, 245, 0.55);
        }

        .dashboard-hero::before {
            content: "";
            position: absolute;
            inset: -20% auto auto -8%;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(15, 95, 92, 0.16), transparent 68%);
            pointer-events: none;
        }

        .dashboard-hero::after {
            content: "";
            position: absolute;
            inset: auto -5% -18% auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(194, 107, 63, 0.16), transparent 68%);
            pointer-events: none;
        }

        .welcome-bar--hero {
            position: relative;
            z-index: 1;
            padding: 0;
        }

        .dashboard-hero__lead {
            position: relative;
            z-index: 1;
            max-width: 68ch;
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.98rem;
        }

        .dashboard-hero__foot {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }

        .dashboard-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .dashboard-summary__card {
            display: grid;
            gap: 6px;
            padding: 18px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #6ee7b7;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .dashboard-summary__label {
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0;
            text-transform: none;
        }

        .dashboard-summary__card strong {
            font-size: clamp(1.25rem, 2vw, 1.5rem);
            line-height: 1;
            color: var(--brand-dark);
            font-weight: 700;
        }

        .dashboard-summary__meta {
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .dashboard-stage {
            margin-top: 24px;
            padding: 20px;
            border-radius: 28px;
            background: #f9fafb;
            border: 1px solid rgba(19, 38, 59, 0.08);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

        .dashboard-stage .tab-nav {
            display: flex;
            gap: 8px;
            margin: 24px 0 0;
            padding: 8px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: #f9fafb;
            overflow-x: auto;
            scrollbar-width: none;
            box-shadow: var(--shadow-soft);
        }

        .dashboard-stage .tab-pane {
            padding-bottom: 6px;
        }

        @media (max-width: 960px) {
            h1 { font-size: clamp(1.75rem, 6vw, 2.6rem); }
            h2 { font-size: clamp(1.2rem, 3.8vw, 1.55rem); }
            h3 { font-size: clamp(1rem, 2.8vw, 1.15rem); }

            .hero,
            .auth-grid,
            .dashboard-summary,
            .hero-metrics,
            .grid--4,
            .grid--3,
            .grid--2,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .topbar__inner,
            .split,
            .panel-header,
            .details-card summary,
            .message__meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .welcome-bar {
                align-items: flex-start;
                gap: 14px;
                padding-top: 22px;
            }

            .welcome-bar__actions {
                width: 100%;
            }

            .welcome-bar__actions .button {
                width: 100%;
                justify-content: center;
            }

            .hero__panel,
            .panel,
            .card,
            .table-card,
            .notice,
            .auth-card,
            .auth-showcase,
            .dashboard-hero,
            .dashboard-stage {
            margin-top: 24px;
            padding: 20px;
            border-radius: 28px;
            background: #f9fafb;
            border: 1px solid rgba(19, 38, 59, 0.08);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

            .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 11px 16px;
            border: 1px solid transparent;
            border-radius: 14px;
            background: transparent;
            color: var(--muted);
            font-weight: 500;
            font-size: .875rem;
            cursor: pointer;
            white-space: nowrap;
            transition: color .18s, background .18s, border-color .18s, transform .18s;
        }

            .tab-pane {
                padding-top: 22px;
            }

            .kpi-row {
                grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            }

            .quick-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .msg-switcher {
            display: flex;
            gap: 4px;
            margin-bottom: 18px;
            background: #f3f4f6;
            border-radius: 12px;
            padding: 4px;
            width: fit-content;
        }

            .msg-tab-btn {
            padding: 8px 20px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--muted);
            font-weight: 500;
            font-size: .875rem;
            cursor: pointer;
            transition: all .18s;
        }

            .msg-card {
                padding: 14px;
                border-radius: 14px;
            }

            .kpi,
            .quick-card {
                padding: 18px 16px;
                border-radius: 18px;
            }
        }

        @media (max-width: 640px) {
            h1 { font-size: clamp(1.55rem, 8.2vw, 2.15rem); }
            h2 { font-size: clamp(1.08rem, 5.6vw, 1.35rem); }
            h3 { font-size: clamp(0.98rem, 4.2vw, 1.12rem); }

            .shell {
                width: min(100% - 20px, 1240px);
            }

            .topbar__inner {
                padding: 14px 0;
            }

            .brand {
                align-items: flex-start;
            }

            .brand__mark {
                width: 38px;
                height: 38px;
                border-radius: 12px;
            }

            .dashboard-stage {
            margin-top: 24px;
            padding: 20px;
            border-radius: 28px;
            background: #f9fafb;
            border: 1px solid rgba(19, 38, 59, 0.08);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

            .hero__panel,
            .panel,
            .card,
            .table-card,
            .notice,
            .auth-card,
            .auth-showcase {
                padding: var(--space-md);
                border-radius: 18px;
            }

            .welcome-bar__name {
                font-size: 1.2rem;
            }

            .role-badge {
                font-size: .7rem;
                padding: 4px 10px;
            }

            .hero {
                padding-top: 22px;
            }

            .nav {
                width: 100%;
            }

            .nav a,
            .nav button {
                width: 100%;
                justify-content: center;
            }

            .kpi {
            background: #ffffff;
            border: 1px solid rgba(34, 139, 34, 0.14);
            border-radius: 8px;
            padding: 20px 18px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

            .kpi__value {
            font-size: clamp(1.25rem, 2vw, 1.5rem);
            font-weight: 700;
            line-height: 1;
            color: var(--ink);
        }

            .quick-card {
                padding: 16px 14px;
                border-radius: 16px;
            }

            .msg-card {
                padding: 12px;
                border-radius: 12px;
            }

            .sec-header {
                gap: 8px;
            }

            .admin-toolbar {
                flex-direction: column;
            }

            .admin-toolbar .field {
                min-width: auto;
                flex: 1 1 100%;
            }

            .admin-toolbar .actions {
                width: 100%;
                flex-direction: column;
            }

            .admin-toolbar .actions button,
            .admin-toolbar .actions a {
                width: 100%;
            }

            .table-card {
                margin-bottom: 0;
            }

            table {
                font-size: 0.81rem;
            }

            th, td {
                padding: 10px 8px;
            }

            .admin-table td:last-child {
                white-space: normal;
            }

            .admin-table a {
                width: 100%;
                margin-bottom: 6px;
                justify-content: center;
            }

            .field label {
                font-size: 0.8rem;
            }

            .field > label {
                display: block;
                margin-bottom: 4px;
            }

            input,
            textarea,
            select,
            .form-control,
            .form-select {
                font-size: 16px;
            }

            .actions {
                flex-wrap: wrap;
            }

            .actions a,
            .actions button {
                font-size: 0.85rem;
                padding: 8px 12px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .hero__panel,
            .panel,
            .stat,
            .card,
            .table-card,
            .message,
            .notice,
            .badge,
            .button,
            button.button,
            .nav a,
            .nav button {
                animation: none;
                transition: none;
                transform: none;
            }
        }
    </style>
</head>
<body class="tw:antialiased tw:bg-emerald-50/30">
    <header class="topbar navbar navbar-expand-lg tw:sticky tw:top-0 tw:z-40 tw:backdrop-blur-sm">
        <div class="shell container-fluid topbar__inner px-0 tw:gap-4">
            <div class="brand navbar-brand me-auto">
                <img src="{{ $senegalFlagUrl ?? asset('senegal-flag.svg') }}" alt="Drapeau Sénégal" class="brand__mark" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;">
                <div class="brand__text">
                    <strong>{{ $portalSettings['site_name'] ?? 'Portail Mairie' }}</strong>
                    @if (! empty($portalSettings['site_description']))
                        <div class="scroll-container">
                            <span class="scroll-text">{{ $portalSettings['site_description'] }}&nbsp;&nbsp;&nbsp;&nbsp;{{ $portalSettings['site_description'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <button class="navbar-toggler border-0 p-1" type="button"
                data-bs-toggle="collapse" data-bs-target="#portalNav"
                aria-controls="portalNav" aria-expanded="false" aria-label="Navigation">
                <i class="bi bi-list tw:text-[1.7rem] tw:text-slate-800"></i>
            </button>

            <div class="collapse navbar-collapse" id="portalNav">
                <nav class="navbar-nav ms-auto align-items-lg-center gap-2 mt-2 mt-lg-0">
                    <a class="nav-link" href="{{ $wordpressUrl }}/"><i class="bi bi-house me-1"></i>Accueil</a>
                    @foreach ($portalLinks as $portalLink)
                        <a class="nav-link" href="{{ $portalLink['url'] }}">{{ $portalLink['label'] }}</a>
                    @endforeach
                    @if ($currentUser)
                        <form action="{{ route('portal.logout') }}" method="post" class="tw:inline">
                            @csrf
                            <button class="btn btn-outline-success rounded-pill px-4" type="submit">
                                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                            </button>
                        </form>
                    @else
                        <a class="btn btn-success rounded-pill px-4" href="{{ route('portal.auth') }}">
                            <i class="bi bi-person-lock me-1"></i>Connexion
                        </a>
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <main class="shell tw:py-4 md:tw:py-6">
        @if (session('status'))
            <div class="notice notice--{{ session('status_type', 'success') }} tw:mt-5">
                <strong>{{ session('status') }}</strong>
            </div>
        @endif

        @if ($errors->any())
            <div class="notice notice--error tw:mt-5">
                <strong>Verification necessaire</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="page-frame">
            @yield('content')
        </div>
        <div class="footer-space"></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

