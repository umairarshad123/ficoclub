<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Service Agreement — 850 FICO Club</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800;900&family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
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

/* ══════════════════════════════════════
   PAGE HERO
══════════════════════════════════════ */
.page-hero {
  padding-top: 162px;
  padding-bottom: 64px;
  background: var(--bg2);
  border-bottom: 1px solid var(--line);
  position: relative;
  overflow: hidden;
}
.page-hero::before {
  content: '';
  position: absolute;
  top: -100px; right: -100px;
  width: 480px; height: 480px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(34,197,94,.06) 0%, transparent 70%);
  pointer-events: none;
}
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 20px;
}
.breadcrumb a { color: var(--green); text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.breadcrumb span { color: var(--muted); }
.page-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--green-lt);
  color: var(--green-d);
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  padding: 6px 14px;
  border-radius: 100px;
  margin-bottom: 20px;
}
.page-hero h1 {
  font-family: 'Sora', sans-serif;
  font-size: clamp(32px, 5vw, 52px);
  font-weight: 800;
  line-height: 1.1;
  color: var(--navy);
  letter-spacing: -1.5px;
  max-width: 760px;
  margin-bottom: 18px;
}
.page-hero h1 em { font-style: normal; color: var(--green); }
.page-hero-sub {
  font-size: 16px;
  color: var(--slate);
  line-height: 1.7;
  max-width: 640px;
  font-weight: 500;
}
.page-hero-meta {
  display: flex;
  align-items: center;
  gap: 24px;
  margin-top: 28px;
  flex-wrap: wrap;
}
.meta-item {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 12px;
  font-weight: 700;
  color: var(--muted);
  letter-spacing: .5px;
}
.meta-item i { color: var(--green); font-size: 13px; }

/* ══════════════════════════════════════
   TABLE OF CONTENTS
══════════════════════════════════════ */
.toc-bar {
  background: var(--bg3);
  border-bottom: 1px solid var(--line);
  padding: 20px 0;
  position: sticky;
  top: 124px;
  z-index: 100;
}
@media(max-width:768px){.toc-bar{top:102px;overflow-x:auto;}}
.toc-inner {
  max-width: 860px;
  margin: 0 auto;
  padding: 0 40px;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  align-items: center;
}
@media(max-width:768px){.toc-inner{padding:0 20px;flex-wrap:nowrap;white-space:nowrap;}}
.toc-label {
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--muted);
  margin-right: 8px;
  flex-shrink: 0;
}
.toc-link {
  font-size: 12px;
  font-weight: 700;
  color: var(--slate);
  text-decoration: none;
  padding: 5px 12px;
  border-radius: 100px;
  border: 1px solid var(--line);
  background: #fff;
  transition: all .2s;
  white-space: nowrap;
}
.toc-link:hover { background: var(--green); color: #fff; border-color: var(--green); }

/* ══════════════════════════════════════
   CONTENT
══════════════════════════════════════ */
.content-wrap {
  max-width: 860px;
  margin: 0 auto;
  padding: 72px 40px 96px;
}
@media(max-width:768px){.content-wrap{padding:48px 20px 72px;}}

.section-label {
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--green);
  margin-bottom: 10px;
  display: block;
}

.content-wrap h2 {
  font-family: 'Sora', sans-serif;
  font-size: clamp(19px, 2.5vw, 24px);
  font-weight: 800;
  color: var(--navy);
  letter-spacing: -.4px;
  margin-bottom: 14px;
  margin-top: 56px;
  padding-top: 56px;
  border-top: 1px solid var(--line);
  scroll-margin-top: 180px;
}
.content-wrap h2:first-of-type { margin-top: 0; padding-top: 0; border-top: none; }

.content-wrap p {
  font-size: 14.5px;
  color: var(--slate);
  line-height: 1.85;
  margin-bottom: 14px;
  font-weight: 500;
}
.content-wrap p strong { color: var(--navy); font-weight: 700; }
.content-wrap a { color: var(--blue); font-weight: 600; text-decoration: none; }
.content-wrap a:hover { text-decoration: underline; }

/* PARTIES BOX */
.parties-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin: 28px 0;
}
@media(max-width:560px){ .parties-grid { grid-template-columns: 1fr; } }
.party-card {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 22px 22px;
}
.party-card .party-role {
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--green-d);
  margin-bottom: 8px;
}
.party-card h4 {
  font-size: 15px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 6px;
}
.party-card p {
  font-size: 13px;
  color: var(--slate);
  line-height: 1.65;
  margin: 0;
  font-weight: 500;
}

