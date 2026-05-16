<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Couples Glow-Up — Enrollment Hub · 850 FICO Club</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  :root{
    --navy:#0d1b3e;--navy-mid:#1a2f5e;--green:#22c55e;--green-dark:#16a34a;
    --green-light:#f0fdf4;--green-border:#bbf7d0;--gold:#ca9a04;--gold-light:#fefce8;
    --bg:#f1f5f9;--white:#fff;--border:#e2e8f0;--text:#0d1b3e;--muted:#6b7280;--light:#9ca3af;
  }
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Nunito Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
  .topbar{background:var(--navy);padding:10px 0;border-bottom:2px solid var(--green);text-align:center}
  .topbar span{color:#fff;font-size:12px;font-weight:800;letter-spacing:.5px}
  .topbar .dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--green);margin-right:7px;vertical-align:middle}
  .wrap{max-width:920px;margin:0 auto;padding:54px 24px 80px}
  .hero{text-align:center;margin-bottom:42px}
  .hero .pill{display:inline-flex;align-items:center;gap:7px;background:var(--green-light);color:var(--green-dark);border:1px solid var(--green-border);border-radius:100px;padding:7px 18px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:18px}
  .hero h1{font-size:clamp(30px,5vw,50px);font-weight:900;color:var(--navy);line-height:1.05;letter-spacing:-1px}
  .hero h1 span{color:var(--green)}
  .hero p{margin-top:14px;font-size:15px;color:var(--muted);font-weight:500;max-width:560px;margin-left:auto;margin-right:auto;line-height:1.6}
  .progress{display:flex;align-items:center;justify-content:center;gap:0;margin:34px 0 8px}
  .pstep{display:flex;align-items:center;gap:10px;font-size:13px;font-weight:800;color:var(--light)}
  .pstep .n{width:30px;height:30px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;background:#fff;transition:all .25s}
  .pstep.done{color:var(--green-dark)}
  .pstep.done .n{background:var(--green);border-color:var(--green);color:#fff}
  .pstep.active{color:var(--navy)}
  .pstep.active .n{background:var(--navy);border-color:var(--navy);color:#fff}
  .pline{width:54px;height:3px;background:var(--border);margin:0 12px;border-radius:3px}
  .pline.done{background:var(--green)}
  .cards{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:38px}
  @media(max-width:680px){.cards{grid-template-columns:1fr}}
  .pcard{background:var(--white);border:1.5px solid #d1dae8;border-radius:20px;padding:30px 26px;box-shadow:0 12px 40px rgba(13,27,62,.08),0 4px 12px rgba(13,27,62,.05);position:relative;display:flex;flex-direction:column;transition:transform .25s,box-shadow .25s}
  .pcard.locked{opacity:.58;filter:grayscale(.3)}
  .pcard.done{border-color:var(--green-border)}
  .pcard:not(.locked):hover{transform:translateY(-4px);box-shadow:0 20px 50px rgba(13,27,62,.12)}
  .pcard .ava{width:62px;height:62px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--navy-mid));display:flex;align-items:center;justify-content:center;font-size:30px;margin-bottom:18px}
  .pcard.done .ava{background:linear-gradient(135deg,var(--green),var(--green-dark))}
  .pcard h3{font-size:21px;font-weight:900;color:var(--navy);margin-bottom:4px}
  .pcard .role{font-size:12px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:14px}
  .pcard .stat{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:800;padding:6px 14px;border-radius:100px;margin-bottom:20px;width:fit-content}
  .stat.s-wait{background:#f1f5f9;color:var(--muted)}
  .stat.s-ready{background:var(--gold-light);color:var(--gold)}
  .stat.s-done{background:var(--green-light);color:var(--green-dark)}
  .pcard p{font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:22px;flex:1}
  .btn{display:flex;align-items:center;justify-content:center;gap:9px;width:100%;height:54px;border:none;border-radius:13px;font-family:inherit;font-size:15px;font-weight:900;letter-spacing:.3px;cursor:pointer;text-decoration:none;transition:all .22s cubic-bezier(.34,1.56,.64,1)}
  .btn-go{background:linear-gradient(135deg,var(--green),var(--green-dark));color:#fff;box-shadow:0 6px 20px rgba(34,197,94,.35)}
  .btn-go:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(34,197,94,.45)}
  .btn-locked{background:#e2e8f0;color:#94a3b8;cursor:not-allowed}
  .btn-done{background:var(--green-light);color:var(--green-dark);border:1.5px solid var(--green-border)}
  .done-banner{margin-top:38px;background:linear-gradient(135deg,var(--navy),var(--navy-mid));border:1.5px solid var(--green);border-radius:20px;padding:38px 30px;text-align:center;color:#fff}
  .done-banner .big{font-size:46px;margin-bottom:10px}
  .done-banner h2{font-size:26px;font-weight:900;margin-bottom:10px}
  .done-banner p{font-size:14px;color:rgba(255,255,255,.78);line-height:1.6;max-width:520px;margin:0 auto}
  .seals{display:flex;align-items:center;justify-content:center;gap:8px;font-size:11px;color:var(--light);font-weight:600;margin-top:34px}
  .seals .sep{opacity:.35}
</style>
</head>
<body>

<div class="topbar"><span><span class="dot"></span>SECURE ENROLLMENT · 850 FICO CLUB · COUPLES GLOW-UP</span></div>

<div class="wrap">

  <div class="hero">
    <div class="pill">💞 Couples Glow-Up · Payment Confirmed</div>
    <h1>Enroll <span>Both Partners</span></h1>
    <p>Your Couples Plan covers two full credit programs. Complete each partner's enrollment below — start with the first partner, then come back here to enroll the second.</p>

    <div class="progress">
      <div class="pstep done"><span class="n">✓</span> Payment</div>
      <div class="pline done"></div>
      <div class="pstep {{ $husbandDone ? 'done' : 'active' }}"><span class="n">{{ $husbandDone ? '✓' : '1' }}</span> Partner 1</div>
      <div class="pline {{ $husbandDone ? 'done' : '' }}"></div>
      <div class="pstep {{ $wifeDone ? 'done' : ($husbandDone ? 'active' : '') }}"><span class="n">{{ $wifeDone ? '✓' : '2' }}</span> Partner 2</div>
      <div class="pline {{ $bothDone ? 'done' : '' }}"></div>
      <div class="pstep {{ $bothDone ? 'done' : '' }}"><span class="n">{{ $bothDone ? '✓' : '3' }}</span> Done</div>
    </div>
  </div>

  @if ($bothDone)
    <div class="done-banner">
      <div class="big">🎉</div>
      <h2>Both Enrollments Complete!</h2>
      <p>Husband and wife are both enrolled. Our team is reviewing both credit files now and will begin the coordinated 3-bureau dispute attack right away. Check your email for portal access and next steps.</p>
    </div>
  @endif

  <div class="cards">

    {{-- ── PARTNER 1 — HUSBAND ── --}}
    <div class="pcard {{ $husbandDone ? 'done' : '' }}">
      <div class="ava">{{ $husbandDone ? '✓' : '🤵' }}</div>
      <div class="role">Partner 1</div>
      <h3>Husband</h3>
      @if ($husbandDone)
        <div class="stat s-done">● Enrolled</div>
        <p>Husband's enrollment is complete and submitted for processing.</p>
        <a class="btn btn-done">✓ Enrollment Complete</a>
      @else
        <div class="stat s-ready">● Ready to enroll</div>
        <p>Enter the husband's personal information, address and verification details to begin his credit program.</p>
        <a class="btn btn-go" href="{{ route('onboarding.form', ['partner' => 'husband']) }}">Start Husband's Enrollment →</a>
      @endif
    </div>

    {{-- ── PARTNER 2 — WIFE ── --}}
    <div class="pcard {{ $wifeDone ? 'done' : (!$husbandDone ? 'locked' : '') }}">
      <div class="ava">{{ $wifeDone ? '✓' : '👰' }}</div>
      <div class="role">Partner 2</div>
      <h3>Wife</h3>
      @if ($wifeDone)
        <div class="stat s-done">● Enrolled</div>
        <p>Wife's enrollment is complete and submitted for processing.</p>
        <a class="btn btn-done">✓ Enrollment Complete</a>
      @elseif (!$husbandDone)
        <div class="stat s-wait">● Waiting</div>
        <p>Complete the husband's enrollment first. The wife's enrollment will unlock right after.</p>
        <a class="btn btn-locked">🔒 Complete Partner 1 First</a>
      @else
        <div class="stat s-ready">● Ready to enroll</div>
        <p>Now enter the wife's personal information, address and verification details to begin her credit program.</p>
        <a class="btn btn-go" href="{{ route('onboarding.form', ['partner' => 'wife']) }}">Start Wife's Enrollment →</a>
      @endif
    </div>

  </div>

  <div class="seals">
    <span>🔒 SSL Secured</span><span class="sep">|</span>
    <span>🛡️ PCI Compliant</span><span class="sep">|</span>
    <span>✓ Invoice {{ $invoiceNumber }}</span>
  </div>

</div>

</body>
</html>
