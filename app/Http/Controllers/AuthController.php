<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /**
     * Tampilkan form login pustakawan
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('buku.index');
        }
        return view('auth.login');
    }

    /**
     * Proses autentikasi pustakawan (Session Regeneration & Cookie Queue)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // WAJIB: Cegah serangan Session Fixation dengan regenerate()
            $request->session()->regenerate();

            // Simpan timestamp login ke session
            session(['login_timestamp' => now()->format('d M Y, H:i:s') . ' WIB']);

            // Simpan email terakhir di HTTP Cookie (berlaku 7 hari / 10080 menit)
            Cookie::queue('del_last_user_email', $request->email, 10080);

            return redirect()->intended(route('buku.index'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '! Sesi pustakawan aktif.');
        }

        return back()->withInput($request->only('email', 'remember'))
            ->with('error', 'Kredensial login tidak valid. Pastikan email dan password benar.');
    }

    /**
     * Logout Pustakawan (Invalidasi Sesi & Regenerasi Token CSRF)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Bersihkan sesi server
        $request->session()->invalidate();

        // Regenerasi token CSRF baru
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesi server telah dihapus dengan aman.');
    }
}