/* SERVICES LIST */
.services-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin: 20px 0;
}
.services-list li {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  font-size: 14px;
  color: var(--slate);
  font-weight: 500;
  line-height: 1.65;
}
.services-list li i { color: var(--green); font-size: 14px; margin-top: 3px; flex-shrink: 0; }
.services-list li strong { color: var(--navy); font-weight: 700; }

/* PRICING TABLE */
.pricing-table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
  overflow: hidden;
  margin: 24px 0;
  border: 1px solid var(--line);
  font-size: 14px;
}
.pricing-table thead tr {
  background: var(--navy);
}
.pricing-table thead th {
  padding: 14px 18px;
  text-align: left;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(255,255,255,.8);
}
.pricing-table tbody tr {
  border-bottom: 1px solid var(--line);
  transition: background .15s;
}
.pricing-table tbody tr:last-child { border-bottom: none; }
.pricing-table tbody tr:hover { background: var(--bg2); }
.pricing-table tbody td {
  padding: 14px 18px;
  color: var(--slate);
  font-weight: 500;
  line-height: 1.5;
}
.pricing-table tbody td:first-child { font-weight: 700; color: var(--navy); }
.pricing-table tbody td .badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 1px;
  text-transform: uppercase;
  padding: 3px 8px;
  border-radius: 100px;
  background: var(--green-lt);
  color: var(--green-d);
  margin-left: 6px;
}

/* ALERT BOXES */
.info-alert {
  border-left: 4px solid var(--green);
  background: var(--green-lt);
  border-radius: var(--r);
  padding: 18px 22px;
  margin: 24px 0;
  display: flex;
  gap: 12px;
  align-items: flex-start;
}
.info-alert i { color: var(--green-d); font-size: 17px; margin-top: 2px; flex-shrink: 0; }
.info-alert p { font-size: 14px; color: var(--navy); font-weight: 600; line-height: 1.65; margin: 0; }
.info-alert strong { color: var(--green-d); }

.warn-alert {
  border-left: 4px solid var(--gold);
  background: var(--gold-lt);
  border-radius: var(--r);
  padding: 18px 22px;
  margin: 24px 0;
  display: flex;
  gap: 12px;
  align-items: flex-start;
}
.warn-alert i { color: #92610a; font-size: 17px; margin-top: 2px; flex-shrink: 0; }
.warn-alert p { font-size: 14px; color: var(--navy); font-weight: 600; line-height: 1.65; margin: 0; }

/* CLAUSE BLOCK */
.clause-block {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 24px 28px;
  margin: 20px 0;
}
.clause-block .clause-num {
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 8px;
}
.clause-block p {
  font-size: 14px;
  color: var(--slate);
  line-height: 1.8;
  margin: 0;
  font-weight: 500;
}
.clause-block p strong { color: var(--navy); font-weight: 700; }

/* DARK BOX */
.dark-box {
  background: var(--navy);
  border-radius: 14px;
  padding: 36px 40px;
  margin: 36px 0;
  position: relative;
  overflow: hidden;
}
@media(max-width:600px){.dark-box{padding:28px 24px;}}
.dark-box::before {
  content: '';
  position: absolute;
  bottom: -60px; right: -60px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: rgba(34,197,94,.07);
  pointer-events: none;
}
.dark-box h3 {
  font-family: 'Sora', sans-serif;
  font-size: 17px;
  font-weight: 800;
  color: #fff;
  margin-bottom: 12px;
  letter-spacing: -.3px;
}
.dark-box p {
  font-size: 14px;
  color: rgba(255,255,255,.7);
  line-height: 1.8;
  margin: 0 0 12px;
  font-weight: 500;
}
.dark-box p:last-of-type { margin-bottom: 0; }
.dark-box strong { color: #fff; font-weight: 700; }
.dark-box ul {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin: 14px 0 0;
}
.dark-box ul li {
  font-size: 13px;
  color: rgba(255,255,255,.75);
  font-weight: 600;
  display: flex;
  gap: 10px;
  align-items: flex-start;
}
.dark-box ul li i { color: var(--green); margin-top: 3px; flex-shrink: 0; }

/* SIGNATURE SECTION */
.sig-section {
  border: 2px dashed var(--line);
  border-radius: 12px;
  padding: 40px;
  margin: 40px 0;
  position: relative;
}
@media(max-width:600px){.sig-section{padding:24px 20px;}}
.sig-section .sig-label {
  position: absolute;
  top: -12px; left: 24px;
  background: #fff;
  padding: 2px 10px;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--green-d);
}
.sig-section h3 {
  font-family: 'Sora', sans-serif;
  font-size: 15px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 20px;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: .5px;
}
.sig-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
}
@media(max-width:560px){.sig-grid{grid-template-columns:1fr;gap:24px;}}
.sig-col .sig-title {
  font-size: 11px;
  font-weight: 900;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 16px;
  display: block;
}
.sig-field-label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .5px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 6px;
  margin-top: 16px;
  display: block;
}
.sig-line {
  border-bottom: 1.5px solid var(--navy);
  min-height: 34px;
}

