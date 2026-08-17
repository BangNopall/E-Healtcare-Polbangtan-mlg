<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'Mahasiswa') {
            if (!$request->is('profile*') && !$request->is('password*') && !$request->is('logout')) {
                if (!$user->cdmi_complete || !$user->dmti_complete || !$user->is_email_changed || !$user->is_password_changed) {
                    return redirect()->route('profile.edit')
                        ->with('error', 'Silakan lengkapi profil, ubah password, dan ganti email Anda terlebih dahulu untuk melanjutkan layanan.');
                }
            }
        }

        return $next($request);
    }
}
