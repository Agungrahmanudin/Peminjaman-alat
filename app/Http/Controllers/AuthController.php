<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function auth(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;

        // Deteksi: email atau username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([
            $field     => $login,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'login' => 'Email / Username atau password salah',
        ]);
    }

    // Redirect ke Google
    public function googleRedirect()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    // Callback dari Google
    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Buat username unik dari nama Google
                $baseUsername = Str::slug($googleUser->getName(), '_');
                $username     = $baseUsername;
                $counter      = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . '_' . $counter++;
                }

                // Buat user baru dengan role peminjam
                $user = User::create([
                    'name'     => $googleUser->getName(),
                    'username' => $username,
                    'email'    => $googleUser->getEmail(),
                    'avatar'   => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'role'     => 'peminjam',
                    'status'   => true,
                ]);
            }

            // Cek status user aktif
            if (!$user->status) {
                return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            }

            // Login user
            Auth::login($user, true);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    // Halaman register
    public function register()
    {
        return view('auth.register');
    }

    // Simpan user baru — role SELALU peminjam, tidak bisa dipilih
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'peminjam', // Selalu peminjam, tidak bisa diubah via register
            'status'   => true,
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil. Silakan login.');
    }
}