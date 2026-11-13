<?php
/**
 * OMNIWIRE MEDIA - UNIFIED SINGLE ARTICLE TEMPLATE (v12.1 HOTFIX)
 * Enforces strict editorial hierarchy, zero raw text leak before <h1>,
 * 16:9 aspect-video hero image, prose-invert body, and uniform 3-col related cards.
 */
$basePrefix = isset($basePrefix) ? $basePrefix : '';
$art = isset($article) ? $article : (isset($art) ? $art : []);

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

$artCat = $art['category'] ?? 'ai-tech';
$artCatLabel = ucwords(str_replace('-', ' ', $artCat));
$artTitle = $art['title'] ?? 'Editorial Intelligence Report';
$artAuthor = str_ireplace('Global Wire', 'OmniWire Media', $art['author'] ?? 'OmniWire Editorial Desk');
$artDate = !empty($art['created_at']) ? date('M d, Y', strtotime($art['created_at'])) : date('M d, Y');
$artReading = $art['reading_time'] ?? '5 min read';
$artHeroImg = !empty($art['featured_image']) ? $art['featured_image'] : (!empty($art['image_url']) ? $art['image_url'] : '/static/images/placeholder.jpg');

// Clean body content without stripping structural wrapper tags
$articleBodyHtml = $art['content'] ?? '';
$articleBodyHtml = str_ireplace(['https://pixeltrio.com/news', 'http://pixeltrio.com/news', 'pixeltrio.com/news', 'pixeltrio.com'], ['https://omniwiremedia.com', 'https://omniwiremedia.com', 'omniwiremedia.com', 'omniwiremedia.com'], $articleBodyHtml);
$articleBodyHtml = str_ireplace('Global Wire', 'OmniWire Media', $articleBodyHtml);
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
  <article class="max-w-4xl mx-auto">
    <!-- 2. Category Badge & Breadcrumb -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
      <nav class="flex items-center gap-2 text-xs text-slate-400 flex-wrap" aria-label="Breadcrumb">
        <a href="<?= $basePrefix ?>/" class="hover:text-emerald-400 transition">Home</a>
        <span>/</span>
        <a href="<?= $basePrefix ?>/category/<?= htmlspecialchars($artCat) ?>" class="hover:text-emerald-400 transition capitalize">
          <?= htmlspecialchars($artCatLabel) ?>
        </a>
        <span>/</span>
        <span class="text-slate-500 truncate max-w-xs"><?= htmlspecialchars($artTitle) ?></span>
      </nav>
      <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
        <?= htmlspecialchars($artCatLabel) ?>
      </span>
    </div>

    <!-- 3. Article Title -->
    <h1 class="text-2xl sm:text-4xl font-extrabold text-white mt-4 mb-3 leading-tight font-serif">
      <?= htmlspecialchars($artTitle) ?>
    </h1>

    <!-- 4. Meta Strip (Author, Publish Date, Reading Time) -->
    <div class="flex flex-wrap items-center justify-between gap-4 py-3 border-y border-slate-800 text-xs text-slate-400">
      <div class="flex items-center gap-3">
        <span class="font-bold text-white"><?= htmlspecialchars($artAuthor) ?></span>
        <span>&bull;</span>
        <span><?= htmlspecialchars($artDate) ?></span>
        <span>&bull;</span>
        <span class="text-emerald-400 font-semibold">⚡ <?= htmlspecialchars($artReading) ?></span>
      </div>
      <div class="text-slate-500 font-medium">
        Verified OmniWire Media Report
      </div>
    </div>

    <!-- 5. Featured Image (16:9 aspect-video) -->
    <img 
      src="<?= htmlspecialchars($artHeroImg) ?>" 
      alt="<?= htmlspecialchars($art['image_alt'] ?? $artTitle) ?>" 
      fetchpriority="high"
      decoding="async"
      class="w-full aspect-video rounded-xl object-cover my-6 border border-slate-800 bg-slate-900"
    >

    <!-- 6. Formatted Article Body -->
    <div class="prose prose-invert max-w-none leading-relaxed"><?= $articleBodyHtml ?></div>
  </article>

  <?php if (!empty($related)): ?>
  <!-- Related Articles in Uniform 3-Column Grid -->
  <section class="max-w-7xl mx-auto mt-14 pt-8 border-t border-slate-800">
    <h3 class="text-xl sm:text-2xl font-bold font-serif text-white mb-6">
      Recommended in <?= htmlspecialchars($artCatLabel) ?>
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($related as $rel): 
        $relImg = !empty($rel['featured_image']) ? $rel['featured_image'] : (!empty($rel['image_url']) ? $rel['image_url'] : '/static/images/placeholder.jpg');
        $relSummary = wp_trim_words(strip_tags(!empty($rel['excerpt']) ? $rel['excerpt'] : ($rel['summary'] ?? '')), 18, '...');
      ?>
      <div class="group flex flex-col justify-between h-full bg-slate-900/60 border border-slate-800 rounded-xl p-4 hover:border-emerald-500/50 transition-all duration-300">
        <div>
          <div class="relative w-full aspect-video rounded-lg overflow-hidden bg-slate-800 mb-3">
            <img src="<?= htmlspecialchars($relImg) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
          </div>
          <h4 class="text-base font-bold text-white line-clamp-2 leading-snug group-hover:text-emerald-400 transition-colors">
            <a href="<?= $basePrefix ?>/article/<?= htmlspecialchars($rel['slug']) ?>">
              <?= htmlspecialchars($rel['title']) ?>
            </a>
          </h4>
          <p class="text-xs text-slate-400 line-clamp-2 mt-2 leading-relaxed">
            <?= htmlspecialchars($relSummary) ?>
          </p>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
          <span><?= date('M d, Y', strtotime($rel['created_at'] ?? 'now')) ?></span>
          <span>⚡ <?= htmlspecialchars($rel['reading_time'] ?? '5 min read') ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</main>
