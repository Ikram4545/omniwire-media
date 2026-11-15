<?php
/**
 * OMNIWIRE MEDIA - LIVE SPORTS CENTER & SCORE TICKER (v1.0)
 * Real-Time Cricket Ball-by-Ball, Football Match Scores & Official Highlights
 */
$pageTitle = "Sports Match Center (Demo Simulation) — OmniWire Media";
$pageDesc = "Interactive match simulation, scorecard prototype, and sports center UI demo on OmniWire Media.";
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
  <link rel="canonical" href="https://omniwiremedia.com/sports/live">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    @keyframes pulse-live { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.15); } }
    .live-dot { animation: pulse-live 1.5s infinite ease-in-out; }
    .ball-badge { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 9999px; font-weight: 800; font-size: 11px; }
    .ball-four { background: #2563eb; color: #fff; }
    .ball-six { background: #16a34a; color: #fff; }
    .ball-wicket { background: #dc2626; color: #fff; }
    .ball-dot { background: #334155; color: #94a3b8; }
    .ball-run { background: #1e293b; color: #f1f5f9; border: 1px solid #475569; }
  </style>
</head>
<body class="bg-[#070A0F] text-slate-100 min-h-screen flex flex-col font-sans antialiased selection:bg-emerald-500 selection:text-white">

  <!-- TOP HEADER / BRAND -->
  <header class="bg-slate-900/90 border-b border-slate-800 sticky top-0 z-50 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
      <a href="/" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center font-bold text-slate-950">
          <i data-lucide="trophy" class="w-5 h-5"></i>
        </div>
        <span class="font-serif font-black text-xl text-white">Omni<span class="text-emerald-500">Wire</span> Sports</span>
      </a>
      <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span> SCORECARD PROTOTYPE / DEMO
        </span>
        <a href="/category/world-sports" class="text-xs text-slate-400 hover:text-white font-medium">Back to News</a>
      </div>
    </div>
  </header>

  <!-- LIVE SCORE TICKER STRIP -->
  <div class="bg-slate-950 border-b border-slate-800/80 overflow-x-auto scrollbar-none py-2.5">
    <div class="max-w-7xl mx-auto px-4 flex items-center gap-6 text-xs whitespace-nowrap">
      <div class="flex items-center gap-2 text-rose-400 font-extrabold uppercase tracking-wider">
        <span class="w-2 h-2 rounded-full bg-rose-500 live-dot"></span> Cricket:
      </div>
      <div class="flex items-center gap-2 text-slate-200">
        <span class="font-bold text-white">PAK 284/7 (50 ov)</span> vs <span class="font-bold text-emerald-400">RSA 142/3 (24.2 ov)</span>
        <span class="text-slate-400 text-[11px]">(RSA need 143 off 154 balls)</span>
      </div>
      <div class="text-slate-700">|</div>
      <div class="flex items-center gap-2 text-sky-400 font-extrabold uppercase tracking-wider">
        <span>⚽ Football:</span>
      </div>
      <div class="flex items-center gap-2 text-slate-200">
        <span class="font-bold text-white">Real Madrid 2</span> - <span class="font-bold text-amber-400">1 Barcelona</span>
        <span class="px-1.5 py-0.5 rounded bg-emerald-950 text-emerald-400 text-[10px] font-bold">78' LIVE</span>
      </div>
    </div>
  </div>

  <!-- MAIN MATCH CONTENT -->
  <main class="max-w-7xl mx-auto px-4 py-8 flex-1 grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT 2 COLS: CRICKET LIVE MATCH CARD -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- PRIMARY MATCH CARD -->
      <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-xl relative overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
          <div>
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Tri-Series ODI &bull; 1st Match</span>
            <h1 class="text-xl sm:text-2xl font-black text-white mt-1">Pakistan vs South Africa</h1>
            <p class="text-xs text-slate-400">Gaddafi Stadium, Lahore &bull; Day/Night ODI</p>
          </div>
          <div class="text-right">
            <span class="px-2.5 py-1 rounded-lg bg-amber-950/80 border border-amber-800/60 text-amber-400 text-xs font-black uppercase tracking-wider inline-flex items-center gap-1">
              DEMO SCORECARD
            </span>
            <div id="refresh-timer" class="text-[10px] text-slate-400 mt-1 font-mono">Curated Match Simulation &bull; UI Prototype</div>
          </div>
        </div>

        <!-- SCORE SUMMARY BANNER -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-950/70 border border-slate-800 mb-6">
          <div>
            <div class="text-xs text-slate-400 font-semibold mb-1">Pakistan (1st Innings)</div>
            <div class="text-2xl font-black text-slate-200">284/7 <span class="text-sm font-normal text-slate-400">(50.0 Overs)</span></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Babar Azam 87 (92) &bull; Shaheen Afridi 34* (18)</div>
          </div>
          <div class="border-t md:border-t-0 md:border-l border-slate-800 pt-3 md:pt-0 md:pl-4">
            <div class="text-xs text-emerald-400 font-semibold mb-1">South Africa (Chasing Target: 285)</div>
            <div class="text-2xl font-black text-emerald-400">142/3 <span class="text-sm font-normal text-slate-300">(24.2 Overs)</span></div>
            <div class="text-xs text-amber-300 font-bold mt-1">Need 143 runs from 154 balls (Req RR: 5.57)</div>
          </div>
        </div>

        <!-- RECENT BALLS STRIP -->
        <div class="mb-6">
          <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">This Over (Over 25):</div>
          <div class="flex items-center gap-2">
            <span class="ball-badge ball-dot">0</span>
            <span class="ball-badge ball-run">1</span>
            <span class="ball-badge ball-four">4</span>
            <span class="ball-badge ball-six">6</span>
            <span class="ball-badge ball-wicket">W</span>
            <span class="ball-badge ball-run">1</span>
            <span class="text-xs text-slate-400 ml-2 font-mono">12 Runs &bull; 1 Wicket</span>
          </div>
        </div>

        <!-- LIVE BATSMEN & BOWLERS SCORECARD TABLE -->
        <div class="overflow-x-auto mb-6">
          <table class="w-full text-xs text-left">
            <thead>
              <tr class="text-slate-400 border-b border-slate-800 pb-2">
                <th class="py-2">Batter on Crease</th>
                <th class="text-center">R</th>
                <th class="text-center">B</th>
                <th class="text-center">4s</th>
                <th class="text-center">6s</th>
                <th class="text-right">SR</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-mono">
              <tr class="text-slate-200">
                <td class="py-2.5 font-sans font-bold text-white flex items-center gap-1.5">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aiden Markram*
                </td>
                <td class="text-center font-bold text-emerald-400">62</td>
                <td class="text-center">54</td>
                <td class="text-center">7</td>
                <td class="text-center">2</td>
                <td class="text-right font-bold">114.8</td>
              </tr>
              <tr class="text-slate-300">
                <td class="py-2.5 font-sans font-semibold">David Miller</td>
                <td class="text-center font-bold">14</td>
                <td class="text-center">11</td>
                <td class="text-center">1</td>
                <td class="text-center">0</td>
                <td class="text-right">127.2</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- CURRENT BOWLER -->
        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between text-xs">
          <div class="flex items-center gap-2">
            <span class="text-slate-400 font-bold">Bowler:</span>
            <span class="font-bold text-white">Shaheen Shah Afridi</span>
          </div>
          <div class="font-mono text-slate-300">
            <span class="text-emerald-400 font-bold">6.2 - 0 - 32 - 2</span> (Econ: 5.05)
          </div>
        </div>

      </div>

      <!-- LIVE BALL-BY-BALL COMMENTARY -->
      <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-xl">
        <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
          <i data-lucide="radio" class="w-4 h-4 text-emerald-400"></i> Ball-by-Ball Live Commentary
        </h2>
        <div class="space-y-4 text-xs divide-y divide-slate-800/60 font-sans">
          
          <div class="pt-3">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-mono font-bold text-emerald-400">24.2</span>
              <span class="px-2 py-0.5 rounded bg-blue-600 text-white font-extrabold text-[10px]">FOUR</span>
            </div>
            <p class="text-slate-300 leading-relaxed">
              Shaheen to Markram, <strong>FOUR runs!</strong> Glorious stroke through extra cover. Overpitched outside off-stump, Markram leans into the drive with high elbow and sends it racing to the boundary ropes. 50 partnership comes up!
            </p>
          </div>

          <div class="pt-3">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-mono font-bold text-slate-400">24.1</span>
              <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-bold text-[10px]">DOT</span>
            </div>
            <p class="text-slate-300 leading-relaxed">
              Shaheen to Markram, no run. Good length ball angling back in on off-stump, defended solidly towards mid-on.
            </p>
          </div>

          <div class="pt-3">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-mono font-bold text-rose-400">23.6</span>
              <span class="px-2 py-0.5 rounded bg-rose-600 text-white font-extrabold text-[10px]">WICKET</span>
            </div>
            <p class="text-slate-300 leading-relaxed">
              Naseem Shah to Bavuma, <strong>OUT! Clean bowled!</strong> Beautiful seam presentation from Naseem, nipping back through the gate to hit the top of off-stump. Pakistan get the breakthrough!
            </p>
          </div>

        </div>
      </div>

    </div>

    <!-- RIGHT 1 COL: OFFICIAL VIDEO HIGHLIGHTS & POLL -->
    <div class="space-y-6">
      
      <!-- OFFICIAL HIGHLIGHTS VIDEO PLAYER -->
      <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 shadow-xl">
        <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3 flex items-center gap-2">
          <i data-lucide="play-circle" class="w-4 h-4 text-rose-500"></i> Official Verified Highlights
        </h3>
        <div class="aspect-video rounded-xl overflow-hidden bg-slate-950 border border-slate-800 mb-3">
          <!-- Embed Official Verified Media -->
          <iframe 
            class="w-full h-full" 
            src="https://www.youtube-nocookie.com/embed/videoseries?list=PL4c_gA4n8DqS3W4P9d6I8E3n4_3Q4P3E3" 
            title="Official Cricket Highlights" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen
          ></iframe>
        </div>
        <p class="text-[11px] text-slate-400">
          Official cricket highlights and match video archives embedded for demonstration purposes.
        </p>
      </div>

      <!-- INTERACTIVE USER MATCH POLL (BOOSTS DWELL TIME & RETENTION) -->
      <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 shadow-xl">
        <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-2 flex items-center gap-2">
          <i data-lucide="vote" class="w-4 h-4 text-emerald-400"></i> Match Prediction Poll
        </h3>
        <p class="text-xs text-slate-400 mb-4">Who will win today's 1st ODI?</p>
        
        <div class="space-y-3 text-xs font-semibold">
          <button onclick="vote('pak')" class="w-full p-3 rounded-xl bg-slate-950 hover:bg-emerald-950/40 border border-slate-800 hover:border-emerald-500/50 flex items-center justify-between text-left transition group">
            <span class="flex items-center gap-2">🇵🇰 Pakistan</span>
            <span class="text-emerald-400 font-mono font-bold" id="pak-pct">68%</span>
          </button>
          
          <button onclick="vote('rsa')" class="w-full p-3 rounded-xl bg-slate-950 hover:bg-emerald-950/40 border border-slate-800 hover:border-emerald-500/50 flex items-center justify-between text-left transition group">
            <span class="flex items-center gap-2">🇿🇦 South Africa</span>
            <span class="text-slate-400 font-mono font-bold" id="rsa-pct">32%</span>
          </button>
        </div>
        <p class="text-[10px] text-slate-500 mt-3 text-center">Over 4,280 fans have voted in this live match poll.</p>
      </div>

      <!-- FOOTBALL CONCURRENT MATCHES -->
      <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 shadow-xl">
        <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3 flex items-center gap-2">
          <i data-lucide="activity" class="w-4 h-4 text-sky-400"></i> Ongoing Global Fixtures
        </h3>
        <div class="space-y-3 text-xs">
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
            <div>
              <div class="font-bold text-white">Real Madrid vs Barcelona</div>
              <div class="text-[10px] text-slate-400">La Liga &bull; Santiago Bernabeu</div>
            </div>
            <div class="text-right">
              <span class="font-mono font-bold text-emerald-400">2 - 1</span>
              <div class="text-[10px] text-rose-400 font-bold">78' LIVE</div>
            </div>
          </div>
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
            <div>
              <div class="font-bold text-white">Arsenal vs Chelsea</div>
              <div class="text-[10px] text-slate-400">Premier League &bull; Emirates</div>
            </div>
            <div class="text-right">
              <span class="font-mono font-bold text-slate-300">0 - 0</span>
              <div class="text-[10px] text-slate-500">Starts 22:30 PKT</div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </main>

  <script>
    lucide.createIcons();
    
    // Prototype handler
    function initSportsDemo() {
      const timerEl = document.getElementById('refresh-timer');
      if (timerEl) {
        timerEl.innerText = 'Curated Match Simulation • UI Prototype Showcase';
      }
    }
    initSportsDemo();

    function vote(team) {
      alert('Thank you for voting! Your prediction has been registered.');
    }
  </script>
</body>
</html>
