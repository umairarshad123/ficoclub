<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout — 850 FICO Club</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    @if(config('services.authorize_net.environment') === 'production')
        <script type="text/javascript" src="https://js.authorize.net/v1/Accept.js" charset="utf-8"></script>
    @else
        <script type="text/javascript" src="https://jstest.authorize.net/v1/Accept.js" charset="utf-8"></script>
    @endif

    <style>
        :root {
            --navy:          #0d1b3e;
            --navy-mid:      #1a2f5e;
            --green:         #22c55e;
            --green-dark:    #16a34a;
            --green-light:   #f0fdf4;
            --green-border:  #bbf7d0;
            --gold:          #ca9a04;
            --gold-light:    #fefce8;
            --gold-border:   #fde68a;
            --white:         #ffffff;
            --bg:            #f1f5f9;
            --card-bg:       #ffffff;
            --border:        #e2e8f0;
            --text:          #0d1b3e;
            --text-mid:      #374151;
            --text-muted:    #6b7280;
            --text-light:    #9ca3af;
            --danger:        #ef4444;
            --danger-bg:     #fef2f2;
            --danger-border: #fecaca;
            --shadow-sm:     0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md:     0 4px 16px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
            --shadow-lg:     0 12px 40px rgba(0,0,0,0.1), 0 4px 12px rgba(0,0,0,0.06);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Nunito Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ═══════════════════════════════════════════════
           BANKING SECURITY BAR — top of page
        ═══════════════════════════════════════════════ */
        .banking-topbar {
            background: var(--navy);
            padding: 9px 0;
            border-bottom: 2px solid var(--green);
        }

        .banking-topbar-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.65);
            letter-spacing: 0.5px;
        }

        .topbar-lock-icon {
            width: 22px;
            height: 22px;
            background: var(--green);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
        }

        .topbar-secure-text {
            color: #fff;
            font-weight: 800;
            font-size: 12px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.55);
        }

        .topbar-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .topbar-sep { width: 1px; height: 14px; background: rgba(255,255,255,0.15); }

        /* ═══════════════════════════════════════════════
           SECURITY PROGRESS BAR — steps indicator
        ═══════════════════════════════════════════════ */
        .security-steps-bar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 0;
        }

        .security-steps-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: stretch;
        }

        .sec-step {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px 14px 0;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-light);
            position: relative;
            flex-shrink: 0;
        }

        .sec-step::after {
            content: '›';
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: var(--border);
            font-weight: 400;
        }

        .sec-step:last-child::after { display: none; }
        .sec-step:last-child { padding-right: 0; }

        .sec-step.active { color: var(--navy); }
        .sec-step.active .step-num {
            background: var(--navy);
            color: #fff;
            border-color: var(--navy);
        }

        .sec-step.done { color: var(--green-dark); }
        .sec-step.done .step-num {
            background: var(--green);
            border-color: var(--green);
            color: #fff;
            font-size: 10px;
        }

        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            color: var(--text-light);
            flex-shrink: 0;
            transition: all 0.2s;
        }

        /* ════ PAGE ════ */
        .page-wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 48px 24px 80px;
        }

        /* ════ HERO ════ */
        .hero {
            margin-bottom: 44px;
            text-align: center;
        }

        .hero-tags {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .hero-tag.green {
            background: var(--green-light);
            color: var(--green-dark);
            border: 1px solid var(--green-border);
        }

        .hero-tag.gold {
            background: var(--gold-light);
            color: var(--gold);
            border: 1px solid var(--gold-border);
        }

        .hero h1 {
            font-size: clamp(40px, 5.5vw, 72px);
            font-weight: 900;
            line-height: 1.0;
            color: var(--navy);
            margin-bottom: 0;
            letter-spacing: -1px;
        }

        .hero h1 .green-line {
            color: var(--green);
            display: block;
            position: relative;
        }

        .hero h1 .green-line::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 210px;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--green));
            border-radius: 100px;
        }

        /* ════ SCORE STRIP ════ */
        .score-strip {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-top: 32px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .score-bad {
            font-size: clamp(38px, 4.5vw, 60px);
            font-weight: 900;
            color: #fca5a5;
            text-decoration: line-through;
            text-decoration-color: #f87171;
            letter-spacing: -2px;
            line-height: 1;
        }

        .score-arrow {
            font-size: 26px;
            color: var(--text-muted);
        }

        .score-good {
            font-size: clamp(38px, 4.5vw, 60px);
            font-weight: 900;
            color: var(--green);
            letter-spacing: -2px;
            line-height: 1;
        }

        .score-divider {
            width: 1px;
            height: 48px;
            background: var(--border);
            margin: 0 4px;
        }

        .score-stat-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--navy);
        }

        .score-stat-sub {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        /* ════ TRUST PILLS ════ */
        .trust-row {
            display: flex;
            gap: 8px;
            margin-top: 26px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .trust-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-mid);
            box-shadow: var(--shadow-sm);
        }

        /* ════ LAYOUT ════ */
        .layout {
            display: grid;
            grid-template-columns: minmax(0,1fr) 368px;
            gap: 28px;
            align-items: start;
        }

        /* ═══════════════════════════════════════════════
           BANKING SECTION HEADER — upgraded
        ═══════════════════════════════════════════════ */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-divider-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .section-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ═══════════════════════════════════════════════
           CARD — banking-grade upgrade
        ═══════════════════════════════════════════════ */
        .card {
            background: var(--card-bg);
            border: 1px solid #d1dae8;
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 20px;
            box-shadow:
                0 1px 3px rgba(13,27,62,0.06),
                0 4px 12px rgba(13,27,62,0.04),
                inset 0 1px 0 rgba(255,255,255,0.9);
            transition: box-shadow 0.25s ease;
            position: relative;
        }

        /* Left accent stripe on each card */
        .card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 16px;
            bottom: 16px;
            width: 3px;
            background: linear-gradient(180deg, var(--green) 0%, var(--navy) 100%);
            border-radius: 0 3px 3px 0;
            opacity: 0.35;
        }

        .card:focus-within {
            box-shadow:
                0 1px 3px rgba(13,27,62,0.08),
                0 6px 20px rgba(13,27,62,0.07),
                0 0 0 3px rgba(34,197,94,0.1),
                inset 0 1px 0 rgba(255,255,255,0.9);
        }

        .card:focus-within::before { opacity: 1; }

        /* ════ FORM ════ */
        .fg2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .fg3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }

        .field { margin-bottom: 16px; }
        .field:last-child { margin-bottom: 0; }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 7px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .field label .req { color: var(--green-dark); margin-left: 2px; }

        /* ═══════════════════════════════════════════════
           INPUTS — banking-grade with inner shadow
        ═══════════════════════════════════════════════ */
        .inp, .sel {
            width: 100%;
            height: 48px;
            background: #f8fafd;
            border: 1.5px solid #d1dae8;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 14px;
            font-family: 'Nunito Sans', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            -webkit-appearance: none;
            appearance: none;
            box-shadow: inset 0 2px 4px rgba(13,27,62,0.05);
        }

        .sel {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 13px center;
            padding-right: 34px;
            cursor: pointer;
        }

        .sel option { background: #fff; color: var(--text); }
        .inp::placeholder { color: #bfc8d9; font-weight: 500; }

        .inp:focus, .sel:focus {
            border-color: var(--green);
            background: #fff;
            box-shadow:
                inset 0 2px 4px rgba(13,27,62,0.03),
                0 0 0 3px rgba(34,197,94,0.12);
        }

        .inp.mono {
            font-family: 'DM Mono', monospace;
            letter-spacing: 2px;
            font-size: 15px;
        }

        /* ═══════════════════════════════════════════════
           FIELD VERIFIED ICON — appears on filled fields
        ═══════════════════════════════════════════════ */
        .field-wrap {
            position: relative;
        }

        .field-wrap .field-icon {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .field-wrap .inp:not(:placeholder-shown) ~ .field-icon,
        .field-wrap .sel:not([value=""]) ~ .field-icon { opacity: 1; }

        /* ════ PAYMENT METHOD BAR ════ */
        .pm-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafd 100%);
            border: 1.5px solid var(--green-border);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 22px;
            box-shadow: 0 2px 8px rgba(34,197,94,0.08);
        }

        .pm-left { display: flex; align-items: center; gap: 12px; }

        .pm-icon {
            width: 42px; height: 42px;
            background: var(--white);
            border: 1px solid var(--green-border);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(34,197,94,0.12);
        }

        .pm-title { font-size: 14px; font-weight: 800; color: var(--navy); }
        .pm-sub { font-size: 12px; color: var(--green-dark); font-weight: 600; margin-top: 2px; }

        .brands { display: flex; gap: 5px; }

        .brand {
            height: 26px; padding: 0 10px;
            border-radius: 6px; font-size: 9px;
            font-weight: 900; letter-spacing: 0.5px;
            display: flex; align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }

        .visa { background: #1a1f71; color: #fff; }
        .mc   { background: #eb001b; color: #fff; }
        .amex { background: #2e77bc; color: #fff; }
        .disc { background: #f76b20; color: #fff; }

        /* ═══════════════════════════════════════════════
           CARD NUMBER — visual card chip area
        ═══════════════════════════════════════════════ */
        .card-visual-hint {
            position: relative;
            margin-bottom: 16px;
        }

        .card-visual-hint .inp.mono {
            padding-left: 48px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='17' viewBox='0 0 22 17' fill='none'%3E%3Crect x='0.5' y='0.5' width='21' height='16' rx='2.5' fill='%23E2E8F0' stroke='%23CBD5E1'/%3E%3Crect x='0.5' y='4.5' width='21' height='3' fill='%23CBD5E1'/%3E%3Crect x='3' y='10' width='7' height='4' rx='1' fill='%23F1C40F' opacity='0.7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 14px center;
        }

        /* ════ LOCK NOTE — upgraded ════ */
        .lock-note {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 16px;
            padding: 14px 16px;
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafd 100%);
            border: 1px solid var(--green-border);
            border-radius: 10px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .lock-note-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;
            background: var(--navy);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .lock-note strong { color: var(--navy); font-weight: 800; }

        /* ═══════════════════════════════════════════════
           SECURITY BADGES ROW — below payment card
        ═══════════════════════════════════════════════ */
        .security-badges {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: -8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .sec-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            background: var(--white);
            border: 1px solid #d1dae8;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            color: var(--navy);
            box-shadow: var(--shadow-sm);
        }

        .sec-badge .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--green);
            flex-shrink: 0;
        }

        .sec-badge-icon { font-size: 13px; }

        /* ════ CHECKBOXES ════ */
        .chk-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .chk-row input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 20px; height: 20px;
            min-width: 20px;
            border: 2px solid var(--border);
            border-radius: 6px;
            background: var(--white);
            cursor: pointer;
            transition: all 0.18s;
            position: relative;
            margin-top: 1px;
        }

        .chk-row input[type="checkbox"]:checked {
            background: var(--green);
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
        }

        .chk-row input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            top: 3px; left: 6px;
            width: 5px; height: 9px;
            border: 2px solid #fff;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        .chk-row span { font-size: 13px; color: var(--text-muted); line-height: 1.6; }
        .chk-row a { color: var(--green-dark); text-decoration: none; font-weight: 700; }
        .chk-row a:hover { text-decoration: underline; }

        /* ════ ERROR BOX ════ */
        #payment-errors {
            display: none;
            margin-bottom: 20px;
            background: var(--danger-bg);
            border: 1.5px solid var(--danger-border);
            color: var(--danger);
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13px;
            font-weight: 600;
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.7;
        }

        /* ════ SIDEBAR ════ */
        .sidebar { position: sticky; top: 24px; }

        .order-card {
            background: var(--white);
            border: 1.5px solid #d1dae8;
            border-radius: 22px;
            overflow: hidden;
            box-shadow:
                0 12px 40px rgba(13,27,62,0.1),
                0 4px 12px rgba(13,27,62,0.06),
                inset 0 1px 0 rgba(255,255,255,1);
        }

        /* Sidebar top banner */
        .order-card-banner {
            background: var(--navy);
            padding: 14px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .order-card-banner-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 800;
            color: rgba(255,255,255,0.7);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .order-card-banner-lock {
            width: 24px;
            height: 24px;
            background: var(--green);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .order-card-banner-right {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            letter-spacing: 0.5px;
        }

        .banner-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse-dot 2s infinite;
        }

        .plan-top { padding: 26px 26px 0; }

        .plan-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 100px;
            padding: 5px 13px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .plan-tag.gold-tag {
            background: var(--gold-light);
            border: 1px solid var(--gold-border);
            color: var(--gold);
        }

        .plan-tag.navy-tag {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: var(--navy-mid);
        }

        .plan-name {
            font-size: 28px;
            font-weight: 900;
            color: var(--navy);
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            line-height: 1.1;
        }

        .plan-tagline {
            font-size: 13px;
            color: var(--text-muted);
            font-style: italic;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .plan-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .features { list-style: none; margin-bottom: 4px; }

        .features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
            font-weight: 600;
            color: var(--text-mid);
            line-height: 1.4;
        }

        .features li:last-child { border-bottom: none; }

        .features li .chk {
            width: 20px; height: 20px;
            min-width: 20px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 11px;
            color: #fff;
            font-weight: 900;
            margin-top: 1px;
        }

        .price-block {
            background: var(--green-light);
            border-top: 1.5px solid var(--green-border);
            border-bottom: 1.5px solid var(--green-border);
            padding: 20px 26px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .price-row .free { color: var(--green-dark); font-weight: 800; }
        .price-row .amount-val { color: var(--text-mid); }

        .price-total-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1.5px solid var(--green-border);
        }

        .price-total-label {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
        }

        .price-total-amount {
            font-size: 52px;
            font-weight: 900;
            color: var(--navy);
            letter-spacing: -2px;
            line-height: 1;
        }

        .price-total-amount sup {
            font-size: 22px;
            vertical-align: super;
            line-height: 0;
            font-weight: 800;
        }

        .price-billing-note {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 600;
            margin-top: 6px;
        }

        /* ═══════════════════════════════════════════════
           CTA — banking-grade pay button
        ═══════════════════════════════════════════════ */
        .cta-wrap { padding: 22px 26px 18px; }

        .pay-btn {
            width: 100%;
            height: 58px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
            color: var(--white);
            font-family: 'Nunito Sans', sans-serif;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 0.5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
            box-shadow:
                0 4px 16px rgba(34,197,94,0.35),
                0 2px 6px rgba(0,0,0,0.1),
                inset 0 1px 0 rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
        }

        .pay-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 28px rgba(34,197,94,0.45),
                0 4px 10px rgba(0,0,0,0.12),
                inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .pay-btn:hover::after { opacity: 1; }
        .pay-btn:active { transform: translateY(0); }
        .pay-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* Pulsing ring on pay button */
        .pay-btn-ring {
            position: absolute;
            inset: -3px;
            border-radius: 16px;
            border: 2px solid var(--green);
            opacity: 0;
            animation: btn-ring 3s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes btn-ring {
            0%, 100% { opacity: 0; transform: scale(1); }
            40% { opacity: 0.4; }
            60% { opacity: 0; transform: scale(1.03); }
        }

        .processing {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .spin {
            width: 18px; height: 18px;
            border: 2.5px solid var(--border);
            border-top-color: var(--green);
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
            flex-shrink: 0;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .success-msg {
            display: none;
            background: var(--green-light);
            border: 1.5px solid var(--green-border);
            border-radius: 12px;
            padding: 14px;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: var(--green-dark);
            margin-top: 10px;
        }

        .seals {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-light);
            font-weight: 600;
            padding: 0 26px 14px;
        }

        .seal-sep { opacity: 0.3; }

        .footer-note {
            text-align: center;
            font-size: 11px;
            color: var(--text-light);
            line-height: 1.55;
            font-weight: 500;
            padding: 0 22px 22px;
        }

        /* ═══════════════════════════════════════════════
           ENCRYPTION INDICATOR — animated in payment card
        ═══════════════════════════════════════════════ */
        .encryption-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f8fafd;
            border: 1px solid #d1dae8;
            border-radius: 10px;
            margin-bottom: 18px;
        }

        .enc-bar {
            flex: 1;
            height: 4px;
            background: #e2e8f0;
            border-radius: 100px;
            overflow: hidden;
        }

        .enc-fill {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, var(--green), #16a34a);
            border-radius: 100px;
            animation: enc-pulse 3s ease-in-out infinite;
            transform-origin: left;
        }

        @keyframes enc-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .enc-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--green-dark);
            white-space: nowrap;
        }

        .enc-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            flex-shrink: 0;
            animation: pulse-dot 2s infinite;
        }

        /* ═══════════════════════════════════════════════
           AGREEMENTS CARD — enhanced
        ═══════════════════════════════════════════════ */
        .agreement-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #f0fdf4, #f8fafd);
            border: 1px solid var(--green-border);
            border-radius: 10px;
            margin-bottom: 18px;
        }

        .agreement-header-icon {
            font-size: 18px;
        }

        .agreement-header-text {
            font-size: 12px;
            font-weight: 700;
            color: var(--navy);
        }

        .agreement-header-sub {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 1px;
        }

        /* ════ RESPONSIVE ════ */
        @media (max-width: 960px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { position: static; }
        }

        @media (max-width: 640px) {
            .fg2, .fg3 { grid-template-columns: 1fr; }
            .page-wrap { padding: 24px 16px 60px; }
            .card { padding: 20px 16px; }
            .brands { display: none; }
            .hero h1 { font-size: 38px; }
            .score-bad, .score-good { font-size: 38px; }
            .security-steps-inner { gap: 0; overflow-x: auto; }
            .sec-step { font-size: 11px; padding: 12px 16px 12px 0; }
        }
    </style>
