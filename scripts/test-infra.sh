#!/bin/bash
# ==============================================================================
# Automated Infrastructure & Smoke Integration Testing (TDD)
# Project: E-Polbangtan HealtCare (E-Klinik)
# ==============================================================================

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${PROJECT_DIR}"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "===================================================================="
echo " [TDD] Menjalankan Pengujian Infrastruktur Docker & Integrasi Sistem"
echo "===================================================================="

PASSED=0
FAILED=0

assert_success() {
    local test_name="$1"
    local command="$2"

    printf "Testing: %-60s " "${test_name}..."
    if eval "${command}" > /dev/null 2>&1; then
        echo -e "[${GREEN}PASS${NC}]"
        PASSED=$((PASSED+1))
    else
        echo -e "[${RED}FAIL${NC}]"
        FAILED=$((FAILED+1))
    fi
}

assert_contains() {
    local test_name="$1"
    local file="$2"
    local pattern="$3"

    printf "Testing: %-60s " "${test_name}..."
    if grep -qE "${pattern}" "${file}" 2>/dev/null; then
        echo -e "[${GREEN}PASS${NC}]"
        PASSED=$((PASSED+1))
    else
        echo -e "[${RED}FAIL${NC}]"
        FAILED=$((FAILED+1))
    fi
}

assert_not_contains() {
    local test_name="$1"
    local file="$2"
    local pattern="$3"

    printf "Testing: %-60s " "${test_name}..."
    if ! grep -qE "${pattern}" "${file}" 2>/dev/null; then
        echo -e "[${GREEN}PASS${NC}]"
        PASSED=$((PASSED+1))
    else
        echo -e "[${RED}FAIL${NC}]"
        FAILED=$((FAILED+1))
    fi
}

echo -e "\n${YELLOW}--- Tahap 1: Verifikasi File Konfigurasi (Static Guard) ---${NC}"

# 1. Pastikan port compose terbuka ke publik 8001
assert_contains "Port compose terbuka ke publik (\${DOCKER_APP_PORT:-8001}:80)" \
    "docker-compose.yml" 'DOCKER_APP_PORT:-8001'

# 2. Pastikan tidak ada nested mount pada Nginx klinik-web (menghindari runc mkdirat crash)
assert_success "Bebas dari nested mount /public/build di dalam klinik-web" \
    "! sed -n '/klinik-web:/,/klinik-worker:/p' docker-compose.yml | grep -q '/var/www/html/public/build'"

assert_contains "Volume build di-mount ke direktori independen /var/www/build:ro" \
    "docker-compose.yml" 'klinik_public_build:/var/www/build:ro'

# 3. Pastikan .env pada app tidak di-mount sebagai read-only (:ro)
assert_success "File .env pada klinik-app di-mount Read-Write (tanpa :ro)" \
    "sed -n '/klinik-app:/,/klinik-web:/p' docker-compose.yml | grep -E -- '- \./\.env:/var/www/html/\.env' | grep -v ':ro'"

# 4. Pastikan Nginx mengonfigurasi alias untuk /build/
assert_contains "Nginx default.conf memiliki alias /var/www/build/;" \
    "nginx/default.conf" 'alias /var/www/build/;'

# 5. Pastikan Nginx memblokir akses ke rpd_private
assert_contains "Nginx default.conf memblokir akses ke /storage/app/rpd_private/" \
    "nginx/default.conf" 'location \^~ /storage/app/rpd_private/'

# 6. Pastikan template .env.production.example mengatur SESSION_SECURE_COOKIE=false
assert_contains "Template .env.production.example SESSION_SECURE_COOKIE=false" \
    ".env.production.example" '^SESSION_SECURE_COOKIE=false'

echo -e "\n${YELLOW}--- Tahap 2: Pengujian Unit & Konfigurasi Laravel (Pest) ---${NC}"
assert_success "Pest ProductionConfigTest lolos (5 assertions)" \
    "php artisan test --filter=ProductionConfigTest"

echo -e "\n${YELLOW}--- Tahap 3: Verifikasi Layanan Kontainer Aktif (Dynamic Test) ---${NC}"

# Cek apakah docker compose sedang berjalan
if docker compose ps --status running --format json 2>/dev/null | grep -q "polbangtan_klinik_app"; then
    echo "Kontainer Docker terdeteksi aktif. Menjalankan pengujian dinamis..."

    APP_PORT=$(grep -E '^DOCKER_APP_PORT=' .env 2>/dev/null | cut -d '=' -f2 | tr -d ' "' || echo "8001")

    # Uji endpoint HTTP login
    assert_success "HTTP GET /login merespons HTTP 200" \
        "curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:${APP_PORT}/login | grep -q '200'"

    # Uji Security Header Nginx
    assert_success "Security Header X-Frame-Options: SAMEORIGIN aktif" \
        "curl -s -I http://127.0.0.1:${APP_PORT}/login | grep -qi 'X-Frame-Options: SAMEORIGIN'"

    # Uji FastCGI dan migrasi database di dalam kontainer
    assert_success "Seluruh 93 Test Pest lolos di dalam kontainer produksi" \
        "docker compose exec -T klinik-app php artisan test"
else
    echo "Catatan: Kontainer Docker belum aktif di host ini. Dynamic smoke test akan otomatis aktif saat 'scripts/deploy.sh' dijalankan di VPS."
fi

echo -e "\n===================================================================="
echo -e "Hasil Pengujian Infrastruktur: ${GREEN}${PASSED} Lolos${NC}, ${RED}${FAILED} Gagal${NC}"
echo "===================================================================="

if [ $FAILED -gt 0 ]; then
    exit 1
fi
