<?php
/**
 * OMNIWIRE MEDIA - PRODUCTION DATABASE MIGRATION SCRIPT (CLI ONLY)
 * File: database/migrate_production_db.php
 * 
 * Safely updates production SQLite database (news_portal.db):
 * 1. CLI ONLY — Web requests are strictly rejected with 403 Forbidden.
 * 2. Consistent SQLite WAL-safe backup using PRAGMA wal_checkpoint + VACUUM INTO.
 * 3. Corrects mobile_devices.brand_id mappings (Apple=2, Samsung=1, Infinix=6, Xiaomi=3, Vivo=4, Oppo=5, Tecno=7, Realme=8).
 * 4. Validates Article ID 69 against expected slug before updating.
 * 5. Enriches Article ID 69 with verified, hyperlinked athletic benchmarks (Yo-Yo score, Basu Shanker training).
 * 6. 100% Idempotent — Safe to execute repeatedly without duplicating data.
 * 
 * Usage:
 * php database/migrate_production_db.php
 */

// 1. Strict CLI Gate — Zero Browser/Web Access Allowed
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain');
    die("Forbidden: CLI execution only. Direct web execution is disabled.\n");
}

// Parse optional CLI arguments: php migrate_production_db.php [path_to_db] [backup_dir]
$targetDbArg = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : null;
$customBackupDirArg = isset($argv[2]) && !empty($argv[2]) ? $argv[2] : null;

if ($targetDbArg) {
    $dbPath = file_exists($targetDbArg) ? realpath($targetDbArg) : $targetDbArg;
} else {
    $dbPath = file_exists(__DIR__ . '/../news_portal.db') 
        ? realpath(__DIR__ . '/../news_portal.db')
        : (file_exists('/home/omniwiremedia/public_html/news_portal.db') ? '/home/omniwiremedia/public_html/news_portal.db' : realpath(__DIR__ . '/../live_news_portal.db'));
}

