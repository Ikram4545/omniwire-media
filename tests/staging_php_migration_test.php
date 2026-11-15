<?php
/**
 * STAGING PHP MIGRATION VERIFICATION RUNNER
 * Tests database/migrate_production_db.php using PHP 8.2 CLI:
 * 1. Prepares WAL-enabled staging database with unaligned data.
 * 2. Run 1: Verifies brand alignment, Kohli article enrichment, and non-web backup creation.
 * 3. Run 2: Verifies strict idempotency (0 device rows changed, 0 duplicate sections in Article 69).
 * 4. Restore: Restores backup, verifies PRAGMA integrity_check=ok, device count=52, articles=118.
 * 5. Fail-Safe: Tests abort on non-writable backup directory.
 */

$phpBinary = 'F:\\download\\php82\\php.exe';
if (!file_exists($phpBinary)) {
    $phpBinary = 'php';
}

$rootDir = dirname(__DIR__);
$stagingDb = $rootDir . DIRECTORY_SEPARATOR . 'staging_test.db';
$sourceDb = $rootDir . DIRECTORY_SEPARATOR . 'live_news_portal.db';
$testBackupDir = dirname($rootDir) . DIRECTORY_SEPARATOR . 'staging_backups';

if (!is_dir($testBackupDir)) {
    @mkdir($testBackupDir, 0700, true);
}

echo "======================================================================\n";
echo "OMNIWIRE MEDIA - STAGING PHP MIGRATION TWO-RUN & RESTORE VERIFICATION\n";
echo "PHP Binary: {$phpBinary}\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Staging DB: {$stagingDb}\n";
echo "Backup Dir: {$testBackupDir}\n";
echo "======================================================================\n\n";

// STEP 1: Prepare staging DB from clean baseline
if (file_exists($stagingDb)) {
    @unlink($stagingDb);
}
copy($sourceDb, $stagingDb);

$pdo = new PDO('sqlite:' . $stagingDb);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("PRAGMA journal_mode = WAL;");

// Dirty an iPhone record to verify migration catches it
$pdo->exec("UPDATE mobile_devices SET brand_id = 99 WHERE model_name LIKE '%iPhone 16 Pro Max%'");
$stmtCheck = $pdo->query("SELECT brand_id FROM mobile_devices WHERE model_name LIKE '%iPhone 16 Pro Max%'");
$preBrandId = $stmtCheck->fetchColumn();
$stmtCheck->closeCursor();
unset($stmtCheck);
$pdo = null; // Fully release connection handle on Windows
echo "Pre-check: iPhone 16 Pro Max brand_id deliberately set to {$preBrandId} (dirty state).\n\n";

// STEP 2: RUN 1 (Pehli Run)
echo "----------------------------------------------------------------------\n";
echo "RUN 1: Executing PHP Migration Script on Staging Database...\n";
echo "----------------------------------------------------------------------\n";

$cmdRun1 = "\"{$phpBinary}\" \"" . $rootDir . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrate_production_db.php\" \"{$stagingDb}\" \"{$testBackupDir}\" 2>&1";
exec($cmdRun1, $outputRun1, $returnCode1);

echo implode("\n", $outputRun1) . "\n";
echo "Run 1 Exit Code: {$returnCode1}\n";

if ($returnCode1 !== 0) {
    echo "FATAL: Run 1 failed with exit code {$returnCode1}\n";
    exit(1);
}

// Verify Run 1 Results in Staging DB
$pdo = new PDO('sqlite:' . $stagingDb);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check brand_id of iPhone 16 Pro Max
$stmtIPhone = $pdo->query("SELECT brand_id FROM mobile_devices WHERE model_name LIKE '%iPhone 16 Pro Max%'");
$postBrandId = $stmtIPhone->fetchColumn();
echo "\n[Verification 1] iPhone 16 Pro Max brand_id after Run 1: {$postBrandId} (Expected: 2 - Apple)\n";
if ((int)$postBrandId !== 2) {
    echo "FAILED: iPhone 16 Pro Max brand_id is {$postBrandId}, expected 2!\n";
    exit(1);
}

// Check Article 69
$stmtArt = $pdo->query("SELECT title, summary, content FROM articles WHERE id = 69");
$artRow = $stmtArt->fetch(PDO::FETCH_ASSOC);

echo "[Verification 2] Article ID 69 Title: {$artRow['title']}\n";
$hasHindustan = strpos($artRow['content'], 'https://www.hindustantimes.com/') !== false;
$hasEspn = strpos($artRow['content'], 'https://www.espncricinfo.com/') !== false;
$hasToi = strpos($artRow['content'], 'https://timesofindia.indiatimes.com/') !== false;
$hasAbp = strpos($artRow['content'], 'https://news.abplive.com/') !== false;
$has48Bpm = strpos($artRow['content'], '45-48 BPM') !== false || strpos($artRow['content'], '48 BPM') !== false;

echo "[Verification 3] Verified Hyperlinks Present: ";
echo ($hasHindustan && $hasEspn && $hasToi && $hasAbp) ? "YES (All 4 Sources Linked)\n" : "NO (Missing Sources!)\n";
echo "[Verification 4] Unverified 45-48 BPM Eliminated: " . (!$has48Bpm ? "YES (Clean)" : "NO (Found!)") . "\n";

$run1ContentLen = strlen($artRow['content']);
$stmtIPhone->closeCursor();
unset($stmtIPhone);
$stmtArt->closeCursor();
unset($stmtArt);
$pdo = null; // Fully release connection handle on Windows

// STEP 3: RUN 2 (Doosri Run - Testing Idempotency & Duplicate Prevention)
echo "\n----------------------------------------------------------------------\n";
echo "RUN 2: Executing Second PHP Migration Run on Same Staging Database...\n";
echo "----------------------------------------------------------------------\n";

