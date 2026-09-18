<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasDMTI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()->dmti == true) {
            if($request->user()->dmti_complete == false) {
                return redirect()->route('profile.edit')->with('error', 'Silahkan lengkapi data diri anda terlebih dahulu.');
            }
        }
        return $next($request);
    }
}
