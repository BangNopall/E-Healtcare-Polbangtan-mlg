# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

E-Polbangtan HealtCare is a Laravel 13 application for Polbangtan Malang (an agricultural polytechnic dormitory/campus). It combines three otherwise-separate domains in one app:

- **Inventaris** — medicine (`obat`), tool (`alat`), and consumable stock management with deposit/withdraw ledgers.
- **Kesehatan** (medical/health) — student & staff medical records (rekam medis), doctor's letters (surat keterangan berobat/sakit/sehat, surat rujukan), QR-code-based check-in.
- **Konseling** (counseling/guidance) — student counseling sessions, guidance schedules (bimbingan), attendance (presensi), and feedback.

The UI text, route names, model names, and variable names are predominantly in **Indonesian** (`obat` = medicine, `alat` = tool, `bloks`/`prodis` = dorm blocks/study programs, `mahasiswa` = student, `karyawan` = staff, `dokter` = doctor, `petugas` = officer/nurse-admin staff). Follow this convention for any new domain code — Indonesian names for domain concepts, English for generic/framework-level code.

## Commands

```bash
# Install dependencies
composer install
npm install

# Local dev (run both concurrently)
php artisan serve
npm run dev              # Vite dev server (Tailwind v4 + Alpine.js + Flowbite)

# Build frontend assets for production
npm run build

# Database
php artisan migrate
php artisan migrate:fresh --seed   # rebuild schema + seed demo data (see DatabaseSeeder)

# Tests — this project uses Pest (not raw PHPUnit syntax) despite phpunit.xml driving the runner
php artisan test
vendor/bin/pest
vendor/bin/pest tests/Feature/ProfileTest.php     # single file
vendor/bin/pest --filter="test name or it() description"  # single test
vendor/bin/pest tests/Feature/Auth                # a directory

# Code style
vendor/bin/pint          # Laravel Pint formatter (fixes in place)
vendor/bin/pint --test   # check only, no changes
```

Tests use SQLite in-memory (`phpunit.xml`). `RefreshDatabase` + `TestCase` are bound globally to the `Feature` suite only, via `uses(TestCase::class, RefreshDatabase::class)->in('Feature')` in `tests/Pest.php` — add the same `uses()` binding there if creating tests outside `tests/Feature`.

## Architecture

### Role-based access control (no package — hand-rolled)

There is no Spatie-permissions or similar package. Roles are a plain string column (`users.role`) with values like `Admin`, `Dokter`, `Psikolog`, `Perawat`, `Karyawan`, `Mahasiswa`. Access control is entirely middleware-based:

- `App\Http\Middleware\EnsureUserHasRole` — aliased as `role` in `app/Http/Kernel.php`. Usage: `Route::middleware('role:Admin,Dokter')`. Checks `$user->hasRole($role)` (simple string equality) against any of the comma-separated roles and `abort(403)` if none match.
- `App\Http\Middleware\EnsureUserHasCDMI` / `EnsureUserHasDMTI` — aliased `cdmi` / `dmti`. Redirect to profile edit if the authenticated user's medical/personal-data record is incomplete (`cdmi_complete`/`dmti_complete` flags on `User`).
- Route groups in `routes/web.php` are organized by role bundle, not by controller — e.g. `role:Mahasiswa,Karyawan` wraps the shared user-facing "medical"/"konseling" self-service routes, `role:Admin,Dokter,Psikolog,Perawat` wraps the staff-facing inventaris/kesehatan/konseling management routes, `role:Admin` wraps user/staff management ("lainnya").
- `routes/api.php` mirrors the same `role:` middleware pattern for internal AJAX/JSON endpoints consumed by the Blade+Alpine frontend (see `App\Http\Controllers\API\InternalApiController` — a single controller aggregating most of these read endpoints).

When adding a new feature route, decide which role bundle it belongs to and add it to the matching `Route::middleware('role:...')->group()` block in `routes/web.php` rather than creating a new group, unless the role combination is genuinely new.

### Domain model patterns