.disclaimer-box {
  background: var(--bg3);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 24px 28px;
  margin-top: 56px;
}
.disclaimer-box p {
  font-size: 12px;
  color: var(--muted);
  line-height: 1.8;
  margin: 0;
  font-weight: 600;
}

/* CTA STRIP */
.cta-strip {
  background: var(--bg2);
  border-top: 1px solid var(--line);
  border-bottom: 1px solid var(--line);
  padding: 64px 0;
  text-align: center;
}
.cta-strip h2 {
  font-family: 'Sora', sans-serif;
  font-size: clamp(24px, 4vw, 36px);
  font-weight: 800;
  color: var(--navy);
  letter-spacing: -1px;
  margin-bottom: 12px;
}
.cta-strip p { font-size: 15px; color: var(--slate); font-weight: 500; margin-bottom: 28px; }
.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--green);
  color: #fff;
  font-size: 15px;
  font-weight: 800;
  padding: 16px 32px;
  border-radius: 100px;
  text-decoration: none;
  box-shadow: 0 4px 24px rgba(34,197,94,.35);
  transition: all .2s;
}
.cta-btn:hover { background: var(--green-d); transform: translateY(-2px); box-shadow: 0 8px 32px rgba(34,197,94,.4); }

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

@media(max-width:860px){.footer-grid{grid-template-columns:1fr 1fr;gap:32px;}footer{padding:48px 0 28px;}.footer-brand{grid-column:span 2;}}
@media(max-width:480px){.footer-grid{grid-template-columns:1fr;}.footer-brand{grid-column:span 1;}.fl-850{font-size:22px!important;}.footer-bottom{flex-direction:column;align-items:flex-start;gap:10px;}.footer-legal{flex-wrap:wrap;gap:12px;}.footer-copy{font-size:11px;}.footer-legal a{font-size:11px;}.fsoc{width:32px!important;height:32px!important;font-size:12px!important;}footer{padding:44px 0 24px!important;}}
</style>
</head>
<body>

<!-- TICKER BAR -->
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

<!-- NAVIGATION -->
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

<!-- MOBILE MENU -->
<div class="mob-menu" id="mobMenu">
  <a href="https://850ficoclub.com/#pricing" onclick="closeMob()">Packages</a>
  <a href="https://850ficoclub.com/funding" onclick="closeMob()">Funding</a>
  <a href="https://850ficoclub.com/#remove" onclick="closeMob()">Services</a>
  <a href="https://850ficoclub.com/#video-testimonials" onclick="closeMob()">Reviews</a>
  <a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button"
     target="_blank"
     class="mob-cta">Free Consultation</a>
</div>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="wrap">
    <div class="breadcrumb">
      <a href="https://850ficoclub.com/">Home</a>
      <span>›</span>
      <span>Service Agreement</span>
    </div>
    <div class="page-hero-badge"><i class="fas fa-file-contract"></i> Legal Agreement</div>
    <h1>Client <em>Service Agreement</em></h1>
    <p class="page-hero-sub">This Service Agreement governs the relationship between 850 FICO Club and each client. Please read this agreement carefully before enrolling in any service plan. By enrolling, you acknowledge that you have read and understood these terms.</p>
    <div class="page-hero-meta">
      <span class="meta-item"><i class="fas fa-gavel"></i> CROA Compliant</span>
      <span class="meta-item"><i class="fas fa-calendar-check"></i> Effective: January 1, 2026</span>
      <span class="meta-item"><i class="fas fa-landmark"></i> Governing Law: State of Michigan</span>
    </div>
  </div>
