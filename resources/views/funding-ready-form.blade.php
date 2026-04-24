<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Business Funding | 850 FICO Club</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800;900&family=Sora:wght@300;400;600;700;800&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* ── ToS HEADER / FOOTER CSS VARS & SHARED ── */
:root {
  --green:#22C55E;--green-d:#16A34A;--green-lt:#DCFCE7;
  --gold:#F5A623;--gold-lt:#FEF3C7;
  --red:#EF4444;--red-lt:#FEE2E2;
  --navy:#0F2044;--navy2:#1A3056;
  --blue:#2563EB;--slate:#475569;--muted:#94A3B8;
  --line:#E8EDF5;--bg:#FFFFFF;--bg2:#F8FAFD;--bg3:#F1F5FF;
  --r:6px;
  --sh:0 4px 24px rgba(15,32,68,.07);
  --sh2:0 12px 48px rgba(15,32,68,.12);
  --sh3:0 24px 80px rgba(15,32,68,.18);
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;font-size:16px;}
body{background:#fff;color:var(--navy);font-family:'Manrope',sans-serif;overflow-x:hidden;}
::-webkit-scrollbar{width:4px;}
::-webkit-scrollbar-thumb{background:var(--green);border-radius:2px;}

.wrap{max-width:1240px;margin:0 auto;padding:0 40px;}
@media(max-width:768px){.wrap{padding:0 20px;}}

/* TICKER */
.ticker-bar{background:var(--navy);padding:10px 0;overflow:hidden;position:fixed;top:0;left:0;right:0;z-index:1002;height:38px;}
.ticker-inner{display:inline-flex;animation:ticker 28s linear infinite;white-space:nowrap;}
.ticker-item{font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.6);padding:0 28px;}
.ticker-dot{color:var(--green);padding:0 4px;align-self:center;}
@keyframes ticker{from{transform:translateX(0);}to{transform:translateX(-50%);}}
@media(max-width:768px){.ticker-bar{height:32px;}.ticker-item{font-size:10px!important;padding:0 16px!important;}}

/* NAV */
nav{position:fixed;top:38px;left:0;right:0;z-index:1000;height:86px;background:rgba(255,255,255,.92);backdrop-filter:blur(28px) saturate(180%);border-bottom:1px solid rgba(232,237,245,.6);transition:all .3s;}
nav.scrolled{height:72px;box-shadow:0 8px 40px rgba(15,32,68,.1);}
.nav-wrap{height:100%;display:flex;align-items:center;justify-content:space-between;max-width:1280px;margin:0 auto;padding:0 48px;}
.nav-logo{display:flex;align-items:center;gap:12px;text-decoration:none;}
.logo-badge{display:flex;flex-direction:column;align-items:flex-start;line-height:1;}
.logo-850{font-family:'Sora',sans-serif;font-size:28px;font-weight:900;letter-spacing:-1px;}
.logo-850 .g{color:var(--green);}.logo-850 .y{color:var(--gold);}.logo-850 .r{color:var(--red);}
.logo-sub{font-size:10px;font-weight:600;letter-spacing:1.2px;color:var(--muted);margin-top:3px;}
.nav-links{display:flex;gap:4px;list-style:none;align-items:center;}
.nav-links a{font-size:14px;font-weight:700;color:var(--slate);text-decoration:none;padding:9px 15px;border-radius:100px;transition:all .2s;position:relative;}
.nav-links a:not(.nav-cta-pill)::after{content:'';position:absolute;bottom:2px;left:50%;right:50%;height:2px;background:var(--green);border-radius:2px;transition:left .25s,right .25s;}
.nav-links a:not(.nav-cta-pill):hover::after{left:14px;right:14px;}
.nav-links a:hover{color:var(--navy);}
.nav-cta-pill{background:var(--green)!important;color:#fff!important;margin-left:8px;box-shadow:0 4px 16px rgba(34,197,94,.35);font-size:14px!important;border-radius:100px!important;}
.nav-cta-pill:hover{background:var(--green-d)!important;transform:translateY(-2px)!important;}

/* HAMBURGER */
.hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:6px;border:none;background:none;}
.hamburger span{display:block;width:22px;height:2px;background:var(--navy);border-radius:2px;transition:all .3s;}
.hamburger.active span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.hamburger.active span:nth-child(2){opacity:0;transform:scaleX(0);}
.hamburger.active span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}

