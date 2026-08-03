# Implementation Plan: Endpoint Penerima SSO `GET /sso`

## Requirements Restatement

Menambahkan endpoint publik `GET /sso` yang menerima handoff otentikasi dari **E-Management**. Alur validasi berurutan (fail-fast, tiap langkah harus lolos sebelum lanjut ke berikutnya):

1. Verifikasi tanda tangan HMAC-SHA256 (`hash_equals`, kunci dari config) → tolak jika tidak cocok.
2. Periksa `expires_at` → tolak jika sudah lewat.
3. Periksa nonce di tabel baru `sso_tickets` → tolak jika sudah pernah dipakai; simpan jika baru.
4. Cari `User` berdasarkan `nim` → tolak + log jika tidak ditemukan; **JANGAN** buat user baru.
5. Jika semua lolos: `Auth::login($user)` lalu redirect ke dashboard konseling (`user.konseling.dashboard`, sesuai temuan laporan sebelumnya).

Test: 1 sukses + 4 gagal (signature salah, kedaluwarsa, nonce reuse, nim tidak ditemukan).

## Patterns to Mirror

| Category | Source | Pattern |
|---|---|---|
| Login + session regen | `app/Http/Controllers/Auth/AuthenticatedSessionController.php:26-33` | `$request->session()->regenerate()` setelah otentikasi berhasil, redirect via named route |
| Ulid primary key untuk tabel log/tiket | `database/migrations/2024_02_15_194926_create_inventory_logs_table.php:14` | `$table->ulid('id')->primary()` — dipakai untuk tabel log/transaksi, bukan tabel entitas inti |
| Logging gagal/error | `app/Http/Controllers/QrController.php:62,85,125` | `Log::error('<pesan Indonesia deskriptif>: ' . $th->getMessage())` |
| Config service pihak ketiga | `config/services.php` | Kredensial dari `env()`, dikelompokkan per integrasi |
| Route publik di luar grup `auth` | `routes/web.php:62-64` | `Route::middleware('guest')->group(...)` didaftarkan sebelum grup `auth` |
| Test Pest untuk auth flow | `tests/Feature/Auth/AuthenticationTest.php` | `test('...', function () { ... $this->assertAuthenticated(); $response->assertRedirect(...); })`, pakai `User::factory()->create()` |

Tidak ada pola *service class* terpisah untuk validasi domain di repo ini (controller di sini cenderung "fat controller", lihat `QrController`) — saya akan mengikuti gaya itu: logika validasi SSO ditulis sebagai method privat dalam satu controller, bukan class service terpisah, agar konsisten dengan codebase yang ada meskipun rule PHP global menyarankan service terpisah.

## Keputusan Desain (asumsi default — beri tahu saya jika ingin diubah)

- **Format payload** (query string GET): `nim`, `expires_at` (unix timestamp detik), `nonce`, `signature` (hex HMAC-SHA256).
- **String yang ditandatangani**: `"{nim}|{expires_at}|{nonce}"` — urutan tetap, didokumentasikan di `config/sso.php` agar E-Management memakai format yang sama.
- **Kunci HMAC**: `config('sso.secret')` ← `env('SSO_SHARED_SECRET')`, ditambahkan ke `.env.example`.
- **Semua 4 kasus gagal → `abort(403)`** dengan pesan generik yang sama persis di response (mencegah oracle/probing — penyerang tidak bisa membedakan "signature salah" vs "nim tidak ada" dari response), tapi **log internal berbeda per kasus** untuk audit.
- **Anti race-condition nonce**: insert `sso_tickets` dilakukan lewat `try { create() } catch (QueryException unique-violation)`, bukan check-lalu-insert terpisah (menghindari TOCTOU jika dua request datang bersamaan dengan nonce sama).
- **Tidak ada pengecekan role eksplisit** pada user hasil pencarian `nim` — sesuai skema, hanya `Mahasiswa` yang punya `nim` terisi, jadi ini implicit filter yang cukup.

## Files to Change

| File | Action | Why |
|---|---|---|
| `database/migrations/2026_08_02_190000_create_sso_tickets_table.php` | CREATE | Tabel penyimpan nonce yang sudah dipakai, mencegah replay |
| `app/Models/SsoTicket.php` | CREATE | Model Eloquent tipis untuk tabel di atas (ulid key, `HasUlids`) |
| `config/sso.php` | CREATE | Kunci HMAC + dokumentasi format canonical string |
| `.env.example` | UPDATE | Tambah `SSO_SHARED_SECRET=` |
| `app/Http/Controllers/Auth/SsoLoginController.php` | CREATE | Endpoint `receive()` — orkestrasi 5 langkah validasi |
| `routes/web.php` | UPDATE | Daftarkan `GET /sso` di luar grup `auth`, di atas grup `guest` |
| `tests/Feature/Auth/SsoLoginTest.php` | CREATE | 1 test sukses + 4 test gagal |

## Tasks

### Task 1: Migration `sso_tickets`
- **Action**: `ulid('id')->primary()`, `string('nonce')->unique()`, `string('nim')->nullable()` (audit), `timestamp('used_at')`, `timestamps()`.
- **Mirror**: pola `inventory_logs`/`obat_logs` (ulid pk untuk tabel transaksional).
- **Validate**: `php artisan migrate --pretend` lalu `php artisan migrate`.