</section>

<!-- TABLE OF CONTENTS -->
<nav class="toc-bar" aria-label="Page sections">
  <div class="toc-inner">
    <span class="toc-label">Sections</span>
    <a href="#parties" class="toc-link">Parties</a>
    <a href="#services" class="toc-link">Services</a>
    <a href="#fees" class="toc-link">Fees &amp; Billing</a>
    <a href="#client-obligations" class="toc-link">Client Obligations</a>
    <a href="#cancellation" class="toc-link">Cancellation</a>
    <a href="#disclaimer" class="toc-link">No Guarantees</a>
    <a href="#limitations" class="toc-link">Limitations</a>
    <a href="#governing" class="toc-link">Governing Law</a>
    <a href="#signatures" class="toc-link">Signatures</a>
  </div>
</nav>

<!-- MAIN CONTENT -->
<main>
  <div class="content-wrap">

    <div class="info-alert">
      <i class="fas fa-circle-info"></i>
      <p>This agreement is entered into pursuant to the <strong>Credit Repair Organizations Act (CROA), 15 U.S.C. § 1679 et seq.</strong> and all applicable federal and state consumer protection laws. You have the right to cancel this agreement within <strong>3 business days</strong> of signing without penalty. See Section 5 for full cancellation terms.</p>
    </div>

    <!-- SECTION 1 — PARTIES -->
    <span class="section-label" id="parties">Section 01</span>
    <h2>Parties to This Agreement</h2>
    <p>This Client Service Agreement ("Agreement") is entered into between the following parties:</p>

    <div class="parties-grid">
      <div class="party-card">
        <div class="party-role">Service Provider</div>
        <h4>850 FICO Club</h4>
        <p>A credit education and consulting company providing dispute assistance guidance, credit report analysis, and consumer financial education services.<br><br>
        <strong>Contact:</strong> info@850ficoclub.com<br>
        <strong>Coverage:</strong> Nationwide — All 50 States</p>
      </div>
      <div class="party-card">
        <div class="party-role">Client</div>
        <h4>The Enrolled Individual</h4>
        <p>The individual who has enrolled in a service plan offered by 850 FICO Club and agreed to the terms of this Agreement. Client information is collected at the time of enrollment and maintained in the secure client portal.</p>
      </div>
    </div>

    <p>By enrolling in any service plan offered by 850 FICO Club, the Client agrees to be bound by all terms and conditions set forth in this Agreement. This Agreement constitutes the entire understanding between the parties with respect to the subject matter herein.</p>

    <!-- SECTION 2 — SERVICES -->
    <span class="section-label" id="services">Section 02</span>
    <h2>Description of Services</h2>
    <p>850 FICO Club provides credit education, credit report analysis, and dispute assistance guidance services to consumers. The specific services included in each membership plan are described below. All services are subject to the terms of this Agreement.</p>

    <p><strong>Services provided by 850 FICO Club may include, as applicable to the enrolled plan:</strong></p>

    <ul class="services-list">
      <li><i class="fas fa-check-circle"></i><span><strong>3-Bureau Credit Report Review</strong> — Analysis of credit reports from Equifax, Experian, and TransUnion to identify items that may be inaccurate, incomplete, or unverifiable.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Dispute Assistance</strong> — Preparation and submission guidance for dispute letters to consumer reporting agencies and/or original creditors for potentially inaccurate or unverifiable information.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Personal Information Updates</strong> — Assistance reviewing and correcting personal identifying information appearing on credit reports.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Credit Education &amp; Guidance</strong> — Personalized credit education, scoring education, and improvement strategy guidance.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Secure Client Portal Access</strong> — Real-time access to case status, dispute tracking, and communication through the 850 FICO Club client portal.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Credit-Building Strategy Guidance</strong> — Guidance on positive credit behaviors, utilization management, and account strategies to support long-term credit improvement.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Ongoing Monitoring Support</strong> — Continued review and guidance as dispute responses are received and credit profiles are updated over time.</span></li>
    </ul>

    <p>Services available under each plan tier (Gold, Platinum, or Couples) are defined at the time of enrollment and may include the items above as applicable to that plan level. 850 FICO Club reserves the right to update service offerings with reasonable notice to enrolled clients.</p>

    <!-- SECTION 3 — FEES -->
    <span class="section-label" id="fees">Section 03</span>
    <h2>Fees, Billing &amp; Payment Terms</h2>
    <p>Fees for services rendered by 850 FICO Club are structured as follows. In accordance with the Credit Repair Organizations Act (CROA), <strong>no fees are charged or collected before the promised services have been performed.</strong></p>

    <table class="pricing-table">
      <thead>
        <tr>
          <th>Plan</th>
          <th>Initial Fee</th>
          <th>Monthly Fee</th>
          <th>Coverage</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Gold Membership</td>
          <td>$249</td>
          <td>$199 / month</td>
          <td>1 Individual</td>
        </tr>
        <tr>
          <td>Platinum Membership <span class="badge">Popular</span></td>
          <td>$500</td>
          <td>$249 / month</td>
          <td>1 Individual</td>
        </tr>
        <tr>
          <td>Couples Membership</td>
          <td>$750</td>
          <td>$399 / month</td>
          <td>2 Individuals</td>
        </tr>
      </tbody>
    </table>

    <div class="clause-block">
      <div class="clause-num">Billing Terms</div>
      <p>The initial fee is charged upon enrollment and covers the first period of services. Recurring monthly fees are billed on the same calendar date each month thereafter. All fees are charged to the payment method on file at the time of billing. <strong>Client is responsible for maintaining a valid payment method on file at all times.</strong> Failed payments may result in a temporary suspension of services until the balance is resolved.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Refund Policy</div>
      <p>If you cancel within 3 business days of signing this Agreement, you are entitled to a full refund of any fees paid, as required by the Credit Repair Organizations Act. After the 3-day cancellation window, fees for services already performed are generally non-refundable. Clients who cancel after the 3-day period will not be billed for future monthly periods following the cancellation date. <strong>850 FICO Club does not issue refunds for services already rendered.</strong></p>
    </div>

    <!-- SECTION 4 — CLIENT OBLIGATIONS -->
    <span class="section-label" id="client-obligations">Section 04</span>
    <h2>Client Obligations &amp; Responsibilities</h2>
    <p>In order for 850 FICO Club to provide services effectively, the Client agrees to the following obligations:</p>

    <ul class="services-list">
      <li><i class="fas fa-circle-check"></i><span><strong>Provide Accurate Information</strong> — Client agrees to provide accurate, complete, and truthful information at all times, including personal identifying information, contact details, and credit-related information.</span></li>
      <li><i class="fas fa-circle-check"></i><span><strong>Grant Authorization</strong> — Client authorizes 850 FICO Club to access, review, and act on their behalf with respect to their credit reports and disputes as outlined in this Agreement.</span></li>
      <li><i class="fas fa-circle-check"></i><span><strong>Respond Promptly</strong> — Client agrees to respond to requests for information, documentation, or signatures in a timely manner. Delays caused by the Client may affect service timelines.</span></li>
      <li><i class="fas fa-circle-check"></i><span><strong>Maintain Current Contact Information</strong> — Client is responsible for keeping their contact information and payment method up to date in the client portal.</span></li>
      <li><i class="fas fa-circle-check"></i><span><strong>Not Make False Representations</strong> — Client agrees not to make any false or misleading statements to consumer reporting agencies, creditors, or any third parties in connection with the services provided under this Agreement.</span></li>
      <li><i class="fas fa-circle-check"></i><span><strong>Understand Limitations</strong> — Client acknowledges that 850 FICO Club cannot guarantee specific outcomes and that results vary based on individual credit profiles and circumstances.</span></li>
    </ul>

    <!-- SECTION 5 — CANCELLATION -->
    <span class="section-label" id="cancellation">Section 05</span>
    <h2>Cancellation &amp; Termination</h2>

    <div class="dark-box">
      <h3>3-Day Right to Cancel — Federal Law</h3>
      <p>Under the <strong>Credit Repair Organizations Act (CROA), 15 U.S.C. § 1679c</strong>, you have the right to cancel this Agreement without penalty or obligation before midnight of the third business day following the date you signed it.</p>
      <p>To cancel within the 3-day window, you must provide written notice to 850 FICO Club. Upon receipt of a valid cancellation notice, 850 FICO Club will refund any money paid within 10 calendar days.</p>
      <ul>
        <li><i class="fas fa-envelope"></i> Email: info@850ficoclub.com</li>
        <li><i class="fas fa-file-lines"></i> Or use the <a href="/notice-of-cancellation" style="color:var(--green);font-weight:700;">Notice of Cancellation form</a> on our website</li>
      </ul>
    </div>

    <p><strong>Cancellation After 3 Business Days:</strong> After the 3-day cancellation window, either party may terminate this Agreement with written notice. The Client will not be billed for any future monthly period following the effective cancellation date. Fees already charged for services performed are non-refundable.</p>

    <p><strong>Termination by 850 FICO Club:</strong> 850 FICO Club reserves the right to terminate this Agreement and suspend services if the Client: (a) fails to maintain a valid payment method, (b) provides false or misleading information, (c) engages in any conduct that violates applicable law, or (d) fails to cooperate with the service process. Written notice of termination will be provided.</p>

    <!-- SECTION 6 — NO GUARANTEES -->
    <span class="section-label" id="disclaimer">Section 06</span>
    <h2>No Guarantee of Results</h2>

    <div class="warn-alert">
      <i class="fas fa-triangle-exclamation"></i>
      <p>850 FICO Club does not guarantee the removal of any specific item from a credit report, any specific credit score increase, or any specific financial outcome. Results vary based on each consumer's individual credit profile and circumstances.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Important Disclosure — Required by Federal Law</div>
      <p>Under the Credit Repair Organizations Act, 850 FICO Club is required to disclose: <strong>We cannot promise to remove accurate, verifiable, and timely information from your credit report.</strong> No credit repair organization can legally guarantee the removal of accurate information. Any representation to the contrary is unlawful. The services provided under this Agreement are dispute assistance, credit education, and guidance services — not guarantees of any specific result.</p>
    </div>

    <p>Client acknowledges and understands that:</p>
    <ul class="services-list">
      <li><i class="fas fa-circle-exclamation" style="color:var(--gold);"></i><span>Credit score improvements, if any, depend on many factors outside the control of 850 FICO Club, including the decisions of consumer reporting agencies, creditors, and debt collectors.</span></li>
      <li><i class="fas fa-circle-exclamation" style="color:var(--gold);"></i><span>The dispute process timeline is governed by federal law (FCRA) and depends on the response of consumer reporting agencies, which may take up to 30–45 days per dispute cycle.</span></li>
      <li><i class="fas fa-circle-exclamation" style="color:var(--gold);"></i><span>Accurate, verifiable negative information cannot be removed from a credit report before its legally mandated expiration period.</span></li>
      <li><i class="fas fa-circle-exclamation" style="color:var(--gold);"></i><span>Individual results shown in marketing materials, testimonials, or examples represent specific client experiences and are not typical or guaranteed.</span></li>
    </ul>

    <!-- SECTION 7 — LIMITATIONS -->
    <span class="section-label" id="limitations">Section 07</span>
    <h2>Limitation of Liability</h2>
    <p>To the fullest extent permitted by applicable law, 850 FICO Club's total liability to the Client for any claims arising out of or related to this Agreement or the services provided shall not exceed the total fees paid by the Client in the 30-day period immediately preceding the event giving rise to the claim.</p>
    <p>850 FICO Club shall not be liable for any indirect, incidental, consequential, special, or punitive damages of any kind, including but not limited to lost profits, lost business opportunities, or reputational harm, even if advised of the possibility of such damages.</p>
    <p>850 FICO Club is not responsible for decisions made by consumer reporting agencies, creditors, lenders, or any third parties in response to disputes or other actions taken under this Agreement.</p>

    <div class="clause-block">
      <div class="clause-num">Indemnification</div>
      <p>The Client agrees to indemnify and hold harmless 850 FICO Club, its officers, employees, and agents from and against any claims, damages, or expenses arising out of: (a) Client's breach of this Agreement, (b) Client's provision of false or misleading information, or (c) Client's violation of any applicable law in connection with the services provided hereunder.</p>
    </div>

    <!-- SECTION 8 — PRIVACY -->
    <span class="section-label">Section 08</span>
    <h2>Privacy &amp; Data Use</h2>
    <p>850 FICO Club collects and processes personal information provided by the Client solely for the purpose of delivering the services described in this Agreement. Client information will not be sold to third parties for marketing purposes.</p>
    <p>By enrolling, the Client authorizes 850 FICO Club to access their credit reports and share necessary information with consumer reporting agencies, creditors, and other parties as required to perform the services under this Agreement.</p>
    <p>For full details on how 850 FICO Club collects, uses, and protects your personal information, please review our <a href="/privacy-policy">Privacy Policy</a>.</p>

    <!-- SECTION 9 — GOVERNING LAW -->
    <span class="section-label" id="governing">Section 09</span>
    <h2>Governing Law &amp; Dispute Resolution</h2>
    <p>This Agreement shall be governed by and construed in accordance with the laws of the <strong>State of Michigan</strong>, without regard to its conflict of law provisions, and applicable federal law including the Credit Repair Organizations Act (CROA) and the Fair Credit Reporting Act (FCRA).</p>

    <div class="clause-block">
      <div class="clause-num">Dispute Resolution</div>
      <p>In the event of any dispute arising out of or relating to this Agreement, the parties agree to first attempt to resolve the matter through good-faith negotiation. If the dispute cannot be resolved through negotiation within 30 days, either party may pursue legal remedies available under applicable law. Nothing in this Agreement limits the Client's right to pursue claims under federal consumer protection laws, including the CROA and FCRA.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Severability</div>
      <p>If any provision of this Agreement is found to be invalid, unenforceable, or contrary to applicable law, that provision shall be modified to the minimum extent necessary to make it enforceable, and the remaining provisions of this Agreement shall remain in full force and effect.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Entire Agreement</div>
      <p>This Agreement, together with the <a href="/privacy-policy">Privacy Policy</a>, <a href="/terms-of-service">Terms of Service</a>, <a href="/consumer-credit-file-rights">Consumer Credit File Rights</a>, and <a href="/notice-of-cancellation">Notice of Cancellation</a>, constitutes the entire agreement between the parties with respect to the subject matter herein and supersedes all prior discussions, representations, or agreements.</p>
    </div>

    <!-- SECTION 10 — SIGNATURES -->
    <span class="section-label" id="signatures">Section 10</span>
    <h2>Acknowledgment &amp; Signatures</h2>
    <p>By signing below, the Client acknowledges that they have read, understood, and agree to all terms and conditions of this Service Agreement, and that they have received a copy of the <strong>Consumer Credit File Rights</strong> disclosure and the <strong>Notice of Cancellation</strong> as required by federal law.</p>

    <div class="sig-section">
      <span class="sig-label">Signature Page</span>
      <h3>Agreement Signatures</h3>
      <div class="sig-grid">
        <div class="sig-col">
          <span class="sig-title">Client</span>
          <span class="sig-field-label">Full Legal Name (Print)</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">Signature</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">Date</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">Phone Number</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">Email Address</span>
          <div class="sig-line"></div>
        </div>
        <div class="sig-col">
          <span class="sig-title">850 FICO Club</span>
          <span class="sig-field-label">Authorized Representative</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">Signature</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">Date</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">Title</span>
          <div class="sig-line"></div>
          <span class="sig-field-label">&nbsp;</span>
          <div class="sig-line" style="opacity:0;pointer-events:none;"></div>
        </div>
      </div>
    </div>

    <div class="info-alert">
      <i class="fas fa-circle-check"></i>
      <p>A copy of this signed Agreement, along with the Consumer Credit File Rights disclosure and Notice of Cancellation, will be provided to the Client at the time of enrollment in accordance with CROA requirements.</p>
    </div>

    <div class="disclaimer-box">
      <p>This Service Agreement is provided in accordance with the Credit Repair Organizations Act, 15 U.S.C. § 1679 et seq. 850 FICO Club provides credit education, credit report analysis, and dispute assistance guidance. We do not guarantee the removal of accurate information or specific credit score increases. This document does not constitute legal advice. Clients should consult a licensed attorney for legal advice specific to their situation. © 2026 850 FICO Club. All rights reserved.</p>
    </div>

  </div>
</main>

<!-- CTA STRIP -->
<section class="cta-strip">
  <div class="wrap">
    <h2>Questions Before You Enroll?</h2>
    <p>Book a free consultation and we'll walk you through everything — no pressure, no obligation.</p>
    <a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button" target="_blank" class="cta-btn">
      <i class="fas fa-calendar-check"></i> Book a Free Consultation
    </a>
  </div>
</section>

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

<!-- SCRIPTS -->
<script src="https://link.msgsndr.com/js/form_embed.js"></script>
<script>
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

<!-- Page Views Tracking -->
<script
  src="https://link.msgsndr.com/js/external-tracking.js"
  data-tracking-id="tk_46603a0b41334768bca5f2642b6b2664">
</script>

</body>
</html>