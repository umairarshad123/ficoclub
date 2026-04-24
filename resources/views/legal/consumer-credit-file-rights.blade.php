<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consumer Credit File Rights — 850 FICO Club</title>
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
  top: -120px; right: -120px;
  width: 500px; height: 500px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(34,197,94,.07) 0%, transparent 70%);
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
.page-hero h1 em {
  font-style: normal;
  color: var(--green);
}
.page-hero-sub {
  font-size: 16px;
  color: var(--slate);
  line-height: 1.7;
  max-width: 620px;
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
   CONTENT
══════════════════════════════════════ */
.content-wrap {
  max-width: 860px;
  margin: 0 auto;
  padding: 72px 40px 96px;
}
@media(max-width:768px){.content-wrap{padding:48px 20px 72px;}}

.law-alert {
  background: var(--green-lt);
  border-left: 4px solid var(--green);
  border-radius: var(--r);
  padding: 20px 24px;
  margin-bottom: 48px;
  display: flex;
  gap: 14px;
  align-items: flex-start;
}
.law-alert i { color: var(--green-d); font-size: 18px; margin-top: 2px; flex-shrink: 0; }
.law-alert p { font-size: 14px; color: var(--navy); font-weight: 600; line-height: 1.65; }
.law-alert strong { color: var(--green-d); }

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
  font-size: clamp(22px, 3vw, 28px);
  font-weight: 800;
  color: var(--navy);
  letter-spacing: -.5px;
  margin-bottom: 16px;
  margin-top: 56px;
  padding-top: 56px;
  border-top: 1px solid var(--line);
}
.content-wrap h2:first-of-type { margin-top: 0; padding-top: 0; border-top: none; }

.content-wrap p {
  font-size: 15px;
  color: var(--slate);
  line-height: 1.8;
  margin-bottom: 16px;
  font-weight: 500;
}
.content-wrap p strong { color: var(--navy); font-weight: 700; }

.rights-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin: 28px 0;
}
@media(max-width:600px){.rights-grid{grid-template-columns:1fr;}}

.right-card {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 22px 20px;
  display: flex;
  gap: 14px;
  align-items: flex-start;
  transition: box-shadow .2s, transform .2s;
}
.right-card:hover {
  box-shadow: var(--sh2);
  transform: translateY(-2px);
}
.right-icon {
  width: 40px; height: 40px;
  border-radius: 8px;
  background: var(--green-lt);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 16px;
  color: var(--green-d);
}
.right-card-text h4 {
  font-size: 13px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 5px;
  line-height: 1.3;
}
.right-card-text p {
  font-size: 12px;
  color: var(--slate);
  line-height: 1.6;
  margin: 0;
  font-weight: 500;
}

.numbered-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin: 24px 0;
}
.numbered-list li {
  display: flex;
  gap: 16px;
  align-items: flex-start;
}
.num-badge {
  min-width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--navy);
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 1px;
  flex-shrink: 0;
}
.numbered-list li p {
  margin: 0;
  font-size: 14px;
  color: var(--slate);
  line-height: 1.7;
  font-weight: 500;
}
.numbered-list li p strong { color: var(--navy); font-weight: 700; }

.highlight-box {
  background: var(--navy);
  border-radius: 12px;
  padding: 32px;
  margin: 40px 0;
  position: relative;
  overflow: hidden;
}
.highlight-box::before {
  content: '';
  position: absolute;
  top: -40px; right: -40px;
  width: 200px; height: 200px;
  border-radius: 50%;
  background: rgba(34,197,94,.08);
  pointer-events: none;
}
.highlight-box h3 {
  font-family: 'Sora', sans-serif;
  font-size: 18px;
  font-weight: 800;
  color: #fff;
  margin-bottom: 12px;
  letter-spacing: -.3px;
}
.highlight-box p {
  font-size: 14px;
  color: rgba(255,255,255,.7);
  line-height: 1.75;
  margin: 0 0 20px;
  font-weight: 500;
}
.highlight-box .hb-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--green);
  color: #fff;
  font-size: 13px;
  font-weight: 800;
  padding: 12px 22px;
  border-radius: 100px;
  text-decoration: none;
  transition: all .2s;
  box-shadow: 0 4px 16px rgba(34,197,94,.35);
}
.highlight-box .hb-link:hover { background: var(--green-d); transform: translateY(-2px); }