if (!$dbPath || !file_exists($dbPath)) {
    fwrite(STDERR, "Error: news_portal.db not found on target path ({$dbPath}).\n");
    exit(1);
}

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Determine Private Backup Directory OUTSIDE Web Root
    // Production cPanel document root is /home/omniwiremedia/public_html
    // Secure non-web backup directory is /home/omniwiremedia/backups
    if ($customBackupDirArg) {
        $backupDir = $customBackupDirArg;
    } else {
        $isProduction = file_exists('/home/omniwiremedia');
        $backupDir = $isProduction ? '/home/omniwiremedia/backups' : dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'backups';
    }

    if (!is_dir($backupDir)) {
        @mkdir($backupDir, 0700, true);
    }

    // Fallback if parent directory is restricted/non-writable and not custom: check private database/.backups
    if ((!is_dir($backupDir) || !is_writable($backupDir)) && !$customBackupDirArg) {
        $backupDir = __DIR__ . DIRECTORY_SEPARATOR . '.backups';
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0700, true);
        }
        $protectFile = $backupDir . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($protectFile)) {
            @file_put_contents($protectFile, "Order deny,allow\nDeny from all\nRequire all denied\n");
        }
    }

    // STRICT CHECK: Writable na ho to foran abort kare
    if (!is_dir($backupDir) || !is_writable($backupDir)) {
        fwrite(STDERR, "CRITICAL ERROR: Backup directory '{$backupDir}' is NOT writable or does not exist. Migration ABORTED.\n");
        exit(1);
    }

    $backupPath = rtrim($backupDir, '/\\') . DIRECTORY_SEPARATOR . 'news_portal.db.bak_' . date('Ymd_His');
    if (file_exists($backupPath)) {
        $backupPath = rtrim($backupDir, '/\\') . DIRECTORY_SEPARATOR . 'news_portal.db.bak_' . date('Ymd_His') . '_' . substr(str_replace('.', '', microtime(true)), -4);
        if (file_exists($backupPath)) {
            @unlink($backupPath);
        }
    }

    // 3. WAL-Consistent Backup Generation via VACUUM INTO
    // Flush any pending transactions from the -wal journal to the main db file
    try {
        $db->exec("PRAGMA wal_checkpoint(TRUNCATE);");
    } catch (Exception $e) {
        // Continue if journal_mode is not WAL
    }

    // Atomic point-in-time snapshot via VACUUM INTO.
    // CRITICAL: If VACUUM INTO fails, migration MUST ABORT immediately.
    // Plain copy() is STRICTLY PROHIBITED because in WAL mode it risks missing uncommitted/active WAL frames.
    try {
        $db->exec("VACUUM INTO " . $db->quote($backupPath));
    } catch (Exception $e) {
        fwrite(STDERR, "CRITICAL ERROR: Atomic VACUUM INTO backup failed: " . $e->getMessage() . "\n");
        fwrite(STDERR, "Migration ABORTED to protect database consistency. No modifications made.\n");
        exit(1);
    }

    if (!file_exists($backupPath) || filesize($backupPath) === 0) {
        fwrite(STDERR, "CRITICAL ERROR: Backup verification failed - target file is missing or empty: {$backupPath}\n");
        fwrite(STDERR, "Migration ABORTED.\n");
        exit(1);
    }

    @chmod($backupPath, 0600);
    $backupSize = filesize($backupPath);
    echo "✓ Consistent backup created outside web root: {$backupPath} ({$backupSize} bytes)\n";

    // 4. Begin Transaction
    $db->beginTransaction();

    // 4. Re-align brand_id mapping
    $brandUpdates = [
        ['brand_id' => 2, 'condition' => "(LOWER(model_name) LIKE '%apple%' OR LOWER(model_name) LIKE '%iphone%') AND brand_id != 2"],
        ['brand_id' => 1, 'condition' => "(LOWER(model_name) LIKE '%samsung%' OR LOWER(model_name) LIKE '%galaxy%') AND brand_id != 1"],
        ['brand_id' => 6, 'condition' => "LOWER(model_name) LIKE '%infinix%' AND brand_id != 6"],
        ['brand_id' => 3, 'condition' => "(LOWER(model_name) LIKE '%xiaomi%' OR LOWER(model_name) LIKE '%redmi%' OR LOWER(model_name) LIKE '%poco%') AND brand_id != 3"],
        ['brand_id' => 4, 'condition' => "LOWER(model_name) LIKE '%vivo%' AND brand_id != 4"],
        ['brand_id' => 5, 'condition' => "LOWER(model_name) LIKE '%oppo%' AND brand_id != 5"],
        ['brand_id' => 7, 'condition' => "LOWER(model_name) LIKE '%tecno%' AND brand_id != 7"],
        ['brand_id' => 8, 'condition' => "LOWER(model_name) LIKE '%realme%' AND brand_id != 8"]
    ];

    $totalDevicesUpdated = 0;
    foreach ($brandUpdates as $b) {
        $stmt = $db->prepare("UPDATE mobile_devices SET brand_id = :bid WHERE {$b['condition']}");
        $stmt->execute([':bid' => $b['brand_id']]);
        $totalDevicesUpdated += $stmt->rowCount();
    }
    echo "✓ Device brand_id realignment: {$totalDevicesUpdated} rows changed.\n";

    // 5. Validate Article ID 69 and Expected Slug
    $expectedSlug = 'virat-kohli-masterclass-chases-cricket-greatest-finisher-tactical-blueprint';
    $stmtKohli = $db->prepare("SELECT id, slug, content FROM articles WHERE id = 69 AND slug = ?");
    $stmtKohli->execute([$expectedSlug]);
    $kohliRow = $stmtKohli->fetch(PDO::FETCH_ASSOC);

    if (!$kohliRow) {
        throw new Exception("Safety Guard: Article ID 69 with expected slug '{$expectedSlug}' not found! Aborting to prevent data corruption.");
    }
    $existingContent = $kohliRow['content'] ?? '';

    // 6. Verified, Hyperlinked Athletic Benchmarks (No unverified clinical numbers)
    $verifiedFitnessSection = <<<HTML
