<?php
/**
 * OMNIWIRE MEDIA - UNIFIED CATEGORY VERTICAL TEMPLATE (v12.1 HOTFIX)
 * Implements Uniform 3-Column Card Blueprint (Strict 16:9 Ratio, Equal Heights, Line-Clamp-2)
 */
$basePrefix = isset($basePrefix) ? $basePrefix : '';
$catName = isset($catName) ? $catName : 'Category Vertical';
$catDesc = isset($catDesc) ? $catDesc : 'Verified trending news, analytical reporting, and breaking updates.';
$catArticles = isset($articles) ? $articles : [];

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
  <!-- CATEGORY HERO HEADER -->
  <div class="p-8 sm:p-12 rounded-3xl bg-slate-900/90 text-white border border-slate-800 shadow-2xl mb-10 animate-fade-in-up">
    <div class="flex items-center gap-2 text-xs uppercase tracking-wider text-emerald-400 font-bold mb-2">
      <i data-lucide="compass" class="w-4 h-4 text-emerald-400"></i> News Vertical
    </div>
    <h1 class="text-3xl sm:text-5xl font-extrabold font-serif text-white tracking-tight"><?= htmlspecialchars($catName) ?></h1>
    <p class="text-slate-300 text-sm sm:text-base mt-2 max-w-2xl leading-relaxed"><?= htmlspecialchars($catDesc) ?></p>
    <div class="mt-4 flex items-center gap-3 text-xs text-slate-400">
      <span class="font-semibold text-emerald-400"><?= count($catArticles) ?> Verified Articles</span>
      <span>&bull;</span>
      <span>Updated 24/7 by Autonomous Wire</span>
    </div>
  </div>

  <!-- UNIFORM CARD BLUEPRINT GRID -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    <?php foreach ($catArticles as $article): 
      $artCat = $article['category'] ?? ($catSlug ?? 'ai-tech');
      $featImg = !empty($article['featured_image']) ? $article['featured_image'] : (!empty($article['image_url']) ? $article['image_url'] : '');
      if (empty($featImg) || str_contains($featImg, 'placeholder') || str_contains($featImg, 'photo-1585829365295')) {
          $featImg = function_exists('getDiverseNicheImage') ? getDiverseNicheImage($artCat, intval($article['id'])) : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80';
      }
      $fallbackImg = function_exists('getDiverseNicheImage') ? getDiverseNicheImage($artCat, intval($article['id']) + 7) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80';
      $excerpt = wp_trim_words(strip_tags(!empty($article['excerpt']) ? $article['excerpt'] : ($article['summary'] ?? '')), 18, '...');
      $readTime = !empty($article['reading_time']) ? $article['reading_time'] : '5 min read';
    ?>
    <div class="omni-article-card group flex flex-col justify-between h-full bg-white border border-slate-200 rounded-xl p-4 shadow-md hover:shadow-lg hover:border-emerald-500/50 transition-all duration-300">
      <div>
        <!-- Locked 16:9 Thumbnail -->
        <div class="relative w-full aspect-video rounded-lg overflow-hidden bg-slate-800 mb-3">
          <img src="<?= htmlspecialchars($featImg) ?>" 
               onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';"
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
          <?= htmlspecialchars($excerpt) ?>
        </p>
      </div>

      <!-- Locked Bottom Meta Footer -->
      <div class="card-meta-footer mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
        <span><?= date('M d, Y', strtotime($article['created_at'])) ?></span>
        <span>⚡ <?= htmlspecialchars($readTime) ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>
