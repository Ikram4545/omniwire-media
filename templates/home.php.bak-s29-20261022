<?php
/**
 * OMNIWIRE MEDIA - UNIFIED HOMEPAGE TEMPLATE (v12.1 HOTFIX)
 * Uses Uniform 3-Column Card Blueprint (Strict 16:9 Ratio, Equal Heights, Line-Clamp-2)
 */
$basePrefix = isset($basePrefix) ? $basePrefix : '';

if (!function_exists('wp_trim_words')) {
    function wp_trim_words($text, $num_words = 18, $more = '...') {
        $clean = trim(strip_tags($text ?? ''));
        $words = preg_split('/\s+/', $clean, $num_words + 1);
        if (count($words) > $num_words) {
            array_pop($words);
            return implode(' ', $words) . $more;
        }
        return implode(' ', $words);
    }
}
?>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
  <!-- HERO & TRENDING SECTION -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10 animate-fade-in-up">
    <?php if (!empty($hero)): ?>
    <div class="lg:col-span-2 group">
      <div class="hero-card relative rounded-3xl overflow-hidden shadow-2xl bg-slate-900 border border-slate-800 h-full">
        <div class="relative w-full aspect-video sm:h-[460px]">
          <img 
            src="<?= htmlspecialchars($hero['image_url'] ?? '') ?>" 
            alt="<?= htmlspecialchars($hero['title'] ?? '') ?>" 
            fetchpriority="high"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-700 brightness-[0.70] absolute inset-0"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
          
          <div class="absolute top-4 left-4 sm:top-6 sm:left-6 flex flex-wrap items-center gap-2 z-10">
            <span class="bg-red-600 text-white font-extrabold text-[10px] sm:text-[11px] uppercase px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Breaking Trending Story
            </span>
            <span class="bg-slate-900/80 backdrop-blur text-slate-200 text-xs font-semibold px-3 py-1 rounded-full border border-slate-700/60">
              <?= htmlspecialchars($hero['reading_time'] ?? '5 min read') ?>
            </span>
          </div>

          <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 space-y-2 sm:space-y-3 z-10">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 block">
              Source: <?= htmlspecialchars(str_ireplace('Global Wire', 'OmniWire Media', $hero['source'] ?? 'OmniWire Editorial Desk')) ?>
            </span>
            <h1 class="font-extrabold text-white text-xl sm:text-2xl lg:text-3xl leading-tight font-serif">
              <a href="<?= $basePrefix ?>/article/<?= htmlspecialchars($hero['slug'] ?? '') ?>" class="text-white hover:text-emerald-400 transition line-clamp-2">
                <?= htmlspecialchars($hero['title'] ?? '') ?>
              </a>
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm line-clamp-2 max-w-2xl hidden sm:block">
              <?= htmlspecialchars(wp_trim_words(strip_tags($hero['summary'] ?? ''), 20, '...')) ?>
            </p>
            <div class="flex items-center justify-between pt-2 border-t border-slate-800/80">
              <span class="text-xs text-slate-300 font-medium"><?= htmlspecialchars(str_ireplace('Global Wire', 'OmniWire Media', $hero['author'] ?? 'OmniWire Desk')) ?></span>
              <a href="<?= $basePrefix ?>/article/<?= htmlspecialchars($hero['slug'] ?? '') ?>" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-400 hover:text-emerald-300">
                Read Analysis &rarr;
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- TRENDING LIST -->
    <div class="rounded-3xl p-6 bg-slate-900/90 border border-slate-800 shadow-sm flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
          <h3 class="font-bold text-base uppercase tracking-wider text-white flex items-center gap-2 font-serif">
            <i data-lucide="flame" class="w-5 h-5 text-red-500"></i> Trending Now
          </h3>
          <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-950/50 text-red-400">Live 24h</span>
        </div>

        <div class="divide-y divide-slate-800/60 mt-2">
          <?php foreach (($trending ?? []) as $idx => $trend): ?>
          <a href="<?= $basePrefix ?>/article/<?= htmlspecialchars($trend['slug']) ?>" class="py-3 flex items-start gap-3.5 group">
            <span class="w-6 h-6 rounded-full bg-slate-800 text-slate-300 font-bold text-xs flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition">
              <?= $idx + 1 ?>
            </span>
            <div class="flex-1 min-w-0">
              <h4 class="text-xs sm:text-sm font-bold text-slate-200 group-hover:text-emerald-400 line-clamp-2 transition leading-snug">
                <?= htmlspecialchars($trend['title']) ?>
              </h4>
              <span class="text-[11px] text-emerald-400 font-semibold capitalize mt-1 block">
                <?= htmlspecialchars(str_replace('-', ' ', $trend['category'])) ?>
              </span>
            </div>
            <img src="<?= htmlspecialchars($trend['image_url']) ?>" alt="" class="w-14 h-14 rounded-xl object-cover border border-slate-800 shrink-0" loading="lazy">
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- EDITORIAL GRID (3-COLUMN UNIFORM BLUEPRINT) -->
  <div class="mb-12">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-8 pb-3 border-b border-slate-800">
      <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-serif tracking-tight">Latest Editorial Intelligence</h2>
        <p class="text-xs text-slate-400 mt-1">Researched, drafted with AI assistance, and reviewed before publishing</p>
      </div>
      <span class="text-xs font-bold text-emerald-400 flex items-center gap-1.5 self-start sm:self-auto">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> Live Global Feed
      </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach (($articles ?? []) as $article): 
        $artCat = $article['category'] ?? 'ai-tech';
        $homeImg = !empty($article['image_url']) ? $article['image_url'] : (!empty($article['featured_image']) ? $article['featured_image'] : '');
        if (empty($homeImg) || str_contains($homeImg, 'placeholder') || str_contains($homeImg, 'photo-1585829365295')) {
            $homeImg = function_exists('getDiverseNicheImage') ? getDiverseNicheImage($artCat, intval($article['id'])) : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80';
        }
        $homeFallback = function_exists('getDiverseNicheImage') ? getDiverseNicheImage($artCat, intval($article['id']) + 7) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80';
        $cleanSummary = wp_trim_words(strip_tags(!empty($article['excerpt']) ? $article['excerpt'] : ($article['summary'] ?? '')), 18, '...');
      ?>
      <!-- UNIFORM CARD BLUEPRINT -->
      <div class="omni-article-card group flex flex-col justify-between h-full bg-white border border-slate-200 rounded-xl p-4 shadow-md hover:shadow-lg hover:border-emerald-500/50 transition-all duration-300">
        <div>
          <!-- Locked 16:9 Thumbnail -->
          <div class="relative w-full aspect-video rounded-lg overflow-hidden bg-slate-800 mb-3">
            <img src="<?= htmlspecialchars($homeImg) ?>" 
                 onerror="this.onerror=null;this.src='<?= $homeFallback ?>';"
                 alt="<?= htmlspecialchars($article['title']) ?>" 
                 loading="lazy" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 text-xs font-semibold rounded-md bg-slate-950/80 text-emerald-400 border border-emerald-500/30 backdrop-blur-sm">
              <?= htmlspecialchars($article['category']) ?>
            </span>
          </div>

          <!-- 2-Line Clamped Title -->
          <h3 class="text-base sm:text-lg font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-emerald-600 transition-colors">
            <a href="<?= $basePrefix ?>/article/<?= htmlspecialchars($article['slug']) ?>" class="text-slate-900 hover:text-emerald-600">
              <?= htmlspecialchars($article['title']) ?>
            </a>
          </h3>

          <!-- 2-Line Clean Summary -->
          <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 mt-2 leading-relaxed">
            <?= htmlspecialchars($cleanSummary) ?>
          </p>
        </div>

        <!-- Locked Bottom Meta Footer -->
        <div class="card-meta-footer mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
          <span><?= date('M d, Y', strtotime($article['created_at'])) ?></span>
          <span>⚡ <?= htmlspecialchars($article['reading_time'] ?? '5 min read') ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>
