<?php
// 1. Config OGAds
$api_key = '38109|xCWqdYWb0ukY5V0knSRcuEJEPiqO1i8wVssqRU2Z4e4d1794';
$user_ip = $_SERVER['REMOTE_ADDR'];
$user_ua = $_SERVER['HTTP_USER_AGENT'];

$endpoint = "https://lockedapp.org/api/v2?ip=" . urlencode($user_ip) . "&user_agent=" . urlencode($user_ua);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Authorization: Bearer $api_key"]
]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response);
$all_offers = ($data && isset($data->success) && $data->success) ? $data->offers : [];

// 2. Filter 1 CPI (Sponsor) + 1 CPA
$cpi = null; $cpa = null;
foreach ($all_offers as $o) {
    if (!$cpi && (stripos($o->adcopy, 'install') !== false || stripos($o->device, 'Android') !== false || stripos($o->device, 'iPhone') !== false)) { $cpi = $o; }
    else if (!$cpa && (stripos($o->adcopy, 'survey') !== false || stripos($o->adcopy, 'enter') !== false)) { $cpa = $o; }
    if ($cpi && $cpa) break;
}
if (!$cpi && isset($all_offers[0])) $cpi = $all_offers[0];
if (!$cpa && isset($all_offers[1])) $cpa = $all_offers[1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Verification - Scandinavian Biolabs</title>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        :root { --brand: #0b2e26; --accent: #10b981; --gold: #FFD700; }
        body { margin: 0; background: var(--brand); font-family: -apple-system, system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; overflow-x: hidden; }
        
        .card { background: white; width: 92%; max-width: 420px; border-radius: 35px; padding: 25px 20px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.4); position: relative; }
        
        /* Product Thumbnails Header */
        .products-header { display: flex; justify-content: center; gap: 10px; margin-bottom: 20px; }
        .p-thumb { width: 50px; height: 50px; border-radius: 12px; border: 2px solid #f0f0f0; object-fit: cover; }

        /* Progress Bar */
        .progress-box { margin-bottom: 20px; }
        .bar-bg { background: #eee; height: 6px; border-radius: 10px; }
        .bar-fill { background: var(--accent); width: 94%; height: 100%; border-radius: 10px; animation: load 2.5s ease; }
        @keyframes load { from { width: 0; } to { width: 94%; } }

        h2 { color: var(--brand); font-size: 20px; font-weight: 900; margin: 0; }
        .instr { color: #666; font-size: 13px; margin: 10px 0 20px; line-height: 1.4; }

        /* Offers */
        .offer { display: flex; align-items: center; padding: 14px; border-radius: 20px; text-decoration: none; transition: 0.3s; margin-bottom: 12px; border: 1px solid #f0f0f0; position: relative; }
        .sponsor { border: 2px solid var(--gold) !important; background: #fffdf2; box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2); }
        .badge { position: absolute; top: -10px; right: 15px; background: var(--gold); color: black; font-size: 9px; font-weight: 900; padding: 3px 10px; border-radius: 50px; }

        .o-img { width: 55px; height: 55px; border-radius: 12px; margin-right: 15px; object-fit: cover; }
        .o-info { flex: 1; text-align: left; }
        .o-name { font-size: 14px; font-weight: 700; color: #111; margin: 0; }
        .o-sub { font-size: 11px; color: #777; margin-top: 2px; }
        
        .btn { background: var(--accent); color: white; padding: 8px 15px; border-radius: 50px; font-size: 11px; font-weight: 800; }
        .btn-gold { background: var(--gold); color: black; }

        /* Fake Notifications */
        #notif { display: none; position: fixed; bottom: 20px; left: 15px; background: white; padding: 10px 15px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); align-items: center; gap: 10px; border: 1px solid #eee; z-index: 999; }
        @keyframes slideIn { from { transform: translateX(-120%); } to { transform: translateX(0); } }
        @keyframes slideOut { from { transform: translateX(0); } to { transform: translateX(-120%); } }
    </style>
</head>
<body>

<div class="card">
    <div class="products-header">
        <img src="https://scandinavianbiolabs.com/cdn/shop/files/90328d3c7b4b5755e19bbf381e4dcb284dae8e0a.jpg?v=1760959842&width=100" class="p-thumb">
        <img src="https://scandinavianbiolabs.com/cdn/shop/files/source_image.jpg?v=1760959842&width=100" class="p-thumb">
        <img src="https://scandinavianbiolabs.com/cdn/shop/files/HGR-bl.jpg?v=1761573050&width=100" class="p-thumb">
    </div>

    <div class="progress-box">
        <div class="bar-bg"><div class="bar-fill"></div></div>
        <small style="color:var(--accent); font-weight:bold; font-size:10px;">Verification: 94% Complete</small>
    </div>

    <h2>Final Step</h2>
    <p class="instr">Install our sponsor app or complete a simple task below to unlock your 100% Discount Coupon.</p>

    <div class="offer-list">
        <?php if($cpi): ?>
            <a href="<?= $cpi->link ?>" class="offer sponsor" onclick="celebrate()">
                <span class="badge">RECOMMENDED</span>
                <img src="<?= str_replace('\/', '/', $cpi->picture) ?>" class="o-img">
                <div class="o-info">
                    <div class="o-name"><?= $cpi->name_short ?></div>
                    <div class="o-sub"><b>Fastest:</b> Install & open sponsor app.</div>
                </div>
                <div class="btn btn-gold">INSTALL</div>
            </a>
        <?php endif; ?>

        <?php if($cpa): ?>
            <a href="<?= $cpa->link ?>" class="offer" onclick="celebrate()">
                <img src="<?= str_replace('\/', '/', $cpa->picture) ?>" class="o-img">
                <div class="o-info">
                    <div class="o-name"><?= $cpa->name_short ?></div>
                    <div class="o-sub"><?= $cpa->adcopy ?></div>
                </div>
                <div class="btn">VERIFY</div>
            </a>
        <?php endif; ?>
    </div>
</div>

<div id="notif">
    <div style="width:30px; height:30px; background:var(--accent); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:14px;">✓</div>
    <div style="text-align:left;">
        <div id="user-name" style="font-size:12px; font-weight:bold; color:var(--brand);">Thomas S.</div>
        <div style="font-size:10px; color:#666;">Just claimed 100% discount!</div>
    </div>
</div>

<script>
    function celebrate() {
        confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
    }

    // Fake Customers Coupons Loop
    const users = ["Thomas S.", "Emma L.", "Mette H.", "Anders P.", "Sofia K.", "Lukas V.", "Erik J.", "Maja N."];
    const cities = ["Oslo", "Stockholm", "Copenhagen", "Helsinki", "Bergen"];

    function showNotif() {
        const notif = document.getElementById('notif');
        const user = users[Math.floor(Math.random()*users.length)];
        const city = cities[Math.floor(Math.random()*cities.length)];
        
        document.getElementById('user-name').innerText = user + " from " + city;
        
        notif.style.display = 'flex';
        notif.style.animation = 'slideIn 0.5s forwards';

        setTimeout(() => {
            notif.style.animation = 'slideOut 0.5s forwards';
            setTimeout(() => { notif.style.display = 'none'; }, 500);
        }, 4000);
    }

    // Auto loop
    setInterval(showNotif, 12000);
    setTimeout(showNotif, 3000);

    // Initial Confetti on load
    window.onload = () => { celebrate(); };
</script>

</body>
</html>