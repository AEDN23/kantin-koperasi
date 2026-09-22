<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            }
            return redirect()->route('transaksi.riwayat');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Nama lengkap, username, atau NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Cari user berdasarkan username, name, email, atau NIP karyawan
        $user = User::with('karyawan')
            ->where(function ($query) use ($loginInput) {
                $query->whereRaw('LOWER(username) = ?', [strtolower($loginInput)])
                    ->orWhereRaw('LOWER(name) = ?', [strtolower($loginInput)])
                    ->orWhereRaw('LOWER(email) = ?', [strtolower($loginInput)])
                    ->orWhereHas('karyawan', function ($q) use ($loginInput) {
                        $q->where('nip', $loginInput);
                    });
            })
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Selamat datang kembali, ' . $user->name . ' (Admin)!');
            }

            return redirect()->intended(route('transaksi.riwayat'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => 'Kombinasi Nama / Username / NIP dan password tidak sesuai.',
            ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('transaksi.index')
            ->with('success', 'Anda telah berhasil keluar (logout).');
    }
}
