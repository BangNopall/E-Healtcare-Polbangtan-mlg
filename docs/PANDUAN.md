# 📖 PANDUAN PENGGUNAAN (USER GUIDE)

Website ini memiliki sistem *Role-Based Access Control* (RBAC) dengan beberapa peran utama: **Mahasiswa, Karyawan, Admin, dan Psikolog**.

## 1. Modul Autentikasi & Profil (Semua Pengguna)
*   **SSO Login**: Pengguna dapat masuk menggunakan Single Sign-On (SSO) dari E-Management Polbangtan.
*   **Lengkapi Profil**: Setelah login pertama kali, pengguna wajib melengkapi data profil sebelum mengakses fitur lain:
    *   **DMTI (Data Pribadi)**: Meliputi NIK, No. BPJS, No. HP, Tempat/Tanggal Lahir, Jenis Kelamin, Usia, dan Golongan Darah.
    *   **CDMI (Data Akademik/Asrama)**: Meliputi NIM, Program Studi (Prodi), Blok Asrama, dan Nomor Ruangan.
    *   **RPD (Riwayat Penyakit Dahulu)**: Fitur untuk mengunggah dokumen riwayat penyakit (format PDF).
    *   **Avatar**: Pengguna dapat memperbarui foto profil maksimal 10MB.

## 2. Modul Mahasiswa
Mahasiswa memiliki akses khusus ke fitur bimbingan dan konseling psikologi.
*   **Dashboard Konseling**: Halaman ringkasan aktivitas konseling mahasiswa.
*   **Kode QR Bimbingan**: Menampilkan QR Code unik milik mahasiswa yang digunakan untuk absensi saat mengikuti jadwal bimbingan dengan *Senso* (Senior Asuh/Mentor).
*   **Feedback Bimbingan**: Mahasiswa dapat mengisi *form* umpan balik (*feedback*) terkait bimbingan yang telah dilaksanakan menggunakan tautan/token khusus, serta melihat kembali riwayat *feedback* yang telah dikirimkan.
*   **Kode QR Konsultasi**: Menampilkan QR Code untuk dipindai oleh Psikolog saat sesi konsultasi psikologi tatap muka.
*   **Riwayat Konsultasi**: Mahasiswa dapat melihat daftar dan detail riwayat konsultasi psikologi yang telah dilakukan (termasuk keluhan, saran, dan rencana tindak lanjut dari psikolog).

## 3. Modul Admin & Psikolog (Bimbingan Konseling)
Psikolog dan Admin memiliki akses untuk mengelola jadwal bimbingan dan hasil konsultasi.
*   **Data Senso**: 
    *   Menetapkan mahasiswa tertentu sebagai *Senso* (Senior Asuh/Mentor).
    *   Menetapkan "Anak Asuh" (Siswa) ke masing-masing *Senso*.
*   **Jadwal Bimbingan**: 
    *   Membuat jadwal bimbingan baru (Tanggal & Materi).
    *   Melihat daftar *Presensi Bimbingan* mahasiswa.
*   **Kamera Scanner (QR Code)**:
    *   **Kamera Bimbingan**: Fitur *scanner* untuk memindai QR Code mahasiswa guna mencatat kehadiran/absensi bimbingan.
    *   **Kamera Konsultasi**: Fitur *scanner* untuk memulai sesi konsultasi dengan mahasiswa. Setelah dipindai, psikolog akan diarahkan ke form konsultasi.
*   **Form & Riwayat Konsultasi**: 
    *   Mengisi form hasil konsultasi (Keluhan, Metode, Diagnosa, Prognosis, Intervensi, Saran, Rencana Tindak Lanjut).
    *   Melihat dan memfilter seluruh riwayat konsultasi mahasiswa.
*   **Riwayat Feedback**: Membaca *feedback* evaluasi yang dikirimkan oleh anak asuh (mahasiswa) terkait pelaksanaan bimbingan.
*   **Export/Cetak Laporan**: Mencetak laporan riwayat konsultasi dan *feedback* dalam format **PDF** maupun **Excel** dengan filter per-bulan.

