<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms of Service — 850 FICO Club</title>
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
.plain-list li i.red { color: var(--red); }
.plain-list li i.gold { color: var(--gold); }
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
.clause-block p + p { margin-top: 10px; }

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
.warn-alert strong { color: #92610a; }

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
.dark-box h3 { font-family:'Sora',sans-serif; font-size:17px; font-weight:800; color:#fff; margin-bottom:12px; letter-spacing:-.3px; }
.dark-box p { font-size:14px; color:rgba(255,255,255,.72); line-height:1.8; margin:0 0 10px; font-weight:500; }
.dark-box p:last-of-type { margin-bottom:0; }
.dark-box strong { color:#fff; font-weight:700; }
.dark-box ul { list-style:none; display:flex; flex-direction:column; gap:8px; margin:14px 0 0; }
.dark-box ul li { font-size:13px; color:rgba(255,255,255,.8); font-weight:600; display:flex; gap:10px; align-items:flex-start; line-height:1.55; }
.dark-box ul li i { color:var(--green); flex-shrink:0; margin-top:3px; }

/* CAPS WARNING */
.caps-warning {
  background: #fff8ed;
  border: 2px solid var(--gold);
  border-radius: 10px;
  padding: 24px 28px;
  margin: 24px 0;
}
.caps-warning p {
  font-size: 13px;
  font-weight: 700;
  color: var(--navy);
  line-height: 1.75;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: .3px;
}

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
      <span>Terms of Service</span>
    </div>
    <div class="page-hero-badge"><i class="fas fa-scroll"></i> Legal Terms</div>
    <h1>Terms of <em>Service</em></h1>
    <p class="page-hero-sub">These Terms of Service govern your access to and use of 850 FICO Club's website, client portal, and all credit education and dispute assistance services. Please read them carefully before using our services.</p>
    <div class="page-hero-meta">
      <span class="meta-item"><i class="fas fa-calendar-check"></i> Effective: January 1, 2026</span>
      <span class="meta-item"><i class="fas fa-rotate"></i> Last Updated: January 1, 2026</span>
      <span class="meta-item"><i class="fas fa-landmark"></i> Governing Law: State of Michigan</span>
    </div>
  </div>
</section>

<!-- TABLE OF CONTENTS -->
<nav class="toc-bar" aria-label="Page sections">
  <div class="toc-inner">
    <span class="toc-label">Sections</span>
    <a href="#acceptance" class="toc-link">Acceptance</a>
    <a href="#services" class="toc-link">Services</a>
    <a href="#eligibility" class="toc-link">Eligibility</a>
    <a href="#accounts" class="toc-link">Accounts</a>
    <a href="#payments" class="toc-link">Payments</a>
    <a href="#prohibited" class="toc-link">Prohibited Use</a>
    <a href="#ip" class="toc-link">Intellectual Property</a>
    <a href="#disclaimer" class="toc-link">Disclaimers</a>
    <a href="#liability" class="toc-link">Liability</a>
    <a href="#termination" class="toc-link">Termination</a>
    <a href="#governing" class="toc-link">Governing Law</a>
    <a href="#contact" class="toc-link">Contact</a>
  </div>
</nav>

<!-- MAIN CONTENT -->
<main>
  <div class="content-wrap">

    <div class="info-alert">
      <i class="fas fa-circle-info"></i>
      <p><strong>By accessing our website or enrolling in any 850 FICO Club service, you agree to be bound by these Terms of Service.</strong> If you do not agree with any part of these terms, you must not use our website or services. These terms are subject to change — your continued use after changes are posted constitutes acceptance.</p>
    </div>

    <!-- SECTION 1 — ACCEPTANCE -->
    <span class="section-label" id="acceptance">Section 01</span>
    <h2>Acceptance of Terms</h2>
    <p>These Terms of Service ("Terms") constitute a legally binding agreement between you ("User," "Client," or "you") and <strong>850 FICO Club</strong> ("Company," "we," "us," or "our") governing your use of our website located at <strong>850ficoclub.com</strong>, our client portal, and all related services, features, and content (collectively, the "Services").</p>
    <p>By accessing or using our website, creating an account, enrolling in a service plan, or clicking "I Agree" on any form or agreement, you confirm that you have read, understood, and agree to be bound by these Terms and our <a href="/privacy-policy">Privacy Policy</a>, which is incorporated herein by reference.</p>
    <p>These Terms apply in addition to any separate <a href="/service-agreement">Service Agreement</a> you may sign upon enrollment. In the event of a conflict between these Terms and a signed Service Agreement, the Service Agreement shall control with respect to the specific services described therein.</p>

    <!-- SECTION 2 — SERVICES -->
    <span class="section-label" id="services">Section 02</span>
    <h2>Description of Services</h2>
    <p>850 FICO Club provides credit education, credit report analysis, and dispute assistance guidance services to consumers across all 50 United States. Our services are designed to help consumers understand their credit reports and exercise their rights under applicable federal consumer protection laws, including the Fair Credit Reporting Act (FCRA) and the Fair Debt Collection Practices Act (FDCPA).</p>

    <div class="clause-block">
      <div class="clause-num">What We Do</div>
      <p>We review your credit reports from the three major consumer reporting agencies (Equifax, Experian, and TransUnion), identify items that may be inaccurate, incomplete, or unverifiable, and provide dispute assistance guidance to help you challenge such items. We also provide credit education, credit-building strategy guidance, and ongoing monitoring support.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">What We Do Not Do</div>
      <p>850 FICO Club is not a law firm and does not provide legal advice. We are not a lender, a debt collector, a financial advisor, or a credit counseling agency. We do not negotiate debts on your behalf, provide legal representation, or guarantee any specific outcome related to your credit profile or financial situation.</p>
    </div>

    <p>Services are delivered as described in the applicable membership plan selected at enrollment. 850 FICO Club reserves the right to modify, update, or discontinue any service offering with reasonable notice to enrolled clients.</p>

    <!-- SECTION 3 — ELIGIBILITY -->
    <span class="section-label" id="eligibility">Section 03</span>
    <h2>Eligibility</h2>
    <p>To use our Services, you must meet the following eligibility requirements:</p>

    <ul class="plain-list">
      <li><i class="fas fa-check-circle"></i><span><strong>Age</strong> — You must be at least 18 years of age. Our services are not available to minors.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Residency</strong> — You must be a resident of the United States. Services are available in all 50 states.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Legal Capacity</strong> — You must have the legal capacity to enter into binding contracts under applicable law.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Accurate Information</strong> — You must provide truthful, accurate, and complete information when enrolling and throughout your engagement with our services.</span></li>
      <li><i class="fas fa-check-circle"></i><span><strong>Not Previously Terminated</strong> — You must not have had a prior account terminated by 850 FICO Club for cause.</span></li>
    </ul>

    <p>By using our Services, you represent and warrant that you meet all of the above eligibility requirements. If you do not meet these requirements, you must not access or use our Services.</p>

    <!-- SECTION 4 — ACCOUNTS -->
    <span class="section-label" id="accounts">Section 04</span>
    <h2>Client Accounts &amp; Portal Access</h2>
    <p>Upon enrollment, you will be provided with access to a secure client portal where you can monitor your case status, review dispute correspondence, and communicate with our team. Your account is personal to you and may not be shared or transferred.</p>

    <ul class="plain-list">
      <li><i class="fas fa-circle-dot"></i><span><strong>Account Security</strong> — You are responsible for maintaining the confidentiality of your login credentials and for all activity that occurs under your account. Notify us immediately at <a href="mailto:info@850ficoclub.com">info@850ficoclub.com</a> if you suspect unauthorized access.</span></li>
      <li><i class="fas fa-circle-dot"></i><span><strong>Accurate Information</strong> — You agree to keep your account information, including contact details and payment method, current and accurate at all times.</span></li>
      <li><i class="fas fa-circle-dot"></i><span><strong>No Sharing</strong> — You may not share your portal login with any third party or allow any other person to access our services through your account.</span></li>
      <li><i class="fas fa-circle-dot"></i><span><strong>Account Suspension</strong> — We reserve the right to suspend or terminate your account if we detect fraudulent activity, misrepresentation, or violations of these Terms.</span></li>
    </ul>

    <!-- SECTION 5 — PAYMENTS -->
    <span class="section-label" id="payments">Section 05</span>
    <h2>Payments, Fees &amp; Billing</h2>
    <p>By enrolling in a service plan, you authorize 850 FICO Club to charge your payment method the fees associated with your selected plan. All fees are described in detail in your <a href="/service-agreement">Service Agreement</a>.</p>

    <div class="clause-block">
      <div class="clause-num">CROA Fee Compliance</div>
      <p>In accordance with the <strong>Credit Repair Organizations Act (CROA)</strong>, 850 FICO Club does not charge or receive any payment for services before those services have been fully performed. The initial enrollment fee covers the first period of services rendered. Ongoing monthly fees are billed in arrears or following service delivery.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Recurring Billing</div>
      <p>Monthly service fees are charged automatically to your payment method on file on a recurring basis. You authorize these recurring charges by enrolling in a service plan. It is your responsibility to ensure your payment method remains valid and has sufficient funds.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Failed Payments</div>
      <p>If a payment fails, we will attempt to notify you and may retry the charge. Continued failure to pay may result in suspension or termination of your services. You remain responsible for any outstanding balances.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Refunds</div>
      <p>If you cancel within 3 business days of signing your Service Agreement, you are entitled to a full refund of any fees paid, as required by CROA. After the 3-day window, fees for services already performed are non-refundable. See the <a href="/notice-of-cancellation">Notice of Cancellation</a> for full cancellation terms.</p>
    </div>

    <!-- SECTION 6 — PROHIBITED USE -->
    <span class="section-label" id="prohibited">Section 06</span>
    <h2>Prohibited Uses</h2>
    <p>You agree not to use our website or services for any purpose that is unlawful, prohibited by these Terms, or otherwise harmful to 850 FICO Club or other users. The following uses are strictly prohibited:</p>

    <ul class="plain-list">
      <li><i class="fas fa-circle-xmark red"></i><span><strong>False Information</strong> — Providing false, misleading, or fraudulent information to 850 FICO Club, consumer reporting agencies, or any third party in connection with our services.</span></li>
      <li><i class="fas fa-circle-xmark red"></i><span><strong>Identity Fraud</strong> — Using our services to create a false identity, impersonate any person or entity, or obtain credit through fraudulent means.</span></li>
      <li><i class="fas fa-circle-xmark red"></i><span><strong>Unauthorized Access</strong> — Attempting to gain unauthorized access to our systems, client portal, or any other user's account or data.</span></li>
      <li><i class="fas fa-circle-xmark red"></i><span><strong>Reverse Engineering</strong> — Attempting to reverse engineer, decompile, or extract the source code of any software used in connection with our services.</span></li>
      <li><i class="fas fa-circle-xmark red"></i><span><strong>Automated Scraping</strong> — Using bots, scrapers, or automated tools to access, copy, or collect data from our website or portal without prior written consent.</span></li>
      <li><i class="fas fa-circle-xmark red"></i><span><strong>Interference</strong> — Transmitting viruses, malware, or other harmful code, or otherwise interfering with the operation of our website or services.</span></li>
      <li><i class="fas fa-circle-xmark red"></i><span><strong>Circumventing Law</strong> — Using our services to facilitate any activity that violates the FCRA, CROA, FDCPA, or any other applicable federal or state law.</span></li>
      <li><i class="fas fa-circle-xmark red"></i><span><strong>Commercial Resale</strong> — Reselling, sublicensing, or otherwise commercially exploiting any part of our services without prior written authorization from 850 FICO Club.</span></li>
    </ul>

    <p>Violation of these prohibitions may result in immediate termination of your account, forfeiture of any fees paid, and potential legal action.</p>

    <!-- SECTION 7 — IP -->
    <span class="section-label" id="ip">Section 07</span>
    <h2>Intellectual Property</h2>
    <p>All content on the 850 FICO Club website and client portal — including but not limited to text, graphics, logos, icons, images, audio clips, digital downloads, data compilations, and software — is the property of 850 FICO Club or its content suppliers and is protected by applicable intellectual property laws.</p>

    <div class="clause-block">
      <div class="clause-num">Limited License</div>
      <p>850 FICO Club grants you a limited, non-exclusive, non-transferable, revocable license to access and use our website and client portal solely for your personal, non-commercial use in connection with the services you have enrolled in. This license does not include the right to reproduce, distribute, modify, publicly display, or create derivative works from any content on our website.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Trademarks</div>
      <p>The 850 FICO Club name, logo, tagline "Credit Is King &amp; Cash Is Power," and all related marks are trademarks of 850 FICO Club. You may not use any of our trademarks without prior written permission. All other trademarks and service marks appearing on our website are the property of their respective owners.</p>
    </div>

    <p>Any unauthorized use of our intellectual property may result in legal action. If you believe any content on our site infringes your intellectual property rights, please contact us at <a href="mailto:info@850ficoclub.com">info@850ficoclub.com</a>.</p>

    <!-- SECTION 8 — DISCLAIMERS -->
    <span class="section-label" id="disclaimer">Section 08</span>
    <h2>Disclaimers &amp; No Guarantees</h2>

    <div class="warn-alert">
      <i class="fas fa-triangle-exclamation"></i>
      <p><strong>850 FICO Club does not guarantee any specific credit score increase, the removal of any specific item from a credit report, or any particular financial outcome.</strong> Results vary based on each consumer's individual credit profile and circumstances.</p>
    </div>

    <div class="caps-warning">
      <p>THE SERVICES ARE PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT. 850 FICO CLUB DOES NOT WARRANT THAT THE SERVICES WILL BE UNINTERRUPTED, ERROR-FREE, OR COMPLETELY SECURE.</p>
    </div>

    <p>Specifically, you acknowledge and agree that:</p>

    <ul class="plain-list">
      <li><i class="fas fa-circle-exclamation gold"></i><span>We cannot guarantee that any disputed item will be removed, modified, or corrected by a consumer reporting agency or information provider.</span></li>
      <li><i class="fas fa-circle-exclamation gold"></i><span>Accurate, verifiable, and timely negative information cannot lawfully be removed from a credit report before its legally mandated expiration period.</span></li>
      <li><i class="fas fa-circle-exclamation gold"></i><span>Credit score outcomes depend on decisions made by consumer reporting agencies, creditors, and lenders that are entirely outside our control.</span></li>
      <li><i class="fas fa-circle-exclamation gold"></i><span>Client examples, testimonials, and score improvement figures presented on our website represent individual experiences and are not typical or guaranteed results.</span></li>
      <li><i class="fas fa-circle-exclamation gold"></i><span>Information provided through our services is for educational purposes only and does not constitute legal, financial, or professional advice.</span></li>
    </ul>

    <!-- SECTION 9 — LIABILITY -->
    <span class="section-label" id="liability">Section 09</span>
    <h2>Limitation of Liability</h2>

    <div class="caps-warning">
      <p>TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, IN NO EVENT SHALL 850 FICO CLUB, ITS OFFICERS, DIRECTORS, EMPLOYEES, AGENTS, OR AFFILIATES BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES — INCLUDING LOST PROFITS, LOST DATA, LOSS OF GOODWILL, OR BUSINESS INTERRUPTION — ARISING OUT OF OR RELATED TO YOUR USE OF OUR SERVICES, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</p>
    </div>

    <p>In no event shall 850 FICO Club's total cumulative liability to you for all claims arising out of or related to these Terms or your use of our Services exceed the total amount of fees you paid to 850 FICO Club in the <strong>three (3) month period</strong> immediately preceding the claim.</p>

    <div class="clause-block">
      <div class="clause-num">Indemnification</div>
      <p>You agree to defend, indemnify, and hold harmless 850 FICO Club and its officers, directors, employees, and agents from and against any and all claims, damages, obligations, losses, liabilities, costs, and expenses (including reasonable attorneys' fees) arising from: (a) your use of the Services; (b) your violation of these Terms; (c) your violation of any third-party right, including any intellectual property right or privacy right; or (d) any claim that your use of the Services caused damage to a third party.</p>
    </div>

    <!-- SECTION 10 — TERMINATION -->
    <span class="section-label" id="termination">Section 10</span>
    <h2>Termination</h2>
    <p>Either party may terminate these Terms and your access to the Services at any time.</p>

    <div class="clause-block">
      <div class="clause-num">Termination by You</div>
      <p>You may cancel your service enrollment at any time by providing written notice to 850 FICO Club. Your <strong>3-day right to cancel without penalty</strong> under CROA is described in the <a href="/notice-of-cancellation">Notice of Cancellation</a>. After the 3-day window, standard cancellation terms under your Service Agreement apply. Cancellation does not entitle you to a refund of fees already charged for services rendered.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Termination by 850 FICO Club</div>
      <p>We reserve the right to suspend or permanently terminate your access to our Services, with or without notice, if you: (a) violate these Terms; (b) provide false or fraudulent information; (c) fail to pay fees when due; (d) engage in conduct that we reasonably believe harms our business, other clients, or third parties; or (e) as required by applicable law or regulation.</p>
    </div>

    <p>Upon termination, your right to access the client portal and receive services will cease immediately. Provisions of these Terms that by their nature should survive termination — including intellectual property rights, disclaimers, limitations of liability, and dispute resolution — shall survive.</p>

    <!-- SECTION 11 — THIRD PARTY LINKS -->
    <span class="section-label">Section 11</span>
    <h2>Third-Party Links &amp; Services</h2>
    <p>Our website may contain links to third-party websites or services, including consumer reporting agencies, scheduling tools, and payment processors. These links are provided for your convenience only. 850 FICO Club has no control over and assumes no responsibility for the content, privacy policies, or practices of any third-party websites or services.</p>
    <p>Your use of third-party websites and services is governed by their respective terms of service and privacy policies. We encourage you to review the terms and privacy policies of any third-party services you access through links on our website.</p>

    <!-- SECTION 12 — GOVERNING LAW -->
    <span class="section-label" id="governing">Section 12</span>
    <h2>Governing Law &amp; Dispute Resolution</h2>
    <p>These Terms shall be governed by and construed in accordance with the laws of the <strong>State of Michigan</strong>, without regard to its conflict of law provisions. All applicable federal laws, including but not limited to the CROA and FCRA, shall also govern these Terms.</p>

    <div class="clause-block">
      <div class="clause-num">Informal Resolution First</div>
      <p>Before initiating any formal legal proceeding, the parties agree to attempt to resolve any dispute through good-faith negotiation for a period of at least 30 days. Contact us at <strong>info@850ficoclub.com</strong> to initiate this process.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Federal Consumer Protection Rights Preserved</div>
      <p>Nothing in these Terms limits or waives any rights you may have under the FCRA, CROA, FDCPA, or any other federal or state consumer protection law. If any provision of these Terms is found to be unenforceable or invalid, that provision shall be limited or eliminated to the minimum extent necessary so that these Terms shall otherwise remain in full force and effect.</p>
    </div>

    <div class="clause-block">
      <div class="clause-num">Class Action Waiver</div>
      <p>To the extent permitted by applicable law, you agree that any dispute resolution proceeding will be conducted only on an individual basis, and not in a class, consolidated, or representative action. This waiver does not apply to claims brought under federal consumer protection statutes that expressly permit class actions.</p>
    </div>

    <!-- SECTION 13 — UPDATES -->
    <span class="section-label">Section 13</span>
    <h2>Changes to These Terms</h2>
    <p>850 FICO Club reserves the right to modify these Terms at any time. When we make material changes, we will update the "Last Updated" date at the top of this page and, where appropriate, notify enrolled clients via email or through the client portal.</p>
    <p>Your continued use of our website or services after the effective date of any revised Terms constitutes your acceptance of the changes. If you do not agree to the updated Terms, you must stop using our services and cancel your enrollment.</p>

    <!-- SECTION 14 — CONTACT -->
    <span class="section-label" id="contact">Section 14</span>
    <h2>Contact Us</h2>
    <p>If you have any questions about these Terms of Service, wish to report a violation, or need assistance with any aspect of our services, please contact us:</p>

    <div class="dark-box">
      <h3>850 FICO Club — Legal &amp; General Inquiries</h3>
      <p>Our team is available Monday through Friday, 10AM–6PM EST. We aim to respond to all inquiries within one business day.</p>
      <ul>
        <li><i class="fas fa-envelope"></i><span><strong>Email:</strong> info@850ficoclub.com</span></li>
        <li><i class="fas fa-clock"></i><span><strong>Hours:</strong> Monday–Friday, 10AM–6PM EST</span></li>
        <li><i class="fas fa-earth-americas"></i><span><strong>Coverage:</strong> Nationwide — All 50 States</span></li>
        <li><i class="fas fa-calendar-check"></i><span><strong>Book a Consultation:</strong> <a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button" target="_blank" style="color:var(--green);font-weight:700;">Schedule Online</a></span></li>
      </ul>
    </div>

    <div class="disclaimer-box">
      <p>These Terms of Service constitute the entire agreement between you and 850 FICO Club with respect to your use of the website and services, and supersede all prior agreements, representations, and understandings. 850 FICO Club provides credit education and consulting services. We do not guarantee the removal of accurate information or specific credit score increases. This document does not constitute legal advice. Consult a licensed attorney for advice specific to your legal situation. © 2026 850 FICO Club. All rights reserved.</p>
    </div>

  </div>
</main>

<!-- CTA STRIP -->
<section class="cta-strip">
  <div class="wrap">
    <h2>Ready to Get Started?</h2>
    <p>Book your free credit analysis — no obligation, no pressure.</p>
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