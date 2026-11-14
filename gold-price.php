<?php
// OmniWire Media — Daily Gold Price in Pakistan (evergreen utility page)
// Renders the latest verified row from gold_rates (news_portal.db). Rates are the APSGJA daily announcement, two-source verified by the daily updater.
$dbPath = file_exists(__DIR__ . '/news_portal.db') 
    ? __DIR__ . '/news_portal.db' 
    : (file_exists(__DIR__ . '/live_news_portal.db') ? __DIR__ . '/live_news_portal.db' : '/home/omniwiremedia/public_html/news_portal.db');
$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$rows = [];
try {
  $rows = $db->query('SELECT * FROM gold_rates ORDER BY rate_date DESC LIMIT 7')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $rows = []; }
$latest = $rows[0] ?? null;
function fmt($n) { return 'Rs ' . number_format((float)$n); }
function dstr($d) { return date('j F Y', strtotime($d)); }
$TOLA = 11.6638;
$title = 'Gold Price in Pakistan Today | 24K Gold Rate per Tola — OmniWire Media';
$desc='Gold price in Pakistan today: 24K gold rate per tola, 22K gold price, silver price in Pakistan and daily gold rate updates from Karachi.';
$stampNote = '';
if ($latest) {
  $title = 'Gold Price in Pakistan Today — ' . dstr($latest['rate_date']) . ' | 24K Gold Rate per Tola ' . fmt($latest['g24_tola']);
$desc='Gold price in Pakistan today: 24K gold rate per tola, 22K gold price, silver price in Pakistan and daily gold rate updates from Karachi.';
  $wd = date('N', strtotime($latest['rate_date']));
  $today = date('Y-m-d');
  if ($latest['rate_date'] !== $today) {
    $stampNote = ($wd >= 6 || $latest['rate_date'] < $today) ? ' — Sarafa markets closed or today\'s rate awaited; showing the last announced rate.' : '';
  }
}
$canon = 'https://omniwiremedia.com/gold-price-in-pakistan-today';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($desc) ?>">
<link rel="canonical" href="<?= $canon ?>">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta property="og:type" content="website">
<meta property="og:site_name" content="OmniWire Media">
<meta property="og:url" content="<?= $canon ?>">
<meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($desc) ?>">
<meta property="og:image" content="https://omniwiremedia.com/static/images/omniwire-logo-dp.jpg">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($title) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($desc) ?>">
<meta name="twitter:image" content="https://omniwiremedia.com/static/images/omniwire-logo-dp.jpg">
<style>
body{font-family:Georgia,'Times New Roman',serif;margin:0;color:#1a1a1a;background:#faf9f7;line-height:1.6}
header.site{background:#0e1a2b;color:#fff;padding:14px 20px}
header.site a{color:#fff;text-decoration:none;font-weight:700;letter-spacing:.04em}
main{max-width:860px;margin:0 auto;padding:24px 18px 48px}
h1{font-size:1.9rem;line-height:1.25;margin:.4em 0 .2em}
h2{font-size:1.3rem;margin-top:2em;border-bottom:2px solid #c9a227;padding-bottom:.25em}
.card{background:#fff;border:1px solid #e5e0d5;border-top:5px solid #c9a227;border-radius:8px;padding:22px;margin:20px 0;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.big{font-size:2.4rem;font-weight:700;color:#0e1a2b}
.up{color:#b3261e}.down{color:#137333}
table{width:100%;border-collapse:collapse;background:#fff;margin:14px 0}
th,td{border:1px solid #e5e0d5;padding:9px 12px;text-align:right}
th:first-child,td:first-child{text-align:left}
th{background:#0e1a2b;color:#fff}
.stamp{color:#5f6368;font-size:.9rem}
.note{background:#fff8e1;border:1px solid #e5d9a0;border-radius:6px;padding:12px 16px;font-size:.92rem}
a{color:#0b57d0}
.faq p{margin:.4em 0 1em}
footer.site{background:#0e1a2b;color:#cfd8e3;padding:22px 20px;font-size:.88rem}
footer.site a{color:#9ec1ff}
.crumb{font-size:.88rem;color:#5f6368}
</style>
<?php if ($latest): ?>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","url":"<?= $canon ?>","name":<?= json_encode($title) ?>,"dateModified":<?= json_encode($latest['rate_date'] . 'T15:30:00+05:00') ?>,"publisher":{"@type":"NewsMediaOrganization","name":"OmniWire Media","url":"https://omniwiremedia.com/"}}
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[
{"@type":"Question","name":"What is the price of 1 tola 24K gold in Pakistan today?","acceptedAnswer":{"@type":"Answer","text":"1 tola of 24K gold in Pakistan is <?= fmt($latest['g24_tola']) ?> as of <?= dstr($latest['rate_date']) ?>, the rate announced by the All Pakistan Sarafa Gems & Jewellers Association."}},
{"@type":"Question","name":"What is the 22K gold rate per tola today?","acceptedAnswer":{"@type":"Answer","text":"The 22K gold rate per tola is <?= fmt(round($latest['g24_tola'] * 22 / 24)) ?> as of <?= dstr($latest['rate_date']) ?>, derived from the 24K rate by purity (22/24)."}},
{"@type":"Question","name":"What is the price of 10 grams of 24K gold today?","acceptedAnswer":{"@type":"Answer","text":"10 grams of 24K gold cost <?= fmt(round($latest['g24_tola'] * 10 / $TOLA)) ?> as of <?= dstr($latest['rate_date']) ?>."}},
{"@type":"Question","name":"What is the silver rate per tola today?","acceptedAnswer":{"@type":"Answer","text":"Silver is <?= fmt($latest['silver_tola']) ?> per tola as of <?= dstr($latest['rate_date']) ?>."}},
{"@type":"Question","name":"Why does the gold rate change every day?","acceptedAnswer":{"@type":"Answer","text":"The Sarafa Association sets the local rate from the international spot price of gold and the interbank dollar-rupee exchange rate, so the Pakistan rate moves whenever either input moves."}},
{"@type":"Question","name":"Is the gold rate the same in Karachi, Lahore and Islamabad?","acceptedAnswer":{"@type":"Answer","text":"The association rate is national, but individual jewellers and cities can differ slightly, and jewellery prices add making charges on top of the raw gold rate."}}
]}
</script>
<?php endif; ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FPZVK4K12W"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("js", new Date());
gtag("config", "G-FPZVK4K12W");
</script>
</head>
<body>
<header class="site"><a href="https://omniwiremedia.com/">OMNIWIRE MEDIA</a></header>
<main>
<p class="crumb"><a href="https://omniwiremedia.com/">Home</a> › <a href="https://omniwiremedia.com/category/fintech-crypto">Fintech &amp; Crypto</a> › Gold Price in Pakistan</p>
<h1>Gold Price in Pakistan Today<?= $latest ? ' (' . dstr($latest['rate_date']) . ')' : '' ?></h1>
<?php if ($latest): ?>
<div class="card">
  <div class="stamp">24K GOLD — PER TOLA</div>
  <div class="big"><?= fmt($latest['g24_tola']) ?></div>
  <?php if ((int)$latest['g24_change'] !== 0): ?>
  <div class="<?= $latest['g24_change'] > 0 ? 'up' : 'down' ?>"><?= $latest['g24_change'] > 0 ? '▲' : '▼' ?> <?= fmt(abs($latest['g24_change'])) ?> vs the previous announced rate</div>
  <?php endif; ?>
  <p style="margin:.6em 0 0">Silver: <strong><?= fmt($latest['silver_tola']) ?></strong> per tola</p>
  <p class="stamp">Last updated: <?= dstr($latest['rate_date']) ?> · Rates announced by the All Pakistan Sarafa Gems &amp; Jewellers Association<?= $stampNote ?></p>
</div>
<h2>Gold and Silver Rates — <?= dstr($latest['rate_date']) ?></h2>
<table>
<tr><th>Purity</th><th>Per tola</th><th>Per 10 grams</th><th>Per gram</th></tr>
<?php foreach ([['24K (999)', 1.0], ['22K (916)', 22/24], ['21K (875)', 21/24]] as $r): $base = $latest['g24_tola'] * $r[1]; ?>
<tr><td><?= $r[0] ?></td><td><?= fmt(round($base)) ?></td><td><?= fmt(round($base * 10 / $TOLA)) ?></td><td><?= fmt(round($base / $TOLA)) ?></td></tr>
<?php endforeach; ?>
<tr><td>Silver (999)</td><td><?= fmt($latest['silver_tola']) ?></td><td><?= fmt($latest['silver_10g']) ?></td><td><?= fmt(round($latest['silver_tola'] / $TOLA)) ?></td></tr>
</table>
<p class="stamp">Market inputs behind the association rate: international spot gold $<?= number_format((float)$latest['spot_usd'], 2) ?>/oz · interbank USD/PKR <?= number_format((float)$latest['usdpkr'], 2) ?>.</p>
<?php if (!empty($latest['why_text'])): ?>
<h2>Why did the rate move?</h2>
<p><?= htmlspecialchars($latest['why_text']) ?></p>
<?php endif; ?>
<?php if (count($rows) > 1): 
  $chartRows = array_reverse($rows);
  $rates = array_column($chartRows, 'g24_tola');
  $minR = min($rates); $maxR = max($rates);
  $spread = max($maxR - $minR, 1);
  $pts = [];
  $numPts = count($chartRows);
  for ($i = 0; $i < $numPts; $i++) {
      $x = $numPts > 1 ? 55 + (540 * $i / ($numPts - 1)) : 300;
      $y = 125 - (($chartRows[$i]['g24_tola'] - $minR) / $spread) * 85;
      $pts[] = round($x, 1) . ',' . round($y, 1);
  }
  $polyline = implode(' ', $pts);
?>
<div class="chart-box" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px 16px;margin:22px 0;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
  <h3 style="margin:0 0 14px 0;font-size:1.1rem;color:#0e1a2b;font-family:sans-serif;">7-Day 24K Gold Price Trend (Rs / Tola)</h3>
  <svg viewBox="0 0 640 160" style="width:100%;height:auto;overflow:visible;font-family:sans-serif;">
    <line x1="55" y1="40" x2="600" y2="40" stroke="#f1f5f9" stroke-width="1"/>
    <line x1="55" y1="82" x2="600" y2="82" stroke="#f1f5f9" stroke-width="1"/>
    <line x1="55" y1="125" x2="600" y2="125" stroke="#f1f5f9" stroke-width="1"/>
    <text x="50" y="44" text-anchor="end" font-size="11" fill="#64748b"><?= fmt($maxR) ?></text>
    <text x="50" y="129" text-anchor="end" font-size="11" fill="#64748b"><?= fmt($minR) ?></text>
    <polyline fill="none" stroke="#d97706" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" points="<?= $polyline ?>"/>
    <?php foreach ($chartRows as $i => $cr): 
      $xy = explode(',', $pts[$i]);
    ?>
      <circle cx="<?= $xy[0] ?>" cy="<?= $xy[1] ?>" r="5" fill="#d97706" stroke="#fff" stroke-width="2"/>
      <text x="<?= $xy[0] ?>" y="150" text-anchor="middle" font-size="10" fill="#475569" font-weight="600"><?= date('j M', strtotime($cr['rate_date'])) ?></text>
    <?php endforeach; ?>
  </svg>
</div>
<?php endif; ?>

<?php if (count($rows) > 1): ?>
<h2>Last 7 announced rates</h2>
<table>
<tr><th>Date</th><th>24K per tola</th><th>Change</th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= dstr($r['rate_date']) ?></td><td><?= fmt($r['g24_tola']) ?></td><td><?= (int)$r['g24_change'] === 0 ? '—' : (($r['g24_change'] > 0 ? '+' : '−') . fmt(abs($r['g24_change']))) ?></td></tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php else: ?>
<div class="note">Today's Sarafa Association rate is being verified from two market sources and will appear here shortly. OmniWire publishes the rate only after both sources agree — never an estimate.</div>
<?php endif; ?>
<h2>How the gold rate in Pakistan is set</h2>
<p>Pakistan does not leave its gold price to chance or to individual shops. Every market day, the All Pakistan Sarafa Gems &amp; Jewellers Association announces the rate that jewellers across the country then trade around. The association builds that figure from two live inputs: the international spot price of gold in US dollars, and the interbank dollar–rupee exchange rate published from the State Bank's market. A customary premium of about twenty dollars per ounce over the international price is included, which is why the local rate rarely matches a simple conversion of the world price.</p>
<p>The unit that matters most in Pakistan is the tola, equal to 11.6638 grams. Prices are quoted for 24-karat gold at 999 purity — effectively pure gold. The 22-karat and 21-karat figures are not separate market decisions; they are the same rate scaled by purity, so 22K is the 24K price multiplied by 22/24. Ten-gram and per-gram prices are the tola price converted by weight. When two of these figures on any rate card disagree with those ratios, the card has a misprint — the ratios are arithmetic, not opinion.</p>
<p>The number on this page is the raw-gold rate. A jeweller's counter price will differ: making charges (mazdoori), design and wastage are added on top, and individual shops and cities vary slightly around the association figure. Rates also move — sometimes sharply — whenever the international price jumps overnight or the rupee moves against the dollar, which is why a rate is only ever "today's" until the next announcement replaces it. OmniWire republishes the association figure each market day after confirming it against two independent published sources, and keeps the previous days in the history table above so readers can see the trend rather than a single number.</p>
<h2>Frequently asked questions</h2>
<div class="faq">
<p><strong>What is the price of 1 tola 24K gold in Pakistan today?</strong></p>
<p><?= $latest ? 'It is ' . fmt($latest['g24_tola']) . ' as of ' . dstr($latest['rate_date']) . ', per the Sarafa Association announcement.' : 'See the rate card above for the latest verified figure.' ?></p>
<p><strong>What is the 22K gold rate per tola today?</strong></p>
<p><?= $latest ? 'It is ' . fmt(round($latest['g24_tola'] * 22 / 24)) . ', derived from the 24K rate by purity (22/24).' : 'It is the 24K rate multiplied by 22/24 — see the table above.' ?></p>
<p><strong>What is the price of 10 grams of 24K gold today?</strong></p>
<p><?= $latest ? 'It is ' . fmt(round($latest['g24_tola'] * 10 / $TOLA)) . ' as of ' . dstr($latest['rate_date']) . '.' : 'See the rates table above.' ?></p>
<p><strong>What is the silver rate per tola today?</strong></p>
<p><?= $latest ? 'Silver is ' . fmt($latest['silver_tola']) . ' per tola as of ' . dstr($latest['rate_date']) . '.' : 'See the rates table above.' ?></p>
<p><strong>Why does the gold rate change every day?</strong></p>
<p>Because its two inputs — the international spot price and the dollar–rupee rate — change every day. The association re-announces whenever they move.</p>
<p><strong>Is the gold rate the same in Karachi, Lahore and Islamabad?</strong></p>
<p>The association rate is national, but jewellers and cities can differ slightly, and jewellery prices add making charges on top of the raw rate.</p>
</div>
<h2>More from OmniWire Media</h2>
<ul>
<li><a href="https://omniwiremedia.com/article/gold-bullion-surges-to-record-highs-central-bank-accumulation-and-inflation-131">Why gold keeps setting records</a> — the story behind the rate.</li>
<li><a href="https://omniwiremedia.com/article/pakistan-and-the-imf-near-the-end-of-review-talks-with-1-2-billion-at-stake-186">Pakistan and the IMF</a> — the rupee context behind local prices.</li>
<li><a href="https://omniwiremedia.com/article/fbr-restore-filer-status-atl-u-form-25000-surcharge-november-2026">FBR ATL-U Form: Restore Filer Status Without Rs 25,000 Surcharge</a> — tax context for gold buyers.</li>
<li><a href="https://omniwiremedia.com/article/bitcoin-pushed-past-86-000-then-the-jobs-data-changed-the-mood">Bitcoin pushed past $86,000</a> — the alternative-asset view.</li>
<li><a href="https://omniwiremedia.com/category/fintech-crypto">More Fintech &amp; Crypto coverage</a></li>
</ul>
<div class="note" style="margin-top:2em">Rates shown are the daily rates announced by the All Pakistan Sarafa Gems &amp; Jewellers Association, compiled from published market sources for information only. Actual buying and selling prices vary by city and jeweller, and jewellery prices exclude making charges. OmniWire Media does not buy or sell gold and accepts no liability for any transaction made on the basis of these indicative rates. This page is not financial advice.</div>
</main>
<footer class="site">OmniWire Media · <a href="https://omniwiremedia.com/editorial-policy">Editorial Policy</a> · <a href="https://omniwiremedia.com/team">Our Team</a> · Rates desk: OmniWire Editorial Desk</footer>
</body>
</html>
