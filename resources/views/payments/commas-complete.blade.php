<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Finalizing Your Enrollment — 850 FICO Club</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root{--navy:#0d1b3e;--green:#22c55e;--green-dark:#16a34a;--green-light:#f0fdf4;--green-border:#bbf7d0;
          --amber-light:#fffbeb;--amber-border:#fde68a;--bg:#f1f5f9;--border:#e2e8f0;--text-mid:#374151}
    *{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px 16px;
         background:var(--bg);font-family:'Nunito Sans',system-ui,sans-serif;color:var(--navy)}
    .card{width:100%;max-width:520px;background:#fff;border:1px solid var(--border);border-radius:18px;
          padding:44px 32px;text-align:center;box-shadow:0 20px 60px rgba(13,27,62,.08)}
    .spin{width:46px;height:46px;margin:0 auto 22px;border-radius:50%;
          border:4px solid var(--green-border);border-top-color:var(--green-dark);animation:spin .9s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
    @media (prefers-reduced-motion:reduce){.spin{animation-duration:3s}}
    .icon{font-size:44px;margin-bottom:16px}
    h1{font-size:24px;font-weight:900;margin-bottom:10px}
    p{color:var(--text-mid);font-size:15px;line-height:1.6}
    .note{margin-top:22px;padding:14px 16px;border-radius:12px;font-size:14px;line-height:1.55;text-align:left}
    .note.slow{display:none;background:var(--amber-light);border:1px solid var(--amber-border)}
    .note.info{background:var(--green-light);border:1px solid var(--green-border)}
    .meta{margin-top:20px;font-size:12px;color:#94a3b8}
    a{color:var(--green-dark);font-weight:800}
</style>
</head>
<body>
<main class="card">

@if ($order->status === \App\Models\CheckoutOrder::STATUS_MISMATCH)
    <div class="icon">🧾</div>
    <h1>We received your payment</h1>
    <p>Thank you, {{ $order->first_name }}. Our team needs to review one detail of your order before we finish your enrollment — we'll contact you at <strong>{{ $order->email }}</strong> shortly.</p>
    <div class="note info">Questions? Email <a href="mailto:info@850ficoclub.com">info@850ficoclub.com</a> and mention invoice <strong>{{ $order->invoice_number }}</strong>.</div>
@else
    <div class="spin" aria-hidden="true"></div>
    <h1>Finalizing your enrollment…</h1>
    <p>Thank you, {{ $order->first_name }}! We're confirming your payment for the <strong>{{ $order->plan_label }}</strong>. This usually takes a few seconds — please keep this page open.</p>

    <div class="note slow" id="slowNote">
        Your payment is still being confirmed by our processor. You don't need to pay again — this page will continue automatically,
        and you'll also receive an email. If nothing happens within a few minutes, contact
        <a href="mailto:info@850ficoclub.com">info@850ficoclub.com</a> with invoice <strong>{{ $order->invoice_number }}</strong>.
    </div>

    <div class="meta">Invoice {{ $order->invoice_number }}</div>

    <script>
    (function () {
        var url = @json($statusUrl);
        var started = Date.now();

        function poll() {
            fetch(url, { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    if (d.redirect) { window.location.href = d.redirect; return; }
                    if (d.status === 'mismatch') { window.location.reload(); return; }
                    next();
                })
                .catch(next);
        }

        function next() {
            var elapsed = Date.now() - started;
            if (elapsed > 45000) document.getElementById('slowNote').style.display = 'block';
            if (elapsed > 15 * 60000) return;                  // stop after 15 min; the email still goes out
            setTimeout(poll, elapsed > 60000 ? 10000 : 2500);
        }

        poll();
    })();
    </script>
@endif

</main>
</body>
</html>
