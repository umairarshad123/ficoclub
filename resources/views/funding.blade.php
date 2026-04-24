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
/* ── CSS VARS ── */
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
body{background:#fff;color:var(--navy);font-family:'Manrope',sans-serif;overflow-x:hidden;-webkit-font-smoothing:antialiased;}
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
.hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:6px;border:none;background:none;}
.hamburger span{display:block;width:22px;height:2px;background:var(--navy);border-radius:2px;transition:all .3s;}
.hamburger.active span:nth-child(1){transform:translateY(7px) rotate(45deg);}
.hamburger.active span:nth-child(2){opacity:0;transform:scaleX(0);}
.hamburger.active span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}
.mob-menu{display:none;position:fixed;top:124px;left:0;right:0;bottom:0;background:#fff;z-index:999;padding:32px 24px;flex-direction:column;gap:4px;border-top:1px solid var(--line);overflow-y:auto;}
.mob-menu.open{display:flex;}
.mob-menu a{font-size:18px;font-weight:700;color:var(--navy);text-decoration:none;padding:16px 0;border-bottom:1px solid var(--line);transition:color .2s;}
.mob-menu a:hover{color:var(--green);}
.mob-menu .mob-cta{background:var(--green);color:#fff;text-align:center;padding:18px;border-radius:100px;border-bottom:none;margin-top:16px;}
@media(max-width:900px){.nav-links{display:none;}.hamburger{display:flex;}.nav-wrap{padding:0 24px;}}
@media(max-width:768px){nav{height:70px!important;top:32px!important;}nav.scrolled{height:60px!important;}.mob-menu{top:102px!important;}.logo-850{font-size:22px!important;}.logo-sub{font-size:9px!important;}}

/* HERO */
.hero{background:#fff;padding:164px 6% 100px;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;top:-160px;right:-160px;width:700px;height:700px;background:radial-gradient(circle,rgba(34,197,94,0.06) 0%,transparent 65%);pointer-events:none;}
.hero-inner{max-width:1280px;margin:0 auto;display:grid;grid-template-columns:52% 1fr;gap:72px;align-items:center;}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:100px;padding:7px 16px;font-size:0.75rem;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--green);margin-bottom:24px;}
.hero-badge .pulse{width:8px;height:8px;border-radius:50%;background:var(--green);animation:pulse 2s ease-in-out infinite;}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.4;transform:scale(1.5)}}
.hero h1{font-family:'Sora',sans-serif;font-size:clamp(2.4rem,4.2vw,3.8rem);font-weight:800;line-height:1.08;letter-spacing:-0.03em;color:var(--navy);margin-bottom:22px;}
.hero h1 .accent{color:var(--green);}
.hero-sub{font-size:1.08rem;color:#64748b;line-height:1.7;margin-bottom:30px;max-width:500px;}
.hero-pills{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:36px;}
.pill{display:flex;align-items:center;gap:7px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:100px;padding:8px 16px;font-size:0.85rem;font-weight:500;color:#475569;}
.pill .ck{color:var(--green);font-weight:700;}
.hero-cta{display:inline-flex;align-items:center;gap:10px;background:var(--green);color:white;font-weight:700;font-size:1.05rem;padding:17px 34px;border-radius:10px;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 20px rgba(34,197,94,0.28);}
.hero-cta:hover{background:var(--green-d);transform:translateY(-2px);box-shadow:0 8px 28px rgba(34,197,94,0.38);}
.hero-cta svg{transition:transform 0.2s;}
.hero-cta:hover svg{transform:translateX(4px);}

/* STATS CARD */
.hero-right{position:relative;}
.stats-card{background:var(--navy);border-radius:22px;padding:42px;color:white;position:relative;overflow:hidden;}
.stats-card::before{content:'';position:absolute;top:-80px;right:-80px;width:280px;height:280px;background:radial-gradient(circle,rgba(34,197,94,0.18) 0%,transparent 70%);}
.sc-label{font-size:0.75rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--green);margin-bottom:22px;}
.sc-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:26px;}
.sc-num{font-size:2.6rem;font-weight:800;color:white;line-height:1;}
.sc-num span{color:var(--green);}
.sc-desc{font-size:0.82rem;color:rgba(255,255,255,0.48);margin-top:5px;}
.sc-divider{height:1px;background:rgba(255,255,255,0.1);margin-bottom:22px;}
.sc-range-label{font-size:0.82rem;color:rgba(255,255,255,0.48);margin-bottom:9px;}
.sc-range{font-size:1.9rem;font-weight:800;color:white;}
.sc-range span{color:var(--green);}
.sc-footer{margin-top:22px;display:flex;align-items:center;gap:10px;background:rgba(255,255,255,0.06);border-radius:10px;padding:14px 16px;}
.sc-footer-text{font-size:0.84rem;color:rgba(255,255,255,0.6);line-height:1.5;}
.sc-footer-text strong{color:white;}
.float-badge{position:absolute;bottom:-18px;left:-18px;background:var(--green);border-radius:14px;padding:14px 20px;color:white;box-shadow:0 8px 26px rgba(34,197,94,0.36);display:flex;align-items:center;gap:11px;}
.fb-num{font-size:1.65rem;font-weight:800;line-height:1;}
.fb-text{font-size:0.78rem;color:rgba(255,255,255,0.85);line-height:1.4;}

