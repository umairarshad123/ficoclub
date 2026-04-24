<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Secure Enrollment</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<style>
    :root {
      --navy:#071428;--navy-2:#0d1f3c;--navy-3:#122040;
      --green:#22c55e;--green-2:#16a34a;--green-3:#0d9440;
      --green-glow:rgba(34,197,94,.35);--green-dim:#f0fdf4;--green-mid:#dcfce7;
      --orange:#f97316;--gold:#f59e0b;--gold-dim:#fffbeb;
      --white:#ffffff;--bg:#ffffff;--bg-2:#f8fafc;
      --rule:#e2e8f0;--rule-2:#cbd5e1;
      --ink:#071428;--ink-2:#1e293b;--ink-3:#475569;--ink-4:#94a3b8;
      --red:#dc2626;--red-dim:#fef2f2;
      --r:16px;--r-sm:10px;--t:.22s;
      --ease:cubic-bezier(.25,.46,.45,.94);
      --ease-spring:cubic-bezier(.34,1.56,.64,1);
      --sh-sm:0 1px 4px rgba(7,20,40,.06);
      --sh:0 8px 32px rgba(7,20,40,.10);
      --sh-lg:0 20px 60px rgba(7,20,40,.14);
      --sh-green:0 8px 32px rgba(34,197,94,.22);
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html{scroll-behavior:smooth;}
    body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--ink);-webkit-font-smoothing:antialiased;overflow-x:hidden;}
    #particleCanvas{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;opacity:.55;}

    /* HERO */
    .hero-band{background:linear-gradient(135deg,var(--navy) 0%,#0a1e3c 50%,#061530 100%);padding:56px 20px 64px;text-align:center;position:relative;overflow:hidden;z-index:10;}
    .hero-band::before{content:"";position:absolute;inset:0;background:radial-gradient(ellipse 70% 60% at 50% 100%,rgba(34,197,94,.13) 0%,transparent 70%);pointer-events:none;}
    .hero-band::after{content:"";position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--green),transparent);}
    .hero-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:10px;font-weight:700;letter-spacing:.28em;text-transform:uppercase;color:var(--green);font-family:'Syne',sans-serif;margin-bottom:18px;animation:fadeInDown .6s var(--ease) both;}
    .hero-eyebrow::before,.hero-eyebrow::after{content:"";width:28px;height:1px;background:linear-gradient(90deg,transparent,var(--green));}
    .hero-eyebrow::after{background:linear-gradient(90deg,var(--green),transparent);}
    .hero-title{font-family:'Syne',sans-serif;font-size:clamp(32px,5vw,52px);font-weight:800;color:var(--white);line-height:1.08;letter-spacing:-.025em;margin-bottom:16px;animation:fadeInDown .7s var(--ease) .1s both;}
    .hero-title .hl-green{color:var(--green);text-shadow:0 0 30px rgba(34,197,94,.4);}
    .hero-sub{font-size:15px;font-weight:400;color:rgba(255,255,255,.6);line-height:1.75;max-width:520px;margin:0 auto 28px;animation:fadeInDown .8s var(--ease) .2s both;}
    .hero-chips{display:flex;justify-content:center;flex-wrap:wrap;gap:10px;animation:fadeInDown .9s var(--ease) .3s both;}
    .hero-chip{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:999px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);font-size:12px;font-weight:500;color:rgba(255,255,255,.75);backdrop-filter:blur(8px);}
    .hero-chip .chip-dot{width:6px;height:6px;border-radius:50%;background:var(--green);box-shadow:0 0 6px var(--green);}
    .hero-next-btn-wrap{display:flex;justify-content:center;margin-top:50px;animation:fadeInDown 1s var(--ease) .4s both;}
    .hero-next-btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 22px;border-radius:999px;background:linear-gradient(135deg,var(--green) 0%,var(--green-2) 100%);color:var(--white);text-decoration:none;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;letter-spacing:.04em;box-shadow:0 10px 30px rgba(34,197,94,.28);border:1px solid rgba(255,255,255,.14);transition:all .25s var(--ease);}
    .hero-next-btn:hover{transform:translateY(-2px);box-shadow:0 14px 38px rgba(34,197,94,.38);filter:brightness(1.05);}
    .glow-orb{position:absolute;border-radius:50%;pointer-events:none;filter:blur(80px);opacity:.25;}
    @keyframes fadeInDown{from{opacity:0;transform:translateY(-14px)}to{opacity:1;transform:translateY(0)}}

    /* FORM SECTION */
    .form-section{background:var(--bg);padding:56px 20px 80px;position:relative;z-index:10;}
    .form-section-inner{max-width:700px;margin:0 auto;}

    /* PROGRESS */
    .prog-wrap{background:var(--white);border:1px solid var(--rule);border-radius:var(--r);box-shadow:var(--sh-sm);padding:20px 24px 18px;margin-bottom:20px;position:relative;overflow:hidden;}
    .prog-wrap::before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--navy),var(--green));border-radius:999px;}
    .prog-steps{display:flex;align-items:center;margin-bottom:14px;}
    .ps{display:flex;align-items:center;gap:10px;flex-shrink:0;}
    .ps-num{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;font-family:'Syne',sans-serif;border:2px solid var(--rule-2);background:var(--white);color:var(--ink-4);transition:all .35s var(--ease-spring);}
    .ps.active .ps-num{background:var(--navy);border-color:var(--navy);color:var(--white);box-shadow:0 0 0 4px rgba(7,20,40,.1),0 4px 14px rgba(7,20,40,.2);transform:scale(1.08);}
    .ps.done .ps-num{background:var(--green);border-color:var(--green);color:var(--white);font-size:0;box-shadow:0 0 0 4px rgba(34,197,94,.15);}
    .ps.done .ps-num::after{content:"✓";font-size:13px;font-weight:900;}
    .ps-label{font-size:12px;font-weight:600;color:var(--ink-4);white-space:nowrap;font-family:'Syne',sans-serif;letter-spacing:.03em;}
    .ps.active .ps-label{color:var(--navy);font-weight:700;}
    .ps.done .ps-label{color:var(--green-2);}
    .ps-conn{flex:1;height:2px;background:var(--rule);margin:0 10px;border-radius:999px;overflow:hidden;position:relative;}
    .ps-conn::after{content:"";position:absolute;inset:0;background:var(--green);transform:scaleX(0);transform-origin:left;transition:transform .5s var(--ease);}
    .ps-conn.filled::after{transform:scaleX(1);}
    .prog-track{height:5px;background:var(--rule);border-radius:999px;overflow:hidden;}
    .prog-fill{height:100%;width:33.333%;background:linear-gradient(90deg,var(--navy),var(--green));border-radius:999px;transition:width .55s var(--ease);position:relative;}
    .prog-fill::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.55),transparent);animation:shimmer 2.2s linear infinite;}
    @keyframes shimmer{0%{transform:translateX(-100%)}100%{transform:translateX(200%)}}

    /* FORM CARD */
    .form-card{background:var(--white);border:1px solid var(--rule);border-radius:20px;box-shadow:var(--sh-lg);overflow:hidden;position:relative;}
    .form-card::before{content:"";position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--navy) 0%,#1d4ed8 35%,var(--green) 65%,#16a34a 100%);}
    .card-head{padding:26px 32px 22px;border-bottom:1px solid var(--rule);background:linear-gradient(180deg,#f8fafc,#fff);display:flex;align-items:center;gap:16px;}
    .ch-icon{width:48px;height:48px;border-radius:14px;background:var(--navy);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--white);box-shadow:0 6px 20px rgba(7,20,40,.2);transition:transform .3s var(--ease-spring);}
    .ch-icon:hover{transform:scale(1.06) rotate(-3deg);}
    .ch-text .ch-step{font-size:10px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--green-2);margin-bottom:3px;font-family:'Syne',sans-serif;}
    .ch-text h3{font-size:21px;font-weight:800;color:var(--navy);letter-spacing:-.015em;line-height:1;font-family:'Syne',sans-serif;}
    .ch-text p{font-size:13px;font-weight:400;color:var(--ink-3);margin-top:4px;}
    .form-body{padding:30px 32px 32px;}
    .form-step{display:none;}
    .form-step.active{display:block;animation:stepIn .3s var(--ease);}
    @keyframes stepIn{from{opacity:0;transform:translateX(16px)}to{opacity:1;transform:translateX(0)}}

    /* FIELDS */
    .fgrid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
    .ffull{grid-column:1/-1;}
    .fg{display:flex;flex-direction:column;gap:7px;}
    .flabel{font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3);font-family:'Syne',sans-serif;}
    .opt{font-size:10.5px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--ink-4);margin-left:3px;}
    .req{color:var(--green-2);margin-left:2px;}
    .fw{position:relative;}
    .fi{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:var(--ink-4);pointer-events:none;transition:color var(--t) var(--ease);}
    input,select{width:100%;height:54px;border-radius:var(--r-sm);border:1.5px solid var(--rule-2);background:#fafbfc;color:var(--ink);font-family:'DM Sans',sans-serif;font-size:15px;font-weight:500;padding:0 18px 0 46px;outline:none;transition:all var(--t) var(--ease);appearance:none;}
    input::placeholder{color:var(--ink-4);font-weight:400;}
    select{padding-right:38px;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='7'%3E%3Cpath d='M1 1l4.5 4.5L10 1' stroke='%2394a3b8' stroke-width='1.6' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;background-color:#fafbfc;}
    input:focus,select:focus{border-color:var(--navy);background:var(--white);box-shadow:0 0 0 3px rgba(7,20,40,.09),var(--sh-sm);transform:translateY(-1px);}
    .fw:focus-within .fi{color:var(--navy);}
    input.valid,select.valid{border-color:var(--green);background:var(--green-dim);}
    input.invalid,select.invalid{border-color:var(--red);background:var(--red-dim);}
    .ferr{display:none;font-size:11.5px;font-weight:600;color:var(--red);align-items:center;gap:5px;}
    .ferr.show{display:flex;}
    .ferr.show::before{content:"!";width:15px;height:15px;border-radius:50%;background:var(--red);color:#fff;font-size:9px;font-weight:900;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .fhint{font-size:12px;font-weight:400;color:var(--ink-4);line-height:1.55;}
    .ssn-eye{position:absolute;right:13px;top:50%;transform:translateY(-50%);width:32px;height:32px;border-radius:8px;border:1px solid var(--rule-2);background:var(--bg-2);color:var(--ink-4);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all var(--t) var(--ease);z-index:3;}
    .ssn-eye:hover{border-color:var(--navy);color:var(--navy);background:#f0f4ff;}

    /* ── PREFILLED / READONLY PLAN FIELDS ── */
    .plan-field-wrap{
      position:relative;
    }
    .plan-field-wrap .fi{color:var(--green-2);}
    input.plan-prefilled{
      background:linear-gradient(135deg,#f0fdf4,#dcfce7)!important;
      border-color:#86efac!important;
      color:var(--ink-2)!important;
      font-weight:700!important;
      cursor:default!important;
    }
    input.plan-prefilled:focus{
      box-shadow:0 0 0 3px rgba(34,197,94,.15),var(--sh-sm)!important;
      transform:none!important;
    }
    .plan-lock-badge{
      position:absolute;
      right:13px;top:50%;transform:translateY(-50%);
      display:inline-flex;align-items:center;gap:5px;
      font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
      color:var(--green-2);background:#dcfce7;border:1px solid #86efac;
      padding:4px 10px;border-radius:999px;
      white-space:nowrap;
    }
    .plan-lock-badge svg{flex-shrink:0;}

    /* SMARTCREDIT BOX */
    .smartcredit-box{border:1.5px solid #bbf7d0;border-radius:var(--r-sm);background:linear-gradient(135deg,#f0fdf4,#ecfdf5);padding:20px 22px;display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;}
    .smartcredit-box-text strong{display:block;font-size:14px;font-weight:700;color:#064e3b;font-family:'Syne',sans-serif;margin-bottom:4px;}
    .smartcredit-box-text p{font-size:12.5px;color:#047857;line-height:1.55;}
    .btn-smartcredit{display:inline-flex;align-items:center;justify-content:center;gap:8px;height:46px;padding:0 22px;border-radius:10px;background:linear-gradient(135deg,var(--green) 0%,var(--green-2) 100%);color:var(--white);font-family:'Syne',sans-serif;font-size:13px;font-weight:700;letter-spacing:.04em;text-decoration:none;border:none;cursor:pointer;white-space:nowrap;flex-shrink:0;box-shadow:0 6px 18px rgba(34,197,94,.3);transition:all .25s var(--ease);}
    .btn-smartcredit:hover{transform:translateY(-2px);box-shadow:0 10px 26px rgba(34,197,94,.4);filter:brightness(1.06);}

    .sec-note{margin-top:22px;display:flex;gap:14px;align-items:flex-start;padding:16px 20px;border-radius:var(--r-sm);background:linear-gradient(135deg,#f0fdf4,#ecfdf5);border:1px solid #bbf7d0;}
    .sec-note-icon{width:36px;height:36px;border-radius:10px;background:var(--navy);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--green);}
    .sec-note strong{display:block;font-size:13px;font-weight:700;color:#064e3b;margin-bottom:3px;font-family:'Syne',sans-serif;}
    .sec-note p{font-size:12.5px;font-weight:400;color:#047857;line-height:1.6;}
    .divider{height:1px;background:linear-gradient(90deg,transparent,var(--rule),transparent);margin:26px 0 22px;}
    .form-nav{display:flex;justify-content:space-between;align-items:center;gap:12px;}

    /* BUTTONS */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 28px;border-radius:12px;border:none;cursor:pointer;font-family:'Syne',sans-serif;font-size:14px;font-weight:700;letter-spacing:.04em;transition:all .25s var(--ease);white-space:nowrap;position:relative;overflow:hidden;}
    .btn::before{content:"";position:absolute;inset:0;background:rgba(255,255,255,.15);transform:translateX(-100%) skewX(-10deg);transition:transform .4s var(--ease);}
    .btn:hover::before{transform:translateX(150%) skewX(-10deg);}
    .btn-ghost{background:transparent;border:1.5px solid var(--rule-2);color:var(--ink-3);}
    .btn-ghost:hover{border-color:var(--ink-3);color:var(--ink-2);background:var(--bg-2);}
    .btn-green{background:linear-gradient(135deg,var(--green) 0%,var(--green-2) 100%);color:var(--white);min-width:190px;box-shadow:var(--sh-green);}
    .btn-green:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(34,197,94,.38);filter:brightness(1.05);}
    .btn-green:active{transform:translateY(0);}
    .btn-green:disabled{opacity:.65;cursor:not-allowed;transform:none;}
    .spin{width:17px;height:17px;border-radius:50%;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;display:none;animation:spin .75s linear infinite;}
    @keyframes spin{to{transform:rotate(360deg)}}
    .btn-green.loading .spin{display:block;}
    .btn-green.loading .btn-text{display:none;}

    /* TRUST ROW */
    .trust-row{display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:18px;padding:18px 32px;border-top:1px solid var(--rule);background:linear-gradient(180deg,#fafbfc,#f8fafc);}
    .ti{display:flex;align-items:center;gap:7px;font-size:11.5px;font-weight:600;color:var(--ink-4);font-family:'Syne',sans-serif;letter-spacing:.03em;}

    /* SUCCESS */
    .success-panel{display:none;padding:60px 32px 64px;text-align:center;}
    .success-panel.show{display:block;animation:successPop .5s var(--ease-spring);}
    @keyframes successPop{from{opacity:0;transform:scale(.92)}to{opacity:1;transform:scale(1)}}
    .success-ring{width:80px;height:80px;border-radius:50%;margin:0 auto 20px;background:linear-gradient(135deg,var(--green-dim),var(--green-mid));border:2px solid #86efac;display:flex;align-items:center;justify-content:center;color:var(--green-2);box-shadow:0 0 40px rgba(34,197,94,.2);animation:ringPulse 2.5s ease-in-out infinite;}
    @keyframes ringPulse{0%,100%{box-shadow:0 0 40px rgba(34,197,94,.2)}50%{box-shadow:0 0 60px rgba(34,197,94,.38)}}
    .success-title{font-size:36px;font-weight:800;color:var(--navy);letter-spacing:-.025em;margin-bottom:10px;font-family:'Syne',sans-serif;}
    .success-sub{max-width:420px;margin:0 auto;font-size:15px;font-weight:400;color:var(--ink-3);line-height:1.75;}

    /* WHAT HAPPENS NEXT */
    .next-section{background:var(--bg-2);border-top:1px solid var(--rule);padding:88px 20px 108px;position:relative;z-index:10;}
    .next-section::before{content:"";position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,transparent,var(--green) 50%,transparent);opacity:.5;}
    .next-inner{max-width:820px;margin:0 auto;}
    .next-header{text-align:center;margin-bottom:60px;}
    .sec-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:10px;font-weight:700;letter-spacing:.24em;text-transform:uppercase;color:var(--green-2);margin-bottom:14px;font-family:'Syne',sans-serif;}
    .sec-eyebrow::before,.sec-eyebrow::after{content:"";width:22px;height:1.5px;background:var(--green);border-radius:999px;}
    .sec-title{font-size:clamp(28px,4vw,42px);font-weight:800;color:var(--navy);line-height:1.1;letter-spacing:-.025em;margin-bottom:12px;font-family:'Syne',sans-serif;}
    .sec-title .hl-green{color:var(--green);}
    .sec-sub{font-size:15px;font-weight:400;color:var(--ink-3);line-height:1.7;max-width:520px;margin:0 auto;}
    .steps-list{display:flex;flex-direction:column;gap:0;}
    .step-row{display:flex;gap:0;position:relative;}
    .step-row:not(:last-child) .step-line-col::after{content:"";position:absolute;top:56px;left:50%;transform:translateX(-50%);width:2px;height:calc(100% - 14px);background:linear-gradient(180deg,var(--green) 0%,rgba(34,197,94,.15) 100%);}
    .step-line-col{width:64px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;padding-top:6px;position:relative;}
    .step-num-badge{width:46px;height:46px;border-radius:50%;background:var(--navy);color:var(--white);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;font-family:'Syne',sans-serif;flex-shrink:0;box-shadow:0 6px 20px rgba(7,20,40,.25);z-index:1;position:relative;transition:transform .3s var(--ease-spring),box-shadow .3s ease;}
    .step-row:hover .step-num-badge{transform:scale(1.1);box-shadow:0 8px 28px rgba(7,20,40,.3),0 0 0 4px rgba(34,197,94,.15);}
    .step-content{flex:1;padding:0 0 52px 22px;}
    .step-row:last-child .step-content{padding-bottom:0;}
    .step-card{background:var(--white);border:1px solid var(--rule);border-radius:var(--r);box-shadow:var(--sh);padding:24px 26px;transition:all .3s var(--ease);position:relative;overflow:hidden;}
    .step-card::before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--navy),var(--green));transform:scaleX(0);transform-origin:left;transition:transform .4s var(--ease);}
    .step-card:hover{box-shadow:var(--sh-lg);border-color:#bbf7d0;transform:translateX(4px);}
    .step-card:hover::before{transform:scaleX(1);}
    .step-card-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:12px;}
    .step-card-icon{width:44px;height:44px;border-radius:12px;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;}
    .step-card-title{font-size:17px;font-weight:800;color:var(--navy);line-height:1.2;letter-spacing:-.01em;font-family:'Syne',sans-serif;}
    .step-card-sub{font-size:11px;font-weight:700;color:var(--green-2);text-transform:uppercase;letter-spacing:.12em;margin-top:3px;font-family:'Syne',sans-serif;}
    .step-card-body{font-size:14.5px;font-weight:400;color:var(--ink-2);line-height:1.75;}
    .step-card-note{margin-top:12px;padding:12px 15px;border-radius:var(--r-sm);background:#fff7ed;border:1px solid #fed7aa;font-size:13px;font-weight:600;color:#9a3412;display:flex;align-items:flex-start;gap:9px;line-height:1.55;}
    .note-icon{flex-shrink:0;margin-top:1px;}
    .step-card-docs{margin-top:14px;display:flex;flex-direction:column;gap:8px;}
    .doc-item{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600;color:var(--ink-2);}
    .doc-icon{width:30px;height:30px;border-radius:8px;background:var(--navy);color:var(--white);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
    .step-card-timeline{margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;}
    .tl-chip{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:999px;background:var(--green-dim);border:1px solid #bbf7d0;font-size:12.5px;font-weight:600;color:var(--green-2);font-family:'Syne',sans-serif;}

    .reveal{opacity:0;transform:translateY(24px);transition:opacity .6s var(--ease),transform .6s var(--ease);}
    .reveal.in{opacity:1;transform:translateY(0);}
    .reveal-delay-1{transition-delay:.1s;}
    .reveal-delay-2{transition-delay:.2s;}
    .reveal-delay-3{transition-delay:.3s;}

    @media(max-width:700px){
      .hero-band{padding:36px 16px 44px;}
      .hero-next-btn-wrap{margin-top:16px;}
      .hero-next-btn{width:100%;max-width:260px;}
      .form-section{padding:32px 14px 56px;}
      .form-body{padding:20px 18px 22px;}
      .card-head{padding:18px 18px 16px;}
      .trust-row{padding:14px 18px;gap:12px;}
      .fgrid{grid-template-columns:1fr;}
      .ffull{grid-column:auto;}
      .form-nav{flex-direction:column-reverse;align-items:stretch;}
      .btn{width:100%;}
      .ps-label{display:none;}
      .next-section{padding:52px 14px 68px;}
      .step-line-col{width:42px;}
      .step-num-badge{width:38px;height:38px;font-size:15px;}
      .step-content{padding:0 0 38px 16px;}
      .step-card{padding:18px 16px;}
      .smartcredit-box{flex-direction:column;align-items:flex-start;}
      .btn-smartcredit{width:100%;}
    }
</style>
</head>
<body>

<canvas id="particleCanvas"></canvas>

<!-- HERO -->
<div class="hero-band">
  <div class="glow-orb" style="width:400px;height:400px;background:var(--green);top:-150px;left:-80px;opacity:.08;"></div>
  <div class="glow-orb" style="width:300px;height:300px;background:#3b82f6;bottom:-100px;right:-60px;opacity:.1;"></div>
  <div class="hero-eyebrow">Secure Enrollment</div>
  <h1 class="hero-title">Complete Your <span class="hl-green">Account Setup</span></h1>
  <p class="hero-sub">Your payment has been received. Complete this final step so we can verify your identity and begin processing your credit file without delay.</p>
  <div class="hero-chips">
    <div class="hero-chip"><span class="chip-dot"></span> Payment Confirmed</div>
    <div class="hero-chip"><span class="chip-dot"></span> 3-Minute Setup</div>
    <div class="hero-chip"><span class="chip-dot"></span> Secure &amp; Encrypted</div>
    <div class="hero-chip"><span class="chip-dot"></span> Results in 30 Days</div>
  </div>
  <div class="hero-next-btn-wrap">
    <a href="#section-next" class="hero-next-btn">What Happens After Submitting?</a>
  </div>
</div>

<!-- FORM SECTION -->
<section class="form-section" id="section-form">
  <div class="form-section-inner">

    <!-- Progress -->
    <div class="prog-wrap reveal" id="progWrap">
      <div class="prog-steps">
        <div class="ps active" id="ps1"><div class="ps-num"><span>1</span></div><div class="ps-label">Personal Info</div></div>
        <div class="ps-conn" id="conn1"></div>
        <div class="ps" id="ps2"><div class="ps-num"><span>2</span></div><div class="ps-label">Address</div></div>
        <div class="ps-conn" id="conn2"></div>
        <div class="ps" id="ps3"><div class="ps-num"><span>3</span></div><div class="ps-label">Verification</div></div>
      </div>
      <div class="prog-track"><div class="prog-fill" id="progBar"></div></div>
    </div>

    <!-- Card -->
    <div class="form-card reveal reveal-delay-1">
      <div class="card-head" id="cardHead">
        <div class="ch-icon" id="chIcon">
          <svg width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg>
        </div>
        <div class="ch-text">
          <div class="ch-step" id="chStep">Step 1 of 3</div>
          <h3 id="chTitle">Personal Information</h3>
          <p id="chDesc">Enter your name and contact details exactly as they appear on your records.</p>
        </div>
      </div>

      <form id="enrollForm"
            action="https://pulse.disputeprocess.com/CustumFieldController"
            onsubmit="return submitForm(this);"
            enctype="multipart/form-data"
            method="POST">

        {{-- ── DisputeFox required hidden fields (verbatim) ── --}}
        <input type="hidden" name="method"                value="addWebFormData">
        <input type="hidden" name="tab_info_id"           value="clE2UXZ1TVEzcHlReTB6NTdhWVhlQT09">
        <input type="hidden" name="redirect_url"          value="">
        <input type="hidden" name="company_id"            value="NGNTK0Ywc2hZcjNEUGV1U2xtNHcyUT09">
        <input type="hidden" name="cust_type"             value="2">
        <input type="hidden" name="add_affiliate_flag"    value="0">
        <input type="hidden" name="assignedto_id"         value="18061">
        <input type="hidden" name="sales_representative_id" value="18061">
        <input type="hidden" name="workflow_statusid"     value="30">
        <input type="hidden" name="folder_statusid"       value="1">
        <input type="hidden" name="customer_statusid"     value="1">
        <input type="hidden" name="portalAccess"          value="1">
        <input type="hidden" name="customerAgreementIDs"  value="39041">
        {{-- Monitoring agency fixed to SmartCredit (6) --}}
        <input type="hidden" name="monitoringAgency"      value="6">

        <input type="hidden" name="textField" id="hiddenPlanName"   value="{{ $planLabel }}">
        <input type="hidden" name="number"    id="hiddenPlanAmount" value="{{ $amount }}">

        <div class="form-body">

          <!-- ═══ STEP 1 — Personal Info ═══ -->
          <div class="form-step active" id="fStep1">
            <div class="fgrid">
              <div class="fg">
                <label class="flabel">First Name <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg>
                  <input type="text" name="firstName" id="firstName"
                         placeholder="John" required autocomplete="given-name"
                         value="{{ $firstName }}">
                </div>
                <div class="ferr" id="firstName-err">First name is required.</div>
              </div>
              <div class="fg">
                <label class="flabel">Middle Name <span class="opt">(optional)</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg>
                  <input type="text" name="middleName" placeholder="Michael" autocomplete="additional-name">
                </div>
              </div>
              <div class="fg">
                <label class="flabel">Last Name <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg>
                  <input type="text" name="lastName" id="lastName"
                         placeholder="Smith" required autocomplete="family-name"
                         value="{{ $lastName }}">
                </div>
                <div class="ferr" id="lastName-err">Last name is required.</div>
              </div>
              <div class="fg">
                <label class="flabel">Suffix <span class="opt">(optional)</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3 4.5h10a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1H3z"/></svg>
                  <select name="select1">
                    <option value="none">None</option>
                    <option value="jr">Jr.</option>
                    <option value="sr">Sr.</option>
                    <option value="i">I</option>
                    <option value="ii">II</option>
                    <option value="iii">III</option>
                    <option value="iv">IV</option>
                  </select>
                </div>
              </div>
              <div class="fg ffull">
                <label class="flabel">Email Address <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114V5.383zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741z"/></svg>
                  <input type="email" name="email" id="emailField"
                         placeholder="john@example.com" required autocomplete="email"
                         value="{{ $email }}">
                </div>
                <div class="ferr" id="email-err">Please enter a valid email address.</div>
              </div>
              <div class="fg ffull">
                <label class="flabel">Mobile Phone <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h6zm-1 12.5V2.5H6v11h4zm-2-1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>
                  <input type="text" name="mobilePhone" id="phoneField"
                         placeholder="(555) 000-0000" required autocomplete="tel" maxlength="14"
                         value="{{ $phone }}">
                </div>
                <div class="ferr" id="phone-err">Please enter a valid 10-digit phone number.</div>
              </div>

              <div class="fg ffull">
                <label class="flabel">
                  Plan Purchased
                  <span class="opt">(set automatically from your payment)</span>
                </label>
                <div class="fw plan-field-wrap">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2H3zm7 4a2 2 0 1 0-4 0 2 2 0 0 0 4 0z"/>
                  </svg>
                  <input type="text"
                         class="plan-prefilled"
                         value="{{ $planLabel }}"
                         readonly
                         tabindex="-1"
                         style="padding-right:110px;">
                  <span class="plan-lock-badge">
                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3A3 3 0 0 0 5 3v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    </svg>
                    Locked
                  </span>
                </div>
              </div>

              <div class="fg ffull">
                <label class="flabel">
                  Plan Amount
                  <span class="opt">(set automatically from your payment)</span>
                </label>
                <div class="fw plan-field-wrap">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.9c-.166-1.6-1.54-2.748-3.97-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.61-2.134c-1.09-.266-1.618-.82-1.618-1.616 0-.903.655-1.603 1.618-1.733v3.349zm1.043 2.503c1.296.35 1.828.903 1.828 1.819 0 1.017-.72 1.674-1.828 1.813V11.15z"/>
                  </svg>
                  <input type="text"
                         class="plan-prefilled"
                         value="${{ $amount }}"
                         readonly
                         tabindex="-1"
                         style="padding-right:110px;">
                  <span class="plan-lock-badge">
                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3A3 3 0 0 0 5 3v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    </svg>
                    Locked
                  </span>
                </div>
              </div>

            </div>
            <div class="divider"></div>
            <div class="form-nav">
              <div></div>
              <button type="button" class="btn btn-green" onclick="nextStep(1)">
                <span class="btn-text">Continue →</span>
                <div class="spin"></div>
              </button>
            </div>
          </div>

          <!-- ═══ STEP 2 — Address ═══ -->
          <div class="form-step" id="fStep2">
            <div class="fgrid">
              <div class="fg ffull">
                <label class="flabel">Street Address <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6-.896.897.707.707L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.543-.543.707-.707-.896-.897-6-6zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l4.5-4.5 5.5 5.5z"/></svg>
                  <input type="text" name="currentAddress" id="addressField"
                         placeholder="123 Main Street, Apt 4B" required autocomplete="street-address"
                         value="{{ $address }}">
                </div>
                <div class="ferr" id="address-err">Street address is required.</div>
              </div>
              <div class="fg">
                <label class="flabel">City <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022z"/></svg>
                  <input type="text" name="city" id="cityField"
                         placeholder="Los Angeles" required autocomplete="address-level2"
                         value="{{ $city }}">
                </div>
                <div class="ferr" id="city-err">City is required.</div>
              </div>
              <div class="fg">
                <label class="flabel">State <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
                  <select name="state" id="stateField" required>
                    <option value="">Select State</option>
                    @php
                      $states = ['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California',
                        'CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia',
                        'HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa',
                        'KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland',
                        'MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri',
                        'MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey',
                        'NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio',
                        'OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina',
                        'SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont',
                        'VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'];
                    @endphp
                    @foreach($states as $abbr => $name)
                      <option value="{{ $abbr }}" {{ ($state === $abbr) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="ferr" id="state-err">Please select a state.</div>
              </div>
              <div class="fg ffull">
                <label class="flabel">ZIP Code <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1z"/></svg>
                  <input type="text" name="textField1" id="zipField"
                         placeholder="10001" required maxlength="5" pattern="[0-9]{5}"
                         autocomplete="postal-code"
                         value="{{ $zip }}">
                </div>
                <div class="ferr" id="zip-err">Please enter a valid 5-digit ZIP code.</div>
              </div>
            </div>
            <div class="divider"></div>
            <div class="form-nav">
              <button type="button" class="btn btn-ghost" onclick="prevStep(2)">← Back</button>
              <button type="button" class="btn btn-green" onclick="nextStep(2)">
                <span class="btn-text">Continue →</span>
                <div class="spin"></div>
              </button>
            </div>
          </div>

          <!-- ═══ STEP 3 — Verification ═══ -->
          <div class="form-step" id="fStep3">
            <div class="fgrid">

              <!-- SSN -->
              <div class="fg ffull">
                <label class="flabel">Social Security Number <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                  <input type="password" name="socialSecurityNumber" id="ssnField"
                         placeholder="Last 4 or full 9 digits" required maxlength="9" autocomplete="off">
                  <button type="button" class="ssn-eye" onclick="toggleSSN()">
                    <svg id="eyeIcon" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>
                  </button>
                </div>
                <div class="ferr" id="ssn-err">Please enter 4–9 digits. Numbers only.</div>
                <div class="fhint">Your SSN is encrypted end-to-end and never stored in plain text.</div>
              </div>

              <!-- Date of Birth -->
              <div class="fg ffull">
                <label class="flabel">Date of Birth <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                  <input type="date" name="dateOfBirth" id="dobField" required>
                </div>
                <div class="ferr" id="dob-err">Please enter your date of birth.</div>
              </div>

              <!-- GET SMARTCREDIT BUTTON -->
              <div class="fg ffull">
                <div class="smartcredit-box">
                  <div class="smartcredit-box-text">
                    <strong>Don't have a SmartCredit account yet?</strong>
                    <p><strong>This will open SmartCredit in a new tab.</strong> After you finish signing up there, come back to this same page to continue Step 2.</p>
                  </div>
                  <a href="https://www.smartcredit.com/?PID=48108" target="_blank" rel="noopener" class="btn-smartcredit">
                    📊 Open SmartCredit in New Tab
                  </a>
                </div>
              </div>

              <!-- SmartCredit Username -->
              <div class="fg ffull">
                <label class="flabel">SmartCredit Username <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg>
                  <input type="text" name="monitoringUsername1" id="scUsername"
                         placeholder="Your SmartCredit username" required autocomplete="username">
                </div>
                <div class="ferr" id="scUsername-err">SmartCredit username is required.</div>
              </div>

              <!-- SmartCredit Password -->
              <div class="fg ffull">
                <label class="flabel">SmartCredit Password <span class="req">*</span></label>
                <div class="fw">
                  <svg class="fi" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                  <input type="password" name="password1" id="scPassword"
                         placeholder="Your SmartCredit password" required autocomplete="current-password">
                </div>
                <div class="ferr" id="scPassword-err">SmartCredit password is required.</div>
              </div>

            </div>

            <div class="sec-note">
              <div class="sec-note-icon">
                <svg width="17" height="17" fill="currentColor" viewBox="0 0 16 16"><path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524z"/><path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/></svg>
              </div>
              <div>
                <strong>Bank-Grade Security</strong>
                <p>All data is transmitted over TLS 1.3 encryption. Your information is never shared with third parties.</p>
              </div>
            </div>

            <div class="divider"></div>
            <div class="form-nav">
              <button type="button" class="btn btn-ghost" onclick="prevStep(3)">← Back</button>
              <button type="submit" class="btn btn-green" id="submitBtn">
                <span class="btn-text">Submit Application ✓</span>
                <div class="spin"></div>
              </button>
            </div>
          </div>

          <!-- SUCCESS -->
          <div class="success-panel" id="successPanel">
            <div class="success-ring">
              <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
            </div>
            <div class="success-title">Application Received!</div>
            <p class="success-sub">Your information has been securely submitted. Check your email for next steps, and scroll down to see exactly what happens from here.</p>
          </div>

        </div>
      </form>

      <div class="trust-row" id="trustStrip">
        <div class="ti"><svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg> 256-bit SSL</div>
        <div class="ti"><svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524z"/></svg> FCRA Compliant</div>
        <div class="ti"><svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/></svg> Secure Data Handling</div>
        <div class="ti"><svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg> Never Sold or Shared</div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT HAPPENS NEXT -->
<section class="next-section" id="section-next">
  <div class="next-inner">
    <div class="next-header reveal">
      <div class="sec-eyebrow">After Submitting</div>
      <h2 class="sec-title">Here's <span class="hl-green">What Happens</span> Next</h2>
      <p class="sec-sub">Follow these steps after you've completed the form above.</p>
    </div>
    <div class="steps-list">
      <div class="step-row reveal">
        <div class="step-line-col"><div class="step-num-badge">1</div></div>
        <div class="step-content"><div class="step-card">
          <div class="step-card-head"><div class="step-card-icon">📧</div><div><div class="step-card-sub">Do this first</div><div class="step-card-title">Check Your Email for Access</div></div></div>
          <div class="step-card-body">You'll receive an email with your secure portal login and all next steps. <strong>Check your inbox now</strong> — it should arrive within a few minutes.</div>
          <div class="step-card-note"><span class="note-icon">⚠️</span><span>Didn't get it? Check your spam or junk folder. If it's still missing, contact our support team directly.</span></div>
        </div></div>
      </div>
      <div class="step-row reveal reveal-delay-1">
        <div class="step-line-col"><div class="step-num-badge">2</div></div>
        <div class="step-content"><div class="step-card">
          <div class="step-card-head"><div class="step-card-icon">📊</div><div><div class="step-card-sub">Required — link in your email</div><div class="step-card-title">Sign Up for SmartCredit</div></div></div>
          <div class="step-card-body">The link to sign up will be in one of the emails you receive. You must complete this to get started.</div>
          <div class="step-card-note"><span class="note-icon">🔴</span><span><strong>Important:</strong> To get results, you must keep your SmartCredit account <strong>active every month</strong>. If it's not active, we cannot deliver the results you want.</span></div>
        </div></div>
      </div>
      <div class="step-row reveal reveal-delay-2">
        <div class="step-line-col"><div class="step-num-badge">3</div></div>
        <div class="step-content"><div class="step-card">
          <div class="step-card-head"><div class="step-card-icon">📁</div><div><div class="step-card-sub">Required — via your portal</div><div class="step-card-title">Upload Your Documents</div></div></div>
          <div class="step-card-body">Log in to your secure portal and upload the following documents so we can begin working on your file:</div>
          <div class="step-card-docs">
            <div class="doc-item"><div class="doc-icon">🪪</div>Social Security Card</div>
            <div class="doc-item"><div class="doc-icon">🆔</div>Government-Issued ID</div>
            <div class="doc-item"><div class="doc-icon">🏠</div>Proof of Address</div>
            <div class="doc-item"><div class="doc-icon">📊</div>SmartCredit Login Details</div>
          </div>
        </div></div>
      </div>
      <div class="step-row reveal reveal-delay-3">
        <div class="step-line-col"><div class="step-num-badge">4</div></div>
        <div class="step-content"><div class="step-card">
          <div class="step-card-head"><div class="step-card-icon">📬</div><div><div class="step-card-sub">Ongoing — as you receive mail</div><div class="step-card-title">Upload Any Mail You Receive</div></div></div>
          <div class="step-card-body">If you receive any mail from credit bureaus or creditors, <strong>upload it to your portal immediately.</strong></div>
          <div class="step-card-note"><span class="note-icon">💡</span><span>Don't wait — timely uploads mean faster results. Every piece of mail matters.</span></div>
        </div></div>
      </div>
      <div class="step-row reveal">
        <div class="step-line-col"><div class="step-num-badge">5</div></div>
        <div class="step-content"><div class="step-card">
          <div class="step-card-head"><div class="step-card-icon">📈</div><div><div class="step-card-sub">Your timeline</div><div class="step-card-title">What to Expect</div></div></div>
          <div class="step-card-body">Dispute results typically take <strong>30 days</strong> for the credit bureaus to respond. You'll receive monthly updates and can check your portal anytime.</div>
          <div class="step-card-timeline">
            <div class="tl-chip">📅 30-day bureau response</div>
            <div class="tl-chip">✅ Monthly updates</div>
            <div class="tl-chip">🔒 Secure portal access 24/7</div>
          </div>
        </div></div>
      </div>
    </div>
  </div>
</section>

<script>
/* ══ PARTICLES ══ */
(function(){
  const canvas=document.getElementById('particleCanvas'),ctx=canvas.getContext('2d');
  let W,H,particles=[];
  const COLORS=['rgba(34,197,94,','rgba(22,163,74,','rgba(7,20,40,','rgba(59,130,246,'];
  function resize(){W=canvas.width=window.innerWidth;H=canvas.height=window.innerHeight;}
  window.addEventListener('resize',resize);resize();
  function rand(a,b){return Math.random()*(b-a)+a;}
  class Particle{
    constructor(){this.reset();this.y=rand(0,H);}
    reset(){this.x=rand(0,W);this.y=H+10;this.r=rand(1,3.5);this.vy=rand(-0.3,-0.9);this.vx=rand(-0.2,0.2);this.opacity=rand(0.04,0.22);this.color=COLORS[Math.floor(rand(0,COLORS.length))];this.life=0;this.maxLife=rand(280,500);}
    update(){this.x+=this.vx;this.y+=this.vy;this.life++;if(this.y<-10||this.life>this.maxLife)this.reset();}
    draw(){ctx.beginPath();ctx.arc(this.x,this.y,this.r,0,Math.PI*2);const fade=Math.min(1,Math.min(this.life/40,(this.maxLife-this.life)/40));ctx.fillStyle=this.color+(this.opacity*fade)+')';ctx.fill();}
  }
  for(let i=0;i<90;i++)particles.push(new Particle());
  function connectParticles(){for(let i=0;i<particles.length;i++){for(let j=i+1;j<particles.length;j++){const dx=particles[i].x-particles[j].x,dy=particles[i].y-particles[j].y,dist=Math.sqrt(dx*dx+dy*dy);if(dist<100){ctx.beginPath();ctx.moveTo(particles[i].x,particles[i].y);ctx.lineTo(particles[j].x,particles[j].y);ctx.strokeStyle=`rgba(34,197,94,${0.04*(1-dist/100)})`;ctx.lineWidth=0.8;ctx.stroke();}}}}
  function loop(){ctx.clearRect(0,0,W,H);connectParticles();particles.forEach(p=>{p.update();p.draw();});requestAnimationFrame(loop);}
  loop();
})();

/* ══ SCROLL REVEAL ══ */
const ro=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');ro.unobserve(e.target);}});},{threshold:0.1});
document.querySelectorAll('.reveal').forEach(el=>ro.observe(el));
setTimeout(()=>document.querySelectorAll('#section-form .reveal').forEach(el=>el.classList.add('in')),100);

/* ══ MASKS ══ */
document.getElementById('phoneField').addEventListener('input',function(){
  const d=this.value.replace(/\D/g,'').substring(0,10);
  let v=d;
  if(d.length>=7)v='('+d.slice(0,3)+') '+d.slice(3,6)+'-'+d.slice(6);
  else if(d.length>=4)v='('+d.slice(0,3)+') '+d.slice(3);
  else if(d.length>=1)v='('+d;
  this.value=v;
});
document.getElementById('zipField').addEventListener('input',e=>e.target.value=e.target.value.replace(/\D/g,'').slice(0,5));
document.getElementById('ssnField').addEventListener('input',e=>e.target.value=e.target.value.replace(/\D/g,'').slice(0,9));

/* ══ SSN TOGGLE ══ */
function toggleSSN(){
  const f=document.getElementById('ssnField'),i=document.getElementById('eyeIcon');
  if(f.type==='password'){f.type='text';i.innerHTML='<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709z"/><path d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>';}
  else{f.type='password';i.innerHTML='<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';}
}

/* ══ STEP UI ══ */
const SM={
  1:{icon:'<path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/>',step:'Step 1 of 3',title:'Personal Information',desc:'Enter your name and contact details exactly as they appear on your records.'},
  2:{icon:'<path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6-.896.897.707.707L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.543-.543.707-.707-.896-.897-6-6zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l4.5-4.5 5.5 5.5z"/>',step:'Step 2 of 3',title:'Residential Address',desc:'Provide your current address as it appears on your government-issued ID.'},
  3:{icon:'<path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>',step:'Step 3 of 3',title:'Identity & Verification',desc:'Enter your SSN, date of birth, and SmartCredit credentials to complete enrollment.'}
};
function updateUI(n){
  document.getElementById('progBar').style.width=((n/3)*100)+'%';
  for(let i=1;i<=3;i++){const t=document.getElementById('ps'+i);t.classList.remove('active','done');if(i<n)t.classList.add('done');else if(i===n)t.classList.add('active');}
  for(let i=1;i<3;i++)document.getElementById('conn'+i).classList.toggle('filled',i<n);
  const m=SM[n];
  document.getElementById('chIcon').querySelector('svg').innerHTML=m.icon;
  document.getElementById('chStep').textContent=m.step;
  document.getElementById('chTitle').textContent=m.title;
  document.getElementById('chDesc').textContent=m.desc;
}
function showStep(n){document.querySelectorAll('.form-step').forEach(s=>s.classList.remove('active'));document.getElementById('fStep'+n).classList.add('active');updateUI(n);document.getElementById('section-form').scrollIntoView({behavior:'smooth',block:'start'});}
function nextStep(f){if(validate(f))showStep(f+1);}
function prevStep(f){showStep(f-1);}

function showE(id,show){const el=document.getElementById(id);if(el)el.classList.toggle('show',show);}
function setFS(f,ok){if(!f)return;f.classList.remove('valid','invalid');f.classList.add(ok?'valid':'invalid');}

function validate(s){
  let ok=true;
  if(s===1){
    const fn=document.getElementById('firstName'),ln=document.getElementById('lastName'),em=document.getElementById('emailField'),ph=document.getElementById('phoneField');
    const fnOk=fn.value.trim().length>0;setFS(fn,fnOk);showE('firstName-err',!fnOk);if(!fnOk)ok=false;
    const lnOk=ln.value.trim().length>0;setFS(ln,lnOk);showE('lastName-err',!lnOk);if(!lnOk)ok=false;
    const emOk=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value.trim());setFS(em,emOk);showE('email-err',!emOk);if(!emOk)ok=false;
    const phOk=ph.value.replace(/\D/g,'').length>=10;setFS(ph,phOk);showE('phone-err',!phOk);if(!phOk)ok=false;
  }
  if(s===2){
    const addr=document.getElementById('addressField'),city=document.getElementById('cityField'),state=document.getElementById('stateField'),zip=document.getElementById('zipField');
    const aOk=addr.value.trim().length>3;setFS(addr,aOk);showE('address-err',!aOk);if(!aOk)ok=false;
    const cOk=city.value.trim().length>0;setFS(city,cOk);showE('city-err',!cOk);if(!cOk)ok=false;
    const sOk=state.value.length>0;setFS(state,sOk);showE('state-err',!sOk);if(!sOk)ok=false;
    const zOk=/^\d{5}$/.test(zip.value.trim());setFS(zip,zOk);showE('zip-err',!zOk);if(!zOk)ok=false;
  }
  return ok;
}

var formData1=new FormData();

const submitForm=function(form){
  const ssn=document.getElementById('ssnField');
  const ssnVal=ssn.value.replace(/\D/g,'');
  const ssnOk=ssnVal.length>=4&&ssnVal.length<=9;
  setFS(ssn,ssnOk);showE('ssn-err',!ssnOk);

  const dob=document.getElementById('dobField');
  const dobOk=dob.value.length>0;
  setFS(dob,dobOk);showE('dob-err',!dobOk);

  const scUser=document.getElementById('scUsername');
  const scUserOk=scUser.value.trim().length>0;
  setFS(scUser,scUserOk);showE('scUsername-err',!scUserOk);

  const scPass=document.getElementById('scPassword');
  const scPassOk=scPass.value.length>0;
  setFS(scPass,scPassOk);showE('scPassword-err',!scPassOk);

  if(!ssnOk||!dobOk||!scUserOk||!scPassOk)return false;

  var elements=form;
  var formData=new FormData();
  var firstName="",lastName="",currentAddress="",city="",state="",zipCode="",mobilePhone="",email="",userPassword="";

  for(var i=0;i<elements.length;i++){
    var currentElement=elements[i].name;
    if(!currentElement)continue;
    if(currentElement.includes("firstName"))firstName=elements[i].value||"";
    if(currentElement.includes("lastName"))lastName=elements[i].value||"";
    if(currentElement.includes("currentAddress"))currentAddress=elements[i].value||"";
    if(currentElement.includes("city"))city=elements[i].value||"";
    if(currentElement.includes("state"))state=elements[i].value||"";
    if(currentElement.includes("zipCode"))zipCode=elements[i].value||"";
    if(currentElement.includes("mobilePhone"))mobilePhone=elements[i].value||"";
    if(currentElement.includes("monitoringUsername"))email=elements[i].value||"";
    if(currentElement.includes("password"))userPassword=elements[i].value||"";
    if(currentElement.includes("socialSecurityNumber")){if(elements[i].value.length>0){if(!SSNValidation(elements[i].value))return false;}}
    if(currentElement.includes("selectBoxes")){var obj={};obj[elements[i].value]=elements[i].checked?true:false;try{formData.append(elements[i].name,JSON.stringify(obj));}catch(e){formData.append(elements[i].name,elements[i].value);}}
    else if(currentElement.includes("uploadDocument")){formData.append(currentElement,elements[i].value+"~~~~"+formData1.get(currentElement));}
    else if(currentElement.includes("radio")){if(elements[i].checked)formData.append(elements[i].name,elements[i].value);}
    else if(currentElement.includes("checkbox")){formData.append(elements[i].name,elements[i].checked);}
    else{formData.append(elements[i].name,elements[i].value);}
  }

  const btn=document.getElementById('submitBtn');
  btn.classList.add('loading');btn.disabled=true;

  var xmlHttp=new XMLHttpRequest();
  xmlHttp.onreadystatechange=function(){
    if(xmlHttp.readyState==4&&xmlHttp.status==200){
      btn.classList.remove('loading');btn.disabled=false;
      var redirectUrl=xmlHttp.responseText;
      if(redirectUrl!==""&&!redirectUrl.includes("Account is in-activated")){
        if(redirectUrl.includes("para=1"))redirectUrl+="&firstname="+firstName+"&lastname="+lastName+"&bill_address="+currentAddress+"&bill_city="+city+"&bill_state="+state+"&bill_zip="+zipCode+"&phone="+mobilePhone+"&email="+email+"&pGUID="+btoa(userPassword);
        redirectUrl=redirectUrl.indexOf("http")>-1?redirectUrl:"http://"+redirectUrl;
        window.location.replace(redirectUrl);
      }else if(redirectUrl.includes("Account is in-activated")){
        alert("Account Inactive: We apologize, but the service associated with this link is currently inactive. Please contact the business or individual who provided you with this link for further assistance.");
      }else{showSuccess();}
    }else if(xmlHttp.readyState==4){btn.classList.remove('loading');btn.disabled=false;showSuccess();}
  };
  xmlHttp.open("post","https://pulse.disputeprocess.com/CustumFieldController?method=addWebFormData");
  xmlHttp.send(formData);
  return false;
};

function SSNValidation(ssn){
  var m=ssn.match(/^[0-9]+$/);
  if(m==null){alert("Invalid SSN. Digits only.");return false;}
  else if(m[0].length>9){alert("SSN should be 9 digits max.");return false;}
  else if(m[0].length<4){alert("Please enter at least the last 4 digits of your SSN.");return false;}
  return true;
}

function showSuccess(){
  document.querySelectorAll('.form-step').forEach(s=>s.style.display='none');
  document.getElementById('successPanel').classList.add('show');
  document.getElementById('progWrap').style.display='none';
  document.getElementById('cardHead').style.display='none';
  document.getElementById('trustStrip').style.display='none';
  document.getElementById('section-form').scrollIntoView({behavior:'smooth'});
}

updateUI(1);
</script>
</body>
</html>