</head>
<body>

{{--
PLAN DATA — passed via URL query param from index.blade.php
Expected param:
plan   = monthly | onetime | couples | vip   (see config/plans.php)
Fallback = config('plans.default') if nothing/invalid passed
--}}




<div class="page-wrap">

    {{-- ════ HERO ════ --}}
    <div class="hero">
        <div class="hero-tags">
            <span class="hero-tag green">FCRA Certified Experts</span>
            <span class="hero-tag gold">Clear Agreements</span>
            <span class="hero-tag green">Secure Checkout</span>
        </div>
        <h1>
            One Step Away<br>
            <span class="green-line">From Better Credit.</span>
        </h1>
        <div class="score-strip">
            <span class="score-bad">480</span>
            <span class="score-arrow">→</span>
            <span class="score-good">850</span>
            <div class="score-divider"></div>
            <div>
                <div class="score-stat-label">FICO Perfect</div>
                <div class="score-stat-sub">Avg. client journey</div>
            </div>
        </div>
        <div class="trust-row">
            <div class="trust-pill">🏛️ FCRA Certified</div>
            <div class="trust-pill">⚡ Same-Day Start</div>
            <div class="trust-pill">⭐ 4.9 / 5 Rating</div>
            <div class="trust-pill">🔒 256-Bit SSL</div>
        </div>
    </div>

    <div class="layout">

        {{-- ════ LEFT — FORM ════ --}}
        <div class="left-col">

            <div id="payment-errors"></div>

            <form id="paymentForm" novalidate>

                {{-- Personal Info --}}
                <div class="section-divider">
                    <span class="section-label">👤 Personal Information</span>
                    <div class="section-divider-line"></div>
                </div>

                <div class="card">
                    <div class="fg2">
                        <div class="field">
                            <label>First Name <span class="req">*</span></label>
                            <input class="inp" type="text" id="first_name" name="first_name" placeholder="John">
                        </div>
                        <div class="field">
                            <label>Last Name <span class="req">*</span></label>
                            <input class="inp" type="text" id="last_name" name="last_name" placeholder="Doe">
                        </div>
                    </div>
                    <div class="fg2">
                        <div class="field">
                            <label>Email Address <span class="req">*</span></label>
                            <input class="inp" type="email" id="email" name="email" placeholder="john@example.com">
                        </div>
                        <div class="field">
                            <label>Phone Number <span class="req">*</span></label>
                            <input class="inp" type="text" id="phone" name="phone" placeholder="(555) 000-0000">
                        </div>
                    </div>
                    <div class="field">
                        <label>Street Address <span class="req">*</span></label>
                        <input class="inp" type="text" id="address" name="address" placeholder="123 Main Street">
                    </div>
                    <div class="fg3">
                        <div class="field">
                            <label>City <span class="req">*</span></label>
                            <input class="inp" type="text" id="city" name="city" placeholder="New York">
                        </div>
                        <div class="field">
                            <label>State <span class="req">*</span></label>
                            <select class="sel" id="state" name="state">
                                <option value="">Select</option>
                                <option value="AL">Alabama</option><option value="AK">Alaska</option>
                                <option value="AZ">Arizona</option><option value="AR">Arkansas</option>
                                <option value="CA">California</option><option value="CO">Colorado</option>
                                <option value="CT">Connecticut</option><option value="DE">Delaware</option>
                                <option value="FL">Florida</option><option value="GA">Georgia</option>
                                <option value="HI">Hawaii</option><option value="ID">Idaho</option>
                                <option value="IL">Illinois</option><option value="IN">Indiana</option>
                                <option value="IA">Iowa</option><option value="KS">Kansas</option>
                                <option value="KY">Kentucky</option><option value="LA">Louisiana</option>
                                <option value="ME">Maine</option><option value="MD">Maryland</option>
                                <option value="MA">Massachusetts</option><option value="MI">Michigan</option>
                                <option value="MN">Minnesota</option><option value="MS">Mississippi</option>
                                <option value="MO">Missouri</option><option value="MT">Montana</option>
                                <option value="NE">Nebraska</option><option value="NV">Nevada</option>
                                <option value="NH">New Hampshire</option><option value="NJ">New Jersey</option>
                                <option value="NM">New Mexico</option><option value="NY">New York</option>
                                <option value="NC">North Carolina</option><option value="ND">North Dakota</option>
                                <option value="OH">Ohio</option><option value="OK">Oklahoma</option>
                                <option value="OR">Oregon</option><option value="PA">Pennsylvania</option>
                                <option value="RI">Rhode Island</option><option value="SC">South Carolina</option>
                                <option value="SD">South Dakota</option><option value="TN">Tennessee</option>
                                <option value="TX">Texas</option><option value="UT">Utah</option>
                                <option value="VT">Vermont</option><option value="VA">Virginia</option>
                                <option value="WA">Washington</option><option value="WV">West Virginia</option>
                                <option value="WI">Wisconsin</option><option value="WY">Wyoming</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>ZIP Code <span class="req">*</span></label>
                            <input class="inp" type="text" id="zip" name="zip" placeholder="10001">
                        </div>
                    </div>
                </div>

                {{-- Payment --}}
                <div class="section-divider">
                    <span class="section-label">💳 Payment Method</span>
                    <div class="section-divider-line"></div>
                </div>

                {{-- Encryption status indicator --}}
                <div class="encryption-status">
                    <span class="enc-dot"></span>
                    <div class="enc-bar"><div class="enc-fill"></div></div>
                    <span class="enc-label">🔒 End-to-End Encrypted</span>
                </div>

                <div class="card">
                    <div class="pm-bar">
                        <div class="pm-left">
                            <div class="pm-icon">💳</div>
                            <div>
                                <div class="pm-title">Credit / Debit Card</div>
                                <div class="pm-sub">Secure · Tokenized · Instant</div>
                            </div>
                        </div>
                        <div class="brands">
                            <span class="brand visa">VISA</span>
                            <span class="brand mc">MC</span>
                            <span class="brand amex">AMEX</span>
                            <span class="brand disc">DISC</span>
                        </div>
                    </div>

                    <div class="field">
                        <label>Cardholder Name <span class="req">*</span></label>
                        <input class="inp" type="text" id="cardName" name="cardName" placeholder="John Doe">
                    </div>

                    <div class="field card-visual-hint">
                        <label>Card Number <span class="req">*</span></label>
                        <input class="inp mono" type="text" id="cardNumber" name="cardNumber" placeholder="•••• •••• •••• ••••" autocomplete="cc-number">
                    </div>

                    <div class="fg3">
                        <div class="field">
                            <label>Month <span class="req">*</span></label>
                            <select class="sel" id="expMonth" name="expMonth">
                                <option value="">MM</option>
                                <option value="01">01</option><option value="02">02</option>
                                <option value="03">03</option><option value="04">04</option>
                                <option value="05">05</option><option value="06">06</option>
                                <option value="07">07</option><option value="08">08</option>
                                <option value="09">09</option><option value="10">10</option>
                                <option value="11">11</option><option value="12">12</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Year <span class="req">*</span></label>
                            <select class="sel" id="expYear" name="expYear">
                                <option value="">YYYY</option>
                                <option value="2026">2026</option><option value="2027">2027</option>
                                <option value="2028">2028</option><option value="2029">2029</option>
                                <option value="2030">2030</option><option value="2031">2031</option>
                                <option value="2032">2032</option><option value="2033">2033</option>
                                <option value="2034">2034</option><option value="2035">2035</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>CVV <span class="req">*</span></label>
                            <input class="inp mono" type="text" id="cardCode" name="cardCode" placeholder="•••" autocomplete="cc-csc">
                        </div>
                    </div>

                    <div class="lock-note">
                        <div class="lock-note-icon">🔒</div>
                        <span>Secure payment powered by Authorize.Net. Your information is protected and encrypted.</span>
                    </div>
                </div>

                {{-- Security badges row --}}
                <div class="security-badges">
                    <div class="sec-badge"><span class="badge-dot"></span><span class="sec-badge-icon">🛡️</span> PCI DSS Level 1</div>
                    <div class="sec-badge"><span class="badge-dot"></span><span class="sec-badge-icon">🔐</span> AES-256 Encrypted</div>
                    <div class="sec-badge"><span class="badge-dot"></span><span class="sec-badge-icon">🏦</span> Bank-Grade Security</div>
                </div>

                {{-- Terms --}}
                <div class="section-divider">
                    <span class="section-label">📋 Agreements</span>
                    <div class="section-divider-line"></div>
                </div>

                <div class="card">
                    <div class="agreement-header">
                        <div class="agreement-header-icon">📜</div>
                        <div>
                            <div class="agreement-header-text">Review & Accept Your Agreements</div>
                            <div class="agreement-header-sub">Please read and confirm each item before completing your purchase.</div>
                        </div>
                    </div>
                    <div class="chk-row">
                        <input type="checkbox" id="agree_terms" name="agree_terms">
                        <span>I accept the <a href="https://850ficoclub.com/terms-of-service" target="_blank">Terms of Service</a> and understand the credit repair process <strong style="color:var(--green-dark)">*</strong></span>
                    </div>
                    <div class="chk-row">
                        <input type="checkbox" id="agree_privacy" name="agree_privacy">
                        <span>I accept the <a href="https://850ficoclub.com/privacy-policy" target="_blank">Privacy Policy</a> and consent to data processing <strong style="color:var(--green-dark)">*</strong></span>
                    </div>
                    <div class="chk-row">
                        <input type="checkbox" id="marketing_opt_in" name="marketing_opt_in">
                        <span>I'd like to receive credit improvement tips and exclusive member offers</span>
                    </div>
                </div>

                <input type="hidden" id="dataDescriptor" name="dataDescriptor">
                <input type="hidden" id="dataValue"      name="dataValue">
                {{-- Plan key sent to backend --}}
                <input type="hidden" id="selected_plan"  name="selected_plan" value="">
                <input type="hidden" id="referral_code" name="referral_code" value="{{ session('referral_code', '') }}">
            </form>
        </div>

        {{-- ════ SIDEBAR ════ --}}
        <div class="sidebar">
            <div class="order-card" id="orderCard">



                <div class="plan-top">
                    <div class="plan-tag gold-tag" id="planTag">✦ YOUR SELECTED PLAN</div>
            <div class="plan-name"    id="planName">&nbsp;</div>
        <div class="plan-tagline" id="planTagline">&nbsp;</div>
        <div class="plan-desc" id="planDesc">&nbsp;</div>

                    <ul class="features" id="planFeatures">
                        {{-- Filled by JS --}}
                    </ul>
                </div>

                <div class="price-block">
                    <div class="price-row">
                <span id="priceRowLabel">&nbsp;</span>
                <span class="amount-val" id="priceRowAmount">&nbsp;</span>
                    </div>
                    <div class="price-row">
                        <span>Setup fee</span>
                        <span class="free">FREE</span>
                    </div>
                    <div class="price-total-row">
                        <div class="price-total-label">Total Today</div>
                        <div class="price-total-amount"><sup>$</sup><span id="priceBig">&nbsp;</span></div>
                    </div>
                    <div class="price-billing-note" id="priceBillingNote">&nbsp;</div>
                </div>

                <div class="cta-wrap">
                    <button type="button" class="pay-btn" id="payNowButton">
                        <span class="pay-btn-ring"></span>
                        🔒&nbsp; Complete Secure Checkout
                    </button>

                    <div class="processing" id="processingText">
                        <div class="spin"></div>
                        Processing your payment...
                    </div>

                    <div class="success-msg" id="successBox"></div>
                </div>

                <div class="seals">
                    <span>🔒 SSL Secured</span><span class="seal-sep">|</span>
                    <span>🛡️ PCI Compliant</span><span class="seal-sep">|</span>
                    <span>✓ Authorize.Net</span>
                </div>

                <div class="footer-note">
                    By completing this purchase you agree to our Terms of Service.<br>
                    A confirmation email is sent immediately after payment.
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// ════════════════════════════════════════════════
// PLAN DEFINITIONS
// Mirrors config/plans.php exactly — same plans as the pricing page
// ════════════════════════════════════════════════
// Built from config/plans.php — single source of truth.
@php
    $jsPlans = [];
    foreach (config('plans.plans', []) as $k => $p) {
        $jsPlans[$k] = [
            'tag'         => '✦ ' . $p['tag'],
            'tagClass'    => $p['tag_class'],
            'name'        => $p['label'],
            'tagline'     => $p['tagline'],
            'desc'        => $p['desc'],
            'displayAmt'  => '$' . number_format((float) $p['amount'], 2),
            'priceBig'    => $p['price_big'],
            'amount'      => $p['amount'],
            'recurring'   => $p['recurring'],
            'compareAt'   => $p['compare_at'] ? '$' . rtrim(rtrim(number_format((float) $p['compare_at'], 2), '0'), '.') : null,
            'save'        => $p['save'],
            'billingNote' => $p['billing_note'],
            'isCouples'   => (bool) ($p['is_couples'] ?? false),
            'features'    => array_values($p['features']),
        ];
    }
    $defaultPlan = config('plans.default', 'onetime');
