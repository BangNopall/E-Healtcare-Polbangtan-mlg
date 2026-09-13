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
     * Jika sesi pengguna berstatus sso_readonly (Pejabat), blokir semua aksi
     * mutasi (POST non-filter, PUT, PATCH, DELETE) dengan 403 Forbidden,
     * namun tetap izinkan pembacaan data (GET, HEAD, OPTIONS) serta pencarian/filter data (POST filter)
     * dan pencetakan laporan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('sso_readonly') === true) {
            // Metode HTTP aman selalu diizinkan
            if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
                return $next($request);
            }

            // Whitelist aksi POST yang aman (hanya filter/search, cetak laporan, atau logout)
            if ($request->isMethod('POST') && $this->isSafePostRequest($request)) {
                return $next($request);
            }

            // Semua operasi mutasi (create, update, delete, destroy) DITOLAK untuk Pejabat
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak: Akun Pejabat hanya memiliki izin Read-Only pada sistem E-Klinik.',
                ], 403);
            }

            abort(403, 'Akses ditolak: Akun Pejabat hanya memiliki izin Read-Only pada sistem E-Klinik.');
        }

        return $next($request);
    }

    /**
     * Tentukan apakah request POST adalah operasi baca/filter yang aman atau logout.
     */
    private function isSafePostRequest(Request $request): bool
    {
        // 1. Logout selalu diizinkan
        if ($request->is('logout') || $request->routeIs('logout')) {
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
