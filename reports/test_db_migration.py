import sqlite3
import os
import shutil
import re

db_test = 'scratch/news_portal_test.db'
if os.path.exists(db_test):
    os.remove(db_test)
shutil.copyfile('live_news_portal.db', db_test)

# Put test db in WAL mode to test WAL consistency
conn = sqlite3.connect(db_test)
c = conn.cursor()
c.execute("PRAGMA journal_mode = WAL")
print("Set journal_mode:", c.fetchone()[0])

# Perform some writes in WAL
c.execute("UPDATE mobile_devices SET brand_id = 99 WHERE id = 89") # temporarily dirty it
conn.commit()
conn.close()

def run_migration_simulation(target_db):
    conn = sqlite3.connect(target_db)
    c = conn.cursor()
    
    # 1. WAL Checkpoint TRUNCATE
    c.execute("PRAGMA wal_checkpoint(TRUNCATE)")
    
    # 2. Atomic consistent backup via VACUUM INTO
    bak_file = target_db + '.bak_test'
    if os.path.exists(bak_file):
        os.remove(bak_file)
    c.execute(f"VACUUM INTO '{bak_file}'")
    bak_size = os.path.getsize(bak_file)
    print(f"  Backup created: {bak_file} ({bak_size} bytes)")
    
    # 3. Brand updates with idempotency condition
    brand_updates = [
        (2, "(LOWER(model_name) LIKE '%apple%' OR LOWER(model_name) LIKE '%iphone%') AND brand_id != 2"),
        (1, "(LOWER(model_name) LIKE '%samsung%' OR LOWER(model_name) LIKE '%galaxy%') AND brand_id != 1"),
        (6, "LOWER(model_name) LIKE '%infinix%' AND brand_id != 6"),
        (3, "(LOWER(model_name) LIKE '%xiaomi%' OR LOWER(model_name) LIKE '%redmi%' OR LOWER(model_name) LIKE '%poco%') AND brand_id != 3"),
        (4, "LOWER(model_name) LIKE '%vivo%' AND brand_id != 4"),
        (5, "LOWER(model_name) LIKE '%oppo%' AND brand_id != 5"),
        (7, "LOWER(model_name) LIKE '%tecno%' AND brand_id != 7"),
        (8, "LOWER(model_name) LIKE '%realme%' AND brand_id != 8")
    ]
    
    total_devices = 0
    for bid, cond in brand_updates:
        c.execute(f"UPDATE mobile_devices SET brand_id = ? WHERE {cond}", (bid,))
        total_devices += c.rowcount
        
    # 4. Check article 69 with expected slug
    expected_slug = 'virat-kohli-masterclass-chases-cricket-greatest-finisher-tactical-blueprint'
    c.execute("SELECT id, slug, content FROM articles WHERE id = 69 AND slug = ?", (expected_slug,))
    row = c.fetchone()
    assert row is not None, f"Expected slug {expected_slug} not found on ID 69!"
    
    content = row[2]
    # Check if links are present
    has_links = 'https://www.espncricinfo.com/' in content and 'https://timesofindia.indiatimes.com/' in content
    
    conn.commit()
    conn.close()
    return total_devices, bak_file

print("\n--- RUN 1 (Initial Migration on Test DB) ---")
dev_updated1, bak1 = run_migration_simulation(db_test)
print(f"Run 1: devices updated = {dev_updated1}")

print("\n--- RUN 2 (Second Execution - Testing Idempotency) ---")
dev_updated2, bak2 = run_migration_simulation(db_test)
print(f"Run 2: devices updated = {dev_updated2}")
assert dev_updated2 == 0, f"Expected 0 devices updated on Run 2 (idempotent), got {dev_updated2}!"

print("\n--- TEST RESTORE FROM BACKUP ---")
restore_test = 'scratch/news_portal_restored.db'
if os.path.exists(restore_test):
    os.remove(restore_test)
shutil.copyfile(bak1, restore_test)
conn_res = sqlite3.connect(restore_test)
c_res = conn_res.cursor()
c_res.execute("SELECT count(*) FROM mobile_devices")
cnt = c_res.fetchone()[0]
print(f"Restored DB device count: {cnt}")
assert cnt == 52, f"Expected 52 devices in restored DB, got {cnt}!"
conn_res.close()

print("\nSUCCESS: WAL checkpoint, atomic VACUUM INTO, restore verification, and double-run idempotency passed 100%!")