### Task 2: Model `SsoTicket`
- **Action**: `use HasUlids;`, `$fillable = ['nonce', 'nim', 'used_at']`, cast `used_at` ke `datetime`.
- **Mirror**: model log lain seperti `ObatLog` (ulid, fillable minimal).

### Task 3: `config/sso.php`
- **Action**: `return ['secret' => env('SSO_SHARED_SECRET')];` + docblock menjelaskan format canonical string `nim|expires_at|nonce`.
- **Mirror**: `config/services.php`.

### Task 4: `SsoLoginController@receive`
- **Action**: method privat berurutan persis sesuai 5 langkah di atas:
  1. `validateSignature()` — bangun canonical string, `hash_hmac('sha256', ..., config('sso.secret'))`, bandingkan dengan `hash_equals`.
  2. `validateExpiry()` — `now()->timestamp > (int) $expiresAt`.
  3. `consumeNonce()` — `SsoTicket::create()` dalam try/catch `QueryException` (kode error unique violation `23000`).
  4. `resolveUser()` — `User::where('nim', $nim)->first()`; jika null → `Log::warning('SSO: nim tidak ditemukan - ' . $nim)` lalu `abort(403)`.
  5. `Auth::login($user)` → `$request->session()->regenerate()` → `redirect()->route('user.konseling.dashboard')`.
  Validasi input dasar (`nim`, `expires_at` numeric, `nonce`, `signature` required) di awal method via `$request->validate([...])`.
- **Mirror**: `AuthenticatedSessionController::store()` untuk pola login+redirect; `QrController` untuk gaya `Log::error/warning`.
- **Validate**: manual test via `php artisan tinker` membuat URL bertanda tangan, cek redirect.

### Task 5: Route
- **Action**: `Route::get('/sso', [SsoLoginController::class, 'receive'])->name('sso.login');` ditaruh di `routes/web.php` **sebelum** `Route::middleware(['auth'])->group(...)`, sejajar dengan grup `guest` — tidak dibungkus middleware `auth`/`role` apa pun (harus publik agar E-Management bisa redirect browser mahasiswa yang belum punya sesi).
- **Mirror**: penempatan `Route::middleware('guest')->group(...)` di baris 62-64.
- **Catatan CSRF**: karena `GET`, `VerifyCsrfToken` tidak memeriksa method ini — tidak perlu menambah `$except`.

### Task 6: Tests (`tests/Feature/Auth/SsoLoginTest.php`)
- **Action**: helper lokal `buildSignedSsoUrl(array $overrides = [])` yang generate nim/expires_at/nonce/signature valid, lalu 5 test:
  1. `sukses login dan redirect ke dashboard konseling saat semua validasi lolos` — `assertAuthenticated()`, `assertRedirect(route('user.konseling.dashboard'))`, `assertDatabaseHas('sso_tickets', ['nonce' => ...])`.
  2. `tolak jika signature tidak cocok` — override `signature` jadi acak, `assertForbidden()`, `assertGuest()`.
  3. `tolak jika sudah kedaluwarsa` — `expires_at` di masa lalu (signature dihitung ulang untuk timestamp itu), `assertForbidden()`, `assertGuest()`.
  4. `tolak jika nonce sudah pernah dipakai` — hit endpoint sukses sekali, ulangi dengan nonce sama → `assertForbidden()` pada percobaan kedua, `assertDatabaseCount('sso_tickets', 1)`.
  5. `tolak dan tidak membuat user baru jika nim tidak ditemukan` — nim acak yang tidak ada di DB, `assertForbidden()`, `assertGuest()`, `assertDatabaseCount('users', 0)` (atau bandingkan count sebelum/sesudah), `Log::shouldReceive('warning')->once()` (via `Log::spy()`).
- **Mirror**: struktur `tests/Feature/Auth/AuthenticationTest.php`.
- **Validate**: `vendor/bin/pest tests/Feature/Auth/SsoLoginTest.php`.

## Validation

```bash
php artisan migrate
vendor/bin/pest tests/Feature/Auth/SsoLoginTest.php
vendor/bin/pest   # full suite, pastikan tidak ada regresi
vendor/bin/pint --test
```

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| E-Management memakai format canonical string berbeda | Medium | Dokumentasikan format persis di `config/sso.php`, koordinasikan dengan tim E-Management sebelum go-live |
| Clock skew antara server E-Management dan server ini membuat `expires_at` valid selalu dianggap kedaluwarsa | Low-Medium | Beri toleransi kecil (misal ±30 detik) — **belum termasuk di scope ini**, bisa ditambah kalau jadi masalah nyata |
| Endpoint publik jadi target brute-force nonce/signature | Low (HMAC 256-bit + `hash_equals` timing-safe) | Tidak perlu rate limit tambahan untuk MVP ini, tapi bisa ditambah `throttle` middleware kalau perlu |
| Nonce disimpan permanen di `sso_tickets` tanpa pembersihan | Low | Tidak dibersihkan di scope ini (data kecil, ulid), bisa ditambah command cleanup nanti jika perlu |

## Acceptance

- [ ] Migration `sso_tickets` berhasil dijalankan
- [ ] Endpoint `GET /sso` publik (tidak kena middleware `auth`)
- [ ] 5 test Pest baru lulus (1 sukses + 4 gagal)
- [ ] `vendor/bin/pint --test` bersih
- [ ] Tidak ada user baru dibuat pada kasus nim tidak ditemukan
- [ ] Log tercatat untuk kasus nim tidak ditemukan
