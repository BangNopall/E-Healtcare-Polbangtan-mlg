#!/bin/bash
# ==============================================================================
# Database Restoration Script for E-Polbangtan HealtCare
# Usage: ./scripts/restore-db.sh /path/to/backup_klinik_YYYYMMDD_HHMMSS.sql.gz
# ==============================================================================

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_FILE="$1"

if [ -z "${BACKUP_FILE}" ] || [ ! -f "${BACKUP_FILE}" ]; then
    echo "Penggunaan: $0 <path_ke_file_backup.sql.gz>"
    exit 1
fi

if [ ! -f "${PROJECT_DIR}/.env" ]; then
    echo "[ERROR] File .env tidak ditemukan di ${PROJECT_DIR}"
    exit 1
fi

DB_DATABASE=$(grep -E '^DB_DATABASE=' "${PROJECT_DIR}/.env" | cut -d '=' -f2 | tr -d ' "' || echo "polbangtan_healtcare")
DB_USERNAME=$(grep -E '^DB_USERNAME=' "${PROJECT_DIR}/.env" | cut -d '=' -f2 | tr -d ' "' || echo "root")
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "${PROJECT_DIR}/.env" | cut -d '=' -f2 | tr -d ' "' || echo "")

echo "PERINGATAN: Restorasi akan menimpa seluruh data pada database '${DB_DATABASE}'!"
read -p "Apakah Anda yakin ingin melanjutkan? (y/N): " -r CONFIRM
if [[ ! $CONFIRM =~ ^[Yy]$ ]]; then
    echo "Restorasi dibatalkan."
    exit 0
fi

echo "[$(date)] Memulai proses restorasi database dari: ${BACKUP_FILE}..."

cd "${PROJECT_DIR}"
if [[ "${BACKUP_FILE}" == *.gz ]]; then
    gunzip -c "${BACKUP_FILE}" | docker compose exec -T klinik-db mysql -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}"
else
    docker compose exec -T klinik-db mysql -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" < "${BACKUP_FILE}"
fi

echo "[$(date)] Restorasi database ${DB_DATABASE} berhasil diselesaikan."