## 4. Modul Manajemen Pengguna (Khusus Admin)
Admin utama memiliki kontrol penuh terhadap *master data* pengguna.
*   **Manajemen Mahasiswa**: Melihat daftar mahasiswa, mencari/memfilter, mereset password, mengubah foto profil, menghapus data, serta memperbarui data akademik (DMTI/CDMI) mahasiswa.
*   **Manajemen Petugas**: Mengelola akun untuk staf, psikolog, atau tenaga medis lainnya.
*   **Manajemen Karyawan**: Melihat daftar karyawan di lingkungan kampus.

*(Catatan: Terdapat juga modul "Kesehatan" untuk rekam medis & surat keterangan sakit/sehat yang saat ini sepertinya masih dalam status pengembangan/disembunyikan dalam sistem).*

---

# 🛠️ PANDUAN TEKNIS (TECHNICAL GUIDE)

Untuk *developer* atau tim IT yang akan memelihara aplikasi ini, berikut adalah panduan teknis terkait arsitektur proyek:

## 1. Stack Teknologi
*   **Framework**: Laravel 13.0 (PHP 8.2+)
*   **Frontend**: Alpine.js, Tailwind CSS v4, Flowbite, dan AOS (Animate On Scroll). Di-*bundle* menggunakan Vite.
*   **Database ORM**: Eloquent ORM (MySQL/MariaDB).
*   **Library Tambahan Pendukung**:
    *   `barryvdh/laravel-dompdf`: Untuk *export* laporan ke PDF.
    *   `maatwebsite/excel`: Untuk *export* laporan ke format Excel (.xlsx).
    *   `simplesoftwareio/simple-qrcode`: Generator QR Code untuk sistem absensi/konsultasi.

## 2. Arsitektur Database & Relasi Utama
*   **Users**: Tabel sentral yang menyimpan akun. Kolom boolean seperti `dmti`, `cdmi`, dan `senso` bertindak sebagai *flag* kelengkapan data dan status peran.
*   **DMTI & CDMI**: Memiliki relasi *One-to-One* dengan `Users`.
*   **RPD**: Memiliki relasi *One-to-Many* dengan `Users` (Satu *user* bisa mengunggah banyak file RPD).
*   **BimbinganSenso**: Tabel *pivot/mapping* yang menghubungkan `senso_id` (User mentor) dengan `siswa_id` (User anak asuh).
*   **DataPsikolog**: Menyimpan catatan medis/psikologis. Berelasi *One-to-Many* (Satu user mahasiswa memiliki banyak riwayat konsultasi).
*   **FeedbackBimbingan**: Mencatat *feedback* dengan relasi ke `jadwal_id`, `siswa_id`, dan `senso_id`.

## 3. Sistem Keamanan & Alur Aplikasi
*   **Middleware Role**: Aplikasi menggunakan *custom middleware* `role:Admin`, `role:Mahasiswa`, `role:Psikolog` untuk melindungi *endpoint*.
*   **Kelengkapan Profil Middleware**: Terdapat *middleware* `profile.complete` yang memaksa pengguna yang baru masuk untuk melengkapi data `DMTI` dan `CDMI` mereka sebelum bisa mengakses rute aplikasi yang lain.
*   **QR Tokenization**: QR Code yang di-*generate* bukan data statis mentah, namun kemungkinan men-*trigger* token unik untuk validasi *form konsultasi* atau absensi di sistem. Hal ini terlihat pada alur `Route::get('/form/konsultasi/{token}')`.
*   **Single Sign-On (SSO)**: Endpoint `/sso` dikonfigurasi untuk menerima *handoff* sesi dari sistem E-Management pusat Polbangtan.

## 4. Struktur Direktori Penting
*   `app/Http/Controllers/ProfileController.php`: Menangani logika validasi kelengkapan data (DMTI, CDMI, dan upload RPD).
*   `app/Http/Controllers/BimbinganKonselingController.php`: Controller raksasa yang menangani *core-logic* sistem psikologi (Penentuan *Senso*, Jadwal, *Feedback*, Hasil Konsultasi, hingga fungsi Export PDF/Excel).
*   `app/Http/Controllers/QrController.php`: Menangani pembuatan dan pemindaian *QR Code*.
*   `routes/web.php`: Pusat *routing*, sangat terstruktur menggunakan pembagian *Group* dan *Prefix* berdasarkan *Role*.
