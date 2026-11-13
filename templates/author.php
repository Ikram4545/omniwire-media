<?php
/**
 * OmniWire Media - Author Profile & Articles Feed
 * Target File: templates/author.php
 * Required variables: $author (array), $authorArticles (array)
 */

$authorInitials = '';
$words = explode(' ', trim($author['name'] ?? ''));
foreach ($words as $w) {
    if (!empty($w)) $authorInitials .= strtoupper($w[0]);
}
if (empty($authorInitials)) $authorInitials = 'OW';
$authorInitials = substr($authorInitials, 0, 2);

$expertiseList = [];
if (!empty($author['expertise'])) {
    $expertiseList = array_map('trim', explode(',', $author['expertise']));
}
?>

<!-- Schema.org Person Structured Data for Google E-E-A-T -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": <?= json_encode($author['name']) ?>,
  "jobTitle": <?= json_encode($author['role']) ?>,
  "worksFor": {
    "@type": "NewsMediaOrganization",
    "name": "OmniWire Media",
    "url": "https://omniwiremedia.com"
  },
  "description": <?= json_encode(strip_tags($author['bio'])) ?>,
  "url": "https://omniwiremedia.com/author/<?= htmlspecialchars($author['slug']) ?>"
  <?php if (!empty($author['linkedin_url'])): ?>, "sameAs": [<?= json_encode($author['linkedin_url']) ?>]<?php endif; ?>
}
</script>