/* MOBILE MENU */
.mob-menu{display:none;position:fixed;top:124px;left:0;right:0;bottom:0;background:#fff;z-index:999;padding:32px 24px;flex-direction:column;gap:4px;border-top:1px solid var(--line);overflow-y:auto;}
.mob-menu.open{display:flex;}
.mob-menu a{font-size:18px;font-weight:700;color:var(--navy);text-decoration:none;padding:16px 0;border-bottom:1px solid var(--line);transition:color .2s;}
.mob-menu a:hover{color:var(--green);}
.mob-menu .mob-cta{background:var(--green);color:#fff;text-align:center;padding:18px;border-radius:100px;border-bottom:none;margin-top:16px;}

@media(max-width:900px){.nav-links{display:none;}.hamburger{display:flex;}.nav-wrap{padding:0 24px;}}
@media(max-width:768px){nav{height:70px!important;top:32px!important;}nav.scrolled{height:60px!important;}.mob-menu{top:102px!important;}.logo-850{font-size:22px!important;}.logo-sub{font-size:9px!important;}}

/* ── FOOTER CSS ── */
footer{background:var(--navy);padding:72px 0 36px;}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:48px;margin-bottom:52px;}
.fl-850{font-family:'Sora',sans-serif;font-size:28px;font-weight:900;letter-spacing:-1px;}
.fl-g{color:var(--green);}.fl-y{color:var(--gold);}.fl-r{color:var(--red);}
.fl-tagline{font-size:10px;color:white;letter-spacing:1.2px;font-weight:600;margin-top:3px;}
.footer-brand>p{font-size:13px;color:white;line-height:1.75;max-width:260px;margin:14px 0 22px;}
.footer-socials{display:flex;gap:10px;}
.fsoc{width:36px;height:36px;border-radius:8px;border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;transition:all .25s;text-decoration:none;}
.fsoc:hover{transform:translateY(-2px);filter:brightness(1.15);}
.fsoc.fb{background:#1877F2;border-color:#1877F2;}
.fsoc.ig{background:radial-gradient(circle at 30% 107%,#fdf497 0%,#fdf497 5%,#fd5949 45%,#d6249f 60%,#285AEB 90%);border-color:transparent;}
.fsoc.tt{background:#000;border-color:#333;}
.footer-col-title{font-size:10px;font-weight:900;letter-spacing:2.5px;text-transform:uppercase;color:white;margin-bottom:18px;}
.footer-links{list-style:none;display:flex;flex-direction:column;gap:10px;}
.footer-links a{font-size:13px;color:white;text-decoration:none;font-weight:600;transition:color .2s;}
.footer-links a:hover{color:var(--green);}
.footer-bottom{border-top:1px solid rgba(255,255,255,.05);padding-top:26px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}
.footer-copy{font-size:12px;color:white;font-weight:600;}
.footer-legal{display:flex;gap:20px;}
.footer-legal a{font-size:12px;color:white;text-decoration:none;font-weight:600;}
.footer-legal a:hover{color:var(--green);}
@media(max-width:860px){.footer-grid{grid-template-columns:1fr 1fr;gap:32px;}footer{padding:48px 0 28px;}.footer-brand{grid-column:span 2;}}
@media(max-width:480px){.footer-grid{grid-template-columns:1fr;}.footer-brand{grid-column:span 1;}.fl-850{font-size:22px!important;}.footer-bottom{flex-direction:column;align-items:flex-start;gap:10px;}.footer-legal{flex-wrap:wrap;gap:12px;}.footer-copy{font-size:11px;}.footer-legal a{font-size:11px;}.fsoc{width:32px!important;height:32px!important;font-size:12px!important;}footer{padding:44px 0 24px!important;}}

/* ── FUNDING PAGE ORIGINAL CSS (untouched) ── */
            *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
            html{scroll-behavior:smooth}
            body{font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;background:var(--white);color:var(--navy);overflow-x:hidden;-webkit-font-smoothing:antialiased}

            /* LOGO (funding page variant — kept for any inline references) */
            .logo{text-decoration:none;display:inline-flex;align-items:baseline;gap:0;line-height:1}
            .logo-850f{font-weight:900;font-size:1.55rem;color:#F97316;letter-spacing:-0.02em}
            .logo-ficof{font-weight:900;font-size:1.55rem;color:#22C55E;letter-spacing:-0.02em;margin:0 6px}
            .logo-clubf{font-weight:900;font-size:1.55rem;color:#EF4444;letter-spacing:-0.02em}
            .logo-tagline{font-size:0.7rem;color:var(--gray-500);font-weight:400;letter-spacing:0.02em;display:block;margin-top:3px}

            /* TICKER (funding page variant — hidden, ToS ticker is used instead) */
            .funding-ticker-bar{display:none;}

            /* HERO */
            .hero{background:#fff;padding:90px 6% 100px;position:relative;overflow:hidden;}
            /* Offset hero for the new fixed nav height (ticker 38px + nav 86px = 124px) */
            .hero{padding-top:164px;}
            .hero::before{content:'';position:absolute;top:-160px;right:-160px;width:700px;height:700px;background:radial-gradient(circle,rgba(34,197,94,0.06) 0%,transparent 65%);pointer-events:none}
            .hero-inner{max-width:1280px;margin:0 auto;display:grid;grid-template-columns:52% 1fr;gap:72px;align-items:center}
            .hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:100px;padding:7px 16px;font-size:0.75rem;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--green);margin-bottom:24px}
            .hero-badge .pulse{width:8px;height:8px;border-radius:50%;background:var(--green);animation:pulse 2s ease-in-out infinite}
            @keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.4;transform:scale(1.5)}}
            .hero h1{font-size:clamp(2.4rem,4.2vw,3.8rem);font-weight:800;line-height:1.08;letter-spacing:-0.03em;color:var(--navy);margin-bottom:22px}
            .hero h1 .accent{color:var(--green)}
            .hero-sub{font-size:1.08rem;color:#64748b;line-height:1.7;margin-bottom:30px;max-width:500px}
            .hero-pills{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:36px}
            .pill{display:flex;align-items:center;gap:7px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:100px;padding:8px 16px;font-size:0.85rem;font-weight:500;color:#475569}
            .pill .ck{color:var(--green);font-weight:700}
            .hero-cta{display:inline-flex;align-items:center;gap:10px;background:var(--green);color:white;font-weight:700;font-size:1.05rem;padding:17px 34px;border-radius:10px;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 20px rgba(34,197,94,0.28)}
            .hero-cta:hover{background:var(--green-d);transform:translateY(-2px);box-shadow:0 8px 28px rgba(34,197,94,0.38)}
            .hero-cta svg{transition:transform 0.2s}
            .hero-cta:hover svg{transform:translateX(4px)}

            /* STATS CARD */
            .hero-right{position:relative}
            .stats-card{background:var(--navy);border-radius:22px;padding:42px;color:white;position:relative;overflow:hidden}
            .stats-card::before{content:'';position:absolute;top:-80px;right:-80px;width:280px;height:280px;background:radial-gradient(circle,rgba(34,197,94,0.18) 0%,transparent 70%)}
            .sc-label{font-size:0.75rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--green);margin-bottom:22px}
            .sc-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:26px}
            .sc-num{font-size:2.6rem;font-weight:800;color:white;line-height:1}
            .sc-num span{color:var(--green)}
            .sc-desc{font-size:0.82rem;color:rgba(255,255,255,0.48);margin-top:5px}
            .sc-divider{height:1px;background:rgba(255,255,255,0.1);margin-bottom:22px}
            .sc-range-label{font-size:0.82rem;color:rgba(255,255,255,0.48);margin-bottom:9px}
            .sc-range{font-size:1.9rem;font-weight:800;color:white}
            .sc-range span{color:var(--green)}
            .sc-footer{margin-top:22px;display:flex;align-items:center;gap:10px;background:rgba(255,255,255,0.06);border-radius:10px;padding:14px 16px}
            .sc-footer-text{font-size:0.84rem;color:rgba(255,255,255,0.6);line-height:1.5}
            .sc-footer-text strong{color:white}
            .float-badge{position:absolute;bottom:-18px;left:-18px;background:var(--green);border-radius:14px;padding:14px 20px;color:white;box-shadow:0 8px 26px rgba(34,197,94,0.36);display:flex;align-items:center;gap:11px}
            .fb-num{font-size:1.65rem;font-weight:800;line-height:1}
            .fb-text{font-size:0.78rem;color:rgba(255,255,255,0.85);line-height:1.4}

            /* PROOF BAR */
            .proof-bar{background:#f8fafc;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;padding:32px 6%}
            .proof-inner{max-width:1280px;margin:0 auto;display:flex;align-items:center;justify-content:center;gap:48px;flex-wrap:wrap}
            .pi{text-align:center}
            .pi-num{font-size:2rem;font-weight:800;color:var(--navy);line-height:1}
            .pi-num span{color:var(--green)}
            .pi-desc{font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin-top:4px}
            .pdiv{width:1px;height:46px;background:#e2e8f0}

            /* HOW IT WORKS */
            .how-section{padding:110px 6%;background:#fff}
            .how-inner{max-width:1280px;margin:0 auto}
            .section-header{text-align:center;margin-bottom:70px}
            .sec-tag{display:inline-flex;align-items:center;gap:7px;background:rgba(34,197,94,0.07);border:1px solid rgba(34,197,94,0.22);border-radius:100px;padding:7px 18px;font-size:0.75rem;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--green);margin-bottom:18px}
            .section-header h2{font-size:clamp(2rem,3.5vw,2.9rem);font-weight:800;letter-spacing:-0.025em;color:var(--navy);line-height:1.15;margin-bottom:14px}
            .section-header p{font-size:1.02rem;color:#64748b;max-width:520px;margin:0 auto;line-height:1.65}
            .steps-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:0;position:relative}
            .steps-grid::before{content:'';position:absolute;top:38px;left:10%;right:10%;height:2px;background:linear-gradient(90deg,var(--green),var(--green-d));z-index:0}
            .step-card{text-align:center;position:relative;z-index:1;padding:0 16px}
            .step-num-circle{width:76px;height:76px;border-radius:50%;background:white;border:2.5px solid var(--green);display:flex;align-items:center;justify-content:center;margin:0 auto 22px;font-weight:800;font-size:1.25rem;color:var(--navy);box-shadow:0 0 0 6px rgba(34,197,94,0.1)}
            .step-card h3{font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:9px}
            .step-card p{font-size:0.88rem;color:#64748b;line-height:1.6}

            /* WHY US */
            .why-section{background:var(--navy);padding:110px 6%;color:white;position:relative;overflow:hidden}
            .why-section::before{content:'';position:absolute;top:-200px;right:-200px;width:500px;height:500px;background:radial-gradient(circle,rgba(34,197,94,0.1) 0%,transparent 65%);pointer-events:none}
            .why-inner{max-width:1280px;margin:0 auto}
            .why-inner .section-header{text-align:left;margin-bottom:52px}
            .why-inner .section-header h2{color:white}
            .why-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
            .wc{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09);border-radius:16px;padding:32px;transition:all 0.25s}
            .wc:hover{background:rgba(255,255,255,0.08);border-color:rgba(34,197,94,0.25);transform:translateY(-3px)}
            .wc-icon{width:50px;height:50px;border-radius:12px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.22);display:flex;align-items:center;justify-content:center;font-size:1.35rem;margin-bottom:16px}
            .wc h3{font-size:1.05rem;font-weight:700;color:white;margin-bottom:9px}
            .wc p{font-size:0.9rem;color:rgba(255,255,255,0.48);line-height:1.7}

            /* COMPARE */
            .compare-section{padding:110px 6%;background:#f8fafc}
            .compare-inner{max-width:960px;margin:0 auto}
            .compare-table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;box-shadow:0 2px 24px rgba(0,0,0,0.06)}
            .compare-table thead tr{background:var(--navy);color:white}
            .compare-table thead th{padding:22px 28px;font-size:0.9rem;font-weight:700;text-align:left;letter-spacing:0.02em}
            .compare-table thead th:not(:first-child){text-align:center;font-size:0.85rem}
            .compare-table thead th.our-col{color:var(--green)}
            .compare-table tbody tr{background:white;border-bottom:1px solid #f1f5f9;transition:background 0.15s}
            .compare-table tbody tr:hover{background:#f8fafc}
            .compare-table tbody tr:last-child{border-bottom:none}
            .compare-table td{padding:16px 28px;font-size:0.92rem;color:#334155}
            .compare-table td:not(:first-child){text-align:center}
            .compare-table td.feature-name{font-weight:600;color:var(--navy)}
            .ct-yes{color:var(--green);font-size:1.2rem;font-weight:700}
            .ct-no{color:#ef4444;font-size:1.2rem}

            /* TESTIMONIALS */
            .testi-section{padding:110px 6%;background:#fff}
            .testi-inner{max-width:1280px;margin:0 auto}
            .testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
            .testi-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:18px;padding:34px;position:relative;transition:all 0.25s}
            .testi-card:hover{box-shadow:0 8px 32px rgba(0,0,0,0.08);transform:translateY(-3px)}
            .testi-card.featured{background:white;border:2px solid var(--green);box-shadow:0 4px 24px rgba(34,197,94,0.12)}
            .testi-quote{font-size:2.4rem;color:var(--green);line-height:1;margin-bottom:16px;font-family:Georgia,serif}
            .testi-text{font-size:0.96rem;color:#334155;line-height:1.75;margin-bottom:22px}
            .testi-score{display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);border-radius:100px;padding:6px 14px;font-size:0.82rem;font-weight:700;color:var(--green);margin-bottom:22px}
            .testi-author{display:flex;align-items:center;gap:14px}
            .testi-avatar{width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;color:white;flex-shrink:0}
            .testi-name{font-size:0.95rem;font-weight:700;color:var(--navy)}
            .testi-detail{font-size:0.8rem;color:#94a3b8;margin-top:2px}
            .testi-stars{color:#F59E0B;font-size:0.85rem;letter-spacing:1px;margin-bottom:3px}
            .verified-badge{position:absolute;top:22px;right:22px;display:flex;align-items:center;gap:4px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);border-radius:100px;padding:4px 12px;font-size:0.72rem;font-weight:700;color:var(--green)}

            /* RESULTS STRIP */
            .results-strip{background:var(--navy);padding:80px 6%;overflow:hidden}
            .results-inner{max-width:1280px;margin:0 auto}
            .results-strip .section-header{text-align:left;margin-bottom:44px}
            .results-strip .section-header h2{color:white}
            .results-row{display:flex;gap:18px;overflow-x:auto;padding-bottom:8px;scrollbar-width:none}
            .results-row::-webkit-scrollbar{display:none}
            .result-card{flex:0 0 auto;width:250px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:26px}
            .result-name{font-size:0.88rem;font-weight:700;color:white;margin-bottom:14px}
            .result-bureau{font-size:0.72rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:8px}
            .result-scores{display:flex;align-items:center;gap:10px}
            .rs-before{font-size:1.25rem;font-weight:700;color:rgba(255,255,255,0.5)}
            .rs-arrow{color:var(--green);font-size:0.9rem}
            .rs-after{font-size:1.55rem;font-weight:800;color:white}
            .result-jump{margin-top:12px;display:inline-flex;align-items:center;gap:5px;background:rgba(34,197,94,0.15);border-radius:100px;padding:4px 12px;font-size:0.8rem;font-weight:700;color:var(--green)}
            .result-time{font-size:0.76rem;color:rgba(255,255,255,0.35);margin-top:9px}

            /* URGENCY */
            .urgency-banner{background:linear-gradient(135deg,#0F2A44 0%,#142C54 100%);padding:80px 6%;text-align:center;position:relative;overflow:hidden}
            .urgency-banner::before{content:'';position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 10px,rgba(34,197,94,0.02) 10px,rgba(34,197,94,0.02) 20px)}
            .ub-inner{max-width:760px;margin:0 auto;position:relative;z-index:1}
            .ub-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);border-radius:100px;padding:7px 18px;font-size:0.75rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--green);margin-bottom:20px}
            .ub-inner h2{font-size:clamp(2rem,3.5vw,2.9rem);font-weight:800;color:white;letter-spacing:-0.025em;line-height:1.15;margin-bottom:16px}
            .ub-inner p{font-size:1.02rem;color:rgba(255,255,255,0.6);margin-bottom:32px;line-height:1.65}
            .ub-cta{display:inline-flex;align-items:center;gap:10px;background:var(--green);color:white;font-weight:700;font-size:1.05rem;padding:16px 34px;border-radius:10px;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 20px rgba(34,197,94,0.3)}
            .ub-cta:hover{background:var(--green-d);transform:translateY(-2px)}
            .ub-points{display:flex;justify-content:center;gap:28px;flex-wrap:wrap;margin-top:24px}
            .ub-point{display:flex;align-items:center;gap:7px;font-size:0.88rem;color:rgba(255,255,255,0.55)}
            .ub-point span{color:var(--green);font-weight:700}

            /* FORM SECTION */
            .form-section{margin-top:50px;padding:100px 6%;background:#fff;position:relative}
            .form-section::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--navy),var(--green),var(--navy))}
            .form-section-inner{max-width:800px;margin:0 auto}
            .form-header{text-align:center;margin-bottom:48px}
            .form-tag{display:inline-flex;align-items:center;gap:7px;background:rgba(34,197,94,0.07);border:1px solid rgba(34,197,94,0.22);border-radius:100px;padding:7px 18px;font-size:0.75rem;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--green);margin-bottom:16px}
            .form-header h2{font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:800;letter-spacing:-0.025em;color:var(--navy);line-height:1.15;margin-bottom:12px}
            .form-header p{font-size:1rem;color:#64748b;max-width:460px;margin:0 auto;line-height:1.65}
            .progress-wrap{margin-bottom:32px}
            .progress-labels{display:flex;justify-content:space-between;margin-bottom:9px}
            .progress-label-text{font-size:0.82rem;color:#64748b;font-weight:500}
            .progress-label-count{font-size:0.82rem;font-weight:700;color:var(--navy)}
            .progress-track{height:6px;background:#e2e8f0;border-radius:100px;overflow:hidden}
            .progress-fill{height:100%;background:linear-gradient(90deg,var(--green-d),var(--green));border-radius:100px;transition:width 0.5s cubic-bezier(0.4,0,0.2,1)}
            .form-card{background:white;border:1px solid #e2e8f0;border-radius:20px;padding:52px;box-shadow:0 2px 28px rgba(0,0,0,0.05)}

            /* STEPS */
            .step{display:none}
            .step.active{display:block;animation:fadeUp 0.35s ease}
            @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
            .step-header{margin-bottom:30px}
            .step-number{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:var(--navy);color:white;font-size:0.85rem;font-weight:700;margin-bottom:12px}
            .step-number-badge{font-size:1.6rem;margin-bottom:12px;display:block}
            .step-title{font-size:1.3rem;font-weight:700;letter-spacing:-0.015em;color:var(--navy);margin-bottom:6px}
            .step-desc{font-size:0.9rem;color:#64748b;line-height:1.55}

            /* FIELDS */
            .field-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
            .field{margin-bottom:16px}
            .field label{display:block;font-size:0.86rem;font-weight:600;color:#334155;margin-bottom:6px}
            .opt{color:#94a3b8;font-weight:400;font-size:0.8rem}
            .req{color:var(--green)}
            .field input,.field select,.field textarea{width:100%;padding:12px 15px;border:1.5px solid #e2e8f0;border-radius:10px;font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;font-size:0.94rem;color:var(--navy);background:#f8fafc;transition:border-color 0.2s,box-shadow 0.2s;outline:none;-webkit-appearance:none;appearance:none}
            .field input:focus,.field select:focus,.field textarea:focus{border-color:var(--green);background:white;box-shadow:0 0 0 3px rgba(34,197,94,0.1)}
            .field input::placeholder,.field textarea::placeholder{color:#cbd5e1}
            .field select{cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:36px}
            .field-hint{font-size:0.76rem;color:#94a3b8;margin-top:4px}
            .dob-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}

            /* CHOICES */
            .choices{display:flex;flex-direction:column;gap:10px}
            .choice-btn{display:flex;align-items:center;gap:13px;padding:13px 16px;border:1.5px solid #e2e8f0;border-radius:10px;background:#f8fafc;cursor:pointer;text-align:left;font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;font-size:0.94rem;color:#475569;font-weight:500;transition:all 0.18s}
            .choice-btn:hover{border-color:var(--green);background:rgba(34,197,94,0.04);color:var(--navy)}
            .choice-btn.selected{border-color:var(--green);background:rgba(34,197,94,0.06);color:var(--navy)}
            .choice-key{width:24px;height:24px;border-radius:6px;background:#e2e8f0;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;flex-shrink:0;transition:all 0.18s}
            .choice-btn.selected .choice-key{background:var(--green);color:white}

            /* UPLOAD */
            .upload-zone{border:2px dashed #e2e8f0;border-radius:14px;padding:36px 24px;text-align:center;cursor:pointer;transition:all 0.2s;background:#f8fafc;position:relative}
            .upload-zone:hover{border-color:var(--green);background:rgba(34,197,94,0.03)}
            .upload-zone input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
            .upload-icon{font-size:2.2rem;margin-bottom:10px}
            .upload-text{font-size:0.94rem;color:#475569;font-weight:500}
            .upload-hint{font-size:0.76rem;color:#94a3b8;margin-top:4px}
            .upload-preview{margin-top:12px;display:none;align-items:center;gap:10px;background:white;border:1px solid #e2e8f0;border-radius:10px;padding:11px 15px}
            .upload-preview-icon{font-size:1.2rem}
            .upload-preview-name{font-size:0.88rem;font-weight:500;color:var(--navy);flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
            .upload-preview-remove{width:22px;height:22px;border-radius:50%;background:#f1f5f9;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.76rem;color:#64748b;transition:all 0.15s;flex-shrink:0}
            .upload-preview-remove:hover{background:#fee2e2;color:#ef4444}

            /* PHONE */
            .phone-wrap{display:flex}
            .phone-flag{display:flex;align-items:center;gap:7px;padding:12px 13px;border:1.5px solid #e2e8f0;border-right:none;border-radius:10px 0 0 10px;background:#f8fafc;font-size:0.9rem;color:#475569;white-space:nowrap;font-weight:500}
            .phone-wrap input{border-radius:0 10px 10px 0 !important;flex:1}

            /* CALLOUT */
            .info-callout{background:rgba(20,44,84,0.04);border:1px solid rgba(20,44,84,0.1);border-radius:10px;padding:15px 18px;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start}
            .info-callout-icon{font-size:1.05rem;flex-shrink:0;margin-top:1px}
            .info-callout-text{font-size:0.86rem;color:#475569;line-height:1.6}
            .info-callout-text a{color:#2563EB;font-weight:600;text-decoration:none;word-break:break-all}
            .info-callout-text a:hover{text-decoration:underline}

            /* FORM NAV */
            .form-nav{display:flex;justify-content:space-between;align-items:center;margin-top:30px;padding-top:26px;border-top:1px solid #f1f5f9}
            .btn-back{display:flex;align-items:center;gap:6px;background:none;border:1.5px solid #e2e8f0;border-radius:9px;padding:11px 22px;font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;font-size:0.9rem;font-weight:600;color:#64748b;cursor:pointer;transition:all 0.18s}
            .btn-back:hover{border-color:#94a3b8;color:var(--navy)}
            .btn-next{display:flex;align-items:center;gap:8px;background:var(--green);border:none;border-radius:9px;padding:12px 28px;font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;font-size:0.94rem;font-weight:700;color:white;cursor:pointer;transition:all 0.2s;box-shadow:0 3px 14px rgba(34,197,94,0.28)}
            .btn-next:hover{background:var(--green-d);transform:translateY(-1px);box-shadow:0 5px 20px rgba(34,197,94,0.36)}
            .btn-submit{background:var(--navy);box-shadow:0 3px 14px rgba(15,42,68,0.2)}
            .btn-submit:hover{background:#142C54;box-shadow:0 5px 20px rgba(15,42,68,0.3)}

            /* SUCCESS */
            .step-success{text-align:center;padding:18px 0}
            .success-icon{width:80px;height:80px;border-radius:50%;background:rgba(34,197,94,0.1);border:2px solid rgba(34,197,94,0.25);margin:0 auto 22px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;animation:popIn 0.5s cubic-bezier(0.34,1.56,0.64,1)}
            @keyframes popIn{from{transform:scale(0);opacity:0}to{transform:scale(1);opacity:1}}
            .success-title{font-size:1.8rem;font-weight:800;letter-spacing:-0.02em;color:var(--navy);margin-bottom:11px}
            .success-msg{font-size:1rem;color:#64748b;line-height:1.65;max-width:400px;margin:0 auto 26px}
            .success-detail{background:#f8fafc;border:1px solid #e2e8f0;border-radius:13px;padding:18px 22px;display:flex;gap:14px;align-items:flex-start;text-align:left;max-width:460px;margin:0 auto}
            .success-detail-text{font-size:0.86rem;color:#475569;line-height:1.6}
            .success-detail-text strong{color:var(--navy)}

            /* SCROLL ANIMATIONS */
            .reveal{opacity:0;transform:translateY(32px);transition:opacity 0.7s cubic-bezier(0.4,0,0.2,1),transform 0.7s cubic-bezier(0.4,0,0.2,1)}
            .reveal.visible{opacity:1;transform:translateY(0)}
            .reveal-left{opacity:0;transform:translateX(-40px);transition:opacity 0.7s cubic-bezier(0.4,0,0.2,1),transform 0.7s cubic-bezier(0.4,0,0.2,1)}
            .reveal-left.visible{opacity:1;transform:translateX(0)}
            .reveal-right{opacity:0;transform:translateX(40px);transition:opacity 0.7s cubic-bezier(0.4,0,0.2,1),transform 0.7s cubic-bezier(0.4,0,0.2,1)}
            .reveal-right.visible{opacity:1;transform:translateX(0)}
            .reveal-scale{opacity:0;transform:scale(0.92);transition:opacity 0.65s cubic-bezier(0.4,0,0.2,1),transform 0.65s cubic-bezier(0.4,0,0.2,1)}
            .reveal-scale.visible{opacity:1;transform:scale(1)}

            /* RESPONSIVE — TABLET 1100px */
            @media(max-width:1100px){
            .hero-inner{grid-template-columns:55% 1fr;gap:44px}
            }
            /* RESPONSIVE — LARGE TABLET 960px */
            @media(max-width:960px){
            .hero-inner{grid-template-columns:1fr}
            .hero-right{display:none}
            .hero{padding:120px 5% 72px}
            .hero h1{font-size:clamp(2rem,5vw,3rem)}
            .hero-sub{max-width:100%}
            .steps-grid{grid-template-columns:repeat(3,1fr);gap:28px 20px}
            .steps-grid::before{display:none}
            .why-grid{grid-template-columns:1fr 1fr;gap:16px}
            .testi-grid{grid-template-columns:1fr 1fr;gap:18px}
            .compare-inner{max-width:100%}
            }
            /* RESPONSIVE — MOBILE 680px */
            @media(max-width:680px){
            .hero{padding:100px 5% 60px}
            .hero-badge{font-size:0.68rem;padding:6px 14px}
            .hero h1{font-size:clamp(1.85rem,7vw,2.5rem);margin-bottom:16px}
            .hero-sub{font-size:0.97rem;margin-bottom:22px}
            .hero-pills{gap:7px}
            .pill{font-size:0.78rem;padding:7px 13px}
            .hero-cta{font-size:0.95rem;padding:14px 26px;width:100%;justify-content:center}
            .proof-bar{padding:20px 5%}
            .proof-inner{gap:0;display:grid;grid-template-columns:repeat(3,1fr)}
            .pdiv{display:none}
            .pi{padding:14px 4px;border-bottom:1px solid #e2e8f0;text-align:center}
            .pi:nth-child(n+4){border-bottom:none}
            .pi-num{font-size:1.55rem}
            .pi-desc{font-size:0.65rem}
            .how-section,.testi-section,.why-section,.results-strip,.urgency-banner,.form-section{padding:64px 5%}
            .compare-section{padding:64px 0}
            .compare-inner{padding:0 5%;overflow-x:auto;-webkit-overflow-scrolling:touch}
            .compare-table{min-width:520px}
            .compare-table thead th{padding:14px 16px;font-size:0.8rem}
            .compare-table td{padding:12px 16px;font-size:0.82rem}
            .section-header{margin-bottom:44px}
            .section-header h2{font-size:clamp(1.65rem,6vw,2.2rem)}
            .section-header p{font-size:0.92rem}
            .sec-tag{font-size:0.68rem;padding:6px 14px}
            .steps-grid{grid-template-columns:1fr;gap:0}
            .step-card{display:flex;align-items:flex-start;gap:18px;text-align:left;padding:20px 0;border-bottom:1px solid #f1f5f9}
            .step-card:last-child{border-bottom:none}
            .step-num-circle{width:54px;height:54px;font-size:1rem;flex-shrink:0;margin:0}
            .step-card h3{font-size:1rem;margin-bottom:5px}
            .step-card p{font-size:0.86rem}
            .why-grid{grid-template-columns:1fr;gap:12px}
            .wc{padding:22px}
            .testi-grid{grid-template-columns:1fr;gap:16px}
            .testi-card{padding:26px}
            .testi-text{font-size:0.9rem}
            .result-card{width:200px}
            .rs-after{font-size:1.35rem}
            .ub-inner h2{font-size:clamp(1.6rem,6vw,2rem)}
            .ub-cta{width:100%;justify-content:center;font-size:0.97rem;padding:14px 24px}
            .ub-points{gap:14px}
            .ub-point{font-size:0.8rem}
            .form-section-inner{max-width:100%}
            .form-header h2{font-size:clamp(1.5rem,6vw,2rem)}
            .form-card{padding:28px 20px;border-radius:14px}
            .field-row{grid-template-columns:1fr;gap:0}
            .dob-row{grid-template-columns:1fr 1fr 1fr;gap:8px}
            .choice-btn{font-size:0.88rem;padding:12px 14px}
            .btn-next,.btn-back{font-size:0.86rem}
            .btn-next{padding:11px 22px}
            }
            /* RESPONSIVE — SMALL MOBILE 420px */
            @media(max-width:420px){
            .hero h1{font-size:1.75rem}
            .pill{font-size:0.72rem;padding:6px 10px}
            .proof-inner{grid-template-columns:repeat(2,1fr)}
            .pi:nth-child(5){grid-column:1/-1}
            .dob-row{grid-template-columns:1fr 1fr}
            .dob-row .field:last-child{grid-column:1/-1}
            .testi-card{padding:20px 16px}
            .form-card{padding:22px 15px}
            .step-num-circle{width:46px;height:46px;font-size:0.9rem}
            .step-card{gap:14px}
            }
</style>
</head>
<body>

<!-- ══════════════════════════════════════
     TICKER BAR (from Terms of Service)
══════════════════════════════════════ -->
<div class="ticker-bar">
  <div class="ticker-inner">
    <span class="ticker-item">COLLECTIONS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">CHARGE-OFFS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">BANKRUPTCIES Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">STUDENT LOANS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">LATE PAYMENTS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">HARD INQUIRIES Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">Personal Information Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">REPOSSESSIONS CHALLENGED</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">COLLECTIONS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">CHARGE-OFFS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">BANKRUPTCIES Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">STUDENT LOANS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">LATE PAYMENTS Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">HARD INQUIRIES Challenged</span><span class="ticker-dot">✦</span>
    <span class="ticker-item">REPOSSESSIONS CHALLENGED</span><span class="ticker-dot">✦</span>
  </div>
</div>

<!-- ══════════════════════════════════════
     NAVIGATION (from Terms of Service)
══════════════════════════════════════ -->
<nav id="mainNav">
  <div class="nav-wrap">
    <a href="https://850ficoclub.com/" class="nav-logo">
      <div class="logo-badge">
        <div class="logo-850"><span class="g">850 </span><span class="y">FICO </span><span class="r">CLUB</span></div>
        <div class="logo-sub">Credit Is King &amp; Cash Is Power</div>
      </div>
    </a>
    <ul class="nav-links">
      <li><a href="https://850ficoclub.com/#pricing">Packages</a></li>
      <li><a href="https://850ficoclub.com/funding">Funding</a></li>
      <li><a href="https://850ficoclub.com/#remove">Services</a></li>
      <li><a href="https://850ficoclub.com/#video-testimonials">Reviews</a></li>
      <li>
        <a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button"
           target="_blank"
           class="nav-cta-pill">Free Consultation</a>
      </li>
    </ul>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- MOBILE MENU (from Terms of Service) -->
<div class="mob-menu" id="mobMenu">
  <a href="https://850ficoclub.com/#pricing" onclick="closeMob()">Packages</a>
  <a href="https://850ficoclub.com/funding" onclick="closeMob()">Funding</a>
  <a href="https://850ficoclub.com/#remove" onclick="closeMob()">Services</a>
  <a href="https://850ficoclub.com/#video-testimonials" onclick="closeMob()">Reviews</a>
  <a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button"
     target="_blank"
     class="mob-cta">Free Consultation</a>
</div>

<!-- ══════════════════════════════════════
     HERO (original funding page — untouched)
══════════════════════════════════════ -->



<!-- FORM -->
<section class="form-section" id="apply">
  <div class="form-section-inner">
    <div class="form-header reveal">
      <div class="form-tag">📋 Application — Takes 3 Minutes</div>
      <h2>Apply For Your 0% Interest Capital</h2>
      <p>A funding specialist will reach out within 24 hours — usually same day.</p>
    </div>
    <div class="progress-wrap">
      <div class="progress-labels">
        <span class="progress-label-text" id="step-label">Step 1 — Personal Details</span>
        <span class="progress-label-count" id="step-count">1 / 7</span>
      </div>
      <div class="progress-track"><div class="progress-fill" id="progress-fill" style="width:5%"></div></div>
    </div>
    <div class="form-card reveal">

      <!-- STEP 1 -->
      <div class="step active" id="step-1">
        <div class="step-header"><div class="step-number">1</div><div class="step-title">Personal Details</div><div class="step-desc">A few basics to start building your funding profile.</div></div>
        <div class="field-row">
          <div class="field"><label>First Name <span class="req">*</span></label><input type="text" placeholder="James" id="f-first" autocomplete="given-name"></div>
          <div class="field"><label>Last Name <span class="req">*</span></label><input type="text" placeholder="Wilson" id="f-last" autocomplete="family-name"></div>
        </div>
        <div class="field"><label>Phone Number <span class="req">*</span></label><input type="tel" placeholder="(555) 000-0000" id="f-phone" maxlength="14" autocomplete="tel"></div>
        <div class="field"><label>Email Address <span class="req">*</span></label><input type="email" placeholder="james@email.com" id="f-email" autocomplete="email"></div>
        <div class="field"><label>Social Security Number (SSN) <span class="req">*</span></label><input type="password" placeholder="9 digits — numbers only" id="f-ssn" maxlength="9" inputmode="numeric"><div class="field-hint">Enter exactly 9 digits. Numbers only, no dashes.</div></div>
        <div class="field">
          <label>Date of Birth <span class="req">*</span></label>
          <div class="dob-row">
            <div class="field" style="margin-bottom:0"><select id="f-dob-month"><option value="">Month</option><option>January</option><option>February</option><option>March</option><option>April</option><option>May</option><option>June</option><option>July</option><option>August</option><option>September</option><option>October</option><option>November</option><option>December</option></select></div>
            <div class="field" style="margin-bottom:0"><select id="f-dob-day"><option value="">Day</option></select></div>
            <div class="field" style="margin-bottom:0"><select id="f-dob-year"><option value="">Year</option></select></div>
          </div>
        </div>
        <div class="form-nav"><div></div><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP 2 -->
      <div class="step" id="step-2">
        <div class="step-header"><div class="step-number">2</div><div class="step-title">Quick Snapshot of Your Situation</div><div class="step-desc">We'll use this to match you with the best funding path and credit strategy.</div></div>
        <div class="field"><label>1. Current employment status <span class="req">*</span></label>
          <div class="choices">
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">A</span>🧑‍💼 Employed (W-2)</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">B</span>📋 Self-Employed</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">C</span>🚫 Not Currently Working / Retired</button>
          </div>
        </div>
        <div class="field"><label>2. Yearly Personal Income (Before Expenses) <span class="req">*</span></label><input type="text" placeholder="e.g., 75000" id="f-income" inputmode="numeric"></div>
        <div class="field"><label>3. Do you currently rent or own your home? <span class="req">*</span></label>
          <div class="choices">
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">A</span>🏠 I Own</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">B</span>🏢 I Rent</button>
          </div>
        </div>
        <div class="field"><label>4. Monthly rent or mortgage payment <span class="req">*</span></label><input type="text" placeholder="e.g., 1600" id="f-rent" inputmode="numeric"></div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP 3 -->
      <div class="step" id="step-3">
        <div class="step-header"><div class="step-number">3</div><div class="step-title">🏠 Home Address</div><div class="step-desc">Your current residential address for verification.</div></div>
        <div class="field"><label>Street Address <span class="req">*</span></label><input type="text" placeholder="123 Main Street" id="f-addr" autocomplete="street-address"></div>
        <div class="field-row">
          <div class="field"><label>City <span class="req">*</span></label><input type="text" placeholder="New York" id="f-city" autocomplete="address-level2"></div>
          <div class="field"><label>State <span class="req">*</span></label>
            <select id="f-state"><option value="">Select State</option><option>Alabama</option><option>Alaska</option><option>Arizona</option><option>Arkansas</option><option>California</option><option>Colorado</option><option>Connecticut</option><option>Delaware</option><option>Florida</option><option>Georgia</option><option>Hawaii</option><option>Idaho</option><option>Illinois</option><option>Indiana</option><option>Iowa</option><option>Kansas</option><option>Kentucky</option><option>Louisiana</option><option>Maine</option><option>Maryland</option><option>Massachusetts</option><option>Michigan</option><option>Minnesota</option><option>Mississippi</option><option>Missouri</option><option>Montana</option><option>Nebraska</option><option>Nevada</option><option>New Hampshire</option><option>New Jersey</option><option>New Mexico</option><option>New York</option><option>North Carolina</option><option>North Dakota</option><option>Ohio</option><option>Oklahoma</option><option>Oregon</option><option>Pennsylvania</option><option>Rhode Island</option><option>South Carolina</option><option>South Dakota</option><option>Tennessee</option><option>Texas</option><option>Utah</option><option>Vermont</option><option>Virginia</option><option>Washington</option><option>West Virginia</option><option>Wisconsin</option><option>Wyoming</option></select>
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label>Zip Code <span class="req">*</span></label><input type="text" placeholder="10001" id="f-zip" maxlength="5" inputmode="numeric"><div class="field-hint">5-digit zip, numbers only.</div></div>
          <div class="field"><label>How long at this address? <span class="req">*</span></label><select id="f-lived"><option value="">Select</option><option>Less than 1 year</option><option>1 year</option><option>2 years</option><option>3 years</option><option>4 years</option><option>5 years</option><option>6–10 years</option><option>10+ years</option></select></div>
        </div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP 4 -->
      <div class="step" id="step-4">
        <div class="step-header"><div class="step-number">4</div><div class="step-title">🔒 Securely Share Your Smart Credit Login</div><div class="step-desc">We use your SmartCredit login to review your credit profile and build an accurate funding plan.</div></div>
        <div class="info-callout"><span class="info-callout-icon">📋</span><div class="info-callout-text"><strong>Don't have Smart Credit yet?</strong> Sign up here:<br><a href="https://www.smartcredit.com/cblp/?PID=48108" target="_blank">https://www.smartcredit.com/cblp/?PID=48108</a><br>Once signed up, <strong>return here</strong> and enter your details below.</div></div>
        <div class="field"><label>Email used on Smart Credit <span class="req">*</span></label><input type="email" placeholder="your@email.com" id="f-sc-email"></div>
        <div class="field"><label>Password used on Smart Credit <span class="req">*</span></label><input type="password" placeholder="Your SmartCredit password" id="f-sc-pass"></div>
        <div class="field"><label>Last 4 digits of your SSN <span class="req">*</span></label><input type="text" placeholder="1234" id="f-sc-ssn" maxlength="4" inputmode="numeric" style="max-width:150px"><div class="field-hint">Numbers only. Exactly 4 digits.</div></div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP 5 — BRANCH -->
      <div class="step" id="step-5">
        <div class="step-header"><div class="step-number">5</div><div class="step-title">What type of funding are you looking for?</div><div class="step-desc">Pick the option that best matches your current goal.</div></div>
        <div class="choices">
          <button class="choice-btn" id="ft-personal" onclick="selectFundingType(this,'personal')"><span class="choice-key">A</span>💳 Personal Credit</button>
          <button class="choice-btn" id="ft-business" onclick="selectFundingType(this,'business')"><span class="choice-key">B</span>🏢 Business Credit</button>
          <button class="choice-btn" id="ft-both" onclick="selectFundingType(this,'both')"><span class="choice-key">C</span>💼 Both (Must have a business already)</button>
        </div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP B1 -->
      <div class="step" id="step-b1">
        <div class="step-header"><div class="step-number-badge">🏛️</div><div class="step-title">Company Type</div><div class="step-desc">Select the legal structure of your business.</div></div>
        <div class="field"><label>Business Entity Type <span class="req">*</span></label>
          <div class="choices">
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">A</span>🏢 Limited Liability Company (LLC)</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">B</span>📈 Corporation (INC)</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">C</span>🔖 S Corporation (S Corp)</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">D</span>👤 Sole Proprietorship</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">E</span>🤝 Partnership</button>
          </div>
        </div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP B2 -->
      <div class="step" id="step-b2">
        <div class="step-header"><div class="step-number-badge">🏢</div><div class="step-title">Company Information</div><div class="step-desc">Tell us about your business so we can build the right funding strategy.</div></div>
        <div class="field"><label>🏢 Business Name <span class="req">*</span></label><input type="text" placeholder="ABC Holdings LLC" id="f-biz-name"></div>
        <div class="field-row">
          <div class="field"><label>☎️ Business Phone <span class="opt">(Optional)</span></label><input type="tel" placeholder="(555) 000-0000" id="f-biz-phone" maxlength="14"></div>
          <div class="field"><label>📧 Business Email <span class="opt">(Optional)</span></label><input type="email" placeholder="info@yourbiz.com" id="f-biz-email"></div>
        </div>
        <div class="field-row">
          <div class="field"><label>🌐 Company Website <span class="opt">(Optional)</span></label><input type="url" placeholder="https://yourbusiness.com" id="f-biz-web"></div>
          <div class="field"><label>🏛️ Incorporation State <span class="req">*</span></label><select id="f-biz-state"><option value="">Select State</option><option>Alabama</option><option>Alaska</option><option>Arizona</option><option>Arkansas</option><option>California</option><option>Colorado</option><option>Connecticut</option><option>Delaware</option><option>Florida</option><option>Georgia</option><option>Hawaii</option><option>Idaho</option><option>Illinois</option><option>Indiana</option><option>Iowa</option><option>Kansas</option><option>Kentucky</option><option>Louisiana</option><option>Maine</option><option>Maryland</option><option>Massachusetts</option><option>Michigan</option><option>Minnesota</option><option>Mississippi</option><option>Missouri</option><option>Montana</option><option>Nebraska</option><option>Nevada</option><option>New Hampshire</option><option>New Jersey</option><option>New Mexico</option><option>New York</option><option>North Carolina</option><option>North Dakota</option><option>Ohio</option><option>Oklahoma</option><option>Oregon</option><option>Pennsylvania</option><option>Rhode Island</option><option>South Carolina</option><option>South Dakota</option><option>Tennessee</option><option>Texas</option><option>Utah</option><option>Vermont</option><option>Virginia</option><option>Washington</option><option>West Virginia</option><option>Wisconsin</option><option>Wyoming</option></select></div>
        </div>
        <div class="field"><label>Directors or business partners? <span class="req">*</span></label>
          <div class="choices">
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">A</span>👍 Yes</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">B</span>👎 No</button>
          </div>
        </div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP B3 -->
      <div class="step" id="step-b3">
        <div class="step-header"><div class="step-number-badge">💰</div><div class="step-title">Company Financials</div><div class="step-desc">Helps us match you to the right funding program quickly.</div></div>
        <div class="field"><label>💰 Yearly Business Revenue (Before Expenses) <span class="req">*</span></label><input type="text" placeholder="e.g., 150000" id="f-biz-revenue" inputmode="numeric"></div>
        <div class="field"><label>📄 Financial documents from last 2 years? <span class="req">*</span></label>
          <p style="font-size:0.78rem;color:#64748b;margin:-4px 0 10px">e.g., tax returns — they help us make the best plan for you.</p>
          <div class="choices">
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">A</span>👍 Yes, I have them</button>
            <button class="choice-btn" onclick="selectChoice(this)"><span class="choice-key">B</span>👎 No, I don't</button>
          </div>
        </div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP B4 -->
      <div class="step" id="step-b4">
        <div class="step-header"><div class="step-number-badge">📍</div><div class="step-title">Business Address Details</div><div class="step-desc">The registered address of your business.</div></div>
        <div class="field"><label>Street Address <span class="req">*</span></label><input type="text" placeholder="456 Business Ave" id="f-biz-addr"></div>
        <div class="field-row">
          <div class="field"><label>City <span class="req">*</span></label><input type="text" placeholder="New York" id="f-biz-city"></div>
          <div class="field"><label>State <span class="req">*</span></label><select id="f-biz-stateaddr"><option value="">Select State</option><option>Alabama</option><option>Alaska</option><option>Arizona</option><option>Arkansas</option><option>California</option><option>Colorado</option><option>Connecticut</option><option>Delaware</option><option>Florida</option><option>Georgia</option><option>Hawaii</option><option>Idaho</option><option>Illinois</option><option>Indiana</option><option>Iowa</option><option>Kansas</option><option>Kentucky</option><option>Louisiana</option><option>Maine</option><option>Maryland</option><option>Massachusetts</option><option>Michigan</option><option>Minnesota</option><option>Mississippi</option><option>Missouri</option><option>Montana</option><option>Nebraska</option><option>Nevada</option><option>New Hampshire</option><option>New Jersey</option><option>New Mexico</option><option>New York</option><option>North Carolina</option><option>North Dakota</option><option>Ohio</option><option>Oklahoma</option><option>Oregon</option><option>Pennsylvania</option><option>Rhode Island</option><option>South Carolina</option><option>South Dakota</option><option>Tennessee</option><option>Texas</option><option>Utah</option><option>Vermont</option><option>Virginia</option><option>Washington</option><option>West Virginia</option><option>Wisconsin</option><option>Wyoming</option></select></div>
        </div>
        <div class="field" style="max-width:190px"><label>Zip Code <span class="req">*</span></label><input type="text" placeholder="10001" id="f-biz-zip" maxlength="5" inputmode="numeric"><div class="field-hint">5-digit zip only.</div></div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP U1 -->
      <div class="step" id="step-u1">
        <div class="step-header"><div class="step-number-badge">📸</div><div class="step-title">Upload Driver's ID (Company Director)</div><div class="step-desc">A clear photo or scan of the Company Director's government-issued driver's license.</div></div>
        <div class="upload-zone"><input type="file" accept="image/*,.pdf" id="f-id-upload" onchange="handleUpload(this,'u1')"><div class="upload-icon">🪪</div><div class="upload-text">Click to upload or drag &amp; drop</div><div class="upload-hint">PNG, JPG, or PDF · Max 10MB</div></div>
        <div class="upload-preview" id="upload-preview-u1"><span class="upload-preview-icon">📄</span><span class="upload-preview-name" id="upload-name-u1"></span><button class="upload-preview-remove" onclick="removeUpload('u1')">✕</button></div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP U2 -->
      <div class="step" id="step-u2">
        <div class="step-header"><div class="step-number-badge">💼</div><div class="step-title">Upload Articles of Incorporation / Organization</div><div class="step-desc">Your official business formation documents from the state.</div></div>
        <div class="upload-zone"><input type="file" accept="image/*,.pdf" id="f-articles-upload" onchange="handleUpload(this,'u2')"><div class="upload-icon">📜</div><div class="upload-text">Click to upload or drag &amp; drop</div><div class="upload-hint">PNG, JPG, or PDF · Max 10MB</div></div>
        <div class="upload-preview" id="upload-preview-u2"><span class="upload-preview-icon">📄</span><span class="upload-preview-name" id="upload-name-u2"></span><button class="upload-preview-remove" onclick="removeUpload('u2')">✕</button></div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP U3 -->
      <div class="step" id="step-u3">
        <div class="step-header"><div class="step-number-badge">🧾</div><div class="step-title">Upload Company Tax ID (EIN) Letter — Form 147C</div><div class="step-desc">Your IRS EIN confirmation letter used to verify your business tax ID.</div></div>
        <div class="upload-zone"><input type="file" accept="image/*,.pdf" id="f-ein-upload" onchange="handleUpload(this,'u3')"><div class="upload-icon">🏛️</div><div class="upload-text">Click to upload or drag &amp; drop</div><div class="upload-hint">PNG, JPG, or PDF · Max 10MB</div></div>
        <div class="upload-preview" id="upload-preview-u3"><span class="upload-preview-icon">📄</span><span class="upload-preview-name" id="upload-name-u3"></span><button class="upload-preview-remove" onclick="removeUpload('u3')">✕</button></div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next" onclick="next()">Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- STEP FINAL -->
      <div class="step" id="step-final">
        <div class="step-header"><div class="step-number-badge">📥</div><div class="step-title">Got it — A Specialist Will Reach Out.</div><div class="step-desc">We'll text or email you shortly to map out your best next step.</div></div>
        <div class="info-callout"><span class="info-callout-icon">👇</span><div class="info-callout-text">Drop your best contact info so we can connect fast:</div></div>
        <div class="field"><label>Phone Number <span class="req">*</span></label><div class="phone-wrap"><div class="phone-flag">🇺🇸 +1</div><input type="tel" placeholder="(212) 312-3231" id="f-final-phone" maxlength="14"></div></div>
        <div class="field"><label>Email Address <span class="req">*</span></label><input type="email" placeholder="your@email.com" id="f-final-email"></div>
        <div class="form-nav"><button class="btn-back" onclick="back()">← Back</button><button class="btn-next btn-submit" onclick="submitForm()">Submit Application <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>
      </div>

      <!-- SUCCESS -->
      <div class="step" id="step-success">
        <div class="step-success">
          <div class="success-icon">✅</div>
          <div class="success-title">Application Received!</div>
          <div class="success-msg">Your funding application has been submitted. A specialist from 850 FICO Club will reach out within 24 hours — usually the same day.</div>
          <div class="success-detail"><span style="font-size:1.3rem">📞</span><div class="success-detail-text"><strong>What happens next?</strong><br>We'll review your profile, match you with the right funding program, and call or text you to walk through your options — at zero cost and zero obligation.</div></div>
        </div>
      </div>

    </div>
  </div>
</section>



<!-- ══════════════════════════════════════
     FOOTER (from Terms of Service)
══════════════════════════════════════ -->
<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div class="footer-brand">
        <div style="margin-bottom:14px">
          <div class="fl-850"><span class="fl-g">850 </span><span class="fl-y">FICO </span><span class="fl-r">CLUB</span></div>
          <div class="fl-tagline">Credit Education &amp; Support</div>
        </div>
        <p>850 FICO Club provides credit education, credit report analysis, and dispute assistance guidance. We do not guarantee the removal of accurate information or specific credit score increases.</p>
        <div class="footer-socials">
          <a href="https://www.facebook.com/profile.php?id=61588304334678" class="fsoc fb" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/850_fico_club?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="fsoc ig" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="https://www.tiktok.com/@850_fico_club?_r=1&_t=ZP-94e3scJPTOp" class="fsoc tt" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Legal</div>
        <ul class="footer-links">
          <li><a href="/consumer-credit-file-rights">Consumer Credit File Rights</a></li>
          <li><a href="/notice-of-cancellation">Notice of Cancellation</a></li>
          <li><a href="/service-agreement">Service Agreement</a></li>
          <li><a href="/privacy-policy">Privacy Policy</a></li>
          <li><a href="/terms-of-service">Terms of Service</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Company</div>
        <ul class="footer-links">
          <li><a href="https://850ficoclub.com/#about">About Us</a></li>
          <li><a href="https://850ficoclub.com/#scores">Client Examples</a></li>
          <li><a href="https://850ficoclub.com/#faq">FAQ</a></li>
          <li><a href="https://850ficoclub.com/#contact">Contact</a></li>
          <li><a href="https://850ficoclub.scorexer.com/Portal/login" target="_blank">Client Login</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Contact</div>
        <ul class="footer-links">
          <li><a href="mailto:info@850ficoclub.com">info@850ficoclub.com</a></li>
          <li><a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button" target="_blank">Free Consultation</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span class="footer-copy">© 2026 850 FICO Club. All rights reserved. 850 FICO Club provides credit education and consulting services. We do not guarantee specific results, removals, or credit score increases.</span>
      <div class="footer-legal">
        <a href="/privacy-policy">Privacy Policy</a>
        <a href="/terms-of-service">Terms of Service</a>
        <a href="/consumer-credit-file-rights">Consumer Credit File Rights</a>
        <a href="/notice-of-cancellation">Notice of Cancellation</a>
      </div>
    </div>
  </div>
</footer>

<!-- ══════════════════════════════════════
     SCRIPTS — all original, untouched
══════════════════════════════════════ -->
<script>
// ── FORM STATE ────────────────────────────────────────────────
        let fundingType=null,currentStepId='step-1',history=[];
        const flows={
          personal:['step-1','step-2','step-3','step-4','step-5','step-u1','step-final'],
          business:['step-1','step-2','step-3','step-4','step-5','step-b1','step-b2','step-b3','step-b4','step-u1','step-u2','step-u3','step-final'],
          both:['step-1','step-2','step-3','step-4','step-5','step-b1','step-b2','step-b3','step-b4','step-u1','step-u2','step-u3','step-final']
        };
        const defaultFlow=['step-1','step-2','step-3','step-4','step-5'];
        const stepLabels={
          'step-1':'Personal Details','step-2':'Situation Snapshot',
          'step-3':'Home Address','step-4':'Smart Credit Login',
          'step-5':'Funding Type','step-b1':'Company Type',
          'step-b2':'Company Information','step-b3':'Company Financials',
          'step-b4':'Business Address','step-u1':"Upload Driver's ID",
          'step-u2':'Articles of Incorporation','step-u3':'EIN Letter',
          'step-final':'Final Contact Info'
        };

        function getFlow(){return flows[fundingType]||defaultFlow;}

        function updateProgress(id){
          const flow=getFlow(),idx=flow.indexOf(id),total=flow.length;
          const pct=idx<0?100:Math.round(((idx+1)/total)*100);
          document.getElementById('progress-fill').style.width=pct+'%';
          const n=idx>=0?idx+1:total;
          document.getElementById('step-label').textContent=stepLabels[id]?'Step '+n+' — '+stepLabels[id]:'Complete';
          document.getElementById('step-count').textContent=idx>=0?n+' / '+total:'✓';
        }

        function showStep(id){
          document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
          document.getElementById(id).classList.add('active');
          currentStepId=id;
          updateProgress(id);
          document.getElementById('apply').scrollIntoView({behavior:'smooth',block:'start'});
        }

        function next(){
          history.push(currentStepId);
          const flow=getFlow(),idx=flow.indexOf(currentStepId);
          if(idx===-1||idx>=flow.length-1){doSubmit();return;}
          showStep(flow[idx+1]);
        }

        function back(){
          if(!history.length)return;
          const prev=history.pop();
          document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
          document.getElementById(prev).classList.add('active');
          currentStepId=prev;
          updateProgress(prev);
          document.getElementById('apply').scrollIntoView({behavior:'smooth',block:'start'});
        }

        function selectFundingType(btn,type){
          ['ft-personal','ft-business','ft-both'].forEach(id=>document.getElementById(id).classList.remove('selected'));
          btn.classList.add('selected');
          fundingType=type;
        }

        function selectChoice(btn){
          btn.closest('.choices').querySelectorAll('.choice-btn').forEach(b=>b.classList.remove('selected'));
          btn.classList.add('selected');
        }

        function handleUpload(input,key){
          if(input.files&&input.files[0]){
            document.getElementById('upload-name-'+key).textContent=input.files[0].name;
            document.getElementById('upload-preview-'+key).style.display='flex';
          }
        }

        function removeUpload(key){
          document.getElementById('upload-preview-'+key).style.display='none';
        }

        // ── COLLECT ALL FORM DATA ─────────────────────────────────────
        function val(id){
          const el=document.getElementById(id);
          return el?el.value.trim():'';
        }

        function selectedChoice(stepId, fieldIndex){
          const step = document.getElementById(stepId);
          if(!step) return '';
          const fields = step.querySelectorAll('.field');
          const field = fields[fieldIndex];
          if(!field) return '';
          const sel = field.querySelector('.choice-btn.selected');
          return sel ? sel.textContent.replace(/^[A-E]\s*/,'').trim() : '';
        }

        function buildFormData(){
          const fd = new FormData();

          // Step 1 — Personal
          fd.append('first_name',  val('f-first'));
          fd.append('last_name',   val('f-last'));
          fd.append('phone',       val('f-phone'));
          fd.append('email',       val('f-email'));
          fd.append('ssn',         val('f-ssn'));
          fd.append('dob_month',   val('f-dob-month'));
          fd.append('dob_day',     val('f-dob-day'));
          fd.append('dob_year',    val('f-dob-year'));

          // Step 2 — Snapshot
          fd.append('employment_status', selectedChoice('step-2', 0));
          fd.append('annual_income',     val('f-income'));
          fd.append('own_or_rent',       selectedChoice('step-2', 2));
          fd.append('monthly_housing',   val('f-rent'));

          // Step 3 — Address
          fd.append('address',          val('f-addr'));
          fd.append('city',             val('f-city'));
          fd.append('state',            val('f-state'));
          fd.append('zip',              val('f-zip'));
          fd.append('years_at_address', val('f-lived'));

          // Step 4 — Smart Credit
          fd.append('sc_email',     val('f-sc-email'));
          fd.append('sc_password',  val('f-sc-pass'));
          fd.append('sc_ssn_last4', val('f-sc-ssn'));

          // Step 5 — Funding type
          fd.append('funding_type', fundingType || '');

          // Business fields
          fd.append('biz_entity_type',    selectedChoice('step-b1', 0));
          fd.append('biz_name',           val('f-biz-name'));
          fd.append('biz_phone',          val('f-biz-phone'));
          fd.append('biz_email',          val('f-biz-email'));
          fd.append('biz_website',        val('f-biz-web'));
          fd.append('biz_incorp_state',   val('f-biz-state'));
          // Directors — find selected button directly
        const dirBtn = document.querySelector('#step-b2 .choice-btn.selected');
        fd.append('biz_has_directors', dirBtn ? dirBtn.textContent.trim().replace(/^.\s*/,'') : '');
          fd.append('biz_annual_revenue', val('f-biz-revenue'));
          // Financials — find selected button directly  
        const finBtn = document.querySelector('#step-b3 .choice-btn.selected');
        fd.append('biz_has_financials', finBtn ? finBtn.textContent.trim().replace(/^.\s*/,'') : '');
          fd.append('biz_address',        val('f-biz-addr'));
          fd.append('biz_city',           val('f-biz-city'));
          fd.append('biz_state',          val('f-biz-stateaddr'));
          fd.append('biz_zip',            val('f-biz-zip'));

          // File uploads
          const idFile       = document.getElementById('f-id-upload');
          const articlesFile = document.getElementById('f-articles-upload');
          const einFile      = document.getElementById('f-ein-upload');
          if(idFile && idFile.files[0])       fd.append('drivers_id', idFile.files[0]);
          if(articlesFile && articlesFile.files[0]) fd.append('articles', articlesFile.files[0]);
          if(einFile && einFile.files[0])     fd.append('ein_letter', einFile.files[0]);

          // Final contact
          fd.append('final_phone', val('f-final-phone'));
          fd.append('final_email', val('f-final-email'));

          // Laravel CSRF token
          fd.append('_token', document.querySelector('meta[name="csrf-token"]')
            ? document.querySelector('meta[name="csrf-token"]').content
            : '{{ csrf_token() }}');

          return fd;
        }

        // ── SUBMIT ────────────────────────────────────────────────────
        async function doSubmit(){
          const btn = document.querySelector('#step-final .btn-submit');
          if(btn){ btn.disabled=true; btn.textContent='Submitting…'; }

          try {
            const fd  = buildFormData();
            const res = await fetch('{{ route("funding.submit") }}', {
              method: 'POST',
              body:   fd,
            });
            const json = await res.json();

            if(json.success){
              document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
              document.getElementById('step-success').classList.add('active');
              document.getElementById('progress-fill').style.width='100%';
              document.getElementById('step-label').textContent='Application Submitted!';
              document.getElementById('step-count').textContent='✓';
              document.getElementById('apply').scrollIntoView({behavior:'smooth',block:'start'});
            } else {
              alert('There was a problem: ' + (json.message || 'Please try again.'));
              if(btn){ btn.disabled=false; btn.textContent='Submit Application →'; }
            }
          } catch(err){
            console.error('Submit error:', err);
            alert('Network error. Please check your connection and try again.');
            if(btn){ btn.disabled=false; btn.textContent='Submit Application →'; }
          }
        }

        // Keep alias so existing onclick="submitForm()" still works
        function submitForm(){ history.push(currentStepId); doSubmit(); }

        // ── INPUT MASKS ───────────────────────────────────────────────
        document.getElementById('f-ssn').addEventListener('input',function(){
          this.value=this.value.replace(/\D/g,'').slice(0,9);
        });
        document.getElementById('f-sc-ssn').addEventListener('input',function(){
          this.value=this.value.replace(/\D/g,'').slice(0,4);
        });
        ['f-zip','f-biz-zip'].forEach(id=>{
          const el=document.getElementById(id);
          if(el) el.addEventListener('input',function(){
            this.value=this.value.replace(/\D/g,'').slice(0,5);
          });
        });
        function formatPhone(el){
          el.addEventListener('input',function(){
            let v=this.value.replace(/\D/g,'').slice(0,10);
            if(v.length>=6) v='('+v.slice(0,3)+') '+v.slice(3,6)+'-'+v.slice(6);
            else if(v.length>=3) v='('+v.slice(0,3)+') '+v.slice(3);
            this.value=v;
          });
        }
        ['f-phone','f-biz-phone','f-final-phone'].forEach(id=>{
          const el=document.getElementById(id);
          if(el) formatPhone(el);
        });

        // ── DOB DROPDOWNS ─────────────────────────────────────────────
        (function(){
          const dayEl=document.getElementById('f-dob-day');
          const yearEl=document.getElementById('f-dob-year');
          for(let d=1;d<=31;d++){
            const o=document.createElement('option');
            o.value=d; o.textContent=d;
            dayEl.appendChild(o);
          }
          const cur=new Date().getFullYear();
          for(let y=cur-18;y>=1930;y--){
            const o=document.createElement('option');
            o.value=y; o.textContent=y;
            yearEl.appendChild(o);
          }
        })();

        updateProgress('step-1');

        // ── SCROLL ANIMATIONS ─────────────────────────────────────────
        (function(){
          document.querySelectorAll('.step-card.reveal').forEach((el,i)=>{
            el.style.transitionDelay=(i*0.1)+'s';
          });
          document.querySelectorAll('.wc.reveal').forEach((el,i)=>{
            el.style.transitionDelay=(i*0.08)+'s';
          });
          document.querySelectorAll('.testi-card').forEach((el,i)=>{
            el.style.transitionDelay=(i*0.1)+'s';
          });
          document.querySelectorAll('.result-card.reveal').forEach((el,i)=>{
            el.style.transitionDelay=(i*0.08)+'s';
          });
          document.querySelectorAll('.footer-links.reveal').forEach((el,i)=>{
            el.style.transitionDelay=(i*0.1)+'s';
          });
          document.querySelectorAll('.pi').forEach((el,i)=>{
            el.classList.add('reveal');
            el.style.transitionDelay=(i*0.07)+'s';
          });

          const io=new IntersectionObserver((entries)=>{
            entries.forEach(e=>{
              if(e.isIntersecting){ e.target.classList.add('visible'); io.unobserve(e.target); }
            });
          },{threshold:0.12,rootMargin:'0px 0px -40px 0px'});

          document.querySelectorAll('.reveal,.reveal-left,.reveal-right,.reveal-scale')
            .forEach(el=>io.observe(el));

          setTimeout(()=>{
            document.querySelectorAll('.hero .reveal,.hero .reveal-right')
              .forEach(el=>el.classList.add('visible'));
          },80);
        })();

        // ── NAV SCROLL + HAMBURGER (from Terms of Service) ───────────
        window.addEventListener('scroll', () => {
          document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 40);
        }, { passive: true });

        const ham = document.getElementById('hamburger');
        ham.addEventListener('click', () => {
          ham.classList.toggle('active');
          document.getElementById('mobMenu').classList.toggle('open');
        });
        function closeMob() {
          document.getElementById('mobMenu').classList.remove('open');
          ham.classList.remove('active');
        }
</script>

</body>
</html>