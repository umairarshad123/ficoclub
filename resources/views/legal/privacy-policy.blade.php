<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy — 850 FICO Club</title>
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
  background: radial-gradient(circle, rgba(37,99,235,.05) 0%, transparent 70%);
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
  background: var(--bg3);
  color: var(--blue);
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  padding: 6px 14px;
  border-radius: 100px;
  margin-bottom: 20px;
  border: 1px solid rgba(37,99,235,.15);
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
}
.meta-item i { color: var(--green); font-size: 13px; }

/* TABLE OF CONTENTS */
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

/* CONTENT */
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

/* WHAT WE COLLECT CARDS */
.collect-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin: 24px 0 32px;
}
@media(max-width:560px){.collect-grid{grid-template-columns:1fr;}}
.collect-card {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 20px;
  display: flex;
  gap: 14px;
  align-items: flex-start;
  transition: box-shadow .2s;
}
.collect-card:hover { box-shadow: var(--sh); }
.collect-icon {
  width: 38px; height: 38px;
  border-radius: 8px;
  background: var(--green-lt);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  color: var(--green-d);
  flex-shrink: 0;
}
.collect-card h4 {
  font-size: 13px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 4px;
}
.collect-card p {
  font-size: 12.5px;
  color: var(--slate);
  line-height: 1.6;
  margin: 0;
  font-weight: 500;
}

/* PLAIN LIST */
.plain-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin: 16px 0 20px;
}
.plain-list li {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  font-size: 14px;
  color: var(--slate);
  font-weight: 500;
  line-height: 1.65;
}
.plain-list li i { color: var(--green); font-size: 13px; margin-top: 3px; flex-shrink: 0; }
.plain-list li strong { color: var(--navy); font-weight: 700; }

/* CLAUSE BLOCK */
.clause-block {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 22px 26px;
  margin: 18px 0;
}
.clause-block .clause-num {
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 8px;
}
.clause-block p { font-size: 14px; color: var(--slate); line-height: 1.8; margin: 0; font-weight: 500; }
.clause-block p strong { color: var(--navy); font-weight: 700; }

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

/* RIGHTS TABLE */
.rights-table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
  overflow: hidden;
  margin: 24px 0;
  border: 1px solid var(--line);
  font-size: 14px;
}
.rights-table thead tr { background: var(--navy); }
.rights-table thead th {
  padding: 13px 18px;
  text-align: left;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(255,255,255,.8);
}
.rights-table tbody tr { border-bottom: 1px solid var(--line); transition: background .15s; }
.rights-table tbody tr:last-child { border-bottom: none; }
.rights-table tbody tr:hover { background: var(--bg2); }
.rights-table tbody td { padding: 13px 18px; color: var(--slate); font-weight: 500; line-height: 1.5; vertical-align: top; }
.rights-table tbody td:first-child { font-weight: 700; color: var(--navy); white-space: nowrap; }
.rights-table .yes { color: var(--green-d); font-weight: 800; }