<div class="max-w-5xl mx-auto space-y-10 animate-fade-in-up">
  
  <!-- Author Profile Card -->
  <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl relative overflow-hidden">
    <!-- Background Glow Accent -->
    <div class="absolute -right-20 -top-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 relative z-10">
      
      <!-- Avatar / Photo -->
      <div class="shrink-0 text-center">
        <?php if (!empty($author['photo_url']) && file_exists(__DIR__ . '/..' . $author['photo_url'])): ?>
          <img 
            src="<?= htmlspecialchars($basePrefix . $author['photo_url']) ?>" 
            alt="<?= htmlspecialchars($author['name']) ?>" 
            width="140" 
            height="140" 
            class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl object-cover border-2 border-emerald-500/60 shadow-lg"
          >
        <?php else: ?>
          <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-800 border-2 border-emerald-500/60 flex items-center justify-center text-white font-serif font-extrabold text-3xl shadow-xl">
            <?= htmlspecialchars($authorInitials) ?>
          </div>
        <?php endif; ?>
        
        <span class="mt-3 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
          <i data-lucide="check-circle" class="w-3 h-3"></i> Verified Author
        </span>
      </div>

      <!-- Identity & Bio -->
      <div class="flex-1 space-y-4 text-center md:text-left">
        <div>
          <h1 class="text-2xl sm:text-3xl font-serif font-extrabold text-white">
            <?= htmlspecialchars($author['name']) ?>
          </h1>
          <p class="text-xs sm:text-sm text-emerald-400 font-semibold mt-0.5">
            <?= htmlspecialchars($author['role']) ?> &bull; OmniWire Media
          </p>
        </div>

        <div class="text-xs sm:text-sm text-slate-300 leading-relaxed space-y-2">
          <?= $author['bio'] ?>
        </div>

        <!-- Areas of Expertise -->
        <?php if (!empty($expertiseList)): ?>
        <div class="pt-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Domain Focus &amp; Sourcing Authority:</span>
          <div class="flex flex-wrap items-center justify-center md:justify-start gap-1.5">
            <?php foreach ($expertiseList as $exp): ?>
              <span class="px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-medium text-slate-300">
                &bull; <?= htmlspecialchars($exp) ?>
              </span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Social & Contact Strip -->
        <div class="pt-3 border-t border-slate-800 flex flex-wrap items-center justify-center md:justify-start gap-4 text-xs">
          <?php if (!empty($author['email'])): ?>
            <a href="mailto:<?= htmlspecialchars($author['email']) ?>" class="text-slate-300 hover:text-emerald-400 flex items-center gap-1.5 transition">
              <i data-lucide="mail" class="w-3.5 h-3.5 text-emerald-400"></i>
              <span>Contact via Newsroom</span>
            </a>
          <?php endif; ?>
          <?php if (!empty($author['linkedin_url'])): ?>
            <a href="<?= htmlspecialchars($author['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer me" class="text-slate-300 hover:text-emerald-400 flex items-center gap-1.5 transition">
              <i data-lucide="linkedin" class="w-3.5 h-3.5 text-blue-400"></i>
              <span>LinkedIn Profile</span>
            </a>
          <?php endif; ?>
          <?php if (!empty($author['twitter_url'])): ?>
            <a href="<?= htmlspecialchars($author['twitter_url']) ?>" target="_blank" rel="noopener noreferrer me" class="text-slate-300 hover:text-emerald-400 flex items-center gap-1.5 transition">
              <i data-lucide="twitter" class="w-3.5 h-3.5 text-cyan-400"></i>
              <span>X (Twitter)</span>
            </a>
          <?php endif; ?>
          <a href="<?= $basePrefix ?>/editorial-policy" class="text-slate-400 hover:text-white flex items-center gap-1 text-[11px] transition">
            <i data-lucide="shield-check" class="w-3 h-3 text-emerald-400"></i>
            <span>Editorial Standards &bull; Primary-Source Verified</span>
          </a>
        </div>

      </div>

    </div>
  </div>

  <!-- Articles Published Feed -->
  <div class="space-y-6">
    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
          <i data-lucide="newspaper" class="w-4 h-4"></i>
        </div>
        <h2 class="text-xl font-serif font-bold text-white">
          Verified Articles by <?= htmlspecialchars($author['name']) ?>
        </h2>
      </div>
      <span class="px-3 py-1 rounded-xl bg-slate-900 border border-slate-800 text-xs font-mono font-bold text-slate-300">
        <?= count($authorArticles) ?> Published
      </span>
    </div>

    <?php if (empty($authorArticles)): ?>
      <div class="p-12 text-center rounded-3xl bg-slate-900 border border-slate-800 text-slate-400 text-xs">
        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
        <p>No published articles currently found under this byline.</p>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($authorArticles as $art): 
          $artImg = !empty($art['featured_image']) ? $art['featured_image'] : (!empty($art['image_url']) ? $art['image_url'] : '');
          $cleanCheck = explode('?', trim($artImg))[0];
          if (!str_starts_with($cleanCheck, '/cache/webp/') || !file_exists(__DIR__ . '/..' . $cleanCheck)) {
              if (function_exists('getDiverseNicheImage')) {
                  $artImg = getDiverseNicheImage($art['category'] ?? 'ai-tech', intval($art['id']));
              }
          }
          $artSummary = !empty($art['excerpt']) ? $art['excerpt'] : (!empty($art['summary']) ? $art['summary'] : '');
        ?>
        <article class="group rounded-3xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col">
          <a href="<?= $basePrefix ?>/article/<?= htmlspecialchars($art['slug']) ?>" class="block relative aspect-video overflow-hidden bg-slate-950">
            <img 
              src="<?= htmlspecialchars($artImg) ?>" 
              alt="<?= htmlspecialchars($art['title']) ?>" 
              loading="lazy" 
              decoding="async"
              class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
            >
            <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-950/80 backdrop-blur text-emerald-400 border border-slate-700">
              <?= htmlspecialchars(str_replace('-', ' ', $art['category'] ?? 'general')) ?>
            </span>
          </a>

          <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
            <div>
              <h3 class="font-serif font-bold text-base text-white group-hover:text-emerald-400 transition line-clamp-2 leading-snug">
                <a href="<?= $basePrefix ?>/article/<?= htmlspecialchars($art['slug']) ?>">
                  <?= htmlspecialchars($art['title']) ?>
                </a>
              </h3>
              <p class="text-xs text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                <?= htmlspecialchars(strip_tags($artSummary)) ?>
              </p>
            </div>

            <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
              <span><?= htmlspecialchars(substr($art['created_at'] ?? '', 0, 10)) ?></span>
              <span class="font-mono text-slate-400 flex items-center gap-1">
                <i data-lucide="eye" class="w-3 h-3 text-slate-500"></i>
                <?= number_format($art['views'] ?? 0) ?> views
              </span>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>

</div>
