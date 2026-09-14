<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();

        // Cek apakah model Activity ada
        if (class_exists('App\Models\Activity')) {
            $activities = Activity::where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();
        } else {
            $activities = collect([]);
        }

        return view('profile.index', compact('user', 'activities'));
    }

    /**
     * Show settings page.
     */
    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        \Log::info('=== UPDATE PROFILE ===');
        \Log::info('User: ' . Auth::id());

        try {
            $user = Auth::user();

            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->company = $request->company;
            $user->location = $request->location;
            $user->save();

            return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $user = Auth::user();

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                // Generate nama file
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Hapus file lama jika ada
                if ($user->avatar && file_exists(public_path($user->avatar))) {
                    unlink(public_path($user->avatar));
                }

                // Upload ke folder public/uploads/avatars
                $destinationPath = public_path('uploads/avatars');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);

                // Update database
                $user->avatar = 'uploads/avatars/' . $filename;
                $user->save();

                return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
            }

            return redirect()->back()->with('error', 'Tidak ada file yang diupload');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update user cover.
     */
    public function updateCover(Request $request)
    {
        try {
            $request->validate([
                'cover' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $user = Auth::user();

            if ($request->hasFile('cover')) {
                $file = $request->file('cover');

                // Generate nama file
                $filename = 'cover_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Hapus file lama jika ada
                if ($user->cover && file_exists(public_path($user->cover))) {
                    unlink(public_path($user->cover));
                }

                // Tujuan upload
                $destinationPath = public_path('uploads/covers');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                // Upload file
                $file->move($destinationPath, $filename);

                // Update database
                $user->cover = 'uploads/covers/' . $filename;
                $user->save();

                return redirect()->back()->with('success', 'Cover photo berhasil diperbarui!');
            }

            return redirect()->back()->with('error', 'Tidak ada file yang diupload');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        \Log::info('=== UPDATE PASSWORD ===');
        \Log::info('User ID: ' . Auth::id());

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Cek current password
        if (!Hash::check($request->current_password, $user->password)) {
            \Log::warning('Current password salah');
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        \Log::info('Password berhasil diupdate');

        return redirect()->route('settings')->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update email.
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah']);
        }

        $user->email = $request->email;
        $user->save();

        return redirect()->route('settings')->with('success', 'Email berhasil diperbarui.');
    }

    /**
     * Delete account.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah']);
        }

        // Hapus avatar jika ada
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        // Hapus cover jika ada
        if ($user->cover && file_exists(public_path($user->cover))) {
            unlink(public_path($user->cover));
        }

        Auth::logout();
        $user->delete();

        return redirect()->route('login')->with('success', 'Akun berhasil dihapus.');
    }

    public function updateNotifications(Request $request)
    {
        // Untuk sementara hanya flash message. Nanti bisa ditambahkan penyimpanan ke DB.
        return redirect()->back()->with('success', 'Preferensi notifikasi berhasil diperbarui.');
    }

    public function updatePrivacy(Request $request)
    {
        return redirect()->back()->with('success', 'Pengaturan privasi berhasil diperbarui.');
    }

    public function updateLanguage(Request $request)
    {
        return redirect()->back()->with('success', 'Bahasa dan wilayah berhasil diperbarui.');
    }
}