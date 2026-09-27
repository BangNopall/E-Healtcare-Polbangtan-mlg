# Panduan Lengkap Deploy Production di VPS Linux Debian (RAM 6GB)
## Sistem E-Polbangtan HealtCare (E-Klinik Polbangtan Malang)

Buku panduan ini disusun oleh Senior System Engineer untuk memandu deployment sistem **E-Polbangtan HealtCare (E-Klinik)** (Laravel 13, PHP 8.4-FPM, MySQL 8.0, Redis 7, Nginx) ke server VPS berbasis **Linux Debian 11 (Bullseye) atau Debian 12 (Bookworm)** dengan spesifikasi RAM 6GB.

---

## 1. Spesifikasi Server & Alokasi Resource (6GB RAM)

- **Sistem Operasi**: Linux Debian 11 / Debian 12 (64-bit)
- **CPU**: Minimal 2 vCPU
- **RAM**: 6 GB (Total batas kontainer ~4.9 GB, menyisakan >1.1 GB untuk OS kernel & disk buffers)
- **Disk**: Minimal 25 GB SSD/NVMe
- **Domain**: Misal `klinik.polbangtanmalang.ac.id` (A record diarahkan ke IP Publik VPS)
- **Port Publik**: Port `8001` (dapat diubah via `DOCKER_APP_PORT`), berjalan harmonis berdampingan dengan E-Management (Port 8000).

### Rincian Batas Resource Kontainer (`docker-compose.yml`)
| Service | Kontainer | CPU Limit | Memory Limit | Peran |
|---|---|---|---|---|
| `klinik-app` | `polbangtan_klinik_app` | 2.0 vCPU | 1536 MB (1.5 GB) | Core PHP 8.4-FPM Runtime |
| `klinik-web` | `polbangtan_klinik_web` | 1.0 vCPU | 256 MB | Nginx Web Server & Static Proxy |
| `klinik-worker` | `polbangtan_klinik_worker` | 1.0 vCPU | 768 MB | Queue Worker Redis |
| `klinik-scheduler` | `polbangtan_klinik_scheduler` | 0.5 vCPU | 384 MB | Cron Runner (`sso:prune-tickets`) |
| `klinik-redis` | `polbangtan_klinik_redis` | 0.5 vCPU | 512 MB | In-Memory Cache, Session & Queue |
| `klinik-db` | `polbangtan_klinik_db` | 2.0 vCPU | 1536 MB (1.5 GB) | Database MySQL 8.0 (Buffer Pool 1GB) |

---

## 2. Persiapan Server VPS Debian (Hardening & Tooling)

### 2.1. Update Sistem & Instalasi Paket Dasar
Login ke VPS via SSH sebagai `root` atau pengguna `sudo`:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git ufw htop unzip ca-certificates gnupg lsb-release
```

### 2.2. Konfigurasi Swap File (2 GB)
Swap 2GB sangat direkomendasikan sebagai jaring pengaman memori saat proses build frontend Vite atau ekspor dokumen massal:
```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile

# Jadikan permanen saat reboot server
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

### 2.3. Konfigurasi Firewall UFW
Buka port SSH, HTTP (80), HTTPS (443), dan Port aplikasi `8001` (jika diakses langsung tanpa Nginx Host). **Jangan membuka port MySQL (3306) atau Redis (6379) ke publik!**
```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 8001/tcp
sudo ufw enable
```

---

## 3. Instalasi Docker Engine & Docker Compose v2 di Debian

Instal Docker langsung dari repositori resmi Docker Inc.:

```bash
# 1. Tambahkan GPG key resmi Docker
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# 2. Tambahkan APT repository Docker
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# 3. Instal Docker Engine & Plugin Docker Compose
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# 4. Konfigurasi Batas Rotasi Log Docker Global (10MB x 3 file)
sudo tee /etc/docker/daemon.json <<EOF
{
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  }
}
EOF
sudo systemctl restart docker
sudo systemctl enable docker
```

---

## 4. Persiapan Proyek & Konfigurasi Lingkungan (`.env`)

### 4.1. Clone Repositori
Tempatkan proyek pada direktori standar `/var/www`:
```bash
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www
cd /var/www
git clone https://github.com/BangNopall/E-Healtcare-Polbangtan-mlg.git polbangtan-klinik
cd polbangtan-klinik
```

### 4.2. Konfigurasi File Lingkungan Produksi (`.env`)
Salin file template `.env.production.example` menjadi `.env`:
```bash
cp .env.production.example .env
```
Edit berkas `.env` menggunakan `nano`:
```bash
nano .env
```
Pastikan mengisi parameter berikut dengan nilai rahasia yang kuat:
1. `APP_KEY`: Dikosongkan sementara, akan di-generate via Artisan pada langkah berikutnya.
2. `APP_URL`: Set ke URL domain publik (misal: `https://klinik.polbangtanmalang.ac.id` atau `http://<IP-VPS>:8001`).
3. `DB_PASSWORD` & `DB_ROOT_PASSWORD`: Buat password yang kuat dan acak.
4. `REDIS_PASSWORD`: Buat password Redis yang kuat.
5. `SSO_SHARED_SECRET`: **PENTING** — samakan nilai ini persis dengan `SSO_SHARED_SECRET` di E-Management agar handoff SSO berjalan sukses.
6. `SESSION_SECURE_COOKIE`: Biarkan `false` jika mengakses via HTTP/IP. Ubah ke `true` HANYA jika domain sudah terpasang SSL/HTTPS.

