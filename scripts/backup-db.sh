#!/bin/bash
# ==============================================================================
# Automated Database Backup Script for E-Polbangtan HealtCare
# Target: Linux Debian VPS
# ==============================================================================

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_DIR="/var/backups/polbangtan_klinik_db"
DATE=$(date +"%Y%m%d_%H%M%S")
FILENAME="backup_klinik_${DATE}.sql.gz"
RETENTION_DAYS=14

mkdir -p "${BACKUP_DIR}"

# Ambil kredensial dari file .env
if [ -f "${PROJECT_DIR}/.env" ]; then
    DB_DATABASE=$(grep -E '^DB_DATABASE=' "${PROJECT_DIR}/.env" | cut -d '=' -f2 | tr -d ' "' || echo "polbangtan_healtcare")
    DB_USERNAME=$(grep -E '^DB_USERNAME=' "${PROJECT_DIR}/.env" | cut -d '=' -f2 | tr -d ' "' || echo "root")
    DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "${PROJECT_DIR}/.env" | cut -d '=' -f2 | tr -d ' "' || echo "")
else
    echo "[ERROR] File .env tidak ditemukan di ${PROJECT_DIR}"
    exit 1
fi

echo "[$(date)] Memulai pencadangan database ${DB_DATABASE}..."

cd "${PROJECT_DIR}"
docker compose exec -T klinik-db mysqldump \
    -u"${DB_USERNAME}" \
    -p"${DB_PASSWORD}" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    "${DB_DATABASE}" | gzip > "${BACKUP_DIR}/${FILENAME}"

echo "[$(date)] Backup berhasil disimpan di: ${BACKUP_DIR}/${FILENAME}"

# Hapus backup yang lebih lama dari masa retensi
find "${BACKUP_DIR}" -type f -name "backup_klinik_*.sql.gz" -mtime +"${RETENTION_DAYS}" -delete
echo "[$(date)] Pembersihan backup lama (> ${RETENTION_DAYS} hari) selesai."
