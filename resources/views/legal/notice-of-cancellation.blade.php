<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notice of Cancellation — 850 FICO Club</title>
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
  background: radial-gradient(circle, rgba(239,68,68,.05) 0%, transparent 70%);
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
  background: var(--red-lt);
  color: var(--red);
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
   CRITICAL NOTICE BANNER
══════════════════════════════════════ */
.critical-banner {
  background: var(--red);
  padding: 20px 0;
}
.critical-banner-inner {
  display: flex;
  align-items: center;
  gap: 16px;
  max-width: 860px;
  margin: 0 auto;
  padding: 0 40px;
}
@media(max-width:768px){.critical-banner-inner{padding:0 20px;flex-direction:column;text-align:center;gap:12px;}}
.critical-banner i {
  font-size: 28px;
  color: #fff;
  flex-shrink: 0;
}
.critical-banner p {
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  line-height: 1.6;
}
.critical-banner p strong {
  font-size: 16px;
  display: block;
  margin-bottom: 2px;
  letter-spacing: -.2px;
}

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
  font-size: clamp(20px, 3vw, 26px);
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

/* KEY FACT CARDS */
.key-facts {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin: 32px 0 40px;
}
@media(max-width:600px){.key-facts{grid-template-columns:1fr;}}

.key-card {
  border-radius: 12px;
  padding: 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.key-card.red { background: var(--red-lt); border: 1px solid rgba(239,68,68,.2); }
.key-card.green { background: var(--green-lt); border: 1px solid rgba(34,197,94,.2); }
.key-card.navy { background: var(--bg3); border: 1px solid var(--line); }

.key-card-icon { font-size: 22px; }
.key-card.red .key-card-icon { color: var(--red); }
.key-card.green .key-card-icon { color: var(--green-d); }
.key-card.navy .key-card-icon { color: var(--navy); }

.key-card h4 {
  font-family: 'Sora', sans-serif;
  font-size: 13px;
  font-weight: 800;
  color: var(--navy);
  line-height: 1.3;
}
.key-card p {
  font-size: 13px;
  color: var(--slate);
  line-height: 1.65;
  margin: 0;
  font-weight: 500;
}

/* FORMAL NOTICE BOX */
.formal-notice {
  background: var(--navy);
  border-radius: 14px;
  padding: 40px 40px 36px;
  margin: 40px 0;
  position: relative;
  overflow: hidden;
}
@media(max-width:600px){.formal-notice{padding:28px 24px;}}
.formal-notice::before {
  content: '';
  position: absolute;
  top: -60px; right: -60px;
  width: 240px; height: 240px;
  border-radius: 50%;
  background: rgba(34,197,94,.06);
  pointer-events: none;
}
.formal-notice-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid rgba(255,255,255,.08);
}
.formal-notice-header i { font-size: 24px; color: var(--gold); }
.formal-notice-header div { }
.formal-notice-header h3 {
  font-family: 'Sora', sans-serif;
  font-size: 18px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -.3px;
  margin-bottom: 3px;
}
.formal-notice-header span {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--gold);
}
.formal-notice p {
  font-size: 14px;
  color: rgba(255,255,255,.75);
  line-height: 1.8;
  margin-bottom: 14px;
  font-weight: 500;
}
.formal-notice p:last-child { margin-bottom: 0; }
.formal-notice strong { color: #fff; font-weight: 700; }
.formal-notice .notice-highlight {
  background: rgba(34,197,94,.12);
  border: 1px solid rgba(34,197,94,.2);
  border-radius: 8px;
  padding: 16px 20px;
  margin: 20px 0;
}
.formal-notice .notice-highlight p {
  font-size: 15px;
  color: #fff;
  font-weight: 700;
  margin: 0;
  line-height: 1.6;
}

/* STEPS */
.steps-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  margin: 28px 0;
  position: relative;
}
.steps-list::before {
  content: '';
  position: absolute;
  left: 19px;
  top: 40px;
  bottom: 40px;
  width: 2px;
  background: var(--line);
}
.step-item {
  display: flex;
  gap: 20px;
  align-items: flex-start;
  padding: 20px 0;
  position: relative;
  z-index: 1;
}
.step-num {
  min-width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--navy);
  color: #fff;
  font-size: 13px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-family: 'Sora', sans-serif;
}
.step-body h4 {
  font-size: 15px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 5px;
}
.step-body p {
  font-size: 14px;
  color: var(--slate);
  line-height: 1.7;
  margin: 0;
  font-weight: 500;
}
.step-body p strong { color: var(--navy); font-weight: 700; }

