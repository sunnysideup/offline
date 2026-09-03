<?php
// not matched — everyone else


// --- HTTP status ------------------------------------------------------------
// 503 = Service Unavailable, the correct code for a temporary outage.
// Search engines treat it as "try again later" and won't de-index the URL.
// Swap to 403 here if you specifically need "Forbidden" instead.
http_response_code(503);

// Tell clients (and crawlers) roughly when to come back. Seconds.
$retryAfter = 3600; // 1 hour
header('Retry-After: ' . $retryAfter);

// Never let a stale maintenance page get cached.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Content-Type: text/html; charset=utf-8');

// Optional: tweak these to taste.
$siteName    = 'Our Site';
$eta         = 'shortly'; // human-friendly ETA shown in the copy
$refreshSecs = 60;        // JS auto-retry interval



?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= htmlspecialchars($siteName) ?> — Back soon</title>
<style>
:root {
    --bg-1: #0f172a;
    --bg-2: #1e293b;
    --accent: #38bdf8;
    --accent-2: #818cf8;
    --text: #e2e8f0;
    --muted: #94a3b8;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

html, body { height: 100%; }

body {
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    color: var(--text);
    background: radial-gradient(1200px 600px at 50% -10%, var(--bg-2), var(--bg-1));
    display: grid;
    place-items: center;
    min-height: 100%;
    padding: 2rem;
    overflow: hidden;
}

.card {
    position: relative;
    max-width: 34rem;
    width: 100%;
    text-align: center;
    padding: 3rem 2.5rem;
    border-radius: 1.25rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
    animation: rise 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@keyframes rise {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Pulsing orb */
.orb {
    width: 84px;
    height: 84px;
    margin: 0 auto 1.75rem;
    border-radius: 50%;
    background: conic-gradient(from 0deg, var(--accent), var(--accent-2), var(--accent));
    display: grid;
    place-items: center;
    animation: spin 6s linear infinite;
}
.orb::before {
    content: "";
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: var(--bg-1);
}
.orb::after {
    content: "";
    position: absolute;
    width: 84px;
    height: 84px;
    border-radius: 50%;
    background: var(--accent);
    opacity: 0.35;
    filter: blur(14px);
    animation: pulse 2.2s ease-in-out infinite;
}

@keyframes spin  { to { transform: rotate(360deg); } }
@keyframes pulse {
    0%, 100% { transform: scale(1);   opacity: 0.25; }
    50%      { transform: scale(1.35); opacity: 0.5;  }
}

h1 {
    font-size: clamp(1.5rem, 4vw, 2rem);
    letter-spacing: -0.02em;
    margin-bottom: 0.75rem;
}

p {
    color: var(--muted);
    line-height: 1.6;
    font-size: 1.05rem;
}

.divider {
    height: 1px;
    margin: 1.75rem 0 1.25rem;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
}

.status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--muted);
}
.dot {
    width: 9px; height: 9px; border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 0 var(--accent);
    animation: blink 1.6s ease-in-out infinite;
}
@keyframes blink {
    0%, 100% { opacity: 1;   box-shadow: 0 0 0 0 rgba(56,189,248,0.6); }
    50%      { opacity: 0.4; box-shadow: 0 0 0 8px rgba(56,189,248,0);  }
}

.countdown { color: var(--accent); font-variant-numeric: tabular-nums; }

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; }
}
</style>
</head>
<body>
<main class="card" role="main">
    <div class="orb" aria-hidden="true"></div>

    <h1>We&rsquo;ll be back <?= htmlspecialchars($eta) ?></h1>
    <p>
    <?= htmlspecialchars($siteName) ?> is down for a spot of scheduled
    maintenance. Nothing to do on your end &mdash; this page will refresh
    itself automatically.
    </p>

    <div class="divider"></div>

    <div class="status">
    <span class="dot" aria-hidden="true"></span>
    Retrying in <span class="countdown" id="countdown"><?= (int) $refreshSecs ?></span>s
    </div>
</main>

<script>
    (function () {
    var seconds = <?= (int) $refreshSecs ?>;
    var el = document.getElementById('countdown');

    var timer = setInterval(function () {
        seconds -= 1;
        if (seconds <= 0) {
        clearInterval(timer);
        // Reload from the server, bypassing cache.
        window.location.reload();
        return;
        }
        el.textContent = seconds;
    }, 1000);
    })();
</script>
</body>
</html>