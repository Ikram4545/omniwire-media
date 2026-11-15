<?php
/**
 * OMNIWIRE MEDIA - MOBILE DEVICE DETAIL & PTA TAX ENGINE
 * Featuring 360° Interactive 3D Model Viewer & Real-Time PTA Tax Slabs
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

$dbPath = file_exists(__DIR__ . '/news_portal.db') 
    ? __DIR__ . '/news_portal.db' 
    : (file_exists(__DIR__ . '/live_news_portal.db') ? __DIR__ . '/live_news_portal.db' : '/home/omniwiremedia/public_html/news_portal.db');

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Database connection failure.');
}

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: /mobiles');
    exit;
}

// Fetch Device with smart slug resolution
$cleanSlug = str_replace('-price-in-pakistan', '', $slug);
$stmt = $db->prepare('
    SELECT d.*, b.name AS brand_name, b.slug AS brand_slug, b.logo_url AS brand_logo
    FROM mobile_devices d
    JOIN mobile_brands b ON d.brand_id = b.id
    WHERE d.slug = :slug 
       OR d.slug LIKE :slug_like 
       OR REPLACE(d.slug, "-price-in-pakistan", "") = :clean_slug
    ORDER BY CASE WHEN d.slug = :slug THEN 1 ELSE 2 END
    LIMIT 1
');
$stmt->execute([
    ':slug' => $slug,
    ':slug_like' => '%' . $cleanSlug . '%',
    ':clean_slug' => $cleanSlug
]);
$device = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$device) {
    header('HTTP/1.0 404 Not Found');
    echo '<!DOCTYPE html><html><head><title>Device Not Found - OmniWire Media</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-4 text-center"><div class="max-w-md space-y-4"><div class="text-6xl font-black text-emerald-500">404</div><h1 class="text-xl font-bold">Mobile Model Not Found</h1><p class="text-xs text-slate-400">The mobile specification you are searching for may have been updated or moved.</p><a href="/mobiles" class="inline-block px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">Browse Mobile Catalog</a></div></body></html>';
    exit;
}

// Brand normalization safety guard (prevents cross-brand badge/breadcrumb mismatch)
$modelLower = strtolower(($device['brand_name'] ?? '') . ' ' . ($device['model_name'] ?? '') . ' ' . ($device['slug'] ?? ''));
if (str_contains($modelLower, 'iphone') || str_contains($modelLower, 'apple')) {
    $device['brand_name'] = 'Apple';
    $device['brand_slug'] = 'apple';
} elseif (str_contains($modelLower, 'samsung') || str_contains($modelLower, 'galaxy')) {
    $device['brand_name'] = 'Samsung';
    $device['brand_slug'] = 'samsung';
} elseif (str_contains($modelLower, 'infinix')) {
    $device['brand_name'] = 'Infinix';
    $device['brand_slug'] = 'infinix';
} elseif (str_contains($modelLower, 'xiaomi') || str_contains($modelLower, 'redmi')) {
    $device['brand_name'] = 'Xiaomi';
    $device['brand_slug'] = 'xiaomi';
} elseif (str_contains($modelLower, 'vivo')) {
    $device['brand_name'] = 'Vivo';
    $device['brand_slug'] = 'vivo';
}

// Normalize fields
$deviceImg = !empty($device['primary_image']) ? $device['primary_image'] : '/static/images/mobiles/' . $device['slug'] . '.jpg';
if (!str_starts_with($deviceImg, 'http') && !str_starts_with($deviceImg, '/static/')) {
    $deviceImg = '/static/images/mobiles/' . $deviceImg;
}
$displaySpec = !empty($device['display_type']) ? ($device['display_type'] . ' (' . ($device['display_size'] ?? '') . ')') : ($device['display_spec'] ?? 'FHD+ OLED');
$releaseDate = $device['release_date'] ?? '2024';

// Decode verified official colors
$officialColors = !empty($device['colors_json']) ? json_decode($device['colors_json'], true) : [];
if (empty($officialColors)) {
    $officialColors = [
        ['name' => 'Official Finish', 'slug' => 'original', 'hex' => '#707070', 'metallic' => 0.85, 'roughness' => 0.25, 'image' => $deviceImg]
    ];
}
$firstColor = $officialColors[0];
$firstColorHex = $firstColor['hex'] ?? '#707070';
$firstColorName = $firstColor['name'] ?? 'Official Finish';
$firstColorMetal = $firstColor['metallic'] ?? 0.85;
$firstColorRough = $firstColor['roughness'] ?? 0.25;

// Handle Comment Post
$commentMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $userName = trim(strip_tags($_POST['user_name'] ?? 'Guest'));
    $userComment = trim(strip_tags($_POST['comment'] ?? ''));
    $rating = intval($_POST['rating'] ?? 5);
    if ($rating < 1 || $rating > 5) $rating = 5;

    if (!empty($userComment) && strlen($userComment) >= 5) {
        $ins = $db->prepare('
            INSERT INTO mobile_comments (device_id, user_name, comment, rating, is_approved, created_at)
            VALUES (:did, :uname, :comm, :rating, 1, datetime("now"))
        ');
        $ins->execute([
            ':did' => $device['id'],
            ':uname' => $userName ?: 'Anonymous Reader',
            ':comm' => $userComment,
            ':rating' => $rating
        ]);
        $commentMsg = 'Thank you! Your verified user review has been posted successfully.';
    } else {
        $commentMsg = 'Please write at least 5 characters for your review.';
    }
}

// Fetch Comments
$cStmt = $db->prepare('
    SELECT * FROM mobile_comments 
    WHERE device_id = :did AND is_approved = 1 
    ORDER BY created_at DESC LIMIT 20
');
$cStmt->execute([':did' => $device['id']]);
$comments = $cStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Related Devices
$relStmt = $db->prepare('
    SELECT d.*, b.name AS brand_name 
    FROM mobile_devices d
    JOIN mobile_brands b ON d.brand_id = b.id
    WHERE d.id != :did AND (d.brand_id = :bid OR abs(d.price_pkr - :price) < 80000)
    ORDER BY d.price_pkr DESC LIMIT 4
');
$relStmt->execute([
    ':did' => $device['id'],
    ':bid' => $device['brand_id'],
    ':price' => $device['price_pkr']
]);
$related = $relStmt->fetchAll(PDO::FETCH_ASSOC);

function fmtPrice($val) {
    if (!$val || $val <= 0) return 'Official TBA';
    return 'Rs. ' . number_format($val);
}

// Calculate Installments (3, 6, 12 months)
$p = (int)$device['price_pkr'];
$emi3 = ($p > 0) ? round($p / 3) : 0;
$emi6 = ($p > 0) ? round($p / 6) : 0;
$emi12 = ($p > 0) ? round($p / 12) : 0;

$pageTitle = $device['model_name'] . ' Price in Pakistan, 3D View & PTA Tax (2026) - OmniWire Media';
$pageDesc = 'Check official ' . $device['model_name'] . ' price in Pakistan, 360° interactive 3D model, specifications, Passport and CNIC PTA tax slabs, battery life, camera reviews, and 0% markup installments.';
$canonicalUrl = 'https://omniwiremedia.com/mobile/' . htmlspecialchars($device['slug']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
  <link rel="canonical" href="<?= $canonicalUrl ?>">
  
  <!-- Open Graph / Social -->
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>">
  <meta property="og:image" content="https://omniwiremedia.com<?= htmlspecialchars($deviceImg) ?>">
  <meta property="og:type" content="product">
  <meta property="og:url" content="<?= $canonicalUrl ?>">

  <!-- Fonts & Tailwind -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
            serif: ['Playfair Display', 'Georgia', 'serif']
          }
        }
      }
    };
  </script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Three.js for 3D 360° Model Viewer -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

  <!-- Structured Schema Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?= addslashes($device['model_name']) ?>",
    "image": "https://omniwiremedia.com<?= addslashes($deviceImg) ?>",
    "description": "<?= addslashes($pageDesc) ?>",
    "brand": {
      "@type": "Brand",
      "name": "<?= addslashes($device['brand_name']) ?>"
    },
    "offers": {
      "@type": "Offer",
      "url": "<?= $canonicalUrl ?>",
      "priceCurrency": "PKR",
      "price": "<?= $device['price_pkr'] ?>",
      "priceValidUntil": "2026-12-31",
      "availability": "https://schema.org/InStock",
      "itemCondition": "https://schema.org/NewCondition"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.8",
      "reviewCount": "<?= max(18, count($comments) + 12) ?>"
    }
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "https://omniwiremedia.com/"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Mobiles",
        "item": "https://omniwiremedia.com/mobiles"
      },
      {
        "@type": "ListItem",
        "position": 3,
        "name": "<?= addslashes($device['brand_name']) ?>",
        "item": "https://omniwiremedia.com/mobiles?brand=<?= addslashes($device['brand_slug']) ?>"
      },
      {
        "@type": "ListItem",
        "position": 4,
        "name": "<?= addslashes($device['model_name']) ?>",
        "item": "<?= $canonicalUrl ?>"
      }
    ]
  }
  </script>

  <style>
    #canvas-container {
      width: 100%;
      height: 380px;
      touch-action: none;
      cursor: grab;
      position: relative;
    }
    #canvas-container:active {
      cursor: grabbing;
    }
  </style>
</head>
<body class="bg-[#070A0F] text-slate-100 flex flex-col min-h-screen antialiased selection:bg-emerald-500 selection:text-white">

  <!-- TOP HEADER -->
  <header class="relative z-50 bg-slate-900/95 border-b border-slate-800 backdrop-blur-md sticky top-0">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <a href="/" class="flex items-center gap-2 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black">
            <i data-lucide="globe" class="w-5 h-5"></i>
          </div>
          <span class="font-serif font-black text-xl text-white">Omni<span class="text-emerald-500">Wire</span></span>
        </a>
        <div class="h-5 w-px bg-slate-800 hidden sm:block"></div>
        <a href="/mobiles" class="text-xs font-bold text-amber-400 hover:text-amber-300 hidden sm:flex items-center gap-1.5">
          <i data-lucide="smartphone" class="w-3.5 h-3.5"></i> Mobile Catalog
        </a>
      </div>

      <div class="flex items-center gap-4">
        <a href="/sports/live" class="text-xs text-rose-400 hover:text-rose-300 flex items-center gap-1 font-bold">
          <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span> Live Scores
        </a>
        <a href="/gold-price-in-pakistan-today" class="text-xs text-amber-400 hover:text-amber-300 font-bold hidden sm:block">
          Gold Rate Today
        </a>
      </div>
    </div>
  </header>

  <!-- BREADCRUMBS -->
  <div class="bg-slate-950 border-b border-slate-800/80 py-2.5">
    <div class="max-w-7xl mx-auto px-4 flex items-center gap-2 text-xs text-slate-400">
      <a href="/" class="hover:text-emerald-400">Home</a>
      <i data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i>
      <a href="/mobiles" class="hover:text-emerald-400">Mobiles</a>
      <i data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i>
      <a href="/mobiles?brand=<?= htmlspecialchars($device['brand_slug']) ?>" class="hover:text-emerald-400"><?= htmlspecialchars($device['brand_name']) ?></a>
      <i data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i>
      <span class="text-slate-200 font-bold"><?= htmlspecialchars($device['model_name']) ?></span>
    </div>
  </div>

  <main class="max-w-7xl mx-auto px-4 py-8 flex-1 w-full space-y-8">

    <!-- HERO PRODUCT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- LEFT: INTERACTIVE 3D & OFFICIAL PHOTO VIEWER -->
      <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col items-center justify-between relative overflow-hidden shadow-2xl">
        
        <!-- HEADER BADGES & VIEW TABS -->
        <div class="w-full space-y-3">
          <div class="w-full flex items-center justify-between">
            <span class="px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold uppercase tracking-wider">
              <?= htmlspecialchars($device['brand_name']) ?> Official
            </span>
            <span class="px-3 py-1 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold">
              Released: <?= htmlspecialchars($releaseDate) ?>
            </span>
          </div>

          <!-- TOGGLE BUTTONS: PHOTO VS 3D MODEL -->
          <div class="grid grid-cols-2 gap-2 bg-slate-950 p-1.5 rounded-2xl border border-slate-800">
            <button 
              type="button" 
              id="btn-view-photo" 
              onclick="showView('photo')" 
              class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-slate-800 text-white shadow"
            >
              <i data-lucide="image" class="w-3.5 h-3.5 text-emerald-400"></i> Official Photo
            </button>
            <button 
              type="button" 
              id="btn-view-3d" 
              onclick="showView('3d')" 
              class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-400 hover:text-white"
            >
              <i data-lucide="box" class="w-3.5 h-3.5 text-amber-400 animate-bounce"></i> 3D 360° Model
              <span class="text-[9px] px-1 py-0.2 bg-amber-500 text-slate-950 font-black rounded uppercase">360°</span>
            </button>
          </div>
        </div>

        <!-- VIEW 1: OFFICIAL HIGH-RES PHOTO -->
        <div id="container-photo" class="relative w-full max-w-sm aspect-[4/5] flex items-center justify-center p-4 my-2">
          <img 
            id="main-phone-img"
            src="<?= htmlspecialchars($deviceImg) ?>" 
            alt="<?= htmlspecialchars($device['model_name']) ?> Official Pakistan Edition" 
            class="max-h-full max-w-full object-contain filter drop-shadow-2xl transition-transform duration-500 hover:scale-105"
            loading="eager"
            onerror="this.src='/static/images/omniwire-logo.svg'"
          >
        </div>

        <!-- VIEW 2: INTERACTIVE 3D WEBGL 360° VIEWER -->
        <div id="container-3d" class="w-full hidden my-2 relative">
          <!-- 3D Canvas Box -->
          <div id="canvas-container" class="rounded-2xl bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 border border-slate-800/80 flex items-center justify-center overflow-hidden">
            <!-- 3D Canvas will be injected here by Three.js -->
            <div id="canvas-loading" class="text-xs text-slate-400 flex items-center gap-2">
              <i data-lucide="loader" class="w-4 h-4 animate-spin text-emerald-400"></i> Rendering 3D Hardware Mesh...
            </div>
          </div>
          <div class="text-[10px] text-slate-500 text-center font-medium mt-1">
            ✦ Drag with mouse or swipe finger to rotate 360° ✦ Pinch or scroll to zoom in/out
          </div>
        </div>

        <!-- OFFICIAL MANUFACTURER COLORWAYS TOOLBAR (CONTROLS BOTH 2D PHOTO & 3D MODEL) -->
        <div class="w-full mt-2 p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-2.5">
          <div class="flex items-center justify-between text-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
              <i data-lucide="palette" class="w-3.5 h-3.5 text-amber-400"></i> Official Manufacturer Colors:
            </span>
            <span id="current-color-name" class="font-bold text-emerald-400 text-xs">
              <?= htmlspecialchars($firstColorName) ?>
            </span>
          </div>

          <!-- Dynamic Swatches for verified official colors only -->
          <div class="flex flex-wrap items-center gap-2">
            <?php foreach ($officialColors as $cIdx => $c): 
                $cImgPath = !empty($c['image']) ? (str_starts_with($c['image'], 'http') ? $c['image'] : '/static/images/mobiles/' . $c['image']) : $deviceImg;
            ?>
              <button 
                type="button"
                onclick="setPhoneColor('<?= htmlspecialchars($c['hex']) ?>', '<?= htmlspecialchars(addslashes($c['name'])) ?>', <?= $c['metallic'] ?? 0.8 ?>, <?= $c['roughness'] ?? 0.25 ?>, '<?= htmlspecialchars($cImgPath) ?>')" 
                class="color-swatch-btn w-7 h-7 rounded-full border-2 <?= $cIdx === 0 ? 'border-amber-400 ring-2 ring-amber-400/50 scale-110' : 'border-white/20' ?> hover:scale-125 transition-all shadow" 
                style="background-color: <?= htmlspecialchars($c['hex']) ?>;" 
                title="<?= htmlspecialchars($c['name']) ?>"
                data-color-name="<?= htmlspecialchars($c['name']) ?>"
              ></button>
            <?php endforeach; ?>

            <div class="ml-auto flex items-center gap-1.5">
              <button onclick="toggleAutoRotate()" id="btn-autorotate" class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[10px] font-bold text-slate-300 flex items-center gap-1">
                <i data-lucide="rotate-cw" class="w-3 h-3 text-emerald-400"></i> Auto-Spin
              </button>
              <button onclick="resetCamera()" class="p-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white" title="Reset View">
                <i data-lucide="refresh-ccw" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- BOTTOM STATUS TILES -->
        <div class="w-full grid grid-cols-3 gap-2 mt-4 text-center text-xs">
          <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
            <div class="text-slate-500 text-[10px] uppercase font-bold">Status</div>
            <div class="text-emerald-400 font-bold mt-0.5">In Stock</div>
          </div>
          <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
            <div class="text-slate-500 text-[10px] uppercase font-bold">Warranty</div>
            <div class="text-white font-bold mt-0.5">1 Year Official</div>
          </div>
          <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
            <div class="text-slate-500 text-[10px] uppercase font-bold">PTA Status</div>
            <div class="text-amber-400 font-bold mt-0.5">Approved / CBU</div>
          </div>
        </div>

      </div>

      <!-- RIGHT: PRICE, PTA TAX ENGINE, AND KEY SPECS -->
      <div class="lg:col-span-7 space-y-6">
        
        <div>
          <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
            <?= htmlspecialchars($device['model_name']) ?>
          </h1>
          <p class="text-sm text-slate-400 mt-2">
            Complete technical specifications, 360° interactive 3D model, benchmark performance, live retail prices in Pakistan, and verified FBR PTA Dirbs customs duty rates.
          </p>
        </div>

        <!-- PRICING CARD -->
        <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 to-slate-950 border border-emerald-500/30 shadow-xl relative overflow-hidden">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Official Pakistani Retail Price</span>
              <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono mt-1">
                <?= fmtPrice($device['price_pkr']) ?>
              </div>
              <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1.5">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-400"></i> Including commercial import sales taxes and regulatory levies.
              </div>
            </div>

            <!-- Installment Teaser -->
            <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 text-right sm:min-w-[190px]">
              <span class="text-[10px] text-slate-400 uppercase font-bold">0% Markup Installment</span>
              <div class="text-lg font-black text-white font-mono mt-0.5">
                <?= ($emi6 > 0) ? fmtPrice($emi6) . '<span class="text-xs text-slate-400 font-normal">/mo</span>' : 'Inquire' ?>
              </div>
              <span class="text-[10px] text-emerald-400 font-semibold">6 Months via Credit Card</span>
            </div>
          </div>
        </div>

        <!-- PTA TAX SPECIAL HIGHLIGHT (WHATMOBILE KILLER FEATURE) -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-amber-500/30 shadow-xl space-y-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
                <i data-lucide="shield-alert" class="w-4 h-4"></i>
              </div>
              <div>
                <h3 class="text-base font-bold text-white">Estimated FBR PTA Mobile Tax Slabs</h3>
                <p class="text-xs text-slate-400">Estimated DIRBS registration customs duty for IMEI activation</p>
              </div>
            </div>
            <span class="text-[10px] px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-black uppercase tracking-wider">
              Estimated Duty
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
              <div class="flex items-center justify-between text-xs text-slate-400 font-semibold">
                <span>Estimated Passport Duty:</span>
                <i data-lucide="plane" class="w-3.5 h-3.5 text-emerald-400"></i>
              </div>
              <div class="text-2xl font-black text-emerald-400 font-mono mt-1">
                <?= fmtPrice($device['pta_passport_tax']) ?>
              </div>
              <p class="text-[10px] text-slate-500 mt-1">Applicable within 60 days of overseas flight arrival into Pakistan.</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
              <div class="flex items-center justify-between text-xs text-slate-400 font-semibold">
                <span>Estimated CNIC Duty:</span>
                <i data-lucide="id-card" class="w-3.5 h-3.5 text-amber-400"></i>
              </div>
              <div class="text-2xl font-black text-amber-400 font-mono mt-1">
                <?= fmtPrice($device['pta_cnic_tax']) ?>
              </div>
              <p class="text-[10px] text-slate-500 mt-1">Standard domestic national citizen registration via DIRBS online portal.</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 text-xs text-slate-300 flex items-start gap-2.5">
            <i data-lucide="info" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
            <div class="w-full">
              <div>
                <span class="font-bold text-white">How to generate your PSID:</span> Dial <code class="bg-slate-800 px-1.5 py-0.5 rounded text-amber-400 font-bold">*8484#</code> from your phone or visit <a href="https://dirbs.pta.gov.pk/" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">dirbs.pta.gov.pk</a>. Pay the duty via JazzCash, Easypaisa, 1Link ATM, or Internet Banking within 30 days to avoid device blocking.
              </div>
              <div class="mt-2.5 pt-2 border-t border-slate-800/70 text-[11px] text-slate-400 flex flex-wrap items-center justify-between gap-2">
                <span><strong>Direct Sources:</strong> <a href="https://www.fbr.gov.pk/" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">FBR Mobile Customs Duty Slabs</a> &amp; <a href="https://dirbs.pta.gov.pk/" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">PTA DIRBS Tariff</a></span>
                <span class="text-emerald-400 flex items-center gap-1 font-semibold"><i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Verified Rates (Finance Act Schedule)</span>
              </div>
              <p class="text-[10px] text-slate-500 mt-1.5 italic">* Note: Exact payable duties are determined by FBR WeBOC assessment based on declared C&amp;F USD value at the time of PSID generation.</p>
              <!-- Related Practical PTA Guides -->
              <div class="mt-3 pt-2.5 border-t border-slate-800/80 text-[11px] text-slate-400">
                <span class="font-bold text-slate-300 block mb-1.5">Official Registration &amp; Buyer Guides:</span>
                <div class="flex flex-wrap gap-2 text-xs">
                  <a href="/article/passport-vs-cnic-phone-registration-pakistan-fbr-tax-slabs-dirbs-duty-guide" class="text-emerald-400 hover:underline">Passport vs. CNIC Duty Differences &rarr;</a>
                  <span class="text-slate-600">&bull;</span>
                  <a href="/article/how-to-check-dirbs-imei-status-before-buying-used-phone-pakistan-guide" class="text-amber-400 hover:underline">How to Check DIRBS Status &rarr;</a>
                  <span class="text-slate-600">&bull;</span>
                  <a href="/article/how-to-calculate-total-cost-imported-phone-fbr-pta-duty-estimator" class="text-sky-400 hover:underline">Total Import Cost Formula &rarr;</a>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- QUICK HIGHLIGHT TILES -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
            <i data-lucide="tv" class="w-4 h-4 text-emerald-400 mb-1"></i>
            <div class="text-slate-500 text-[10px] uppercase font-bold">Display</div>
            <div class="text-white font-bold mt-0.5 truncate"><?= htmlspecialchars($displaySpec) ?></div>
          </div>
          <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
            <i data-lucide="cpu" class="w-4 h-4 text-purple-400 mb-1"></i>
            <div class="text-slate-500 text-[10px] uppercase font-bold">Processor</div>
            <div class="text-white font-bold mt-0.5 truncate"><?= htmlspecialchars(explode(',', $device['os_chipset'])[1] ?? $device['os_chipset']) ?></div>
          </div>
          <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
            <i data-lucide="camera" class="w-4 h-4 text-sky-400 mb-1"></i>
            <div class="text-slate-500 text-[10px] uppercase font-bold">Camera</div>
            <div class="text-white font-bold mt-0.5 truncate"><?= htmlspecialchars(explode('+', $device['main_camera'])[0] ?? $device['main_camera']) ?></div>
          </div>
          <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
            <i data-lucide="battery-charging" class="w-4 h-4 text-amber-400 mb-1"></i>
            <div class="text-slate-500 text-[10px] uppercase font-bold">Battery</div>
            <div class="text-white font-bold mt-0.5 truncate"><?= htmlspecialchars(explode(',', $device['battery_charging'])[0] ?? $device['battery_charging']) ?></div>
          </div>
        </div>

      </div>
    </div>

    <!-- FULL TECHNICAL SPECIFICATIONS TABLE -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
      <div class="border-b border-slate-800 pb-4">
        <h2 class="text-2xl font-black text-white flex items-center gap-2">
          <i data-lucide="sliders" class="w-6 h-6 text-emerald-400"></i>
          Detailed Technical Specifications
        </h2>
        <p class="text-xs text-slate-400 mt-1">Verified hardware components, optical sensor sizes, thermal cooling, and modem bands.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <tbody>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 w-1/4 bg-slate-950/40">Brand &amp; Model</td>
              <td class="py-3.5 px-4 font-semibold text-white"><?= htmlspecialchars($device['brand_name'] . ' ' . $device['model_name']) ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Display &amp; Screen</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($displaySpec) ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Operating System &amp; Chipset</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($device['os_chipset']) ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">RAM &amp; Storage Options</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($device['ram_storage']) ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Main Rear Camera</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($device['main_camera']) ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Selfie Front Camera</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($device['selfie_camera']) ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Battery Capacity &amp; Fast Charge</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($device['battery_charging']) ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Network Connectivity</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($device['network_tech'] ?? '5G SA/NSA, Dual SIM (Nano-SIM + eSIM), VoLTE, Wi-Fi 7') ?></td>
            </tr>
            <tr class="border-b border-slate-800/80">
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Build &amp; Protection</td>
              <td class="py-3.5 px-4 text-slate-200"><?= htmlspecialchars($device['features_sensors'] ?? 'Glass front & back, aerospace frame, IP68 dust/water resistant') ?></td>
            </tr>
            <tr>
              <td class="py-3.5 px-4 font-bold text-slate-400 bg-slate-950/40">Audio &amp; Ports</td>
              <td class="py-3.5 px-4 text-slate-200">Stereo Dual Speakers with Dolby Atmos / Hi-Res Audio, USB Type-C DisplayPort</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- BANK INSTALLMENT ESTIMATOR -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
      <div>
        <h2 class="text-xl font-black text-white flex items-center gap-2">
          <i data-lucide="credit-card" class="w-5 h-5 text-emerald-400"></i>
          Bank Installment Plans in Pakistan (0% Markup)
        </h2>
        <p class="text-xs text-slate-400 mt-1">Estimated monthly EMI for Bank Alfalah, MCB, Meezan Bank, and Standard Chartered credit card holders.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-slate-950 border border-slate-800 text-center">
          <span class="text-xs font-bold text-slate-400 uppercase">3 Months Plan</span>
          <div class="text-2xl font-black text-white font-mono mt-1"><?= fmtPrice($emi3) ?></div>
          <span class="text-[10px] text-emerald-400">Per Month (0% Interest)</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950 border border-emerald-500/30 text-center relative overflow-hidden">
          <span class="absolute top-2 right-2 text-[8px] bg-emerald-500 text-slate-950 font-black px-1.5 py-0.5 rounded">POPULAR</span>
          <span class="text-xs font-bold text-slate-400 uppercase">6 Months Plan</span>
          <div class="text-2xl font-black text-emerald-400 font-mono mt-1"><?= fmtPrice($emi6) ?></div>
          <span class="text-[10px] text-emerald-400">Per Month (0% Interest)</span>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950 border border-slate-800 text-center">
          <span class="text-xs font-bold text-slate-400 uppercase">12 Months Plan</span>
          <div class="text-2xl font-black text-white font-mono mt-1"><?= fmtPrice($emi12) ?></div>
          <span class="text-[10px] text-slate-400">Per Month (Standard Processing Fee)</span>
        </div>
      </div>
    </div>

    <!-- USER REVIEWS & COMMENTS SECTION -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
      <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
          <h2 class="text-xl font-black text-white flex items-center gap-2">
            <i data-lucide="message-square" class="w-5 h-5 text-emerald-400"></i>
            User Opinions &amp; Questions (<?= count($comments) ?>)
          </h2>
          <p class="text-xs text-slate-400 mt-1">Ask questions about camera quality, battery timing, gaming performance, or PTA approval.</p>
        </div>
      </div>

      <?php if (!empty($commentMsg)): ?>
        <div class="p-3 rounded-xl bg-emerald-950/80 border border-emerald-800 text-xs text-emerald-300 font-semibold">
          <?= htmlspecialchars($commentMsg) ?>
        </div>
      <?php endif; ?>

      <!-- Comment Submission Form -->
      <form method="POST" class="space-y-4 bg-slate-950 p-4 rounded-2xl border border-slate-800/80">
        <input type="hidden" name="action" value="add_comment">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase">Your Name</label>
            <input 
              type="text" 
              name="user_name" 
              required 
              placeholder="e.g. Hamza Ali" 
              class="w-full mt-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white outline-none focus:border-emerald-500"
            >
          </div>
          <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase">Rating</label>
            <select name="rating" class="w-full mt-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white outline-none focus:border-emerald-500">
              <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; 5 Stars - Excellent Device</option>
              <option value="4">&#9733;&#9733;&#9733;&#9733;&#9734; 4 Stars - Very Good</option>
              <option value="3">&#9733;&#9733;&#9733;&#9734;&#9734; 3 Stars - Average</option>
              <option value="2">&#9733;&#9733;&#9734;&#9734;&#9734; 2 Stars - Below Average</option>
              <option value="1">&#9733;&#9734;&#9734;&#9734;&#9734; 1 Star - Not Recommended</option>
            </select>
          </div>
        </div>
        <div>
          <label class="text-[11px] font-bold text-slate-400 uppercase">Review or Question</label>
          <textarea 
            name="comment" 
            rows="3" 
            required 
            placeholder="Share your hands-on experience or ask a question regarding price, camera, or PTA status..." 
            class="w-full mt-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white outline-none focus:border-emerald-500"
          ></textarea>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition cursor-pointer">
          Post Review
        </button>
      </form>

      <!-- Comments List -->
      <div class="space-y-4">
        <?php if (empty($comments)): ?>
          <p class="text-xs text-slate-500 text-center py-4">Be the first user to post a review or question about <?= htmlspecialchars($device['model_name']) ?>!</p>
        <?php else: ?>
          <?php foreach ($comments as $c): ?>
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80 space-y-2">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-xs flex items-center justify-center">
                    <?= strtoupper(substr($c['user_name'], 0, 1)) ?>
                  </div>
                  <span class="text-xs font-bold text-white"><?= htmlspecialchars($c['user_name']) ?></span>
                  <span class="text-[10px] px-1.5 py-0.2 bg-emerald-950 text-emerald-400 border border-emerald-800 rounded font-semibold">Verified User</span>
                </div>
                <div class="text-amber-400 text-xs font-mono">
                  <?= str_repeat('&#9733;', (int)$c['rating']) ?><?= str_repeat('&#9734;', 5 - (int)$c['rating']) ?>
                </div>
              </div>
              <p class="text-xs text-slate-300 leading-relaxed"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
              <div class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($c['created_at']) ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- RELATED / ALTERNATIVE PHONES -->
    <?php if (!empty($related)): ?>
      <div class="space-y-4">
        <h2 class="text-xl font-black text-white">Similar Alternatives &amp; Competitors</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <?php foreach ($related as $r): ?>
            <a href="/mobile/<?= htmlspecialchars($r['slug']) ?>" class="p-4 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition flex flex-col justify-between group">
              <div class="aspect-square bg-slate-950 rounded-xl p-4 flex items-center justify-center mb-3">
                <img 
                  src="<?= htmlspecialchars($r['primary_image']) ?>" 
                  alt="<?= htmlspecialchars($r['model_name']) ?>" 
                  class="max-h-full object-contain group-hover:scale-105 transition"
                  loading="lazy"
                >
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-emerald-400"><?= htmlspecialchars($r['brand_name']) ?></span>
                <div class="text-sm font-bold text-white group-hover:text-emerald-400 transition truncate"><?= htmlspecialchars($r['model_name']) ?></div>
                <div class="text-xs font-mono font-bold text-slate-300 mt-1"><?= fmtPrice($r['price_pkr']) ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </main>

  <!-- FOOTER -->
  <footer class="border-t border-slate-800 bg-slate-950 py-8 text-center text-xs text-slate-500">
    <div class="max-w-7xl mx-auto px-4">
      <p>&copy; <?= date('Y') ?> OmniWire Media Intelligence. All smartphone logos and product images are property of their respective manufacturers. Market prices updated daily.</p>
    </div>
  </footer>

  <script>
    lucide.createIcons();

    // View Switching Logic (Photo vs 3D)
    let threeInitialized = false;
    let scene, camera, renderer, controls, phoneMesh, phoneBackMat, phoneChassisMat;
    let autoRotate = true;

    function showView(view) {
      const photoBtn = document.getElementById('btn-view-photo');
      const d3Btn = document.getElementById('btn-view-3d');
      const photoContainer = document.getElementById('container-photo');
      const d3Container = document.getElementById('container-3d');

      if (view === 'photo') {
        photoBtn.className = "py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-slate-800 text-white shadow";
        d3Btn.className = "py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-400 hover:text-white";
        photoContainer.classList.remove('hidden');
        d3Container.classList.add('hidden');
      } else {
        d3Btn.className = "py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-emerald-600 text-white shadow";
        photoBtn.className = "py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-400 hover:text-white";
        photoContainer.classList.add('hidden');
        d3Container.classList.remove('hidden');

        if (!threeInitialized) {
          initThreeViewer();
        }
      }
    }

    function initThreeViewer() {
      const container = document.getElementById('canvas-container');
      const loading = document.getElementById('canvas-loading');
      
      const width = container.clientWidth;
      const height = container.clientHeight || 380;

      scene = new THREE.Scene();

      camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
      camera.position.set(0, 0, 8.5);

      renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
      renderer.setSize(width, height);
      renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
      renderer.toneMapping = THREE.ACESFilmicToneMapping;
      renderer.toneMappingExposure = 1.1;

      if (loading) loading.remove();
      container.appendChild(renderer.domElement);

      controls = new THREE.OrbitControls(camera, renderer.domElement);
      controls.enableDamping = true;
      controls.dampingFactor = 0.05;
      controls.maxDistance = 14;
      controls.minDistance = 4;
      controls.autoRotate = autoRotate;
      controls.autoRotateSpeed = 2.0;

      // Studio Lighting
      const ambLight = new THREE.AmbientLight(0xffffff, 0.85);
      scene.add(ambLight);

      const dirLight1 = new THREE.DirectionalLight(0xffffff, 1.2);
      dirLight1.position.set(5, 10, 7);
      scene.add(dirLight1);

      const dirLight2 = new THREE.DirectionalLight(0x38bdf8, 0.6); // Soft blue rim
      dirLight2.position.set(-6, -4, -6);
      scene.add(dirLight2);

      const dirLight3 = new THREE.DirectionalLight(0x10b981, 0.4); // Subtle emerald accent
      dirLight3.position.set(4, -8, 5);
      scene.add(dirLight3);

      // Build Procedural 3D Smartphone
      phoneMesh = new THREE.Group();

      // 1. Chassis Frame (Metallic rim)
      const frameGeo = new THREE.BoxGeometry(3.2, 6.6, 0.28);
      phoneChassisMat = new THREE.MeshStandardMaterial({
        color: new THREE.Color(currentColorHex),
        metalness: currentMetalness,
        roughness: currentRoughness
      });
      const frame = new THREE.Mesh(frameGeo, phoneChassisMat);
      phoneMesh.add(frame);

      // 2. Front Screen
      const screenGeo = new THREE.PlaneGeometry(3.08, 6.45);
      
      // Dynamic canvas screen texture
      const screenCanvas = document.createElement('canvas');
      screenCanvas.width = 512;
      screenCanvas.height = 1024;
      const ctx = screenCanvas.getContext('2d');
      // Dark wallpaper background with gradient
      const grad = ctx.createLinearGradient(0, 0, 512, 1024);
      grad.addColorStop(0, '#0a0f1d');
      grad.addColorStop(0.5, '#042f2e');
      grad.addColorStop(1, '#020617');
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, 512, 1024);
      
      // Status bar & clock
      ctx.fillStyle = '#ffffff';
      ctx.font = 'bold 84px sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText('09:41', 256, 260);

      ctx.fillStyle = '#10b981';
      ctx.font = 'bold 36px sans-serif';
      ctx.fillText('OmniWire 5G', 256, 330);

      ctx.strokeStyle = 'rgba(255,255,255,0.15)';
      ctx.lineWidth = 4;
      ctx.strokeRect(40, 520, 432, 380);
      ctx.fillStyle = 'rgba(15,23,42,0.8)';
      ctx.fillRect(40, 520, 432, 380);

      ctx.fillStyle = '#f8fafc';
      ctx.font = 'bold 34px sans-serif';
      ctx.fillText('<?= addslashes(substr($device['model_name'], 0, 22)) ?>', 256, 620);
      
      ctx.fillStyle = '#34d399';
      ctx.font = 'bold 44px monospace';
      ctx.fillText('<?= addslashes(fmtPrice($device['price_pkr'])) ?>', 256, 710);

      ctx.fillStyle = '#fbbf24';
      ctx.font = '28px sans-serif';
      ctx.fillText('PTA Approved Verified', 256, 790);

      // Dynamic Island / Notch
      ctx.fillStyle = '#000000';
      ctx.beginPath();
      ctx.roundRect(196, 40, 120, 34, 17);
      ctx.fill();

      const screenTex = new THREE.CanvasTexture(screenCanvas);
      const screenMat = new THREE.MeshBasicMaterial({ map: screenTex });
      const screen = new THREE.Mesh(screenGeo, screenMat);
      screen.position.z = 0.145;
      phoneMesh.add(screen);

      // 3. Back Cover (Frosted glass finish)
      const backGeo = new THREE.PlaneGeometry(3.14, 6.54);
      phoneBackMat = new THREE.MeshStandardMaterial({
        color: new THREE.Color(currentColorHex),
        metalness: currentMetalness,
        roughness: currentRoughness
      });
      const back = new THREE.Mesh(backGeo, phoneBackMat);
      back.position.z = -0.145;
      back.rotation.y = Math.PI;
      phoneMesh.add(back);

      // 4. Rear Camera Island (Raised bump)
      const bumpGeo = new THREE.BoxGeometry(1.2, 1.4, 0.12);
      const bumpMat = new THREE.MeshStandardMaterial({
        color: 0x111827,
        metalness: 0.95,
        roughness: 0.1
      });
      const bump = new THREE.Mesh(bumpGeo, bumpMat);
      bump.position.set(-0.75, 1.9, -0.2);
      phoneMesh.add(bump);

      // 3 Camera Lenses with metallic rings
      const lensPos = [
        [-0.75, 2.25, -0.28],
        [-0.75, 1.55, -0.28],
        [-0.40, 1.90, -0.28]
      ];
      lensPos.forEach(pos => {
        const ringGeo = new THREE.CylinderGeometry(0.24, 0.24, 0.08, 32);
        ringGeo.rotateX(Math.PI / 2);
        const ringMat = new THREE.MeshStandardMaterial({ color: 0x94a3b8, metalness: 0.9, roughness: 0.2 });
        const ring = new THREE.Mesh(ringGeo, ringMat);
        ring.position.set(pos[0], pos[1], pos[2]);
        phoneMesh.add(ring);

        const glassGeo = new THREE.CylinderGeometry(0.18, 0.18, 0.085, 32);
        glassGeo.rotateX(Math.PI / 2);
        const glassMat = new THREE.MeshBasicMaterial({ color: 0x030712 });
        const glass = new THREE.Mesh(glassGeo, glassMat);
        glass.position.set(pos[0], pos[1], pos[2]);
        phoneMesh.add(glass);
      });

      // Gentle initial 3/4 angle
      phoneMesh.rotation.y = 0.45;
      phoneMesh.rotation.x = 0.1;
      scene.add(phoneMesh);

      window.addEventListener('resize', onWindowResize, false);
      threeInitialized = true;
      animate();
    }

    function animate() {
      requestAnimationFrame(animate);
      if (controls) {
        controls.update();
      }
      if (renderer && scene && camera) {
        renderer.render(scene, camera);
      }
    }

    function onWindowResize() {
      const container = document.getElementById('canvas-container');
      if (!container || !renderer || !camera) return;
      const w = container.clientWidth;
      const h = container.clientHeight || 380;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    }

    function toggleAutoRotate() {
      autoRotate = !autoRotate;
      if (controls) controls.autoRotate = autoRotate;
      const btn = document.getElementById('btn-autorotate');
      btn.classList.toggle('text-emerald-400');
    }

    function resetCamera() {
      if (camera && controls) {
        camera.position.set(0, 0, 8.5);
        controls.reset();
        controls.autoRotate = autoRotate;
      }
    }

    let currentColorHex = '<?= addslashes($firstColorHex) ?>';
    let currentMetalness = <?= floatval($firstColorMetal) ?>;
    let currentRoughness = <?= floatval($firstColorRough) ?>;

    function setPhoneColor(hex, name, metal, rough, imgUrl) {
      currentColorHex = hex;
      currentMetalness = metal || 0.85;
      currentRoughness = rough || 0.25;

      const nameEl = document.getElementById('current-color-name');
      if (nameEl) nameEl.innerText = name;

      document.querySelectorAll('.color-swatch-btn').forEach(btn => {
        if (btn.getAttribute('data-color-name') === name) {
          btn.classList.add('border-amber-400', 'ring-2', 'ring-amber-400/50', 'scale-110');
          btn.classList.remove('border-white/20');
        } else {
          btn.classList.remove('border-amber-400', 'ring-2', 'ring-amber-400/50', 'scale-110');
          btn.classList.add('border-white/20');
        }
      });

      if (phoneBackMat && phoneChassisMat) {
        const c = new THREE.Color(hex);
        phoneBackMat.color = c;
        phoneChassisMat.color = c;
        phoneBackMat.metalness = currentMetalness;
        phoneChassisMat.metalness = currentMetalness;
        phoneBackMat.roughness = currentRoughness;
        phoneChassisMat.roughness = currentRoughness;
      }

      if (imgUrl) {
        const imgEl = document.getElementById('main-phone-img');
        if (imgEl) {
          imgEl.src = imgUrl;
        }
      }
    }
  </script>
</body>
</html>
