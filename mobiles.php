<?php
/**
 * OMNIWIRE MEDIA - MOBILE SPECIFICATIONS CATALOG HUB (v2.0)
 * Live Smartphone Database with Verified PTA Tax Slabs & 3D Model Previews
 */
$dbPath = file_exists(__DIR__ . '/news_portal.db') 
    ? __DIR__ . '/news_portal.db' 
    : (file_exists(__DIR__ . '/live_news_portal.db') ? __DIR__ . '/live_news_portal.db' : '/home/omniwiremedia/public_html/news_portal.db');

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Database connection failure.');
}

// Fetch all devices
$devices = $db->query("
    SELECT d.*, b.name as brand_name, b.slug as brand_slug 
    FROM mobile_devices d 
    JOIN mobile_brands b ON d.brand_id = b.id 
    ORDER BY d.price_pkr DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Brand normalization guard
foreach ($devices as &$dev) {
    $mLower = strtolower(($dev['brand_name'] ?? '') . ' ' . ($dev['model_name'] ?? '') . ' ' . ($dev['slug'] ?? ''));
    if (str_contains($mLower, 'iphone') || str_contains($mLower, 'apple')) {
        $dev['brand_name'] = 'Apple';
        $dev['brand_slug'] = 'apple';
    } elseif (str_contains($mLower, 'samsung') || str_contains($mLower, 'galaxy')) {
        $dev['brand_name'] = 'Samsung';
        $dev['brand_slug'] = 'samsung';
    } elseif (str_contains($mLower, 'infinix')) {
        $dev['brand_name'] = 'Infinix';
        $dev['brand_slug'] = 'infinix';
    } elseif (str_contains($mLower, 'xiaomi') || str_contains($mLower, 'redmi')) {
        $dev['brand_name'] = 'Xiaomi';
        $dev['brand_slug'] = 'xiaomi';
    } elseif (str_contains($mLower, 'vivo')) {
        $dev['brand_name'] = 'Vivo';
        $dev['brand_slug'] = 'vivo';
    }
}
unset($dev);

$totalDevices = count($devices);

// Fetch brands with dynamic counts
$brands = $db->query("
    SELECT b.*, COUNT(d.id) AS device_count 
    FROM mobile_brands b 
    LEFT JOIN mobile_devices d ON d.brand_id = b.id 
    WHERE b.is_active = 1 
    GROUP BY b.id 
    ORDER BY device_count DESC, b.name ASC
")->fetchAll(PDO::FETCH_ASSOC);

function fmtPrice($n) { 
    if (!$n || $n <= 0) return 'Official TBA';
    return 'Rs. ' . number_format((float)$n); 
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mobile Prices in Pakistan 2026 | <?= $totalDevices ?>+ Phones, 3D View & PTA Tax — OmniWire Media</title>
  <meta name="description" content="Explore complete official mobile prices in Pakistan for 2026. Compare <?= $totalDevices ?>+ Samsung, Apple iPhone, Xiaomi, Vivo, Oppo, Infinix, Tecno, Realme phones with 360° 3D models and exact PTA tax on Passport vs CNIC.">
  <link rel="canonical" href="https://omniwiremedia.com/mobiles">
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
</head>
<body class="bg-[#070A0F] text-slate-100 min-h-screen flex flex-col font-sans antialiased selection:bg-emerald-500 selection:text-white">

  <!-- TOP HEADER -->
  <header class="bg-slate-900/90 border-b border-slate-800 sticky top-0 z-50 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
      <a href="/" class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center font-bold text-slate-950">
          <i data-lucide="smartphone" class="w-5 h-5"></i>
        </div>
        <span class="font-serif font-black text-xl text-white">Omni<span class="text-emerald-500">Mobiles</span></span>
      </a>
      
      <!-- SEARCH BAR -->
      <div class="flex-1 max-w-md hidden sm:block">
        <div class="relative">
          <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input 
            type="text" 
            id="mobile-search-input"
            placeholder="Search <?= $totalDevices ?>+ phones (iPhone 16, S24 Ultra, Redmi, Camon 30, GT 20)..." 
            class="w-full pl-10 pr-4 py-2 rounded-xl text-xs bg-slate-800 border border-slate-700/80 focus:border-emerald-500 text-white placeholder-slate-400 outline-none transition"
            onkeyup="applyFilters()"
          >
        </div>
      </div>

      <div class="flex items-center gap-3">
        <a href="/sports/live" class="text-xs text-rose-400 hover:text-rose-300 flex items-center gap-1 font-bold">
          <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span> Live Scores
        </a>
        <a href="/gold-price-in-pakistan-today" class="text-xs text-amber-400 hover:text-amber-300 font-bold hidden md:block">Gold Rate</a>
      </div>
    </div>
  </header>

  <!-- BRAND LOGO TAPE (FILTER BUTTONS) -->
  <div class="bg-slate-950 border-b border-slate-800/80 overflow-x-auto scrollbar-none py-3 sticky top-16 z-40 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 flex items-center gap-2">
      <button onclick="setBrandFilter('all')" id="brand-btn-all" class="brand-btn active px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap">
        <span>All Brands</span>
        <span class="px-1.5 py-0.2 rounded-md bg-emerald-800 text-[10px]"><?= count($devices) ?></span>
      </button>
      <?php foreach ($brands as $b): ?>
        <button onclick="setBrandFilter('<?= htmlspecialchars($b['slug']) ?>')" id="brand-btn-<?= htmlspecialchars($b['slug']) ?>" class="brand-btn px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-emerald-500/40 text-xs font-bold text-slate-300 transition flex items-center gap-1.5 whitespace-nowrap">
          <span><?= htmlspecialchars($b['name']) ?></span>
          <span class="px-1.5 py-0.2 rounded-md bg-slate-800 text-[10px] text-slate-400 font-mono"><?= $b['device_count'] ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- MAIN CATALOG -->
  <main class="max-w-7xl mx-auto px-4 py-8 flex-1 w-full">
    
    <!-- HEADER BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-800/80 gap-4 mb-8">
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2">
          Latest Mobile Prices in Pakistan 2026
          <span class="text-xs px-2.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-mono font-bold">
            <?= count($devices) ?> Models Live
          </span>
        </h1>
        <p class="text-xs text-slate-400 mt-1">Official retail prices, 360° interactive 3D model viewer, technical specs, and estimated <a href="https://www.fbr.gov.pk/" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">FBR</a> &amp; <a href="https://dirbs.pta.gov.pk/" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">PTA DIRBS</a> customs duty slabs <span class="text-emerald-400 font-semibold">&bull; Verified Rates (Finance Act Schedule)</span>.</p>
      </div>

      <!-- FILTER CONTROLS -->
      <div class="flex flex-wrap items-center gap-3 text-xs">
        <!-- Price Range Filter -->
        <div class="flex items-center gap-1.5">
          <span class="text-slate-400">Price:</span>
          <select id="price-filter" onchange="applyFilters()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-200 outline-none focus:border-emerald-500">
            <option value="all">All Prices</option>
            <option value="under35k">Under Rs. 35,000</option>
            <option value="35k-75k">Rs. 35,000 - 75,000</option>
            <option value="75k-150k">Rs. 75,000 - 150,000</option>
            <option value="150k-250k">Rs. 150,000 - 250,000</option>
            <option value="flagship">Rs. 250,000+ Flagship</option>
          </select>
        </div>

        <!-- Sort Filter -->
        <div class="flex items-center gap-1.5">
          <span class="text-slate-400">Sort:</span>
          <select id="sort-filter" onchange="applySort()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-200 outline-none focus:border-emerald-500">
            <option value="price-desc">Price: High to Low</option>
            <option value="price-asc">Price: Low to High</option>
          </select>
        </div>
      </div>
    </div>

    <!-- MOBILE DEVICE GRID (68 PHONES) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="mobile-grid">
      <?php foreach ($devices as $d): ?>
        <div 
          class="mobile-card rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition shadow-lg overflow-hidden flex flex-col justify-between group" 
          data-title="<?= strtolower($d['brand_name'] . ' ' . $d['model_name']) ?>"
          data-brand="<?= htmlspecialchars($d['brand_slug']) ?>"
          data-price="<?= (int)$d['price_pkr'] ?>"
        >
          <div>
            <!-- Image Area -->
            <div class="p-6 bg-slate-950 flex items-center justify-center relative overflow-hidden aspect-[4/3]">
              <span class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-emerald-950/80 border border-emerald-800/60 text-emerald-400 text-[10px] font-bold uppercase tracking-wider">
                <?= htmlspecialchars($d['brand_name']) ?>
              </span>

              <!-- 3D Available Badge -->
              <span class="absolute top-3 right-3 px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[9px] font-black uppercase flex items-center gap-1">
                <i data-lucide="box" class="w-3 h-3"></i> 3D 360°
              </span>

              <?php $cardImg = (str_starts_with($d['primary_image'], 'http') || str_starts_with($d['primary_image'], '/static/')) ? $d['primary_image'] : '/static/images/mobiles/' . $d['primary_image']; ?>
              <img 
                src="<?= htmlspecialchars($cardImg) ?>" 
                alt="<?= htmlspecialchars($d['model_name']) ?> price in Pakistan"
                class="max-h-40 max-w-full object-contain group-hover:scale-105 transition duration-300"
                loading="lazy"
                onerror="this.src='/static/images/omniwire-logo.svg'"
              >
            </div>

            <!-- Details Area -->
            <div class="p-5 space-y-3">
              <div>
                <h3 class="font-bold text-base text-white group-hover:text-emerald-400 transition truncate">
                  <a href="/mobile/<?= htmlspecialchars($d['slug']) ?>">
                    <?= htmlspecialchars($d['model_name']) ?>
                  </a>
                </h3>
                <div class="text-lg font-black text-emerald-400 mt-0.5 font-mono">
                  <?= fmtPrice($d['price_pkr']) ?>
                </div>
              </div>

              <!-- PTA TAX CARD (EXCLUSIVE FEATURE) -->
              <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800/80 text-[11px] space-y-1">
                <div class="flex items-center justify-between text-slate-300 font-semibold">
                  <span class="text-slate-400">Est. Passport Duty:</span>
                  <span class="text-emerald-400 font-mono font-bold"><?= fmtPrice($d['pta_passport_tax']) ?></span>
                </div>
                <div class="flex items-center justify-between text-slate-300 font-semibold">
                  <span class="text-slate-400">Est. CNIC Duty:</span>
                  <span class="text-amber-400 font-mono font-bold"><?= fmtPrice($d['pta_cnic_tax']) ?></span>
                </div>
              </div>

              <!-- Quick Specs Icons -->
              <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-400 pt-1 border-t border-slate-800/60">
                <div class="flex items-center gap-1.5 truncate" title="Chipset">
                  <i data-lucide="cpu" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                  <span class="truncate"><?= htmlspecialchars(explode(',', $d['os_chipset'])[1] ?? $d['os_chipset']) ?></span>
                </div>
                <div class="flex items-center gap-1.5 truncate" title="Main Camera">
                  <i data-lucide="camera" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                  <span class="truncate"><?= htmlspecialchars(explode('+', $d['main_camera'])[0] ?? $d['main_camera']) ?></span>
                </div>
                <div class="flex items-center gap-1.5 truncate" title="Battery">
                  <i data-lucide="battery" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                  <span class="truncate"><?= htmlspecialchars(explode(',', $d['battery_charging'])[0] ?? $d['battery_charging']) ?></span>
                </div>
                <div class="flex items-center gap-1.5 truncate" title="RAM & Storage">
                  <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                  <span class="truncate"><?= htmlspecialchars(explode(',', $d['ram_storage'])[0] ?? $d['ram_storage']) ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Action Buttons -->
          <div class="p-5 pt-0 flex items-center gap-2">
            <a href="/mobile/<?= htmlspecialchars($d['slug']) ?>" class="flex-1 py-2 px-3 rounded-xl bg-slate-800 hover:bg-emerald-600 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
              <span>Full Specs &amp; 3D</span>
              <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

    <!-- NO RESULTS NOTICE -->
    <div id="no-results" class="hidden text-center py-16 space-y-3">
      <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-slate-500">
        <i data-lucide="search-x" class="w-6 h-6"></i>
      </div>
      <h3 class="text-lg font-bold text-white">No Smartphone Matches Your Search</h3>
      <p class="text-xs text-slate-400">Try adjusting your brand or price filter.</p>
      <button onclick="resetAllFilters()" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">Reset All Filters</button>
    </div>

  </main>

  <footer class="border-t border-slate-800 bg-slate-950 py-8 text-center text-xs text-slate-500">
    <div class="max-w-7xl mx-auto px-4">
      <p>&copy; <?= date('Y') ?> OmniWire Media Intelligence. Showing complete list of <?= $totalDevices ?>+ verified smartphones across Pakistan. All product images are property of their respective manufacturers.</p>
    </div>
  </footer>

  <script>
    lucide.createIcons();

    let currentBrand = 'all';

    function setBrandFilter(brand) {
      currentBrand = brand;
      document.querySelectorAll('.brand-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white');
        btn.classList.add('bg-slate-900', 'text-slate-300');
      });
      const activeBtn = document.getElementById('brand-btn-' + brand);
      if (activeBtn) {
        activeBtn.classList.remove('bg-slate-900', 'text-slate-300');
        activeBtn.classList.add('bg-emerald-600', 'text-white');
      }
      applyFilters();
    }

    function applyFilters() {
      const query = (document.getElementById('mobile-search-input')?.value || '').toLowerCase().trim();
      const priceRange = document.getElementById('price-filter')?.value || 'all';
      const cards = document.querySelectorAll('.mobile-card');
      let visibleCount = 0;

      cards.forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const brand = card.getAttribute('data-brand') || '';
        const price = parseInt(card.getAttribute('data-price') || '0', 10);

        // Brand match
        const matchesBrand = (currentBrand === 'all' || brand === currentBrand);

        // Search match
        const matchesQuery = (!query || title.includes(query));

        // Price range match
        let matchesPrice = true;
        if (priceRange === 'under35k') {
          matchesPrice = (price < 35000);
        } else if (priceRange === '35k-75k') {
          matchesPrice = (price >= 35000 && price < 75000);
        } else if (priceRange === '75k-150k') {
          matchesPrice = (price >= 75000 && price < 150000);
        } else if (priceRange === '150k-250k') {
          matchesPrice = (price >= 150000 && price < 250000);
        } else if (priceRange === 'flagship') {
          matchesPrice = (price >= 250000);
        }

        if (matchesBrand && matchesQuery && matchesPrice) {
          card.classList.remove('hidden');
          visibleCount++;
        } else {
          card.classList.add('hidden');
        }
      });

      const noRes = document.getElementById('no-results');
      if (noRes) {
        if (visibleCount === 0) noRes.classList.remove('hidden');
        else noRes.classList.add('hidden');
      }
    }

    function applySort() {
      const sortVal = document.getElementById('sort-filter')?.value;
      const grid = document.getElementById('mobile-grid');
      const cards = Array.from(grid.querySelectorAll('.mobile-card'));

      cards.sort((a, b) => {
        const priceA = parseInt(a.getAttribute('data-price') || '0', 10);
        const priceB = parseInt(b.getAttribute('data-price') || '0', 10);
        return (sortVal === 'price-asc') ? (priceA - priceB) : (priceB - priceA);
      });

      cards.forEach(c => grid.appendChild(c));
    }

    function resetAllFilters() {
      if (document.getElementById('mobile-search-input')) document.getElementById('mobile-search-input').value = '';
      if (document.getElementById('price-filter')) document.getElementById('price-filter').value = 'all';
      setBrandFilter('all');
    }
  </script>
</body>
</html>
