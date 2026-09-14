<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter berdasarkan role jika ada
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status == 'active');
        }

        // Search berdasarkan nama atau email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('users.index', compact('users'));
    }
        use Notifiable;

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $isAdmin = auth()->user()->role === 'admin';

        $rules = [
            'name'     => 'required',
            'username' => 'required|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Hanya admin yang wajib & boleh memilih role
        if ($isAdmin) {
            $rules['role'] = 'required';
        }

        $request->validate($rules);

        $user           = new User();
        $user->name     = $request->name;
        $user->username = $request->username;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone    = $request->phone;
        $user->location = $request->location;
        $user->status   = $request->has('status') ? 1 : 0;
        $user->company  = null;
        $user->cover    = null;

        // Admin bisa pilih role bebas; petugas selalu set peminjam
        $user->role = $isAdmin ? $request->role : 'peminjam';

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file       = $request->file('avatar');
            $filename   = time() . '_' . $file->getClientOriginalName();
            $uploadPath = public_path('uploads/avatars');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $isAdmin = auth()->user()->role === 'admin';

        $rules = [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'location' => 'nullable|string',
            'status'   => 'nullable|boolean',
        ];

        if ($isAdmin) {
            $rules['role'] = 'required|in:admin,petugas,peminjam';
        }

        $request->validate($rules);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'location' => $request->location,
            'status'   => $request->has('status') ? 1 : 0,
        ];

        // Hanya admin yang bisa mengubah role
        if ($isAdmin) {
            $data['role'] = $request->role;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $file       = $request->file('avatar');
            $filename   = time() . '_' . $file->getClientOriginalName();
            $uploadPath = public_path('uploads/avatars');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        DB::update("UPDATE users SET status = ? WHERE id = ?", [$request->status, $user->id]);

        return redirect()->route('users.index')
            ->with('success', 'Status berhasil diubah menjadi ' . ($request->status ? 'Aktif' : 'Tidak Aktif'));
    }
}