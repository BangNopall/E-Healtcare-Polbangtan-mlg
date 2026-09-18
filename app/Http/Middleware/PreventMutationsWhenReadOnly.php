<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventMutationsWhenReadOnly
{
    /**
     * Handle an incoming request.
     *
     * Jika sesi pengguna berstatus sso_readonly (Pejabat):
     * 1. Blokir akses ke halaman profil (/profile*) dan alihkan ke /konseling.
     * 2. Izinkan pembacaan data umum (GET, HEAD, OPTIONS).
     * 3. Izinkan aksi POST yang aman (filter/search, print, login, logout).
     * 4. Blokir semua aksi mutasi (POST non-filter, PUT, PATCH, DELETE).
     *    - Untuk AJAX/JSON: kembalikan JSON 403 Forbidden.
     *    - Untuk Form Web biasa: alihkan kembali (redirect back) dengan flash error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('sso_readonly') === true) {
            // 1. Larang akses ke halaman profil akun admin (bahkan metode GET)
            if ($request->is('profile*') || ($request->route() && str_starts_with($request->route()->getName() ?? '', 'profile.'))) {
                return redirect()->route('konseling.dashboard')
                    ->with('error', 'Akses halaman profil dinonaktifkan dalam mode Pejabat (Read-Only).');
            }

            // 2. Metode HTTP aman selalu diizinkan untuk melihat data
            if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
                return $next($request);
            }

            // 3. Whitelist aksi POST yang aman (filter/search, cetak laporan, login, atau logout)
            if ($request->isMethod('POST') && $this->isSafePostRequest($request)) {
                return $next($request);
            }

            // 4. Semua operasi mutasi (create, update, delete, destroy) DITOLAK untuk Pejabat
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Akses ditolak: Akun Pejabat hanya memiliki izin Read-Only pada sistem E-Klinik.',
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'Aksi ditolak: Akun Pejabat hanya memiliki izin Read-Only pada sistem E-Klinik.');
        }

        return $next($request);
    }

    /**
     * Tentukan apakah request POST adalah operasi baca/filter yang aman atau login/logout.
     */
    private function isSafePostRequest(Request $request): bool
    {
        // 1. Logout & Login selalu diizinkan
        if ($request->is('logout') || $request->routeIs('logout') || $request->is('login') || $request->routeIs('login')) {
            return true;
        }

        // 2. Route pencarian & filter data diizinkan
        if ($request->is('*filter*') || ($request->route() && str_contains($request->route()->getName() ?? '', 'filter'))) {
            return true;
        }

        // 3. Route pencetakan laporan diizinkan
        if ($request->is('*print*') || ($request->route() && str_contains($request->route()->getName() ?? '', 'print'))) {
            return true;
        }

        return false;
    }
}