@endphp
var PLANS = {!! json_encode($jsPlans, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
var DEFAULT_PLAN = {!! json_encode($defaultPlan) !!};

        // ════════════════════════════════════════════════
        // READ PLAN FROM URL PARAM
        // e.g. /accept-checkout?plan=onetime
        // ════════════════════════════════════════════════
        function getUrlParam(key) {
            var params = new URLSearchParams(window.location.search);
            return params.get(key);
        }
        
        var planKey = getUrlParam('plan') || DEFAULT_PLAN;
        if (!PLANS[planKey]) planKey = DEFAULT_PLAN; // safety fallback

        var plan = PLANS[planKey];

    // ════ POPULATE SIDEBAR ════
    document.getElementById('planTag').className    = 'plan-tag ' + plan.tagClass;
    document.getElementById('planTag').textContent  = plan.tag;
    document.getElementById('planName').textContent = plan.name;
    document.getElementById('planTagline').textContent = plan.tagline;
    document.getElementById('planDesc').textContent = plan.desc;
    document.getElementById('priceRowLabel').textContent  = plan.name;
    document.getElementById('priceRowAmount').textContent = plan.displayAmt;
    document.getElementById('priceBig').textContent       = plan.priceBig;
    document.getElementById('priceBillingNote').textContent = plan.billingNote;
    document.getElementById('selected_plan').value = planKey;

    // Strike-through "was" price + savings badge (only when applicable)
    (function () {
        var rowAmt = document.getElementById('priceRowAmount');
        if (plan.compareAt) {
            var was = document.createElement('span');
            was.textContent = plan.compareAt + '  ';
            was.style.cssText = 'text-decoration:line-through;color:var(--text-light);font-weight:600;margin-right:6px;';
            rowAmt.parentNode.insertBefore(was, rowAmt);
        }
        if (plan.save) {
            var tot = document.querySelector('.price-total-label');
            if (tot) {
                var s = document.createElement('span');
                s.textContent = 'SAVE $' + plan.save;
                s.style.cssText = 'display:inline-block;margin-left:8px;background:var(--green);color:#fff;font-size:10px;font-weight:800;letter-spacing:.5px;padding:3px 9px;border-radius:100px;vertical-align:middle;';
                tot.appendChild(s);
            }
        }
    })();

    // Features
    var ul = document.getElementById('planFeatures');
    ul.innerHTML = '';
    plan.features.forEach(function (f) {
        var li = document.createElement('li');
        li.innerHTML = '<span class="chk">✓</span> ' + f;
        ul.appendChild(li);
    });

    // ════════════════════════════════════════════════
    // PAYMENT LOGIC — UNCHANGED
    // ════════════════════════════════════════════════
    document.getElementById('payNowButton').addEventListener('click', function () {
        clearErrors();
        var errs = validateForm();
        if (errs.length > 0) { showErrors(errs); return; }
        tokenizeCard();
    });

    function tokenizeCard() {
        var authData = {
            clientKey:  "{{ config('services.authorize_net.public_client_key') }}",
            apiLoginID: "{{ config('services.authorize_net.api_login_id') }}"
        };
        var cardData = {
            cardNumber: document.getElementById('cardNumber').value.replace(/\s+/g, ''),
            month:      document.getElementById('expMonth').value,
            year:       document.getElementById('expYear').value,
            cardCode:   document.getElementById('cardCode').value
        };
        var btn = document.getElementById('payNowButton');
        btn.disabled = true;
        btn.style.display = 'none';
        document.getElementById('processingText').style.display = 'flex';
        Accept.dispatchData({ authData: authData, cardData: cardData }, responseHandler);
    }

    function responseHandler(response) {
        if (response.messages.resultCode === "Error") {
            var errors = [];
            for (var i = 0; i < response.messages.message.length; i++) {
                errors.push(response.messages.message[i].code + ': ' + response.messages.message[i].text);
            }
            showErrors(errors);
            resetButton();
            return;
        }
        document.getElementById('dataDescriptor').value = response.opaqueData.dataDescriptor;
        document.getElementById('dataValue').value      = response.opaqueData.dataValue;
        submitPayment();
    }

    function submitPayment() {
        fetch("/accept-payment", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept":       "application/json"
            },
            body: JSON.stringify({
                dataDescriptor:   document.getElementById('dataDescriptor').value,
                dataValue:        document.getElementById('dataValue').value,
                first_name:       document.getElementById('first_name').value,
                last_name:        document.getElementById('last_name').value,
                email:            document.getElementById('email').value,
                phone:            document.getElementById('phone').value,
                address:          document.getElementById('address').value,
                city:             document.getElementById('city').value,
                state:            document.getElementById('state').value,
                zip:              document.getElementById('zip').value,
                cardName:         document.getElementById('cardName').value,
                selected_plan:    document.getElementById('selected_plan').value,
                agree_terms:      document.getElementById('agree_terms').checked   ? 1 : 0,
                agree_privacy:    document.getElementById('agree_privacy').checked ? 1 : 0,
                marketing_opt_in: document.getElementById('marketing_opt_in').checked ? 1 : 0,
                referral_code:    document.getElementById('referral_code').value || ''
            })
        })
        .then(async function (res) {
            var data = await res.json();
            if (!res.ok) { data._httpStatus = res.status; throw data; }
            return data;
        })
        .then(function (data) {
            document.getElementById('processingText').style.display = 'none';
            var sb = document.getElementById('successBox');
            sb.style.display = 'block';
            sb.innerHTML = '✅ Payment successful! Redirecting you now...';
            setTimeout(function () { window.location.href = data.redirect; }, 1400);
        })
        .catch(function (error) {
            resetButton();
            console.log("FULL ERROR:", JSON.stringify(error, null, 2));
            var messages = [];
            if (error && error.message) messages.push(error.message);
            if (error && error.transaction_errors && error.transaction_errors.length) {
                error.transaction_errors.forEach(function (e) {
                    messages.push('Transaction error ' + e.errorCode + ': ' + e.errorText);
                });
            }
            if (error && error.response) {
                var txe  = error.response.transactionResponse && error.response.transactionResponse.errors;
                var apim = error.response.messages && error.response.messages.message;
                if (txe  && txe.length)  txe.forEach(function (e)  { messages.push('TX '  + e.errorCode + ': ' + e.errorText); });
                if (apim && apim.length) apim.forEach(function (m) { messages.push('API ' + m.code + ': ' + m.text); });
                if (!txe || !txe.length) messages.push('Raw: ' + JSON.stringify(error.response, null, 2));
            }
            if (messages.length === 0) {
                messages.push(error && error.name === 'TypeError'
                    ? 'Network error — check HTTPS / ngrok connection.'
                    : 'Unknown error. Open F12 console for details.');
            }
            showErrors(messages);
        });
    }

    function resetButton() {
        var btn = document.getElementById('payNowButton');
        btn.disabled = false;
        btn.style.display = 'flex';
        document.getElementById('processingText').style.display = 'none';
    }

    function validateForm() {
        var errors = [];
        var fields = [
            { id: 'first_name', label: 'First Name' }, { id: 'last_name',  label: 'Last Name' },
            { id: 'email',      label: 'Email Address' }, { id: 'phone',   label: 'Phone Number' },
            { id: 'address',    label: 'Address' },       { id: 'city',    label: 'City' },
            { id: 'state',      label: 'State' },         { id: 'zip',     label: 'ZIP Code' },
            { id: 'cardName',   label: 'Cardholder Name' }, { id: 'cardNumber', label: 'Card Number' },
            { id: 'expMonth',   label: 'Expiration Month' }, { id: 'expYear',  label: 'Expiration Year' },
            { id: 'cardCode',   label: 'CVV' }
        ];
        fields.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (!el || !el.value || !el.value.trim()) errors.push(f.label + ' is required.');
        });
        if (!document.getElementById('agree_terms').checked)   errors.push('You must accept the Terms of Service.');
        if (!document.getElementById('agree_privacy').checked) errors.push('You must accept the Privacy Policy.');
        return errors;
    }

    function showErrors(errors) {
        var box = document.getElementById('payment-errors');
        box.innerHTML  = '⚠️  ' + errors.join('<br>');
        box.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function clearErrors() {
        var box = document.getElementById('payment-errors');
        box.innerHTML = ''; box.style.display = 'none';
    }

    // Formatters
    document.getElementById('cardNumber').addEventListener('input', function (e) {
        var v = e.target.value.replace(/\D/g, '').substring(0, 16);
        e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
    });
    document.getElementById('cardCode').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
    });
    document.getElementById('zip').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^a-zA-Z0-9 -]/g, '').substring(0, 10);
    });
</script>

</body>
</html>