/* PROOF BAR */
.proof-bar{background:#f8fafc;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;padding:32px 6%;}
.proof-inner{max-width:1280px;margin:0 auto;display:flex;align-items:center;justify-content:center;gap:48px;flex-wrap:wrap;}
.pi{text-align:center;}
.pi-num{font-size:2rem;font-weight:800;color:var(--navy);line-height:1;}
.pi-num span{color:var(--green);}
.pi-desc{font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin-top:4px;}
.pdiv{width:1px;height:46px;background:#e2e8f0;}

/* HOW IT WORKS */
.how-section{padding:110px 6%;background:#fff;}
.how-inner{max-width:1280px;margin:0 auto;}
.section-header{text-align:center;margin-bottom:70px;}
.sec-tag{display:inline-flex;align-items:center;gap:7px;background:rgba(34,197,94,0.07);border:1px solid rgba(34,197,94,0.22);border-radius:100px;padding:7px 18px;font-size:0.75rem;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--green);margin-bottom:18px;}
.section-header h2{font-family:'Sora',sans-serif;font-size:clamp(2rem,3.5vw,2.9rem);font-weight:800;letter-spacing:-0.025em;color:var(--navy);line-height:1.15;margin-bottom:14px;}
.section-header p{font-size:1.02rem;color:#64748b;max-width:520px;margin:0 auto;line-height:1.65;}
.steps-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:0;position:relative;}
.steps-grid::before{content:'';position:absolute;top:38px;left:10%;right:10%;height:2px;background:linear-gradient(90deg,var(--green),var(--green-d));z-index:0;}
.step-card{text-align:center;position:relative;z-index:1;padding:0 16px;}
.step-num-circle{width:76px;height:76px;border-radius:50%;background:white;border:2.5px solid var(--green);display:flex;align-items:center;justify-content:center;margin:0 auto 22px;font-weight:800;font-size:1.25rem;color:var(--navy);box-shadow:0 0 0 6px rgba(34,197,94,0.1);}
.step-card h3{font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:9px;}
.step-card p{font-size:0.88rem;color:#64748b;line-height:1.6;}

/* WHY US */
.why-section{background:var(--navy);padding:110px 6%;color:white;position:relative;overflow:hidden;}
.why-section::before{content:'';position:absolute;top:-200px;right:-200px;width:500px;height:500px;background:radial-gradient(circle,rgba(34,197,94,0.1) 0%,transparent 65%);pointer-events:none;}
.why-inner{max-width:1280px;margin:0 auto;}
.why-inner .section-header{text-align:left;margin-bottom:52px;}
.why-inner .section-header h2{color:white;}
.why-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
.wc{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09);border-radius:16px;padding:32px;transition:all 0.25s;}
.wc:hover{background:rgba(255,255,255,0.08);border-color:rgba(34,197,94,0.25);transform:translateY(-3px);}
.wc-icon{width:50px;height:50px;border-radius:12px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.22);display:flex;align-items:center;justify-content:center;font-size:1.35rem;margin-bottom:16px;}
.wc h3{font-size:1.05rem;font-weight:700;color:white;margin-bottom:9px;}
.wc p{font-size:0.9rem;color:rgba(255,255,255,0.48);line-height:1.7;}

/* COMPARE */
.compare-section{padding:110px 6%;background:#f8fafc;}
.compare-inner{max-width:960px;margin:0 auto;}
.compare-table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;box-shadow:0 2px 24px rgba(0,0,0,0.06);}
.compare-table thead tr{background:var(--navy);color:white;}
.compare-table thead th{padding:22px 28px;font-size:0.9rem;font-weight:700;text-align:left;letter-spacing:0.02em;}
.compare-table thead th:not(:first-child){text-align:center;font-size:0.85rem;}
.compare-table thead th.our-col{color:var(--green);}
.compare-table tbody tr{background:white;border-bottom:1px solid #f1f5f9;transition:background 0.15s;}
.compare-table tbody tr:hover{background:#f8fafc;}
.compare-table tbody tr:last-child{border-bottom:none;}
.compare-table td{padding:16px 28px;font-size:0.92rem;color:#334155;}
.compare-table td:not(:first-child){text-align:center;}
.compare-table td.feature-name{font-weight:600;color:var(--navy);}
.ct-yes{color:var(--green);font-size:1.2rem;font-weight:700;}
.ct-no{color:#ef4444;font-size:1.2rem;}

/* TESTIMONIALS */
.testi-section{padding:110px 6%;background:#fff;}
.testi-inner{max-width:1280px;margin:0 auto;}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
.testi-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:18px;padding:34px;position:relative;transition:all 0.25s;}
.testi-card:hover{box-shadow:0 8px 32px rgba(0,0,0,0.08);transform:translateY(-3px);}
.testi-card.featured{background:white;border:2px solid var(--green);box-shadow:0 4px 24px rgba(34,197,94,0.12);}
.testi-quote{font-size:2.4rem;color:var(--green);line-height:1;margin-bottom:16px;font-family:Georgia,serif;}
.testi-text{font-size:0.96rem;color:#334155;line-height:1.75;margin-bottom:22px;}
.testi-score{display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);border-radius:100px;padding:6px 14px;font-size:0.82rem;font-weight:700;color:var(--green);margin-bottom:22px;}
.testi-author{display:flex;align-items:center;gap:14px;}
.testi-avatar{width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;color:white;flex-shrink:0;}
.testi-name{font-size:0.95rem;font-weight:700;color:var(--navy);}
.testi-detail{font-size:0.8rem;color:#94a3b8;margin-top:2px;}
.testi-stars{color:#F59E0B;font-size:0.85rem;letter-spacing:1px;margin-bottom:3px;}
.verified-badge{position:absolute;top:22px;right:22px;display:flex;align-items:center;gap:4px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);border-radius:100px;padding:4px 12px;font-size:0.72rem;font-weight:700;color:var(--green);}

/* RESULTS STRIP */
.results-strip{background:var(--navy);padding:80px 6%;overflow:hidden;}
.results-inner{max-width:1280px;margin:0 auto;}
.results-strip .section-header{text-align:left;margin-bottom:44px;}
.results-strip .section-header h2{color:white;}
.results-row{display:flex;gap:18px;overflow-x:auto;padding-bottom:8px;scrollbar-width:none;}
.results-row::-webkit-scrollbar{display:none;}
.result-card{flex:0 0 auto;width:250px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:26px;}
.result-name{font-size:0.88rem;font-weight:700;color:white;margin-bottom:14px;}
.result-bureau{font-size:0.72rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:8px;}
.result-scores{display:flex;align-items:center;gap:10px;}
.rs-before{font-size:1.25rem;font-weight:700;color:rgba(255,255,255,0.5);}
.rs-arrow{color:var(--green);font-size:0.9rem;}
.rs-after{font-size:1.55rem;font-weight:800;color:white;}
.result-jump{margin-top:12px;display:inline-flex;align-items:center;gap:5px;background:rgba(34,197,94,0.15);border-radius:100px;padding:4px 12px;font-size:0.8rem;font-weight:700;color:var(--green);}
.result-time{font-size:0.76rem;color:rgba(255,255,255,0.35);margin-top:9px;}

/* URGENCY */
.urgency-banner{background:linear-gradient(135deg,#0F2A44 0%,#142C54 100%);padding:80px 6%;text-align:center;position:relative;overflow:hidden;}
.urgency-banner::before{content:'';position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 10px,rgba(34,197,94,0.02) 10px,rgba(34,197,94,0.02) 20px);}
.ub-inner{max-width:760px;margin:0 auto;position:relative;z-index:1;}
.ub-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);border-radius:100px;padding:7px 18px;font-size:0.75rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--green);margin-bottom:20px;}
.ub-inner h2{font-family:'Sora',sans-serif;font-size:clamp(2rem,3.5vw,2.9rem);font-weight:800;color:white;letter-spacing:-0.025em;line-height:1.15;margin-bottom:16px;}
.ub-inner p{font-size:1.02rem;color:rgba(255,255,255,0.6);margin-bottom:32px;line-height:1.65;}
.ub-cta{display:inline-flex;align-items:center;gap:10px;background:var(--green);color:white;font-weight:700;font-size:1.05rem;padding:16px 34px;border-radius:10px;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 20px rgba(34,197,94,0.3);}
.ub-cta:hover{background:var(--green-d);transform:translateY(-2px);}
.ub-points{display:flex;justify-content:center;gap:28px;flex-wrap:wrap;margin-top:24px;}
.ub-point{display:flex;align-items:center;gap:7px;font-size:0.88rem;color:rgba(255,255,255,0.55);}
.ub-point span{color:var(--green);font-weight:700;}

/* ── FORM SECTION (NEW TYPEFORM) ── */
.form-section{padding:100px 6%;background:#fff;position:relative;}
.form-section::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--navy),var(--green),var(--navy));}
.form-section-inner{max-width:780px;margin:0 auto;}
.form-header{text-align:center;margin-bottom:40px;}
.form-tag{display:inline-flex;align-items:center;gap:7px;background:rgba(34,197,94,0.07);border:1px solid rgba(34,197,94,0.22);border-radius:100px;padding:7px 18px;font-size:0.75rem;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:var(--green);margin-bottom:16px;}
.form-header h2{font-family:'Sora',sans-serif;font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:800;letter-spacing:-0.025em;color:var(--navy);line-height:1.15;margin-bottom:12px;}
.form-header p{font-size:1rem;color:#64748b;max-width:460px;margin:0 auto;line-height:1.65;}

/* PROGRESS */
.progress-wrap{margin-bottom:28px;}
.progress-labels{display:flex;justify-content:space-between;margin-bottom:9px;}
.progress-label-text{font-size:0.82rem;color:#64748b;font-weight:500;}
.progress-label-count{font-size:0.82rem;font-weight:700;color:var(--navy);}
.progress-track{height:6px;background:#e2e8f0;border-radius:100px;overflow:hidden;}
.progress-fill{height:100%;background:linear-gradient(90deg,var(--green-d),var(--green));border-radius:100px;transition:width 0.5s cubic-bezier(0.4,0,0.2,1);}

/* FORM CARD */
.form-card{background:white;border:1px solid #e2e8f0;border-radius:20px;padding:48px;box-shadow:0 2px 28px rgba(0,0,0,0.05);}

/* TYPEFORM STEPS */
.tf-step{display:none;}
.tf-step.active{display:block;animation:fadeUp 0.35s ease;}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}

.step-q-number{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:var(--navy);color:#fff;font-size:0.78rem;font-weight:800;border-radius:7px;margin-bottom:14px;}
.step-q-title{font-family:'Sora',sans-serif;font-size:1.3rem;font-weight:700;color:var(--navy);line-height:1.35;margin-bottom:6px;}
.step-q-subtitle{font-size:0.88rem;color:#64748b;margin-bottom:22px;line-height:1.6;}

/* CHOICE BUTTONS */
.tf-choices{display:flex;flex-direction:column;gap:10px;margin-bottom:24px;}
.tf-choice{display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:1.5px solid #e2e8f0;border-radius:11px;background:#f8fafc;cursor:pointer;text-align:left;font-family:'Manrope',sans-serif;font-size:0.93rem;color:#475569;font-weight:600;transition:all 0.18s;width:100%;}
.tf-choice:hover{border-color:var(--green);background:rgba(34,197,94,0.04);color:var(--navy);}
.tf-choice.selected{border-color:var(--green);background:rgba(34,197,94,0.07);color:var(--navy);}
.tf-choice.multi-selected{border-color:var(--blue);background:rgba(37,99,235,0.06);color:var(--navy);}
.choice-key{min-width:26px;height:26px;border-radius:6px;background:#e2e8f0;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:800;transition:all 0.18s;flex-shrink:0;margin-top:1px;}
.tf-choice.selected .choice-key{background:var(--green);color:white;}
.tf-choice.multi-selected .choice-key{background:var(--blue);color:white;}

/* OK / BACK BUTTONS */
.tf-ok{display:inline-flex;align-items:center;gap:8px;background:var(--navy);border:none;border-radius:9px;padding:12px 26px;font-family:'Manrope',sans-serif;font-size:0.93rem;font-weight:700;color:white;cursor:pointer;transition:all 0.2s;box-shadow:0 3px 14px rgba(15,32,68,0.18);}
.tf-ok:hover{background:#142C54;transform:translateY(-1px);box-shadow:0 5px 20px rgba(15,32,68,0.28);}
.tf-ok svg{transition:transform 0.2s;}
.tf-ok:hover svg{transform:translateX(3px);}
.tf-back{display:inline-flex;align-items:center;gap:6px;background:none;border:1.5px solid #e2e8f0;border-radius:9px;padding:11px 20px;font-family:'Manrope',sans-serif;font-size:0.88rem;font-weight:600;color:#64748b;cursor:pointer;transition:all 0.18s;}
.tf-back:hover{border-color:#94a3b8;color:var(--navy);}
.form-nav{display:flex;justify-content:space-between;align-items:center;margin-top:8px;gap:12px;}
.form-nav-right{display:flex;align-items:center;gap:12px;}
.press-enter{font-size:0.75rem;color:#94a3b8;font-weight:500;}

/* INFO / WARNING CARDS */
.info-card{background:rgba(34,197,94,0.05);border:1px solid rgba(34,197,94,0.2);border-radius:13px;padding:20px 22px;margin-bottom:22px;}
.info-card-title{font-family:'Sora',sans-serif;font-size:1.12rem;font-weight:700;color:var(--navy);margin-bottom:8px;}
.info-card p{font-size:0.88rem;color:#475569;line-height:1.7;margin-bottom:6px;}
.info-card p:last-child{margin-bottom:0;}
.warning-card{background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.18);border-radius:13px;padding:20px 22px;margin-bottom:22px;}
.warning-card-title{font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;color:#B91C1C;margin-bottom:8px;}
.warning-card p{font-size:0.88rem;color:#475569;line-height:1.7;margin-bottom:6px;}
.warning-card p:last-child{margin-bottom:0;}

/* TEXT INPUTS */
.tf-field{margin-bottom:18px;}
.tf-field label{display:block;font-size:0.86rem;font-weight:700;color:#334155;margin-bottom:7px;}
.tf-field input{width:100%;padding:13px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-family:'Manrope',sans-serif;font-size:0.95rem;color:var(--navy);background:#f8fafc;transition:border-color 0.2s,box-shadow 0.2s;outline:none;}
.tf-field input:focus{border-color:var(--green);background:white;box-shadow:0 0 0 3px rgba(34,197,94,0.1);}
.tf-field input::placeholder{color:#cbd5e1;}
.phone-wrap{display:flex;}
.phone-flag{display:flex;align-items:center;gap:7px;padding:12px 13px;border:1.5px solid #e2e8f0;border-right:none;border-radius:10px 0 0 10px;background:#f8fafc;font-size:0.9rem;color:#475569;white-space:nowrap;font-weight:600;}
.phone-wrap input{border-radius:0 10px 10px 0!important;flex:1;}
.multi-hint{font-size:0.76rem;color:#94a3b8;font-weight:500;margin-bottom:14px;font-style:italic;}

/* SUCCESS */
.step-success{text-align:center;padding:20px 0;}
.success-icon{width:80px;height:80px;border-radius:50%;background:rgba(34,197,94,0.1);border:2px solid rgba(34,197,94,0.25);margin:0 auto 22px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;animation:popIn 0.5s cubic-bezier(0.34,1.56,0.64,1);}
@keyframes popIn{from{transform:scale(0);opacity:0}to{transform:scale(1);opacity:1}}
.success-title{font-family:'Sora',sans-serif;font-size:1.8rem;font-weight:800;letter-spacing:-0.02em;color:var(--navy);margin-bottom:11px;}
.success-msg{font-size:1rem;color:#64748b;line-height:1.65;max-width:420px;margin:0 auto 26px;}
.success-detail{background:#f8fafc;border:1px solid #e2e8f0;border-radius:13px;padding:18px 22px;display:flex;gap:14px;align-items:flex-start;text-align:left;max-width:480px;margin:0 auto;}
.success-detail-text{font-size:0.88rem;color:#475569;line-height:1.6;}
.success-detail-text strong{color:var(--navy);}

/* SCROLL ANIMATIONS */
.reveal{opacity:0;transform:translateY(32px);transition:opacity 0.7s cubic-bezier(0.4,0,0.2,1),transform 0.7s cubic-bezier(0.4,0,0.2,1);}
.reveal.visible{opacity:1;transform:translateY(0);}
.reveal-left{opacity:0;transform:translateX(-40px);transition:opacity 0.7s cubic-bezier(0.4,0,0.2,1),transform 0.7s cubic-bezier(0.4,0,0.2,1);}
.reveal-left.visible{opacity:1;transform:translateX(0);}
.reveal-right{opacity:0;transform:translateX(40px);transition:opacity 0.7s cubic-bezier(0.4,0,0.2,1),transform 0.7s cubic-bezier(0.4,0,0.2,1);}
.reveal-right.visible{opacity:1;transform:translateX(0);}
.reveal-scale{opacity:0;transform:scale(0.92);transition:opacity 0.65s cubic-bezier(0.4,0,0.2,1),transform 0.65s cubic-bezier(0.4,0,0.2,1);}
.reveal-scale.visible{opacity:1;transform:scale(1);}

/* FOOTER */
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

/* RESPONSIVE */
@media(max-width:1100px){.hero-inner{grid-template-columns:55% 1fr;gap:44px;}}
@media(max-width:960px){
  .hero-inner{grid-template-columns:1fr;}.hero-right{display:none;}
  .hero{padding:120px 5% 72px;}
  .steps-grid{grid-template-columns:repeat(3,1fr);gap:28px 20px;}
  .steps-grid::before{display:none;}
  .why-grid{grid-template-columns:1fr 1fr;gap:16px;}
  .testi-grid{grid-template-columns:1fr 1fr;gap:18px;}
}
@media(max-width:680px){
  .hero{padding:100px 5% 60px;}
  .hero h1{font-size:clamp(1.85rem,7vw,2.5rem);}
  .hero-cta{width:100%;justify-content:center;}
  .proof-inner{gap:0;display:grid;grid-template-columns:repeat(3,1fr);}
  .pdiv{display:none;}
  .pi{padding:14px 4px;border-bottom:1px solid #e2e8f0;}
  .pi-num{font-size:1.55rem;}
  .pi-desc{font-size:0.65rem;}
  .how-section,.testi-section,.why-section,.results-strip,.urgency-banner,.form-section{padding:64px 5%;}
  .compare-section{padding:64px 0;}
  .compare-inner{padding:0 5%;overflow-x:auto;}
  .compare-table{min-width:520px;}
  .steps-grid{grid-template-columns:1fr;gap:0;}
  .step-card{display:flex;align-items:flex-start;gap:18px;text-align:left;padding:20px 0;border-bottom:1px solid #f1f5f9;}
  .step-card:last-child{border-bottom:none;}
  .step-num-circle{width:54px;height:54px;font-size:1rem;flex-shrink:0;margin:0;}
  .why-grid{grid-template-columns:1fr;}
  .testi-grid{grid-template-columns:1fr;}
  .form-card{padding:26px 18px;border-radius:14px;}
  .step-q-title{font-size:1.1rem;}
}
@media(max-width:860px){.footer-grid{grid-template-columns:1fr 1fr;gap:32px;}footer{padding:48px 0 28px;}.footer-brand{grid-column:span 2;}}
@media(max-width:480px){.footer-grid{grid-template-columns:1fr;}.footer-brand{grid-column:span 1;}.fl-850{font-size:22px!important;}.footer-bottom{flex-direction:column;align-items:flex-start;}.footer-legal{flex-wrap:wrap;gap:12px;}}
</style>
</head>
<body>

<!-- TICKER -->
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

<!-- NAV -->
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
      <li><a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button" target="_blank" class="nav-cta-pill">Free Consultation</a></li>
    </ul>
    <button class="hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
</nav>

<div class="mob-menu" id="mobMenu">
  <a href="https://850ficoclub.com/#pricing" onclick="closeMob()">Packages</a>
  <a href="https://850ficoclub.com/funding" onclick="closeMob()">Funding</a>
  <a href="https://850ficoclub.com/#remove" onclick="closeMob()">Services</a>
  <a href="https://850ficoclub.com/#video-testimonials" onclick="closeMob()">Reviews</a>
  <a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button" target="_blank" class="mob-cta">Free Consultation</a>
</div>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div>
      <div class="hero-badge reveal"><div class="pulse"></div>Now Accepting Applications</div>
      <h1 class="reveal" style="transition-delay:0.1s">Access <span class="accent">0% Interest Capital</span><br>That Turns Slow Months<br>Into Business Growth.</h1>
      <p class="hero-sub reveal" style="transition-delay:0.18s">No fluff. Just 0% interest capital that hits like steroids for your business. No collateral, no revenue proof, no tax returns required.</p>
      <div class="hero-pills reveal" style="transition-delay:0.26s">
        <div class="pill"><span class="ck">✓</span> No Collateral</div>
        <div class="pill"><span class="ck">✓</span> No Revenue Proof</div>
        <div class="pill"><span class="ck">✓</span> No Tax Returns</div>
        <div class="pill"><span class="ck">✓</span> 0% Interest</div>
      </div>
      <a href="#apply" class="hero-cta reveal" style="transition-delay:0.34s">Get My Funding Now <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    </div>
    <div class="hero-right">
      <div class="stats-card reveal-right">
        <div class="sc-label">📊 Funding Overview</div>
        <div class="sc-grid">
          <div><div class="sc-num">$250<span>K+</span></div><div class="sc-desc">Max funding available</div></div>
          <div><div class="sc-num"><span>0</span>%</div><div class="sc-desc">Interest rate (intro)</div></div>
          <div><div class="sc-num">30<span>–60</span></div><div class="sc-desc">Days to funding</div></div>
          <div><div class="sc-num">30<span>K+</span></div><div class="sc-desc">Clients funded</div></div>
        </div>
        <div class="sc-divider"></div>
        <div class="sc-range-label">Typical funding range</div>
        <div class="sc-range">$50,000 <span>→</span> $250,000</div>
        <div class="sc-footer"><span>🔒</span><div class="sc-footer-text"><strong>100% Confidential.</strong> Your data is never sold or shared.</div></div>
      </div>
      <div class="float-badge"><div class="fb-num">97%</div><div class="fb-text">Approval<br>Success Rate</div></div>
    </div>
  </div>
</section>

<!-- PROOF BAR -->
<div class="proof-bar">
  <div class="proof-inner">
    <div class="pi"><div class="pi-num">30<span>K+</span></div><div class="pi-desc">Clients Funded</div></div>
    <div class="pdiv"></div>
    <div class="pi"><div class="pi-num">$250<span>K</span></div><div class="pi-desc">Max Per Client</div></div>
    <div class="pdiv"></div>
    <div class="pi"><div class="pi-num"><span>0</span>%</div><div class="pi-desc">Intro Interest Rate</div></div>
    <div class="pdiv"></div>
    <div class="pi"><div class="pi-num">97<span>%</span></div><div class="pi-desc">Approval Rate</div></div>
    <div class="pdiv"></div>
    <div class="pi"><div class="pi-num">30<span>–60</span></div><div class="pi-desc">Days to Funded</div></div>
  </div>
</div>

<!-- ══════════════════════════════════════
     FORM SECTION — NEW TYPEFORM
══════════════════════════════════════ -->
<section class="form-section" id="apply">
  <div class="form-section-inner">
    <div class="form-header reveal">
      <div class="form-tag">📋 Application — Takes 2 Minutes</div>
      <h2>See How Much Funding You Qualify For</h2>
      <p>Answer a few quick questions and a specialist will map out your best funding path.</p>
    </div>

    <div class="progress-wrap">
      <div class="progress-labels">
        <span class="progress-label-text" id="step-label">Funding Goal</span>
        <span class="progress-label-count" id="step-count">1 / 9</span>
      </div>
      <div class="progress-track"><div class="progress-fill" id="progress-fill" style="width:5%"></div></div>
    </div>

    <div class="form-card reveal">

      <!-- Q1: Funding Amount -->
      <div class="tf-step active" id="q1">
        <div class="step-q-number">1</div>
        <div class="step-q-title">What funding amount would support your goals?</div>
        <div class="tf-choices">
          <button class="tf-choice" onclick="selectSingle(this,'q1')"><span class="choice-key">A</span>$10,000 - $25,000</button>
          <button class="tf-choice" onclick="selectSingle(this,'q1')"><span class="choice-key">B</span>$25,000 - $50,000</button>
          <button class="tf-choice" onclick="selectSingle(this,'q1')"><span class="choice-key">C</span>$50,000 - $100,000</button>
          <button class="tf-choice" onclick="selectSingle(this,'q1')"><span class="choice-key">D</span>$100,000 - $250,000</button>
        </div>
        <div class="form-nav">
          <div></div>
          <div class="form-nav-right">
            <span class="press-enter">Press Enter ↵</span>
            <button class="tf-ok" onclick="goNext('q1','q2')">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q2: Accuracy Confirmation -->
      <div class="tf-step" id="q2">
        <div class="step-q-number">2</div>
        <div class="step-q-title">This form helps us evaluate how we can best assist you. Please provide accurate and honest information so we can offer the right solutions.</div>
        <div class="step-q-subtitle">Do you confirm that all information provided will be accurate and truthful?</div>
        <div class="tf-choices">
          <button class="tf-choice" onclick="selectSingle(this,'q2')"><span class="choice-key">A</span>Yes, I confirm all information provided will be accurate and truthful.</button>
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q2','q1')">← Back</button>
          <div class="form-nav-right">
            <button class="tf-ok" onclick="goNext('q2','q3')">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q3: Credit Card Limit -->
      <div class="tf-step" id="q3">
        <div class="step-q-number">3</div>
        <div class="step-q-title">Do your personal credit cards have a combined credit limit of $15,000 or more?</div>
        <div class="step-q-subtitle">Do not include business credit cards or authorized user accounts.</div>
        <div class="tf-choices">
          <button class="tf-choice" onclick="selectSingle(this,'q3')"><span class="choice-key">A</span>Yes, I have $15,000 or more in total personal credit card limits</button>
          <button class="tf-choice" onclick="selectSingle(this,'q3')"><span class="choice-key">B</span>No, I have less than $15,000 in total personal credit card limits</button>
          <button class="tf-choice" onclick="selectSingle(this,'q3')"><span class="choice-key">C</span>No, I do not have any personal credit cards</button>
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q3','q2')">← Back</button>
          <div class="form-nav-right">
            <span class="press-enter">Press Enter ↵</span>
            <button class="tf-ok" onclick="goNext('q3','q4')">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q4: Credit Utilization -->
      <div class="tf-step" id="q4">
        <div class="step-q-number">4</div>
        <div class="step-q-title">Approximately what percentage of your personal credit card limits are you using right now?</div>
        <div class="step-q-subtitle">For reference: Using $10,000 of a $20,000 total limit equals 50% usage.</div>
        <div class="tf-choices">
          <button class="tf-choice" onclick="selectSingle(this,'q4')"><span class="choice-key">A</span>0 - 10%</button>
          <button class="tf-choice" onclick="selectSingle(this,'q4')"><span class="choice-key">B</span>11 - 20%</button>
          <button class="tf-choice" onclick="selectSingle(this,'q4')"><span class="choice-key">C</span>21 - 30%</button>
          <button class="tf-choice" onclick="selectSingle(this,'q4')"><span class="choice-key">D</span>Over 30% — I can reduce my balances to 10–20% within 30–60 days (required to qualify)</button>
          <button class="tf-choice" onclick="selectSingle(this,'q4')"><span class="choice-key">E</span>Over 30% — I cannot reduce my balances within the next 30–60 days</button>
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q4','q3')">← Back</button>
          <div class="form-nav-right">
            <span class="press-enter">Press Enter ↵</span>
            <button class="tf-ok" onclick="goNext('q4','q5')">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q5: FICO Score (dynamic message) -->
      <div class="tf-step" id="q5">
        <div class="step-q-number">5</div>
        <div class="step-q-title" id="q5-title">Exciting news! Based on the information you've shared so far, you could potentially access $50K–$100K in funding to support your goals. To continue, please tell us your personal FICO score as reported by Experian, TransUnion, and Equifax.</div>
        <div class="tf-choices">
          <button class="tf-choice" onclick="selectSingle(this,'q5')"><span class="choice-key">A</span>800+</button>
          <button class="tf-choice" onclick="selectSingle(this,'q5')"><span class="choice-key">B</span>750 - 799</button>
          <button class="tf-choice" onclick="selectSingle(this,'q5')"><span class="choice-key">C</span>700 - 749</button>
          <button class="tf-choice" onclick="selectSingle(this,'q5')"><span class="choice-key">D</span>650 - 699</button>
          <button class="tf-choice" onclick="selectSingle(this,'q5')"><span class="choice-key">E</span>Below 650</button>
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q5','q4')">← Back</button>
          <div class="form-nav-right">
            <span class="press-enter">Press Enter ↵</span>
            <button class="tf-ok" onclick="goNext('q5','q6')">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q6: Business Situation -->
      <div class="tf-step" id="q6">
        <div class="step-q-number">6</div>
        <div class="step-q-title">Which best describes your situation?</div>
        <div class="tf-choices">
          <button class="tf-choice" onclick="selectSingle(this,'q6')"><span class="choice-key">A</span>I have a registered business</button>
          <button class="tf-choice" onclick="selectSingle(this,'q6')"><span class="choice-key">B</span>I don't have a business, I'm interested in personal funding</button>
          <button class="tf-choice" onclick="selectSingle(this,'q6')"><span class="choice-key">C</span>I don't have a business but I'm interested in purchasing an existing company (aged corp)</button>
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q6','q5')">← Back</button>
          <div class="form-nav-right">
            <span class="press-enter">Press Enter ↵</span>
            <button class="tf-ok" onclick="goNext('q6','q7')">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q7: Annual Income -->
      <div class="tf-step" id="q7">
        <div class="step-q-number">7</div>
        <div class="step-q-title">What is your current annual personal income?</div>
        <div class="step-q-subtitle">Include your salary, wages, or other personal income sources.</div>
        <div class="tf-choices">
          <button class="tf-choice" onclick="selectSingle(this,'q7')"><span class="choice-key">A</span>Less than $25,000</button>
          <button class="tf-choice" onclick="selectSingle(this,'q7')"><span class="choice-key">B</span>$25,000 – $49,999</button>
          <button class="tf-choice" onclick="selectSingle(this,'q7')"><span class="choice-key">C</span>$50,000 – $74,999</button>
          <button class="tf-choice" onclick="selectSingle(this,'q7')"><span class="choice-key">D</span>$75,000 – $99,999</button>
          <button class="tf-choice" onclick="selectSingle(this,'q7')"><span class="choice-key">E</span>$100,000 or more</button>
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q7','q6')">← Back</button>
          <div class="form-nav-right">
            <span class="press-enter">Press Enter ↵</span>
            <button class="tf-ok" onclick="goNext('q7','q8')">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q8: Negative Marks (multi-select) -->
      <div class="tf-step" id="q8">
        <div class="step-q-number">8</div>
        <div class="step-q-title">Do you have any of the following negative marks on your credit profile?</div>
        <div class="multi-hint">Select all that apply</div>
        <div class="tf-choices" id="q8-choices">
          <button class="tf-choice" onclick="selectMulti(this,'q8','none')" data-val="Bankruptcy"><span class="choice-key">A</span>Bankruptcy</button>
          <button class="tf-choice" onclick="selectMulti(this,'q8','none')" data-val="Collections or Charge-offs"><span class="choice-key">B</span>Collections or Charge-offs</button>
          <button class="tf-choice" onclick="selectMulti(this,'q8','none')" data-val="Foreclosures"><span class="choice-key">C</span>Foreclosures</button>
          <button class="tf-choice" onclick="selectMulti(this,'q8','none')" data-val="Late Payments"><span class="choice-key">D</span>Late Payments</button>
          <button class="tf-choice" onclick="selectMulti(this,'q8','none-only')" data-val="None"><span class="choice-key">E</span>None of the above, my credit is clean</button>
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q8','q7')">← Back</button>
          <div class="form-nav-right">
            <span class="press-enter">Press Enter ↵</span>
            <button class="tf-ok" onclick="handleQ8Next()">OK <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q9a: Clean Credit Contact Info -->
      <div class="tf-step" id="q9-clean">
        <div class="step-q-number">9</div>
        <div class="info-card">
          <div class="info-card-title">🎉 Great News — You're Well Positioned!</div>
          <p>Based on your answers, you have a strong credit profile and solid potential for funding. Our team can match you with the right 0% interest capital program right away.</p>
          <p>Please enter your information below so a specialist can reach out and walk you through your options — at zero cost and zero obligation.</p>
        </div>
        <div class="tf-field">
          <label>First Name <span style="color:var(--green)">*</span></label>
          <input type="text" id="qc-first" placeholder="James" autocomplete="given-name">
        </div>
        <div class="tf-field">
          <label>Last Name <span style="color:var(--green)">*</span></label>
          <input type="text" id="qc-last" placeholder="Wilson" autocomplete="family-name">
        </div>
        <div class="tf-field">
          <label>Phone Number <span style="color:var(--green)">*</span></label>
          <div class="phone-wrap">
            <div class="phone-flag">🇺🇸 +1</div>
            <input type="tel" id="qc-phone" placeholder="(555) 000-0000" maxlength="14" autocomplete="tel">
          </div>
        </div>
        <div class="tf-field">
          <label>Email Address <span style="color:var(--green)">*</span></label>
          <input type="email" id="qc-email" placeholder="james@email.com" autocomplete="email">
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q9-clean','q8')">← Back</button>
          <div class="form-nav-right">
            <button class="tf-ok" style="background:var(--green);box-shadow:0 3px 14px rgba(34,197,94,0.3);" onclick="submitReadyForm('clean')">Submit Application <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- Q9b: Negative Marks Contact Info -->
      <div class="tf-step" id="q9-negative">
        <div class="step-q-number">9</div>
        <div class="warning-card">
          <div class="warning-card-title">You're Not Denied, You're Just Not Positioned Yet</div>
          <p>Negative marks like late payments, collections, charge-offs, or bankruptcies can limit the funding options available right now.</p>
          <p>But the good news is these issues are extremely common and often fixable.</p>
          <p>Our team helps clients remove negative items and strengthen their credit profile so they can qualify for better approvals and stronger funding opportunities.</p>
          <p>The next step is a quick strategy call where we'll review your situation and show you the fastest path to improving your credit and positioning yourself for funding.</p>
          <p><strong>Please enter your information below to continue.</strong></p>
        </div>
        <div class="tf-field">
          <label>First Name <span style="color:var(--green)">*</span></label>
          <input type="text" id="qn-first" placeholder="James" autocomplete="given-name">
        </div>
        <div class="tf-field">
          <label>Last Name <span style="color:var(--green)">*</span></label>
          <input type="text" id="qn-last" placeholder="Wilson" autocomplete="family-name">
        </div>
        <div class="tf-field">
          <label>Phone Number <span style="color:var(--green)">*</span></label>
          <div class="phone-wrap">
            <div class="phone-flag">🇺🇸 +1</div>
            <input type="tel" id="qn-phone" placeholder="(555) 000-0000" maxlength="14" autocomplete="tel">
          </div>
        </div>
        <div class="tf-field">
          <label>Email Address <span style="color:var(--green)">*</span></label>
          <input type="email" id="qn-email" placeholder="james@email.com" autocomplete="email">
        </div>
        <div class="form-nav">
          <button class="tf-back" onclick="goPrev('q9-negative','q8')">← Back</button>
          <div class="form-nav-right">
            <button class="tf-ok" style="background:var(--navy);" onclick="submitReadyForm('negative')">Submit &amp; Get My Strategy Call <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>

      <!-- SUCCESS -->
      <div class="tf-step" id="q-success">
        <div class="step-success">
          <div class="success-icon">✅</div>
          <div class="success-title">Application Received!</div>
          <div class="success-msg">Your application has been submitted. A specialist from 850 FICO Club will reach out within 24 hours — usually the same day.</div>
          <div class="success-detail">
            <span style="font-size:1.3rem">📞</span>
            <div class="success-detail-text"><strong>What happens next?</strong><br>We'll review your profile, match you with the right funding program, and call or text you to walk through your options — at zero cost and zero obligation.</div>
          </div>
        </div>
      </div>

    </div><!-- /.form-card -->
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section">
  <div class="how-inner">
    <div class="section-header reveal">
      <div class="sec-tag">🔄 The Process</div>
      <h2>How It Works — 5 Simple Steps</h2>
      <p>From application to funded in as little as 30 days. Here's exactly what happens when you work with us.</p>
    </div>
    <div class="steps-grid">
      <div class="step-card reveal"><div class="step-num-circle">01</div><h3>Free Analysis</h3><p>We review your full credit profile and map out exactly what funding you qualify for.</p></div>
      <div class="step-card reveal"><div class="step-num-circle">02</div><h3>Custom Strategy</h3><p>We build a tailored funding plan — personal credit, business lines, or both — for your situation.</p></div>
      <div class="step-card reveal"><div class="step-num-circle">03</div><h3>Credit Optimization</h3><p>We clean and optimize your credit profile so you qualify at the highest limit possible.</p></div>
      <div class="step-card reveal"><div class="step-num-circle">04</div><h3>Applications Submitted</h3><p>We submit strategic applications to lenders with 0% intro APR offers on your behalf.</p></div>
      <div class="step-card reveal"><div class="step-num-circle">05</div><h3>Capital in Hand</h3><p>Funding hits your account. You grow. We keep monitoring and protecting your profile.</p></div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testi-section">
  <div class="testi-inner">
    <div class="section-header reveal">
      <div class="sec-tag">⭐ Client Stories</div>
      <h2>What Our Clients Are Saying</h2>
      <p>Real people. Real results. Every story below belongs to a verified 850 FICO Club client.</p>
    </div>
    <div class="testi-grid">
      <div class="testi-card featured reveal-scale">
        <div class="verified-badge">✓ Verified Client</div>
        <div class="testi-quote">"</div>
        <div class="testi-score">⬆ +318 Points · Business Funded: $85,000</div>
        <div class="testi-text">I came in with a 441 credit score and two failed loan applications behind me. Within 3 months, I was at 759 and had $85,000 in 0% business credit lines. I used it to buy equipment and hire two employees. This team literally changed my life.</div>
        <div class="testi-stars">★★★★★</div>
        <div class="testi-author"><div class="testi-avatar" style="background:#0F2A44">JG</div><div><div class="testi-name">Jeffrey G.</div><div class="testi-detail">Small Business Owner · Miami, FL</div></div></div>
      </div>
      <div class="testi-card reveal">
        <div class="verified-badge">✓ Verified Client</div>
        <div class="testi-quote">"</div>
        <div class="testi-score">⬆ +229 Points · Mortgage Approved</div>
        <div class="testi-text">I was denied for a mortgage twice. My score was a 519 and I had 4 collections dragging me down. 850 FICO Club wiped them all. Six months later I closed on my first home. I literally cried. Worth every penny and more.</div>
        <div class="testi-stars">★★★★★</div>
        <div class="testi-author"><div class="testi-avatar" style="background:#16a34a">SM</div><div><div class="testi-name">Sarah M.</div><div class="testi-detail">Homeowner · Dallas, TX</div></div></div>
      </div>
      <div class="testi-card reveal">
        <div class="verified-badge">✓ Verified Client</div>
        <div class="testi-quote">"</div>
        <div class="testi-score">⬆ +259 Points · $120K Capital Secured</div>
        <div class="testi-text">We needed capital to expand our e-commerce brand but banks kept saying no. 850 FICO Club rebuilt my personal profile and got us $120K in business funding at 0% interest. Our revenue doubled in 6 months. Absolute game-changer.</div>
        <div class="testi-stars">★★★★★</div>
        <div class="testi-author"><div class="testi-avatar" style="background:#142C54">DP</div><div><div class="testi-name">Devon P.</div><div class="testi-detail">E-Commerce Founder · Brooklyn, NY</div></div></div>
      </div>
      <div class="testi-card reveal">
        <div class="verified-badge">✓ Verified Client</div>
        <div class="testi-quote">"</div>
        <div class="testi-score">⬆ +255 Points · Business Loan Approved</div>
        <div class="testi-text">I was turned down for a business loan twice before. My score jumped 255 points and I got approved for funding I was denied on twice before. The process was completely transparent — they texted me every single time something was deleted. Unreal.</div>
        <div class="testi-stars">★★★★★</div>
        <div class="testi-author"><div class="testi-avatar" style="background:#F97316">MT</div><div><div class="testi-name">Marcus T.</div><div class="testi-detail">Restaurant Owner · Los Angeles, CA</div></div></div>
      </div>
      <div class="testi-card reveal">
        <div class="verified-badge">✓ Verified Client</div>
        <div class="testi-quote">"</div>
        <div class="testi-score">⬆ +276 Points · Car Loan &amp; Home Rental</div>
        <div class="testi-text">I couldn't even get approved for an apartment. My score was 412. Within 7 months it was 688 — I got the apartment AND a car loan at a rate I never thought I'd qualify for. The advisors were always available and always honest with me.</div>
        <div class="testi-stars">★★★★★</div>
        <div class="testi-author"><div class="testi-avatar" style="background:#7C3AED">TW</div><div><div class="testi-name">Tanya W.</div><div class="testi-detail">Healthcare Worker · Chicago, IL</div></div></div>
      </div>
      <div class="testi-card reveal">
        <div class="verified-badge">✓ Verified Client</div>
        <div class="testi-quote">"</div>
        <div class="testi-score">⬆ +303 Points · $45K Personal Credit</div>
        <div class="testi-text">I genuinely thought my credit was unfixable. Student loans, a medical collection, two charge-offs. All gone. My score went from 394 to 697. I now have $45K in personal credit lines at 0% and I feel financially free for the first time in a decade.</div>
        <div class="testi-stars">★★★★★</div>
        <div class="testi-author"><div class="testi-avatar" style="background:#0891b2">CL</div><div><div class="testi-name">Cassidy L.</div><div class="testi-detail">Nurse Practitioner · Atlanta, GA</div></div></div>
      </div>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="why-section">
  <div class="why-inner">
    <div class="section-header reveal" style="text-align:left;margin-bottom:52px">
      <div class="sec-tag" style="background:rgba(34,197,94,0.12);color:var(--green)">💡 Why 850 FICO Club</div>
      <h2 style="color:white">Why Other Companies Fail You.<br>Why We Don't.</h2>
    </div>
    <div class="why-grid">
      <div class="wc reveal"><div class="wc-icon">💳</div><h3>0% Interest — For Real</h3><p>We specialize in 0% APR credit lines and business funding vehicles. No predatory rates, no hidden fees.</p></div>
      <div class="wc reveal"><div class="wc-icon">🚫</div><h3>No Collateral. Ever.</h3><p>Our funding paths are unsecured — built around your credit profile, not your property or assets.</p></div>
      <div class="wc reveal"><div class="wc-icon">⚡</div><h3>Funded in 30–60 Days</h3><p>From application to capital in your hands. No 6-month bank wait. No runaround.</p></div>
      <div class="wc reveal"><div class="wc-icon">📊</div><h3>Personalized Strategy</h3><p>Every client gets a custom roadmap — personal credit, business funding, or both.</p></div>
      <div class="wc reveal"><div class="wc-icon">🔒</div><h3>100% Confidential</h3><p>Your data stays private. We never sell your information to anyone, ever.</p></div>
      <div class="wc reveal"><div class="wc-icon">⚖️</div><h3>FCRA + FDCPA Experts</h3><p>Federal consumer law applied to maximize your credit profile so you qualify at the highest level.</p></div>
    </div>
  </div>
</section>

<!-- URGENCY -->
<div class="urgency-banner">
  <div class="ub-inner reveal">
    <div class="ub-tag">⏰ Limited Availability</div>
    <h2>Spots Are Filling Up Fast.<br>Don't Wait on Your Growth.</h2>
    <p>We only take on a limited number of new clients each month to ensure every person gets full advisor attention. Apply today — it's free and takes 2 minutes.</p>
    <a href="#apply" class="ub-cta">Apply Now — It's Free <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    <div class="ub-points">
      <div class="ub-point"><span>✓</span> No Upfront Fees</div>
      <div class="ub-point"><span>✓</span> No Long-Term Contracts</div>
      <div class="ub-point"><span>✓</span> Results in 30–45 Days</div>
      <div class="ub-point"><span>✓</span> 100% Confidential</div>
    </div>
  </div>
</div>

<!-- FOOTER -->
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
          <a href="https://www.facebook.com/profile.php?id=61588304334678" class="fsoc fb" target="_blank"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/850_fico_club?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="fsoc ig" target="_blank"><i class="fab fa-instagram"></i></a>
          <a href="https://www.tiktok.com/@850_fico_club?_r=1&_t=ZP-94e3scJPTOp" class="fsoc tt" target="_blank"><i class="fab fa-tiktok"></i></a>
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

<script>
// ── STATE ───────────────────────────────────────
const answers = {};
const totalSteps = 9;
const stepOrder = ['q1','q2','q3','q4','q5','q6','q7','q8'];

// ── PROGRESS ────────────────────────────────────
function updateProgress(qId) {
  const labels = {
    'q1':'Funding Goal','q2':'Confirmation','q3':'Credit Cards',
    'q4':'Credit Utilization','q5':'FICO Score','q6':'Business Situation',
    'q7':'Annual Income','q8':'Credit Profile','q9-clean':'Contact Info',
    'q9-negative':'Contact Info','q-success':'Complete'
  };
  const idx = stepOrder.indexOf(qId);
  const num = idx >= 0 ? idx + 1 : totalSteps;
  const pct = qId === 'q-success' ? 100 : Math.round((num / totalSteps) * 100);
  document.getElementById('progress-fill').style.width = pct + '%';
  document.getElementById('step-label').textContent = labels[qId] || 'Almost Done';
  document.getElementById('step-count').textContent = qId === 'q-success' ? '✓' : num + ' / ' + totalSteps;
}

// ── SHOW STEP ────────────────────────────────────
function showStep(qId) {
  document.querySelectorAll('.tf-step').forEach(s => s.classList.remove('active'));
  document.getElementById(qId).classList.add('active');
  updateProgress(qId);
  document.getElementById('apply').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── SINGLE SELECT ────────────────────────────────
function selectSingle(btn, qId) {
  btn.closest('.tf-choices').querySelectorAll('.tf-choice').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');
  answers[qId] = btn.textContent.replace(/^[A-E]\s*/,'').trim();
}

// ── MULTI SELECT ────────────────────────────────
function selectMulti(btn, qId, mode) {
  const container = btn.closest('.tf-choices');
  if (mode === 'none-only') {
    container.querySelectorAll('.tf-choice').forEach(b => b.classList.remove('multi-selected'));
    btn.classList.add('multi-selected');
  } else {
    container.querySelectorAll('.tf-choice').forEach(b => {
      if (b.getAttribute('onclick') && b.getAttribute('onclick').includes('none-only')) {
        b.classList.remove('multi-selected');
      }
    });
    btn.classList.toggle('multi-selected');
  }
  const selected = [...container.querySelectorAll('.tf-choice.multi-selected')].map(b => b.dataset.val);
  answers[qId] = selected.join(', ');
}

// ── NEXT / PREV ──────────────────────────────────
function goNext(fromId, toId) {
  if (toId === 'q5') updateQ5Message();
  showStep(toId);
}
function goPrev(fromId, toId) { showStep(toId); }

// ── Q5 DYNAMIC MESSAGE ───────────────────────────
function updateQ5Message() {
  const amt = answers['q1'] || '';
  let range = 'significant capital';
  if (amt.includes('10,000 - 25,000')) range = '$10K–$25K';
  else if (amt.includes('25,000 - 50,000')) range = '$25K–$50K';
  else if (amt.includes('50,000 - 100,000')) range = '$50K–$100K';
  else if (amt.includes('100,000')) range = '$100K–$250K';
  document.getElementById('q5-title').textContent =
    'Exciting news! Based on the information you\'ve shared so far, you could potentially access ' +
    range + ' in funding to support your goals. To continue, please tell us your personal FICO score as reported by Experian, TransUnion, and Equifax.';
}

// ── Q8 BRANCH ────────────────────────────────────
function handleQ8Next() {
  const selected = answers['q8'] || '';
  const hasNegative = selected && selected !== 'None';
  showStep(hasNegative ? 'q9-negative' : 'q9-clean');
}

// ── PHONE FORMAT ─────────────────────────────────
function formatPhone(input) {
  input.addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 10);
    if (v.length >= 6) v = '(' + v.slice(0,3) + ') ' + v.slice(3,6) + '-' + v.slice(6);
    else if (v.length >= 3) v = '(' + v.slice(0,3) + ') ' + v.slice(3);
    this.value = v;
  });
}
['qc-phone','qn-phone'].forEach(id => {
  const el = document.getElementById(id);
  if (el) formatPhone(el);
});

// ── KEYBOARD ENTER ───────────────────────────────
document.addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
    const activeStep = document.querySelector('.tf-step.active');
    if (!activeStep) return;
    const okBtn = activeStep.querySelector('.tf-ok');
    if (okBtn) okBtn.click();
  }
});

// ── BUILD PAYLOAD ────────────────────────────────
function buildPayload(contactType) {
  const p = contactType === 'clean' ? 'qc' : 'qn';
  return {
    funding_amount_goal:   answers['q1'] || '',
    accuracy_confirmed:    answers['q2'] || '',
    credit_card_limit_15k: answers['q3'] || '',
    credit_utilization:    answers['q4'] || '',
    fico_score_range:      answers['q5'] || '',
    business_situation:    answers['q6'] || '',
    annual_income:         answers['q7'] || '',
    negative_marks:        answers['q8'] || '',
    first_name:            document.getElementById(p + '-first').value.trim(),
    last_name:             document.getElementById(p + '-last').value.trim(),
    phone:                 document.getElementById(p + '-phone').value.trim(),
    email:                 document.getElementById(p + '-email').value.trim(),
    lead_type:             contactType === 'clean' ? 'funding-ready' : 'credit-repair-needed',
    submitted_at:          new Date().toISOString(),
    page_source:           'funding',
  };
}

// ── SUBMIT ───────────────────────────────────────
async function submitReadyForm(contactType) {
  const p = contactType === 'clean' ? 'qc' : 'qn';
  const first = document.getElementById(p + '-first').value.trim();
  const last  = document.getElementById(p + '-last').value.trim();
  const phone = document.getElementById(p + '-phone').value.trim();
  const email = document.getElementById(p + '-email').value.trim();

  if (!first || !last || !phone || !email) {
    alert('Please fill in all required fields.');
    return;
  }

  const qId = contactType === 'clean' ? 'q9-clean' : 'q9-negative';
  const btn = document.querySelector('#' + qId + ' .tf-ok:last-of-type');
  if (btn) { btn.disabled = true; btn.textContent = 'Submitting…'; }

  const payload = buildPayload(contactType);
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  try {
    await fetch('{{ route("funding.ready.submit") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify(payload),
    });
  } catch(e) { console.error(e); }

  document.getElementById('progress-fill').style.width = '100%';
  document.getElementById('step-label').textContent = 'Application Submitted!';
  document.getElementById('step-count').textContent = '✓';
  showStep('q-success');
}

// ── NAV + HAMBURGER ──────────────────────────────
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

// ── SCROLL ANIMATIONS ────────────────────────────
(function(){
  document.querySelectorAll('.step-card.reveal').forEach((el,i) => el.style.transitionDelay = (i*0.1)+'s');
  document.querySelectorAll('.wc.reveal').forEach((el,i) => el.style.transitionDelay = (i*0.08)+'s');
  document.querySelectorAll('.testi-card').forEach((el,i) => el.style.transitionDelay = (i*0.1)+'s');
  document.querySelectorAll('.pi').forEach((el,i) => { el.classList.add('reveal'); el.style.transitionDelay = (i*0.07)+'s'; });

  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('visible'); io.unobserve(e.target); } });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.reveal,.reveal-left,.reveal-right,.reveal-scale').forEach(el => io.observe(el));
  setTimeout(() => { document.querySelectorAll('.hero .reveal,.hero .reveal-right').forEach(el => el.classList.add('visible')); }, 80);
})();

updateProgress('q1');
</script>
</body>
</html>