- Most domain models use `HasUlids` (sortable, e.g. `ObatLog`, `AlatLog`, `ConsumableLog`) or `HasUuids` (e.g. `InventoryObat`, `InventoryAlat`, `InventoryConsumable`) as primary keys instead of auto-increment integers — check the model before assuming `id` is an integer.
- Inventory items (`InventoryObat`, `InventoryAlat`, `InventoryConsumable`) each have a paired `*Log` model (`ObatLog`/`InventoryLog`, `AlatLog`, `ConsumableLog`) that records `deposit`/`withdraw` transactions with a `type` column and `Qty`. Dashboard/report aggregation (`DashboardController`, `RekamMedisController`, export classes) reads directly from these log tables rather than maintaining a separate running total — stock (`stok`) on the inventory model itself is the authoritative current quantity, logs are the audit trail.
- Student medical/personal data is split across `CDMI` (Campus Data / academic-side profile: NIM, room, prodi/blok) and `DMTI` (Dormitory/personal medical profile: NIK, BPJS, blood type, phone) — both `hasOne` off `User`. `Karyawan` (staff) only get a `DMTI`; only `Mahasiswa` (students) get both `CDMI` and `DMTI`. The `User` model's `boot()` method auto-assigns `cdmi`/`dmti` flags and generates QR tokens (`kesehatan_token`, `bimbingan_token`, `konsultasi_token`) based on `role` at creation time — extend this switch when adding a new role that needs its own token/profile behavior.
- "Surat" (letter) models — `SuratKeteranganBerobat` (treatment certificate), `SuratKeteranganSakit` (sick note), `SuratKeteranganSehat` (health certificate), `SuratRujukan` (referral letter) — follow a consistent lifecycle: created from a `RekamMedis` (medical record) entry by a doctor, then viewable/printable (`barryvdh/laravel-dompdf`) by the student/staff who owns it. `SuratManagementController` handles the staff-side create/review/print flow per letter type; `User\KesehatanUserController` handles the self-service view/print flow.
- QR-code check-in flows (health check-in, bimbingan/counseling check-in, konsultasi/consultation check-in) are driven by time-limited tokens on `User` (`*_token` + `*_token_expired_at`, refreshed via `simplesoftwareio/simple-qrcode`) and consumed via `QrController`.

### Controllers

Controllers are organized by domain area, not RESTful resource-per-controller: `InventarisObatController`/`InventarisAlatController`/`InventarisConsumableController` (inventory), `KesehatanController`/`RekamMedisController`/`SuratManagementController` (medical staff-side), `BimbinganKonselingController` (counseling staff-side), `MahasiswaManagementController`/`KaryawanManagementController`/`PetugasManagementController` (user/staff admin, under the "lainnya" route prefix), and `User\KesehatanUserController`/`User\KonselingUserController` (self-service views for Mahasiswa/Karyawan). Filter/search endpoints on list views are typically separate `POST .../filter` actions on the same controller rather than query-string GET filtering.

### Frontend

- Blade templates + Alpine.js + Flowbite components, styled with **Tailwind CSS v4** (via `@tailwindcss/postcss`, configured through `@import "tailwindcss"` in `resources/css/app.css` — there is no `tailwind.config.js`; v4 uses `@source` directives in CSS instead, see `resources/css/app.css`).
- Vite is the bundler (`vite.config.js` — inputs are `resources/css/app.css` and `resources/js/app.js`).
- Views are organized by domain to mirror the controllers/routes: `resources/views/{inventaris,kesehatan,konseling,lainnya}/...`, shared layout in `resources/views/layouts`, printable PDF views in `resources/views/print`.
- Excel exports (`app/Exports/*`) use `maatwebsite/excel`'s `FromView` concern, rendering an existing Blade view (in `resources/views/print/*-excel.blade.php`) rather than building spreadsheets programmatically.

### Auth scaffolding

Standard Laravel Breeze (`laravel/breeze`) session-based auth — `App\Http\Controllers\Auth\*`, routes in `routes/auth.php`. No Sanctum SPA/token auth is actively used despite the dependency being present (`HasApiTokens` is on `User` but unused in routes — `api.php` relies on the same session-based `role` middleware, not `auth:sanctum`).