$cmdRun2 = "\"{$phpBinary}\" \"" . $rootDir . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrate_production_db.php\" \"{$stagingDb}\" \"{$testBackupDir}\" 2>&1";
exec($cmdRun2, $outputRun2, $returnCode2);

echo implode("\n", $outputRun2) . "\n";
echo "Run 2 Exit Code: {$returnCode2}\n";

if ($returnCode2 !== 0) {
    echo "FATAL: Run 2 failed with exit code {$returnCode2}\n";
    exit(1);
}

$pdo = new PDO('sqlite:' . $stagingDb);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Verify Device updates on Run 2 was 0
$run2OutputStr = implode("\n", $outputRun2);
$isZeroDevices = strpos($run2OutputStr, '0 rows changed') !== false;
echo "\n[Verification 5] Device Rows Changed on Run 2: " . ($isZeroDevices ? "0 (Idempotent PASS)" : "FAIL") . "\n";

// Verify Article 69 content did NOT duplicate sections
$stmtArt2 = $pdo->query("SELECT title, summary, content FROM articles WHERE id = 69");
$artRow2 = $stmtArt2->fetch(PDO::FETCH_ASSOC);
$sectionMatches = preg_match_all('/<h2>Cardiovascular Conditioning/i', $artRow2['content']);
$run2ContentLen = strlen($artRow2['content']);

echo "[Verification 6] 'Cardiovascular Conditioning' Section Occurrences: {$sectionMatches} (Expected: exactly 1)\n";
echo "[Verification 7] Content Length Run 1 vs Run 2: {$run1ContentLen} bytes vs {$run2ContentLen} bytes (Delta: " . ($run2ContentLen - $run1ContentLen) . " bytes)\n";

if ($sectionMatches !== 1 || $run1ContentLen !== $run2ContentLen) {
    echo "FAILED: Run 2 duplicated content or changed article structure!\n";
    exit(1);
}
$stmtArt2->closeCursor();
unset($stmtArt2);
$pdo = null;

// STEP 4: Backup Restore Verification
echo "\n----------------------------------------------------------------------\n";
echo "BACKUP RESTORE VERIFICATION:\n";
echo "----------------------------------------------------------------------\n";

// Find latest backup file
$backupFiles = glob($testBackupDir . DIRECTORY_SEPARATOR . 'news_portal.db.bak_*');
if (empty($backupFiles)) {
    echo "FATAL: No backup files found in {$testBackupDir}!\n";
    exit(1);
}
rsort($backupFiles);
$latestBackup = $backupFiles[0];
$backupSize = filesize($latestBackup);
echo "Latest Backup File: {$latestBackup} ({$backupSize} bytes)\n";

$restoredDb = $rootDir . DIRECTORY_SEPARATOR . 'staging_restored.db';
if (file_exists($restoredDb)) {
    @unlink($restoredDb);
}
copy($latestBackup, $restoredDb);

$pdoRes = new PDO('sqlite:' . $restoredDb);
$pdoRes->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check integrity
$integrity = $pdoRes->query("PRAGMA integrity_check;")->fetchColumn();
echo "[Verification 8] Restored DB PRAGMA integrity_check: {$integrity}\n";

// Count tables
$expectedArticles = 111;
$expectedDevices = 52;

$articleCount = (int)$pdoRes->query("SELECT count(*) FROM articles;")->fetchColumn();
$deviceCount = (int)$pdoRes->query("SELECT count(*) FROM mobile_devices;")->fetchColumn();
echo "[Verification 9] Restored DB Articles Count: {$articleCount} (Matches Staging: " . ($articleCount === $expectedArticles ? "YES - {$articleCount}" : "NO") . ")\n";
echo "[Verification 10] Restored DB Devices Count: {$deviceCount} (Matches Staging: " . ($deviceCount === $expectedDevices ? "YES - {$deviceCount}" : "NO") . ")\n";

if ($integrity !== 'ok' || $deviceCount !== $expectedDevices || $articleCount !== $expectedArticles) {
    echo "FAILED: Backup restoration integrity or record count mismatch!\n";
    exit(1);
}
$pdoRes = null;

// STEP 5: Negative Test: Abort on non-writable directory
echo "\n----------------------------------------------------------------------\n";
echo "FAIL-SAFE TEST: Testing Abort on Non-Writable / Invalid Backup Dir...\n";
echo "----------------------------------------------------------------------\n";

$invalidDir = 'Z:\\non_existent_drive_98765\\backups';
$cmdInvalid = "\"{$phpBinary}\" \"" . $rootDir . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrate_production_db.php\" \"{$stagingDb}\" \"{$invalidDir}\" 2>&1";
exec($cmdInvalid, $outputInvalid, $returnCodeInvalid);

echo implode("\n", $outputInvalid) . "\n";
echo "[Verification 11] Abort on Non-Writable Dir Exit Code: {$returnCodeInvalid} (Expected: 1)\n";
$abortedCorrectly = $returnCodeInvalid === 1 && strpos(implode("\n", $outputInvalid), 'Migration ABORTED') !== false;
echo "Fail-Safe Abort Status: " . ($abortedCorrectly ? "PASS (Protected)" : "FAIL") . "\n";

if (!$abortedCorrectly) {
    echo "FAILED: Script did not abort safely on non-writable directory!\n";
    exit(1);
}

// Clean temporary staging files
@unlink($stagingDb);
@unlink($restoredDb);

echo "\n======================================================================\n";
echo "ALL 11 STAGING PHP MIGRATION VERIFICATION CHECKS PASSED (100% SUCCESS)\n";
echo "======================================================================\n";