Kunci hak akses file `.env` agar tidak terbaca oleh pengguna lain di server:
```bash
chmod 600 .env
```

---

## 5. Menjalankan Kontainer & Inisialisasi Database

### 5.1. Build & Start Kontainer Docker
Jalankan stack produksi:
```bash
docker compose build
docker compose up -d
```
Periksa status kontainer:
```bash
docker compose ps
```
Pastikan seluruh 6 kontainer berstatus `healthy` atau `Up`:
- `polbangtan_klinik_app` (PHP 8.4 FPM)
- `polbangtan_klinik_web` (Nginx di port 8001)
- `polbangtan_klinik_worker` (Queue Worker)
- `polbangtan_klinik_scheduler` (Cron Scheduler)
- `polbangtan_klinik_redis` (Redis 7)
- `polbangtan_klinik_db` (MySQL 8.0)

### 5.2. Generate Application Key
```bash
docker compose exec klinik-app php artisan key:generate
```

### 5.3. Migrasi Database Awal & Seeding Data Master
Jalankan migrasi database:
```bash
docker compose exec klinik-app php artisan migrate --force
```
Jalankan seeder master (HANYA dijalankan sekali pada inisialisasi awal database):
```bash
docker compose exec klinik-app php artisan db:seed --force
```

### 5.4. Optimasi Cache Laravel 13
```bash
docker compose exec klinik-app php artisan config:cache
docker compose exec klinik-app php artisan route:cache
docker compose exec klinik-app php artisan view:cache
```

### 5.5. Menjalankan Uji Infrastruktur TDD
Jalankan skrip uji infrastruktur untuk memastikan tidak ada celah deployment:
```bash
./scripts/test-infra.sh
```

---

## 6. Setup SSL / HTTPS via Host Nginx & Certbot (Let's Encrypt)

Jika Anda ingin mengakses sistem menggunakan domain resmi (misal: `klinik.polbangtanmalang.ac.id`), gunakan Nginx host sebagai Reverse Proxy:

### 6.1. Instalasi Nginx Host & Certbot
```bash
sudo apt install -y nginx certbot python3-certbot-nginx
```

### 6.2. Konfigurasi Virtual Host Nginx
Salin konfigurasi reverse proxy:
```bash
sudo cp nginx/host-vps-nginx.conf.example /etc/nginx/sites-available/klinik.polbangtanmalang.ac.id
```
Buka file dan sesuaikan `server_name`:
```bash
sudo nano /etc/nginx/sites-available/klinik.polbangtanmalang.ac.id
```
Aktifkan konfigurasi dan reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/klinik.polbangtanmalang.ac.id /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6.3. Generate Sertifikat SSL Otomatis
```bash
sudo certbot --nginx -d klinik.polbangtanmalang.ac.id
```
Pilih opsi **Redirect HTTP to HTTPS**. Setelah sertifikat aktif:
1. Buka kembali `.env`: `nano .env`
2. Ubah `SESSION_SECURE_COOKIE=true` dan `APP_URL=https://klinik.polbangtanmalang.ac.id`
3. Perbarui cache: `docker compose exec klinik-app php artisan config:cache`

---

## 7. Setup Backup Otomatis Database (Cronjob)

Jadwalkan pencadangan database MySQL terkompresi setiap malam pukul 02:30 WIB dengan retensi 14 hari:

1. Buka crontab root:
```bash
sudo crontab -e
```
2. Tambahkan baris jadwal berikut:
```cron
30 2 * * * /var/www/polbangtan-klinik/scripts/backup-db.sh >> /var/log/polbangtan_klinik_backup.log 2>&1
```

### Prosedur Restorasi Database (Disaster Recovery)
Jika terjadi kendala data dan ingin mengembalikan database dari backup:
```bash
./scripts/restore-db.sh /var/backups/polbangtan_klinik_db/backup_klinik_YYYYMMDD_HHMMSS.sql.gz
```

---

## 8. Panduan Pemeliharaan & Operasional Harian

### 8.1. Perintah Deployment Cepat (Update Aplikasi)
Setiap kali ada pembaruan kode di git, cukup jalankan skrip otomatis:
```bash
./scripts/deploy.sh
```

### 8.2. Memeriksa Log Kontainer
```bash
# Log seluruh layanan secara real-time
docker compose logs -f

# Log spesifik aplikasi Laravel
docker compose logs -f klinik-app

# Log antrean worker
docker compose logs -f klinik-worker

# Log web server Nginx
docker compose logs -f klinik-web
```

### 8.3. Merestart Layanan Tertentu
```bash
# Restart queue worker
docker compose restart klinik-worker

# Restart scheduler
docker compose restart klinik-scheduler

# Restart seluruh kontainer
docker compose restart
```

---

**Tim Pengembang & Operasional Sistem Polbangtan Malang**
