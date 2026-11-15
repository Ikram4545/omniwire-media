<?php
// Petrol Price in Pakistan Today — standalone utility page
// Verified prices: OGRA / Petroleum Division notification effective 5 November 2026 (notification issued 4 November 2026; no end date asserted - sources phrase validity as for 5 November)
// Multi-source agreement checked 5 Nov 2026 (~20:30 PKT, scheduled run): The News full page citing the OGRA notification (petrol 369.06 to 362.00 -7.06, HSD 391.52 to 387.81 -3.71, effective Nov 5), Islamabad Post full page citing the OGRA notification (same figures, same changes, full table, effective Nov 5) all agree. Corroborated by Business Recorder (via PakGold/danioPSX), Mettis Global via Investify, Green Post, The Standard, TechX, Taaza Taren, Pakistan Point, 247News. No conflict on the NEW petrol/diesel figures. Disregarded: Pakistan Today / PakObserver "Rs 7.6" typo in the delta text (final price Rs 362 matches all sources); Pakistan Today diesel "from Rs387.81 to Rs387.81" typo (final figure matches). Kerosene/LDO: no 5 Nov change in the petrol/diesel notification reporting; Pakistan Today fuel page effective 5 Nov lists 323.67/269.09, carried unchanged from the verified 3 Nov revision.
$effective_date = '5 November 2026';
$effective_iso = '2026-11-05';
$petrol = 362.00; $petrol_prev = 369.06; $petrol_change = -7.06;
$hsd = 387.81; $hsd_prev = 391.52; $hsd_change = -3.71;
$kerosene = 323.67; $kerosene_prev = 323.67; $kerosene_change = 0; // unchanged in 5 Nov revision; last verified change 3 Nov (323.67); Pakistan Today fuel page effective 5 Nov lists same 323.67, no new figure confirmed
$ldo = 269.09; $ldo_prev = 269.09; $ldo_change = 0; // unchanged in 5 Nov revision; last verified change 3 Nov (269.09); Pakistan Today fuel page effective 5 Nov lists same 269.09, no new figure confirmed
?><!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Petrol Price in Pakistan Today — <?php echo $effective_date; ?> | Petrol Rs <?php echo number_format($petrol,2); ?> per Litre — OmniWire Media</title>
<meta name="description" content="Petrol price in Pakistan today: petrol rate per litre, diesel and HSD price, OGRA petrol price notification and daily petrol price updates.">
<link rel="canonical" href="https://omniwiremedia.com/petrol-price-in-pakistan-today">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta property="og:type" content="website">
<meta property="og:site_name" content="OmniWire Media">
<meta property="og:url" content="https://omniwiremedia.com/petrol-price-in-pakistan-today">
<meta property="og:title" content="Petrol Price in Pakistan Today | Petrol Rs <?php echo number_format($petrol,2); ?> per Litre — OmniWire Media">
<meta property="og:description" content="Petrol price in Pakistan today: petrol rate per litre, diesel and HSD price, OGRA petrol price notification and daily petrol price updates.">
<meta property="og:image" content="https://omniwiremedia.com/static/images/omniwire-logo-dp.jpg">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Petrol Price in Pakistan Today | Petrol Rs <?php echo number_format($petrol,2); ?> per Litre — OmniWire Media">
<meta name="twitter:description" content="Petrol price in Pakistan today: petrol rate per litre, diesel and HSD price, OGRA petrol price notification and daily petrol price updates.">
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
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"Petrol Price in Pakistan Today","url":"https://omniwiremedia.com/petrol-price-in-pakistan-today","dateModified":"<?php echo $effective_iso; ?>","publisher":{"@type":"Organization","name":"OmniWire Media"}}
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[
{"@type":"Question","name":"What is the petrol price in Pakistan today?","acceptedAnswer":{"@type":"Answer","text":"Petrol (Motor Spirit) is Rs <?php echo number_format($petrol,2); ?> per litre, effective <?php echo $effective_date; ?>, as notified by OGRA and the Petroleum Division."}},
{"@type":"Question","name":"What is the diesel price in Pakistan today?","acceptedAnswer":{"@type":"Answer","text":"High-Speed Diesel (HSD) is Rs <?php echo number_format($hsd,2); ?> per litre, effective <?php echo $effective_date; ?>."}},
{"@type":"Question","name":"When did the latest petrol price change take effect?","acceptedAnswer":{"@type":"Answer","text":"The latest revision took effect on <?php echo $effective_date; ?>. Petrol fell by Rs 7.06 per litre to Rs 362.00 and diesel fell by Rs 3.71 per litre to Rs 387.81 compared with the previous notification."}},
{"@type":"Question","name":"How often do petrol prices change in Pakistan?","acceptedAnswer":{"@type":"Answer","text":"Since July 2026, OGRA revises petrol and diesel prices on working days under a daily pricing mechanism based on a seven-working-day rolling average of international prices. Prices are normally unchanged on weekends and public holidays."}},
{"@type":"Question","name":"Is the petrol price the same in Karachi, Lahore and Islamabad?","acceptedAnswer":{"@type":"Answer","text":"Yes. The OGRA-notified ex-depot price is a nationwide ceiling price and is the same across Pakistan, helped by the Inland Freight Equalisation Margin."}},
{"@type":"Question","name":"What are the kerosene and light diesel prices?","acceptedAnswer":{"@type":"Answer","text":"Kerosene oil is Rs <?php echo number_format($kerosene,2); ?> per litre and Light Diesel Oil is Rs <?php echo number_format($ldo,2); ?> per litre (both unchanged in the latest notification)."}}
]}
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FPZVK4K12W"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("js", new Date());
gtag("config", "G-FPZVK4K12W");
</script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1432014132233776" crossorigin="anonymous"></script>
</head>
<body>
<header class="site"><a href="https://omniwiremedia.com/">OMNIWIRE MEDIA</a></header>
<main>
<p class="crumb"><a href="https://omniwiremedia.com/">Home</a> &rsaquo; <a href="https://omniwiremedia.com/category/fintech-crypto">Fintech &amp; Crypto</a> &rsaquo; Petrol Price in Pakistan</p>
<h1>Petrol Price in Pakistan Today (<?php echo $effective_date; ?>)</h1>
<div class="card">
<div class="stamp">Last updated: <?php echo $effective_date; ?> &middot; Rates notified by the Oil and Gas Regulatory Authority (OGRA) / Petroleum Division, effective <?php echo $effective_date; ?></div>
<p style="margin:.6em 0 .1em">Petrol (Motor Spirit) per litre</p>
<div class="big">Rs <?php echo number_format($petrol,2); ?></div>
<p><span class="down">(&#9660; Rs 7.06)</span> vs previous Rs <?php echo number_format($petrol_prev,2); ?> &nbsp;|&nbsp; High-Speed Diesel: <strong>Rs <?php echo number_format($hsd,2); ?> per litre</strong> <span class="down">(&#9660; Rs 3.71)</span></p>
</div>
<h2>Current fuel prices in Pakistan</h2>
<table>
<tr><th>Fuel</th><th>Price per litre</th><th>Previous price</th><th>Change</th></tr>
<tr><td>Petrol (Motor Spirit / MS)</td><td>Rs <?php echo number_format($petrol,2); ?></td><td>Rs <?php echo number_format($petrol_prev,2); ?></td><td class="down">− Rs 7.06</td></tr>
<tr><td>High-Speed Diesel (HSD)</td><td>Rs <?php echo number_format($hsd,2); ?></td><td>Rs <?php echo number_format($hsd_prev,2); ?></td><td class="down">− Rs 3.71</td></tr>
<tr><td>Kerosene Oil (SKO)</td><td>Rs <?php echo number_format($kerosene,2); ?></td><td>Rs <?php echo number_format($kerosene_prev,2); ?></td><td>—</td></tr>
<tr><td>Light Diesel Oil (LDO)</td><td>Rs <?php echo number_format($ldo,2); ?></td><td>Rs <?php echo number_format($ldo_prev,2); ?></td><td>—</td></tr>
</table>
<p class="stamp">Petrol fell by Rs 7.06 and diesel fell by Rs 3.71 in the <?php echo $effective_date; ?> notification. Kerosene and light diesel oil are unchanged at Rs 323.67 and Rs 269.09. Prices are ex-depot, nationwide.</p>
<h2>Petrol and diesel prices — recent days</h2>
<?php
// OmniWire's own verified record. DAILY BUILDER: prepend today's row here
// (date, petrol, diesel) — the table and chart below render from this array.
$history = array(
  array('5 Nov 2026', 362.00, 387.81, '5 Nov'),
  array('4 Nov 2026', 369.06, 391.52, '4 Nov'),
  array('3 Nov 2026', 374.51, 395.20, '3 Nov'),
  array('31 Oct 2026', 377.91, 402.96, '31 Oct'),
  array('30 Oct 2026', 377.91, 400.67, '30 Oct'),
  array('29 Oct 2026', 377.91, 399.82, '29 Oct'),
  array('28 Oct 2026', 376.06, 398.07, '28 Oct'),
  array('27 Oct 2026', 379.25, 395.82, '27 Oct'),
  array('24 Oct 2026', 377.71, 393.66, '24 Oct'),
  array('23 Oct 2026', 377.71, 391.75, '23 Oct'),
  array('22 Oct 2026', 379.31, 390.11, '22 Oct'),
  array('21 Oct 2026', 382.02, 389.80, '21 Oct'),
  array('20 Oct 2026', 381.14, 389.27, '20 Oct'),
  array('17–19 Oct 2026', 386.30, 396.96, '17–19 Oct'),
);
$chart = array_slice($history, 0, 10);
$vals = array();
foreach ($chart as $r) $vals[] = $r[1];
$vmin = min($vals); $vmax = max($vals); $span = max($vmax - $vmin, 1);
$pts = array();
$n = count($chart);
for ($i = 0; $i < $n; $i++) {
  $x = $n > 1 ? 50 + (610 - 50) * $i / ($n - 1) : 330;
  $y = 150 - (($chart[$n - 1 - $i][1] - $vmin) / $span) * 120;
  $pts[] = array($x, $y, $chart[$n - 1 - $i][1], $chart[$n - 1 - $i][3]);
}
?>
<svg viewBox="0 0 660 195" style="width:100%;max-width:660px;background:#fff;border:1px solid #e2e8f0;border-radius:8px" role="img" aria-label="Petrol price trend chart">
<polyline fill="none" stroke="#0e7a4f" stroke-width="2.5" points="<?php foreach ($pts as $p) echo round($p[0]) . ',' . round($p[1]) . ' '; ?>"/>
<?php foreach ($pts as $p): ?>
<circle cx="<?php echo round($p[0]); ?>" cy="<?php echo round($p[1]); ?>" r="4" fill="#0e7a4f"/>
<text x="<?php echo round($p[0]); ?>" y="<?php echo round($p[1]) - 10; ?>" text-anchor="middle" font-size="12" fill="#0f172a"><?php echo number_format($p[2], 2); ?></text>
<text x="<?php echo round($p[0]); ?>" y="178" text-anchor="middle" font-size="11" fill="#64748b"><?php echo htmlspecialchars($p[3]); ?></text>
<?php endforeach; ?>
</svg>
<table>
<tr><th>Effective date</th><th>Petrol per litre</th><th>Diesel per litre</th><th>Petrol change</th></tr>
<?php for ($i = 0; $i < count($history); $i++): $r = $history[$i]; $prev = isset($history[$i + 1]) ? $history[$i + 1][1] : null; ?>
<tr><td><?php echo htmlspecialchars($r[0]); ?></td><td>Rs <?php echo number_format($r[1], 2); ?></td><td>Rs <?php echo number_format($r[2], 2); ?></td><td><?php echo $prev === null ? '—' : (($r[1] - $prev) >= 0 ? '+ Rs ' . number_format($r[1] - $prev, 2) : '− Rs ' . number_format($prev - $r[1], 2)); ?></td></tr>
<?php endfor; ?>
</table>
<p class="stamp">Source: OGRA / Petroleum Division notifications, cross-checked across at least two independent national sources before publication. This is OmniWire&rsquo;s own record, kept since 17 October 2026.</p>
<h2>What changed in the latest notification?</h2>
<p>On <?php echo $effective_date; ?>, petrol fell by Rs 7.06, down from Rs <?php echo number_format($petrol_prev,2); ?> to Rs <?php echo number_format($petrol,2); ?> per litre, while high-speed diesel became cheaper by Rs 3.71, down from Rs <?php echo number_format($hsd_prev,2); ?> to Rs <?php echo number_format($hsd,2); ?>. Kerosene (Rs <?php echo number_format($kerosene,2); ?>) and light diesel oil (Rs <?php echo number_format($ldo,2); ?>) are unchanged. This follows the previous revision on 4 November, when petrol fell by Rs 5.45, from Rs 374.51 to Rs 369.06, and diesel fell by Rs 3.68, from Rs 395.20 to Rs 391.52. The Petroleum Division said the revision reflects movements in international petroleum prices, including Platts rates, premiums and other associated costs. Petrol is used mainly in cars, motorcycles and small vehicles, so its change is felt most directly by households; diesel powers freight, buses and agricultural machinery, so even small diesel moves can affect transport costs.</p>
<h2>How petrol prices are set in Pakistan</h2>
<p>Petrol and diesel prices in Pakistan are not set by individual pumps. The Oil and Gas Regulatory Authority (OGRA) works out the ex-depot price under the federal government&rsquo;s petroleum pricing mechanism, and the Petroleum Division issues the notification that brings the new rate into force. The calculation starts with the international price of refined petrol and diesel, quoted through Platts benchmarks in the Arab Gulf market, and adds the cost of bringing the fuel to Pakistan: premiums, freight and other incidentals.</p>
<p>On top of the import cost come the Inland Freight Equalisation Margin (IFEM), which keeps the pump price broadly the same in every city instead of cheaper near Karachi and dearer up-country, the margins of oil marketing companies and dealers, and government levies. The petroleum levy and climate support levy are fixed by the government and can change the final price even when world prices do not move. The rupee&ndash;dollar exchange rate also matters: a weaker rupee makes imported fuel more expensive in local terms.</p>
<p>For many years prices were revised every fifteen days, on the 1st and 16th of the month. That changed during the extreme volatility of 2026. Since July 2026, OGRA has revised petrol and diesel prices on working days under a daily mechanism that uses a seven-working-day rolling average of international prices, so local prices follow world markets more quickly. Daily adjustments are normally unchanged on Saturdays, Sundays and public holidays. That is why checking the effective date matters: a rate is only &ldquo;today&rsquo;s&rdquo; until the next notification replaces it. OmniWire publishes the notified figure only after it agrees across at least two independent published sources reporting the same OGRA / Petroleum Division notification, and states the effective date honestly on this page.</p>
<h2>Frequently asked questions</h2>
<div class="faq">
<p><strong>What is the petrol price in Pakistan today?</strong></p>
<p>Petrol (Motor Spirit) is Rs <?php echo number_format($petrol,2); ?> per litre, effective <?php echo $effective_date; ?>, as notified by OGRA and the Petroleum Division.</p>
<p><strong>What is the diesel price in Pakistan today?</strong></p>
<p>High-Speed Diesel (HSD) is Rs <?php echo number_format($hsd,2); ?> per litre, effective <?php echo $effective_date; ?>.</p>
<p><strong>When did the latest petrol price change take effect?</strong></p>
<p>The latest revision took effect on <?php echo $effective_date; ?>. Petrol fell by Rs 7.06 per litre to Rs 362.00 and diesel fell by Rs 3.71 per litre to Rs 387.81 compared with the previous notification.</p>
<p><strong>How often do petrol prices change in Pakistan?</strong></p>
<p>Since July 2026, OGRA revises petrol and diesel prices on working days under a daily pricing mechanism based on a seven-working-day rolling average of international prices. Prices are normally unchanged on weekends and public holidays.</p>
<p><strong>Is the petrol price the same in Karachi, Lahore and Islamabad?</strong></p>
<p>Yes. The OGRA-notified ex-depot price is a nationwide ceiling price and is the same across Pakistan, helped by the Inland Freight Equalisation Margin.</p>
<p><strong>What are the kerosene and light diesel prices?</strong></p>
<p>Kerosene oil is Rs <?php echo number_format($kerosene,2); ?> per litre and Light Diesel Oil is Rs <?php echo number_format($ldo,2); ?> per litre (both unchanged in the latest notification).</p>
</div>
<h2>More from OmniWire Media</h2>
<ul>
<li><a href="https://omniwiremedia.com/gold-price-in-pakistan-today">Gold price in Pakistan today</a> — the sister daily rate page.</li>
<li><a href="https://omniwiremedia.com/category/fintech-crypto">More Fintech &amp; Crypto coverage</a></li>
</ul>
<div class="note" style="margin-top:2em">Rates shown are the prices notified by the Oil and Gas Regulatory Authority (OGRA) and the Petroleum Division, compiled from published sources for information only. Actual pump prices may vary slightly by outlet, and OmniWire Media does not sell fuel and accepts no liability for any transaction made on the basis of these indicative rates. This page is not financial advice.</div>
</main>
<footer class="site">OmniWire Media &middot; <a href="https://omniwiremedia.com/editorial-policy">Editorial Policy</a> &middot; <a href="https://omniwiremedia.com/team">Our Team</a> &middot; Rates desk: OmniWire Editorial Desk</footer>
</body>
</html>
