<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // redirect user role 
                $role = Auth::user()->role;

                return redirect('/');
                // if($role == 'Admin' || $role == 'Dokter' || $role == 'Psikiater'){
                //     return redirect(RouteServiceProvider::HOMEADMIN);
                // } elseif ($role == 'Karyawan'|| $role == 'Mahasiswa' ) {
                //     return redirect(RouteServiceProvider::HOMEUSER);
                // }
            }
        }

        return $next($request);
    }
}
