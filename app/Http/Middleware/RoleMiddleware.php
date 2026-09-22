<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'))->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = auth()->user();

        // Jika user memiliki salah satu dari roles yang diizinkan
        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // Jika karyawan mencoba mengakses halaman khusus admin
        if ($user->isKaryawan()) {
            return redirect()->route('transaksi.riwayat')
                ->with('error', 'Akses ditolak. Anda hanya memiliki akses untuk melihat riwayat transaksi dan laporan Anda sendiri.');
        }

        abort(403, 'Akses tidak diizinkan.');
    }
}