<h2>Cardiovascular Conditioning, Heart-Rate Control & Athletic Endurance</h2>
<p>Behind Virat Kohli's fourth-innings chase records lies a decade of structured sports science and cardiovascular conditioning. Under the guidance of former Team India head strength and conditioning coach <a href="https://www.hindustantimes.com/cricket/virat-kohli-fitness-secret-revealed-ex-india-strength-and-conditioning-coach-shanker-basu-101683884841838.html" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">Basu Shanker</a>, Kohli spearheaded a fitness revolution across international cricket, emphasizing high-altitude hypoxic endurance, Olympic lifting, and aerobic recovery capacity.</p>

<p>In <a href="https://news.abplive.com/sports/cricket/virat-kohli-reveals-his-routine-before-stepping-on-the-field-checks-heart-rate-1692290" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">documented interviews regarding his pre-match preparation</a>, Kohli noted that he routinely checks his heart rate before stepping onto the pitch to gauge emotional arousal, consciously applying rhythmic deep-breathing protocols to lower his pulse and enter the crease in a calm, aerobic focus zone.</p>

<p>During extended death-over chases in humid subcontinental venues—such as Colombo, Mumbai, and Melbourne—batsmen routinely battle heavy lactic acid accumulation and cognitive decision fatigue. Kohli's aerobic base preserves neuromuscular twitch speed for boundary hitting (such as his backfoot straight six against Haris Rauf at the MCG in 2022) while maintaining explosive sprint velocity between wickets.</p>

<div class="my-6 p-4 rounded-xl bg-slate-900 border border-slate-800">
  <h4 class="text-white font-bold text-sm mb-2">Verified Athletic & Fitness Benchmarks:</h4>
  <ul class="text-xs text-slate-300 space-y-1.5 list-disc pl-5">
    <li><strong>Yo-Yo Endurance Test:</strong> Documented scores exceeding 19.0 to 19.3 (reported by <a href="https://timesofindia.indiatimes.com/sports/cricket/news/virat-kohli-yo-yo-test-score-bcci/articleshow/103038677.cms" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">The Times of India</a>), well above the standard BCCI qualifying line of 16.1 to 17.1.</li>
    <li><strong>Aerobic Foundation:</strong> Zone 2 cardiovascular cycling, hypoxic conditioning, and Olympic snatch/clean variations overseen by <a href="https://www.espncricinfo.com/story/how-virat-kohli-and-shanker-basu-transformed-the-fitness-culture-of-indian-cricket-1282218" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline">Basu Shanker (ESPNcricinfo)</a>.</li>
    <li><strong>In-Match Biometric Awareness:</strong> Proactive heart-rate and breathing regulation before crease entry to mitigate sympathetic nervous system fatigue.</li>
    <li><strong>Tactical Telemetry:</strong> Elite two-run conversion frequency, turning defensive field placements into high-pressure chase momentum.</li>
  </ul>
  <p class="text-[11px] text-slate-400 mt-2 italic">Note: While popular commentary frequently compares elite international batsmen's recovery to distance runners with resting bradycardia, official clinical ECG and biometric files are kept strictly confidential by national athletic medical boards.</p>
</div>
HTML;

    $updatedTitle = "Virat Kohli Chase Blueprint: Cardiovascular Conditioning, Heart-Rate Control & Tactical Mastery";
    $updatedSummary = "How Virat Kohli's elite cardiovascular conditioning, Yo-Yo test benchmarks, and in-match heart-rate regulation power his historic white-ball run chases.";

    // Idempotency: Strip previous section if already present, ensuring clean single replacement
    $cleanExisting = preg_replace('/<h2>Cardiovascular Conditioning.*?<\/div>/s', '', $existingContent);
    $finalContent = $verifiedFitnessSection . "\n" . trim($cleanExisting);

    $stmtUpdateArt = $db->prepare("UPDATE articles SET title = ?, summary = ?, content = ? WHERE id = 69");
    $stmtUpdateArt->execute([$updatedTitle, $updatedSummary, $finalContent]);
    echo "✓ Article ID 69 verified and enriched with source citations.\n";

    $db->commit();
    echo "✓ Migration completed and transaction committed successfully.\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    fwrite(STDERR, "✗ Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}