/* MAIL FORM */
.mail-form-box {
  border: 2px dashed var(--line);
  border-radius: 12px;
  padding: 36px 40px;
  margin: 36px 0;
  position: relative;
}
@media(max-width:600px){.mail-form-box{padding:24px 20px;}}
.mail-form-box .form-label {
  position: absolute;
  top: -12px;
  left: 24px;
  background: #fff;
  padding: 2px 10px;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--green-d);
}
.mail-form-box h3 {
  font-family: 'Sora', sans-serif;
  font-size: 16px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 20px;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: .5px;
}
.form-field {
  margin-bottom: 18px;
}
.form-field label {
  display: block;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 6px;
}
.form-field .field-line {
  border-bottom: 1.5px solid var(--navy);
  padding-bottom: 6px;
  font-size: 14px;
  color: var(--slate);
  font-weight: 500;
  min-height: 30px;
}
.form-field .field-line.blank {
  color: transparent;
  user-select: none;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
@media(max-width:500px){.form-row{grid-template-columns:1fr;}}

.mail-form-box .sig-area {
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1px solid var(--line);
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
@media(max-width:500px){.mail-form-box .sig-area{grid-template-columns:1fr;}}

.sig-field label {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--muted);
  display: block;
  margin-bottom: 8px;
}
.sig-line {
  border-bottom: 1.5px solid var(--navy);
  height: 36px;
}

.send-to-box {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: var(--r);
  padding: 20px 24px;
  margin-top: 20px;
}
.send-to-box p {
  font-size: 13px;
  font-weight: 700;
  color: var(--navy);
  margin: 0 0 4px;
}
.send-to-box address {
  font-size: 13px;
  font-style: normal;
  color: var(--slate);
  font-weight: 500;
  line-height: 1.7;
}
.send-to-box a { color: var(--blue); font-weight: 600; text-decoration: none; }
.send-to-box a:hover { text-decoration: underline; }

/* INFO ALERTS */
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
.warn-alert i { color: var(--gold); font-size: 17px; margin-top: 2px; flex-shrink: 0; }
.warn-alert p { font-size: 14px; color: var(--navy); font-weight: 600; line-height: 1.65; margin: 0; }

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
      <span>Notice of Cancellation</span>
    </div>
    <div class="page-hero-badge"><i class="fas fa-circle-xmark"></i> Your Right to Cancel</div>
    <h1>Notice of <em>Cancellation</em></h1>
    <p class="page-hero-sub">Under the Credit Repair Organizations Act (CROA), you have the absolute right to cancel your contract with 850 FICO Club within three business days — without any penalty or obligation whatsoever.</p>
    <div class="page-hero-meta">
      <span class="meta-item"><i class="fas fa-gavel"></i> Credit Repair Organizations Act (CROA)</span>
      <span class="meta-item"><i class="fas fa-calendar-check"></i> Last Updated: January 2026</span>
      <span class="meta-item"><i class="fas fa-landmark"></i> Federal Law — 15 U.S.C. § 1679</span>
    </div>
  </div>
</section>

<!-- CRITICAL BANNER -->
<div class="critical-banner">
  <div class="critical-banner-inner">
    <i class="fas fa-triangle-exclamation"></i>
    <p>
      <strong>You May Cancel This Contract Without Penalty Within 3 Business Days</strong>
      This is a federally required notice under the Credit Repair Organizations Act. You do not need a reason to cancel. No fees will be charged if you cancel within the allowable window.
    </p>
  </div>
</div>

<!-- MAIN CONTENT -->
<main>
  <div class="content-wrap">

    <!-- KEY FACTS -->
    <div class="key-facts">
      <div class="key-card red">
        <div class="key-card-icon"><i class="fas fa-clock"></i></div>
        <h4>3 Business Days</h4>
        <p>You have 3 full business days from the date you signed your contract to cancel — no questions asked.</p>
      </div>
      <div class="key-card green">
        <div class="key-card-icon"><i class="fas fa-dollar-sign"></i></div>
        <h4>Zero Penalty</h4>
        <p>Cancellation within the 3-day window carries absolutely no fees, charges, or financial obligations.</p>
      </div>
      <div class="key-card navy">
        <div class="key-card-icon"><i class="fas fa-file-signature"></i></div>
        <h4>Written Notice Required</h4>
        <p>Your cancellation must be submitted in writing. Use the form below or send written notice to our address.</p>
      </div>
    </div>

    <!-- SECTION 1 -->
    <span class="section-label">Section 01</span>
    <h2>Your Right to Cancel — Federal Law</h2>
    <p>Under the <strong>Credit Repair Organizations Act (CROA), 15 U.S.C. § 1679 et seq.</strong>, you have the right to cancel your contract with 850 FICO Club at any time before midnight of the third business day after the date you signed the contract.</p>
    <p>This right cannot be waived or limited in any way. Any provision in a contract that attempts to waive this right is void and unenforceable under federal law.</p>

    <div class="info-alert">
      <i class="fas fa-circle-check"></i>
      <p><strong>"Business days"</strong> means any day other than Sunday or a federal legal public holiday. Count the day after you signed your contract as Day 1.</p>
    </div>

    <!-- FORMAL NOTICE -->
    <div class="formal-notice">
      <div class="formal-notice-header">
        <i class="fas fa-scale-balanced"></i>
        <div>
          <h3>Official Federal Notice — CROA § 1679c</h3>
          <span>Mandatory Disclosure</span>
        </div>
      </div>
      <p>You entered into a contract with <strong>850 FICO Club</strong> for credit repair services. Federal law provides that:</p>
      <div class="notice-highlight">
        <p>"You have a right to cancel this contract, for any reason, within 3 business days from the date you signed it."</p>
      </div>
      <p>If you cancel, 850 FICO Club must refund any money paid by you within <strong>10 calendar days</strong> of receiving your cancellation notice. 850 FICO Club must also return any contracts or other documents you may have signed.</p>
      <p>To cancel, you must notify us in writing. You may use the cancellation form on this page, send a written letter, or send an email clearly stating your intent to cancel and identifying your contract.</p>
    </div>

    <!-- SECTION 2 -->
    <span class="section-label">Section 02</span>
    <h2>How to Cancel Your Contract</h2>
    <p>To exercise your right to cancel, you must notify 850 FICO Club in writing within 3 business days of signing your service agreement. Follow the steps below:</p>

    <div class="steps-list">
      <div class="step-item">
        <div class="step-num">1</div>
        <div class="step-body">
          <h4>Prepare Your Written Notice</h4>
          <p>Write a clear statement that you are canceling your contract with 850 FICO Club. Include your full name, the date you signed your contract, and your contact information. You may use the cancellation form below.</p>
        </div>
      </div>
      <div class="step-item">
        <div class="step-num">2</div>
        <div class="step-body">
          <h4>Send Before the Deadline</h4>
          <p>Your written notice must be <strong>sent</strong> (not just postmarked) before midnight of the third business day following the date of your contract. If you are mailing the notice, use a method that provides delivery confirmation.</p>
        </div>
      </div>
      <div class="step-item">
        <div class="step-num">3</div>
        <div class="step-body">
          <h4>Deliver to 850 FICO Club</h4>
          <p>Send your cancellation notice to us via email at <strong>info@850ficoclub.com</strong> Keep a copy of everything you send.</p>
        </div>
      </div>
      <div class="step-item">
        <div class="step-num">4</div>
        <div class="step-body">
          <h4>Receive Your Refund</h4>
          <p>Once we receive your valid cancellation notice, 850 FICO Club is required by law to <strong>refund any payments made within 10 calendar days</strong> and return any signed documents to you.</p>
        </div>
      </div>
    </div>

    <!-- SECTION 3 — CANCELLATION FORM -->
    <span class="section-label">Section 03</span>
    <h2>Cancellation Form</h2>
    <p>You may use the form below as your written notice of cancellation. Print this page, complete all fields, sign, and deliver to 850 FICO Club via email, phone, or certified mail before the deadline.</p>

    <div class="mail-form-box">
      <span class="form-label">Cancellation Form — Print &amp; Complete</span>
      <h3>Notice of Cancellation</h3>

      <div class="form-row">
        <div class="form-field">
          <label>Date of Contract (Original Signing Date)</label>
          <div class="field-line blank">__________________________</div>
        </div>
        <div class="form-field">
          <label>Date of This Cancellation Notice</label>
          <div class="field-line blank">__________________________</div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Client Full Name</label>
          <div class="field-line blank">__________________________</div>
        </div>
        <div class="form-field">
          <label>Phone Number</label>
          <div class="field-line blank">__________________________</div>
        </div>
      </div>

      <div class="form-field">
        <label>Client Mailing Address</label>
        <div class="field-line blank">__________________________</div>
      </div>

      <div class="form-field">
        <label>Email Address on File</label>
        <div class="field-line blank">__________________________</div>
      </div>

      <div class="form-field" style="margin-top:20px;">
        <label>Statement of Cancellation</label>
        <div class="field-line" style="min-height:56px;padding-top:6px;color:var(--slate);font-size:14px;line-height:1.6;">
          I hereby cancel my contract with 850 FICO Club, effective the date shown above. I understand that I am entitled to a full refund of any money paid within 10 calendar days of receipt of this notice.
        </div>
      </div>

      <div class="sig-area">
        <div class="sig-field">
          <label>Client Signature</label>
          <div class="sig-line"></div>
        </div>
        <div class="sig-field">
          <label>Date Signed</label>
          <div class="sig-line"></div>
        </div>
      </div>

      <div class="send-to-box">
        <p>Submit This Form To:</p>
        <address>
          <strong>850 FICO Club</strong><br>
          Email: <a href="mailto:info@850ficoclub.com">info@850ficoclub.com</a><br>
          Coverage: Nationwide — All 50 States
        </address>
      </div>
    </div>

    <div class="warn-alert">
      <i class="fas fa-triangle-exclamation"></i>
      <p>Keep a copy of your completed cancellation form for your records. If mailing, use certified mail with return receipt requested so you have proof of delivery and the date sent.</p>
    </div>

    <!-- SECTION 4 -->
    <span class="section-label">Section 04</span>
    <h2>Cancellation After 3 Business Days</h2>
    <p>If more than 3 business days have passed since you signed your contract, you may still cancel your services at any time by providing written notice to 850 FICO Club. However, the automatic no-penalty cancellation right under CROA applies only within the 3-business-day window.</p>
    <p>For cancellations outside the 3-day window, please refer to the terms outlined in your <a href="/service-agreement" style="color:var(--blue);font-weight:700;">Service Agreement</a> or contact us directly to discuss your options. We are committed to transparent communication and will work with you in good faith.</p>

    <div class="info-alert">
      <i class="fas fa-phone"></i>
      <p>Have questions about your cancellation rights or need assistance? Email <strong>info@850ficoclub.com</strong>. Our team is available Monday–Friday, 10AM–6PM EST.</p>
    </div>

    <!-- SECTION 5 -->
    <span class="section-label">Section 05</span>
    <h2>Prohibited Practices Under CROA</h2>
    <p>The Credit Repair Organizations Act prohibits credit repair companies from engaging in the following practices. 850 FICO Club is fully committed to compliance with all applicable federal law:</p>

    <div class="steps-list">
      <div class="step-item">
        <div class="step-num" style="background:var(--red);">✗</div>
        <div class="step-body">
          <h4>Charging Fees Before Services Are Performed</h4>
          <p>Under CROA, a credit repair organization may not charge or receive any money before the promised services have been fully performed.</p>
        </div>
      </div>
      <div class="step-item">
        <div class="step-num" style="background:var(--red);">✗</div>
        <div class="step-body">
          <h4>Making False or Misleading Statements</h4>
          <p>Credit repair organizations may not make any untrue or misleading representation of services, or advise clients to make any untrue or misleading statement to a consumer reporting agency or creditor.</p>
        </div>
      </div>
      <div class="step-item">
        <div class="step-num" style="background:var(--red);">✗</div>
        <div class="step-body">
          <h4>Altering Consumer Identification</h4>
          <p>Credit repair organizations may not advise any consumer to alter their identification for the purpose of concealing adverse credit history.</p>
        </div>
      </div>
      <div class="step-item">
        <div class="step-num" style="background:var(--red);">✗</div>
        <div class="step-body">
          <h4>Waiving Consumer Rights</h4>
          <p>Any waiver of a consumer's rights under CROA is void and unenforceable. 850 FICO Club will never ask you to waive your legal rights.</p>
        </div>
      </div>
    </div>

    <div class="disclaimer-box">
      <p>This Notice of Cancellation is provided in accordance with the requirements of the Credit Repair Organizations Act, 15 U.S.C. § 1679c. 850 FICO Club provides credit education, credit report analysis, and dispute assistance guidance. We do not guarantee the removal of accurate information or specific credit score increases. This page does not constitute legal advice. For legal advice specific to your situation, consult a licensed attorney. © 2026 850 FICO Club. All rights reserved.</p>
    </div>

  </div>
</main>

<!-- CTA STRIP -->
<section class="cta-strip">
  <div class="wrap">
    <h2>Questions About Your Services?</h2>
    <p>Our team is here to help. Book a free consultation </p>
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