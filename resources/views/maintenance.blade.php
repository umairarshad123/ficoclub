<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>We'll Be Back Soon — 850 FICO Club</title>
<link rel="icon" href="/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --navy:#0F2044;--navy2:#1A3056;
        --green:#22C55E;--gold:#F5A623;--red:#EF4444;
        --muted:#94A3B8;--line:rgba(255,255,255,.12);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{
        min-height:100vh;display:flex;align-items:center;justify-content:center;
        padding:24px 16px;
        font-family:'Manrope',system-ui,-apple-system,sans-serif;color:#fff;
        background:
            radial-gradient(1000px 500px at 15% -10%, rgba(34,197,94,.18), transparent 60%),
            radial-gradient(900px 500px at 110% 110%, rgba(245,166,35,.16), transparent 60%),
            linear-gradient(160deg,var(--navy) 0%,var(--navy2) 100%);
    }
    .card{
        width:100%;max-width:560px;text-align:center;
        padding:48px 36px;border-radius:20px;
        background:rgba(255,255,255,.04);border:1px solid var(--line);
        box-shadow:0 30px 80px rgba(0,0,0,.35);
        backdrop-filter:blur(6px);
    }
    .brand{
        font-family:'Sora',sans-serif;font-weight:800;font-size:22px;letter-spacing:.5px;
        margin-bottom:28px;
    }
    .brand .n8{color:var(--green)}.brand .n5{color:var(--gold)}.brand .n0{color:var(--red)}
    .icon{
        width:72px;height:72px;margin:0 auto 24px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        background:rgba(245,166,35,.12);border:1px solid rgba(245,166,35,.35);
    }
    .icon svg{width:34px;height:34px;color:var(--gold);animation:spin 6s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
    @media (prefers-reduced-motion:reduce){.icon svg{animation:none}}
    h1{font-family:'Sora',sans-serif;font-weight:700;font-size:clamp(26px,6vw,34px);line-height:1.2;margin-bottom:14px}
    p{color:#CBD5E1;font-size:16px;line-height:1.65}
    .bar{height:4px;border-radius:4px;margin:32px auto 0;max-width:220px;overflow:hidden;background:var(--line)}
    .bar span{display:block;height:100%;width:40%;border-radius:4px;
        background:linear-gradient(90deg,var(--green),var(--gold),var(--red));
        animation:slide 1.8s ease-in-out infinite}
    @keyframes slide{0%{transform:translateX(-100%)}100%{transform:translateX(250%)}}
    @media (prefers-reduced-motion:reduce){.bar span{animation:none;width:100%}}
    .foot{margin-top:28px;font-size:13px;color:var(--muted)}
    @media (max-width:480px){.card{padding:36px 22px}}
</style>
</head>
<body>
    <main class="card">
        <div class="brand"><span class="n8">8</span><span class="n5">5</span><span class="n0">0</span> FICO CLUB</div>

        <div class="icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
        </div>

        <h1>We'll be back soon</h1>
        <p>Our website is currently undergoing scheduled maintenance while we make some improvements. Thank you for your patience — please check back shortly.</p>

        <div class="bar" aria-hidden="true"><span></span></div>

        <div class="foot">&copy; {{ date('Y') }} 850 FICO Club</div>
    </main>
</body>
</html>
