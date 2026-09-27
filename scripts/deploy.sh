#!/bin/bash
# ==============================================================================
# Automated Production Deployment Script for Debian Linux VPS
# Project: E-Polbangtan HealtCare (E-Klinik)
# ==============================================================================

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${PROJECT_DIR}"

echo "===================================================================="
echo "[$(date)] Memulai deployment produksi E-Polbangtan HealtCare..."
echo "===================================================================="

# 1. Pastikan file .env ada
if [ ! -f ".env" ]; then
    echo "[ERROR] File .env tidak ditemukan! Silakan salin dari .env.production.example"
    exit 1
fi

# 2. Ambil update kode terbaru dari branch produksi jika memakai git
if [ -d ".git" ]; then
    echo "[1/6] Mengambil pembaruan git terbaru..."
    git pull origin release/v2.0.0 || git pull origin main || git pull origin master || echo "Git pull dilewati."
fi

# 3. Build image produksi dengan Docker Compose
echo "[2/6] Membangun image Docker produksi (Node.js Vite + PHP 8.3-FPM)..."
docker compose build --pull

# 4. Jalankan seluruh kontainer di latar belakang
echo "[3/6] Menjalankan kontainer produksi..."
docker compose up -d --remove-orphans

# 5. Jalankan migrasi database terisolasi
echo "[4/6] Menjalankan migrasi database terisolasi..."
docker compose exec -T klinik-app php artisan migrate --force

# 6. Optimasi Cache Laravel 13
echo "[5/6] Memperbarui cache Laravel (config, route, view)..."
docker compose exec -T klinik-app php artisan config:cache
docker compose exec -T klinik-app php artisan route:cache
docker compose exec -T klinik-app php artisan view:cache

# 7. Restart worker agar memuat kode baru
echo "[6/6] Melakukan restart antrean worker..."
docker compose exec -T klinik-app php artisan queue:restart

echo "===================================================================="
echo "[$(date)] Deployment Sukses! Status Kontainer E-Klinik:"
echo "===================================================================="
docker compose ps