.agency-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin: 24px 0;
}
.agency-list li {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: var(--r);
  padding: 16px 20px;
  font-size: 14px;
  color: var(--slate);
  font-weight: 500;
  line-height: 1.65;
}
.agency-list li strong { color: var(--navy); font-weight: 700; display: block; margin-bottom: 2px; }
.agency-list li a { color: var(--blue); text-decoration: none; font-weight: 600; }
.agency-list li a:hover { text-decoration: underline; }

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
  line-height: 1.75;
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
.cta-strip p {
  font-size: 15px;
  color: var(--slate);
  font-weight: 500;
  margin-bottom: 28px;
}
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
      <span>Consumer Credit File Rights</span>
    </div>
    <div class="page-hero-badge"><i class="fas fa-shield-halved"></i> Your Federal Rights</div>
    <h1>Consumer <em>Credit File</em> Rights</h1>
    <p class="page-hero-sub">Under the Fair Credit Reporting Act (FCRA), you have important rights regarding your credit file. Understanding these rights is the first step toward taking control of your financial profile.</p>
    <div class="page-hero-meta">
      <span class="meta-item"><i class="fas fa-gavel"></i> Fair Credit Reporting Act</span>
      <span class="meta-item"><i class="fas fa-calendar-check"></i> Last Updated: January 2026</span>
      <span class="meta-item"><i class="fas fa-landmark"></i> Federal Law — All 50 States</span>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<main>
  <div class="content-wrap">

    <div class="law-alert">
      <i class="fas fa-circle-info"></i>
      <p>This page contains a summary of your rights under the <strong>Fair Credit Reporting Act (FCRA), 15 U.S.C. § 1681 et seq.</strong> The FCRA gives you specific rights in dealing with consumer reporting agencies (CRAs) — the companies that compile and sell credit reports. This is a federally mandated disclosure required for all credit repair organizations.</p>
    </div>

    <!-- Section 1 -->
    <span class="section-label">Section 01</span>
    <h2>You Must Be Told If Information Has Been Used Against You</h2>
    <p>Anyone who uses a credit report or another type of consumer report to deny your application for credit, insurance, or employment — or to take another adverse action against you — must tell you, and must give you the name, address, and phone number of the agency that provided the information.</p>

    <!-- Section 2 -->
    <span class="section-label">Section 02</span>
    <h2>Your Core Rights Under the FCRA</h2>
    <p>The Fair Credit Reporting Act gives every American consumer the following rights with respect to their credit file and consumer reports:</p>

    <div class="rights-grid">
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-file-lines"></i></div>
        <div class="right-card-text">
          <h4>Right to Know What's in Your File</h4>
          <p>You have the right to know what information is in your file at any consumer reporting agency at any time.</p>
        </div>
      </div>
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-eye-slash"></i></div>
        <div class="right-card-text">
          <h4>Right to Know Your Credit Score</h4>
          <p>You have the right to ask for your credit score. Consumer reporting agencies may charge a reasonable fee.</p>
        </div>
      </div>
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-magnifying-glass"></i></div>
        <div class="right-card-text">
          <h4>Right to Dispute Inaccurate Information</h4>
          <p>If you identify information in your file that is inaccurate or incomplete, you have the right to dispute it.</p>
        </div>
      </div>
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-trash-can"></i></div>
        <div class="right-card-text">
          <h4>Right to Remove Outdated Information</h4>
          <p>In most cases, a CRA may not report negative information that is more than 7 years old (10 years for bankruptcy).</p>
        </div>
      </div>
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-ban"></i></div>
        <div class="right-card-text">
          <h4>Right to Limit Prescreened Offers</h4>
          <p>You may opt out of unsolicited credit and insurance offers based on information in your credit file.</p>
        </div>
      </div>
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-lock"></i></div>
        <div class="right-card-text">
          <h4>Right to Seek Damages</h4>
          <p>If a CRA or user of consumer reports violates the FCRA, you may seek damages in state or federal court.</p>
        </div>
      </div>
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-shield"></i></div>
        <div class="right-card-text">
          <h4>Right to Place a Security Freeze</h4>
          <p>You have the right to place a security freeze on your credit file to prevent new credit from being opened in your name.</p>
        </div>
      </div>
      <div class="right-card">
        <div class="right-icon"><i class="fas fa-id-card"></i></div>
        <div class="right-card-text">
          <h4>Identity Theft Protections</h4>
          <p>If you are a victim of identity theft, you have additional rights including blocking fraudulent information from appearing on your report.</p>
        </div>
      </div>
    </div>

    <!-- Section 3 -->
    <span class="section-label">Section 03</span>
    <h2>Your Free Annual Credit Report</h2>
    <p>You have the right to receive one free copy of your credit report every 12 months from each of the three major consumer reporting agencies — Equifax, Experian, and TransUnion — by visiting the official federally authorized source:</p>
    <ul class="numbered-list">
      <li>
        <span class="num-badge">1</span>
        <p><strong>AnnualCreditReport.com</strong> — The only federally authorized source for free credit reports from all three bureaus. Visit <a href="https://www.annualcreditreport.com" target="_blank" style="color:var(--blue);font-weight:700;">www.annualcreditreport.com</a> or call <strong>1-877-322-8228</strong>.</p>
      </li>
      <li>
        <span class="num-badge">2</span>
        <p><strong>Equifax</strong> — P.O. Box 740241, Atlanta, GA 30374-0241 | <strong>(800) 685-1111</strong> | <a href="https://www.equifax.com" target="_blank" style="color:var(--blue);font-weight:700;">equifax.com</a></p>
      </li>
      <li>
        <span class="num-badge">3</span>
        <p><strong>Experian</strong> — P.O. Box 2002, Allen, TX 75013 | <strong>(888) 397-3742</strong> | <a href="https://www.experian.com" target="_blank" style="color:var(--blue);font-weight:700;">experian.com</a></p>
      </li>
      <li>
        <span class="num-badge">4</span>
        <p><strong>TransUnion</strong> — P.O. Box 2000, Chester, PA 19016 | <strong>(800) 916-8800</strong> | <a href="https://www.transunion.com" target="_blank" style="color:var(--blue);font-weight:700;">transunion.com</a></p>
      </li>
    </ul>

    <!-- Section 4 -->
    <span class="section-label">Section 04</span>
    <h2>Your Right to Dispute Inaccurate or Incomplete Information</h2>
    <p>Under the FCRA, both the consumer reporting agency and the information provider (i.e., the person, company, or organization that provides information about you to a CRA) are responsible for correcting inaccurate or incomplete information in your report.</p>
    <p>To take advantage of all your rights under this law, contact the consumer reporting agency and the information provider directly. The process works as follows:</p>

    <ul class="numbered-list">
      <li>
        <span class="num-badge">1</span>
        <p><strong>Tell the CRA in writing</strong> what information you believe is inaccurate or incomplete. Include copies (not originals) of documents that support your position. The CRA must investigate your dispute within 30 days unless it considers your dispute frivolous.</p>
      </li>
      <li>
        <span class="num-badge">2</span>
        <p><strong>The CRA must forward</strong> all relevant information you provide about the dispute to the information provider. After the information provider receives notice of the dispute from a CRA, it must investigate and report the results back to the CRA.</p>
      </li>
      <li>
        <span class="num-badge">3</span>
        <p><strong>If the information is inaccurate</strong> or cannot be verified, the information provider must notify all nationwide CRAs to correct your file. Disputed information that cannot be verified must be deleted from your file.</p>
      </li>
      <li>
        <span class="num-badge">4</span>
        <p><strong>You may also add a statement</strong> (up to 100 words) to your credit file explaining any dispute that was investigated but not resolved to your satisfaction.</p>
      </li>
      <li>
        <span class="num-badge">5</span>
        <p><strong>If your dispute results in a change</strong> to your report, the CRA cannot reinsert into your file a disputed item unless the information provider certifies that it is accurate and complete. The CRA must also notify you in writing within 5 days of reinserting the item.</p>
      </li>
    </ul>

    <!-- Section 5 -->
    <span class="section-label">Section 05</span>
    <h2>How Long Can Negative Information Remain on Your Report?</h2>
    <p>Under the FCRA, most negative information can only appear on your credit report for a limited period of time:</p>

    <ul class="numbered-list">
      <li>
        <span class="num-badge" style="background:var(--red);">!</span>
        <p><strong>Bankruptcy (Chapter 7)</strong> — Up to 10 years from the date of filing</p>
      </li>
      <li>
        <span class="num-badge" style="background:var(--red);">!</span>
        <p><strong>Bankruptcy (Chapter 13)</strong> — Up to 7 years from the date of filing</p>
      </li>
      <li>
        <span class="num-badge" style="background:var(--gold);color:var(--navy);">!</span>
        <p><strong>Collections, Charge-Offs, Late Payments</strong> — Up to 7 years from the date of the first delinquency</p>
      </li>
      <li>
        <span class="num-badge" style="background:var(--gold);color:var(--navy);">!</span>
        <p><strong>Judgments &amp; Civil Suits</strong> — Up to 7 years or until the statute of limitations expires, whichever is longer</p>
      </li>
      <li>
        <span class="num-badge" style="background:var(--green);">✓</span>
        <p><strong>Positive Information</strong> — Can remain on your report indefinitely and generally helps your credit profile</p>
      </li>
      <li>
        <span class="num-badge" style="background:var(--green);">✓</span>
        <p><strong>Hard Inquiries</strong> — Up to 2 years from the date of the inquiry</p>
      </li>
    </ul>

    <!-- Section 6 -->
    <span class="section-label">Section 06</span>
    <h2>Your Rights Regarding Credit Repair Organizations</h2>
    <p>Under the <strong>Credit Repair Organizations Act (CROA)</strong>, credit repair companies are prohibited from taking certain actions and you have specific rights when working with them. These protections include:</p>

    <ul class="numbered-list">
      <li>
        <span class="num-badge">1</span>
        <p>A credit repair organization <strong>cannot charge you any fees</strong> before the promised services have been fully performed.</p>
      </li>
      <li>
        <span class="num-badge">2</span>
        <p>You have the right to <strong>cancel your contract with any credit repair organization</strong> within 3 business days without any penalty or obligation.</p>
      </li>
      <li>
        <span class="num-badge">3</span>
        <p>Credit repair organizations <strong>must give you a copy</strong> of the "Consumer Credit File Rights Under State and Federal Law" before you sign a contract — this document fulfills that requirement.</p>
      </li>
      <li>
        <span class="num-badge">4</span>
        <p>All contracts with credit repair organizations <strong>must be in writing</strong> and must specify the services to be performed, the timeframe, total cost, and any guarantees.</p>
      </li>
      <li>
        <span class="num-badge">5</span>
        <p>Credit repair organizations <strong>cannot make false claims</strong> about their services or your legal rights, or advise you to make untrue or misleading statements to a CRA.</p>
      </li>
    </ul>

    <div class="highlight-box">
      <h3>Important Notice from 850 FICO Club</h3>
      <p>850 FICO Club provides credit education, credit report analysis, and dispute assistance guidance. We do not guarantee the removal of accurate information or specific credit score increases. You have the right to dispute inaccurate information directly with consumer reporting agencies at no cost.</p>
      <p>No one — including 850 FICO Club — can legally remove accurate, timely negative information from a credit report. If you have questions about your rights or our services, please contact us directly.</p>
      <a href="https://app.acuityscheduling.com/schedule/6f79e0fc/appointment/81383610/calendar/12495171?ref=booking_button" target="_blank" class="hb-link">
        <i class="fas fa-calendar-check"></i> Book a Free Consultation
      </a>
    </div>

    <!-- Section 7 -->
    <span class="section-label">Section 07</span>
    <h2>Where to File a Complaint</h2>
    <p>If you believe a consumer reporting agency or information provider has violated the FCRA, you may file a complaint with the following agencies:</p>

    <ul class="agency-list">
      <li>
        <strong>Consumer Financial Protection Bureau (CFPB)</strong>
        1700 G Street NW, Washington, DC 20552 | 1-855-411-2372 | <a href="https://www.consumerfinance.gov" target="_blank">consumerfinance.gov</a>
      </li>
      <li>
        <strong>Federal Trade Commission (FTC)</strong>
        600 Pennsylvania Ave NW, Washington, DC 20580 | 1-877-382-4357 | <a href="https://www.ftc.gov" target="_blank">ftc.gov</a>
      </li>
      <li>
        <strong>Your State Attorney General's Office</strong>
        Many states have additional consumer protection laws. Contact your state attorney general's office for state-specific rights and remedies.
      </li>
    </ul>

    <div class="disclaimer-box">
      <p>This notice is provided in accordance with the requirements of the Credit Repair Organizations Act, 15 U.S.C. § 1679 et seq., and the Fair Credit Reporting Act, 15 U.S.C. § 1681 et seq. 850 FICO Club provides this disclosure as a federally required notice to all prospective clients. The information on this page is provided for general educational purposes and does not constitute legal advice. For legal advice regarding your specific situation, please consult a licensed attorney. Results of credit repair services vary by individual and no specific outcome is guaranteed.</p>
    </div>

  </div>
</main>

<!-- CTA STRIP -->
<section class="cta-strip">
  <div class="wrap">
    <h2>Ready to Review Your Credit Report?</h2>
    <p>Book a free credit analysis. We'll walk through your 3-bureau report and explain what may be affecting your score.</p>
    <a href="https://api.leadconnectorhq.com/widget/booking/jdPgwX73WgVZQuCJKFlI" target="_blank" class="cta-btn">
      <i class="fas fa-calendar-check"></i> Get My Free Credit Analysis
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