/* CONTACT DARK BOX */
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
.dark-box h3 { font-family:'Sora',sans-serif; font-size:17px; font-weight:800; color:#fff; margin-bottom:12px; letter-spacing:-.3px; }
.dark-box p { font-size:14px; color:rgba(255,255,255,.7); line-height:1.8; margin:0 0 10px; font-weight:500; }
.dark-box p:last-of-type { margin-bottom:0; }
.dark-box strong { color:#fff; font-weight:700; }
.dark-box ul { list-style:none; display:flex; flex-direction:column; gap:8px; margin:14px 0 0; }
.dark-box ul li { font-size:13px; color:rgba(255,255,255,.8); font-weight:600; display:flex; gap:10px; align-items:center; }
.dark-box ul li i { color:var(--green); flex-shrink:0; }

.disclaimer-box {
  background: var(--bg3);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 24px 28px;
  margin-top: 56px;
}
.disclaimer-box p { font-size: 12px; color: var(--muted); line-height: 1.8; margin: 0; font-weight: 600; }

/* CTA STRIP */
.cta-strip { background:var(--bg2); border-top:1px solid var(--line); border-bottom:1px solid var(--line); padding:64px 0; text-align:center; }
.cta-strip h2 { font-family:'Sora',sans-serif; font-size:clamp(24px,4vw,36px); font-weight:800; color:var(--navy); letter-spacing:-1px; margin-bottom:12px; }
.cta-strip p { font-size:15px; color:var(--slate); font-weight:500; margin-bottom:28px; }
.cta-btn { display:inline-flex; align-items:center; gap:10px; background:var(--green); color:#fff; font-size:15px; font-weight:800; padding:16px 32px; border-radius:100px; text-decoration:none; box-shadow:0 4px 24px rgba(34,197,94,.35); transition:all .2s; }
.cta-btn:hover { background:var(--green-d); transform:translateY(-2px); box-shadow:0 8px 32px rgba(34,197,94,.4); }

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
      <span>Privacy Policy</span>
    </div>
    <div class="page-hero-badge"><i class="fas fa-lock"></i> Privacy &amp; Data Protection</div>
    <h1>Privacy <em>Policy</em></h1>
    <p class="page-hero-sub">850 FICO Club is committed to protecting your personal information. This Privacy Policy explains what data we collect, how we use it, who we share it with, and what rights you have over your information.</p>
    <div class="page-hero-meta">
      <span class="meta-item"><i class="fas fa-calendar-check"></i> Effective: January 1, 2026</span>
      <span class="meta-item"><i class="fas fa-rotate"></i> Last Updated: January 1, 2026</span>
      <span class="meta-item"><i class="fas fa-landmark"></i> Governing Law: State of Michigan &amp; Federal Law</span>
    </div>
  </div>
</section>

<!-- TABLE OF CONTENTS -->
<nav class="toc-bar" aria-label="Page sections">
  <div class="toc-inner">
    <span class="toc-label">Sections</span>
    <a href="#information-we-collect" class="toc-link">What We Collect</a>
    <a href="#how-we-use" class="toc-link">How We Use It</a>
    <a href="#sharing" class="toc-link">Who We Share With</a>
    <a href="#cookies" class="toc-link">Cookies</a>
    <a href="#sms" class="toc-link">SMS &amp; Communications</a>
    <a href="#your-rights" class="toc-link">Your Rights</a>
    <a href="#security" class="toc-link">Security</a>
    <a href="#children" class="toc-link">Children</a>
    <a href="#changes" class="toc-link">Updates</a>
    <a href="#contact-us" class="toc-link">Contact</a>
  </div>
</nav>

<!-- MAIN CONTENT -->
<main>
  <div class="content-wrap">

    <div class="info-alert">
      <i class="fas fa-circle-info"></i>
      <p>This Privacy Policy applies to all services offered by <strong>850 FICO Club</strong>, including our website at <strong>850ficoclub.com</strong>, our client portal, and all credit education and dispute assistance services. By using our services, you consent to the data practices described in this policy.</p>
    </div>

    <!-- SECTION 1 -->
    <span class="section-label" id="information-we-collect">Section 01</span>
    <h2>Information We Collect</h2>
    <p>We collect information that is necessary to provide our credit education and dispute assistance services. The types of information we may collect include:</p>

    <div class="collect-grid">
      <div class="collect-card">
        <div class="collect-icon"><i class="fas fa-user"></i></div>
        <div>
          <h4>Personal Identification</h4>
          <p>Full legal name, date of birth, Social Security Number (for credit report access), current and previous addresses, phone number, and email address.</p>
        </div>
      </div>
      <div class="collect-card">
        <div class="collect-icon"><i class="fas fa-credit-card"></i></div>
        <div>
          <h4>Financial Information</h4>
          <p>Credit report data from Equifax, Experian, and TransUnion, account information, payment history, and billing details necessary to process your service fees.</p>
        </div>
      </div>
      <div class="collect-card">
        <div class="collect-icon"><i class="fas fa-comments"></i></div>
        <div>
          <h4>Communications</h4>
          <p>Records of correspondence between you and 850 FICO Club via phone, email, SMS, or through the client portal, including consultation notes and service requests.</p>
        </div>
      </div>
      <div class="collect-card">
        <div class="collect-icon"><i class="fas fa-globe"></i></div>
        <div>
          <h4>Technical &amp; Usage Data</h4>
          <p>IP address, browser type, device information, pages visited, time spent on site, and other analytics data collected automatically when you visit our website or use our portal.</p>
        </div>
      </div>
      <div class="collect-card">
        <div class="collect-icon"><i class="fas fa-file-signature"></i></div>
        <div>
          <h4>Service-Related Documents</h4>
          <p>Signed service agreements, authorization forms, dispute letters, bureau correspondence, and any documents submitted to or received from consumer reporting agencies on your behalf.</p>
        </div>
      </div>
      <div class="collect-card">
        <div class="collect-icon"><i class="fas fa-mobile-screen"></i></div>
        <div>
          <h4>SMS &amp; Marketing Consent</h4>
          <p>Records of your opt-in or opt-out status for SMS communications, appointment reminders, and marketing messages, including the date and method of consent.</p>
        </div>
      </div>
    </div>

    <p>We collect this information directly from you when you enroll in our services, fill out forms on our website, contact us by phone or email, or interact with our client portal. We may also receive information from third-party sources such as consumer reporting agencies when you authorize us to access your credit reports.</p>

    <!-- SECTION 2 -->
    <span class="section-label" id="how-we-use">Section 02</span>
    <h2>How We Use Your Information</h2>
    <p>850 FICO Club uses the information we collect for the following purposes:</p>

    <ul class="plain-list">
      <li><i class="fas fa-check-circle"></i><span><strong>To Provide Our Services</strong> — To deliver the credit education, credit report analysis, and dispute assistance services you enrolled in, including preparing and submitting dispute letters on your behalf.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>To Process Payments</strong> — To charge service fees according to your enrollment plan and maintain accurate billing records.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>To Communicate With You</strong> — To send service updates, case status notifications, appointment reminders, and responses to your inquiries via phone, email, or SMS.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>To Maintain Your Client Portal</strong> — To create, maintain, and update your secure client portal account with service progress and documentation.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>To Comply With Legal Obligations</strong> — To fulfill our legal obligations under the Fair Credit Reporting Act (FCRA), Credit Repair Organizations Act (CROA), and other applicable federal and state laws.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>To Improve Our Services</strong> — To analyze usage patterns and service outcomes in order to improve the quality and effectiveness of our offerings. This analysis uses aggregated, de-identified data only.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>To Prevent Fraud &amp; Protect Security</strong> — To detect, investigate, and prevent fraudulent transactions, unauthorized access, and other illegal activity.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>For Marketing Communications</strong> — With your prior consent, to send promotional information about our services or special offers. You may opt out at any time.</span></li>
    </ul>

    <p>We will not use your personal information for any purpose that is incompatible with the purposes described in this policy without first obtaining your consent or as otherwise required or permitted by law.</p>

    <!-- SECTION 3 -->
    <span class="section-label" id="sharing">Section 03</span>
    <h2>How We Share Your Information</h2>
    <p><strong>850 FICO Club does not sell your personal information to third parties.</strong> We may share your information with the following categories of recipients, only as necessary to provide services or comply with legal requirements:</p>

    <div class="clause-block">
      <div class="clause-num">Consumer Reporting Agencies</div>
      <p>We share information with Equifax, Experian, and TransUnion as necessary to access your credit reports and submit dispute correspondence on your behalf, pursuant to your written authorization.</p>
    </div>
    <div class="clause-block">
      <div class="clause-num">Service Providers &amp; Technology Partners</div>
      <p>We work with third-party service providers who assist us in operating our website, client portal, payment processing, and business communications. These providers are contractually required to protect your information and are permitted to use it only as directed by 850 FICO Club. Current service provider categories include CRM platforms, payment processors, SMS communication platforms, and website hosting providers.</p>
    </div>
    <div class="clause-block">
      <div class="clause-num">Legal &amp; Regulatory Authorities</div>
      <p>We may disclose your information when required by law, court order, or government regulation, or when we reasonably believe disclosure is necessary to protect the rights, property, or safety of 850 FICO Club, our clients, or the public.</p>
    </div>
    <div class="clause-block">
      <div class="clause-num">Business Transfers</div>
      <p>In the event that 850 FICO Club is involved in a merger, acquisition, or sale of assets, your personal information may be transferred to the successor entity. You will be notified of any such change and the applicable privacy policy of the new entity.</p>
    </div>

    <div class="info-alert">
      <i class="fas fa-shield-halved"></i>
      <p><strong>We never sell your personal data.</strong> Your information is used solely to provide the services you enrolled in and to communicate with you about those services. Third-party advertisers have no access to your personal information through 850 FICO Club.</p>
    </div>

    <!-- SECTION 4 -->
    <span class="section-label" id="cookies">Section 04</span>
    <h2>Cookies &amp; Tracking Technologies</h2>
    <p>Our website uses cookies and similar tracking technologies to improve your experience and understand how our site is used. Cookies are small data files placed on your device when you visit our website.</p>

    <ul class="plain-list">
      <li><i class="fas fa-circle-dot"></i><span><strong>Essential Cookies</strong> — Required for the website to function properly. These cannot be disabled without affecting core site functionality.</span></li>
      <li><i class="fas fa-circle-dot"></i><span><strong>Analytics Cookies</strong> — Used to understand how visitors interact with our website, which pages are most popular, and how visitors navigate through the site. We use this data in aggregate form.</span></li>
      <li><i class="fas fa-circle-dot"></i><span><strong>Marketing &amp; Tracking Pixels</strong> — We may use tracking pixels (including those provided by our CRM platform) to measure the effectiveness of our communications and advertising. These may track page visits and conversions.</span></li>
    </ul>

    <p>Most web browsers allow you to control cookies through their settings. Disabling certain cookies may affect the functionality of our website. You may also opt out of certain analytics tracking by adjusting your browser settings or using browser extensions designed for this purpose.</p>

    <!-- SECTION 5 -->
    <span class="section-label" id="sms">Section 05</span>
    <h2>SMS Communications &amp; Text Messaging</h2>
    <p>By providing your phone number and checking the SMS consent checkbox during enrollment or form submission, you agree to receive text messages from 850 FICO Club. These messages may include:</p>

    <ul class="plain-list">
      <li><i class="fas fa-message"></i><span>Service updates and case status notifications</span></li>
      <li><i class="fas fa-message"></i><span>Appointment reminders and consultation confirmations</span></li>
      <li><i class="fas fa-message"></i><span>Important account or billing notifications</span></li>
      <li><i class="fas fa-message"></i><span>Promotional offers (with separate consent)</span></li>
    </ul>

    <div class="clause-block">
      <div class="clause-num">How to Opt Out of SMS</div>
      <p>You may opt out of SMS communications at any time by replying <strong>STOP</strong> to any text message you receive from us. You may also contact us directly at <strong>info@850ficoclub.com</strong> or to request removal from SMS communications. Message and data rates may apply. Message frequency varies.</p>
    </div>

    <p>Opting out of SMS communications will not affect your ability to receive services or communicate with us through other channels such as email or phone.</p>

    <!-- SECTION 6 -->
    <span class="section-label" id="your-rights">Section 06</span>
    <h2>Your Privacy Rights</h2>
    <p>Depending on your state of residence and applicable law, you may have the following rights with respect to your personal information:</p>

    <table class="rights-table">
      <thead>
        <tr>
          <th>Right</th>
          <th>Description</th>
          <th>Available</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Right to Access</td>
          <td>Request a copy of the personal information we hold about you.</td>
          <td class="yes">✓ Yes</td>
        </tr>
        <tr>
          <td>Right to Correction</td>
          <td>Request correction of inaccurate or incomplete personal information.</td>
          <td class="yes">✓ Yes</td>
        </tr>
        <tr>
          <td>Right to Deletion</td>
          <td>Request deletion of your personal information, subject to legal retention requirements.</td>
          <td class="yes">✓ Yes</td>
        </tr>
        <tr>
          <td>Right to Opt Out of Marketing</td>
          <td>Opt out of receiving marketing communications from 850 FICO Club at any time.</td>
          <td class="yes">✓ Yes</td>
        </tr>
        <tr>
          <td>Right to Data Portability</td>
          <td>Request a copy of your data in a portable, machine-readable format where technically feasible.</td>
          <td class="yes">✓ Yes</td>
        </tr>
        <tr>
          <td>Right to Restrict Processing</td>
          <td>Request that we limit how we process your personal information in certain circumstances.</td>
          <td class="yes">✓ Yes</td>
        </tr>
        <tr>
          <td>Right to Non-Discrimination</td>
          <td>You will not receive different service quality or pricing for exercising your privacy rights.</td>
          <td class="yes">✓ Yes</td>
        </tr>
      </tbody>
    </table>

    <p>To exercise any of the rights above, please contact us using the information in Section 10 of this policy. We will respond to verified requests within 30 days. We may need to verify your identity before processing certain requests.</p>

    <p>Please note that some information may need to be retained to comply with our legal obligations under the FCRA, CROA, or other applicable laws, even after a deletion request.</p>

    <!-- SECTION 7 -->
    <span class="section-label" id="security">Section 07</span>
    <h2>Data Security</h2>
    <p>850 FICO Club takes the security of your personal information seriously. We implement reasonable administrative, technical, and physical safeguards designed to protect your information from unauthorized access, disclosure, alteration, or destruction.</p>

    <ul class="plain-list">
      <li><i class="fas fa-shield-halved"></i><span><strong>Encrypted Transmission</strong> — All data transmitted between your browser and our website is encrypted using SSL/TLS technology.</span></li>
      <li><i class="fas fa-shield-halved"></i><span><strong>Secure Client Portal</strong> — Access to your credit documents and case information is protected by secure login credentials through our third-party portal provider.</span></li>
      <li><i class="fas fa-shield-halved"></i><span><strong>Access Controls</strong> — We limit access to your personal information to employees and contractors who need it to perform their job functions.</span></li>
      <li><i class="fas fa-shield-halved"></i><span><strong>Data Breach Response</strong> — In the event of a data breach that affects your personal information, we will notify you as required by applicable law.</span></li>
    </ul>

    <p>While we take reasonable steps to protect your information, no method of electronic transmission or storage is 100% secure. We cannot guarantee the absolute security of your information, and you provide it at your own risk.</p>

    <!-- SECTION 8 -->
    <span class="section-label" id="children">Section 08</span>
    <h2>Children's Privacy</h2>
    <p>Our services are intended for adults aged 18 and older. <strong>850 FICO Club does not knowingly collect personal information from anyone under the age of 18.</strong> If you believe we have inadvertently collected information from a minor, please contact us immediately and we will take steps to delete that information as promptly as possible.</p>

    <!-- SECTION 9 -->
    <span class="section-label" id="changes">Section 09</span>
    <h2>Changes to This Privacy Policy</h2>
    <p>850 FICO Club reserves the right to update or modify this Privacy Policy at any time. When we make material changes, we will update the "Last Updated" date at the top of this page and, where appropriate, notify enrolled clients by email or through the client portal.</p>
    <p>Your continued use of our services after the effective date of any changes constitutes your acceptance of the updated Privacy Policy. We encourage you to review this page periodically to stay informed about how we protect your information.</p>

    <!-- SECTION 10 -->
    <span class="section-label" id="contact-us">Section 10</span>
    <h2>Contact Us About Privacy</h2>
    <p>If you have questions, concerns, or requests regarding this Privacy Policy or the personal information we hold about you, please contact us using any of the following methods:</p>

    <div class="dark-box">
      <h3>Privacy Contact — 850 FICO Club</h3>
      <p>Our team is available to address any privacy-related questions or to process rights requests. Please allow up to 30 days for a response to formal rights requests.</p>
      <ul>
        <li><i class="fas fa-envelope"></i> <strong>Email:</strong> info@850ficoclub.com</li>
        <li><i class="fas fa-clock"></i> <strong>Hours:</strong> Monday–Friday, 10AM–6PM EST</li>
        <li><i class="fas fa-earth-americas"></i> <strong>Coverage:</strong> Nationwide — All 50 States</li>
      </ul>
    </div>

    <div class="disclaimer-box">
      <p>This Privacy Policy is provided for informational purposes and constitutes a binding agreement between you and 850 FICO Club regarding the collection, use, and protection of your personal information. This policy does not constitute legal advice. For legal questions specific to your situation, please consult a licensed attorney. 850 FICO Club provides credit education and consulting services. We do not guarantee the removal of accurate information or specific credit score increases. © 2026 850 FICO Club. All rights reserved.</p>
    </div>

  </div>
</main>

<!-- CTA STRIP -->
<section class="cta-strip">
  <div class="wrap">
    <h2>Questions About Your Data?</h2>
    <p>Reach out directly — our team is here to help with any privacy-related concerns.</p>
    <a href="mailto:info@850ficoclub.com" class="cta-btn">
      <i class="fas fa-envelope"></i> Email Us Directly
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