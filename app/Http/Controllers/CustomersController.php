<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Gunakan scope customers() dari model
        $query = User::customers();

        // Search (jika ada)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter status (jika ada)
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'active') {
                $query->active(); // Gunakan scope active()
            } else {
                $query->where('status', false);
            }
        }

        // Ambil data peminjaman untuk setiap customer dengan eager loading yang lengkap
        $customers = $query->with([
            'peminjaman' => function($q) {
                $q->select('id', 'user_id', 'status', 'tanggal_pinjam', 'tanggal_kembali_rencana', 'tanggal_kembali_realisasi');
            },
            'peminjaman.pengembalian' => function($q) {
                $q->select('id', 'peminjaman_id', 'tanggal_kembali_realisasi', 'denda', 'denda_dibayar', 'status_denda');
            }
        ])->latest()->paginate(15);

        // Hitung statistik untuk setiap customer
        $customers->getCollection()->transform(function($customer) {
            $peminjaman = $customer->peminjaman;
            $pengembalian = $peminjaman->pluck('pengembalian')->filter();

            $customer->stats = [
                'total_loans' => $peminjaman->count(),
                'active_loans' => $peminjaman->where('status', 'dipinjam')->count(),
                'completed_loans' => $peminjaman->whereIn('status', ['dikembalikan', 'terlambat'])->count(),
                'total_denda' => $pengembalian->sum('denda'),
                'total_denda_dibayar' => $pengembalian->sum('denda_dibayar'),
            ];

            return $customer;
        });

        return view('customers.index', compact('customers'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $customer)
    {
        // Pastikan ini adalah customer (role = peminjam)
        if ($customer->role != 'peminjam') {
            abort(404, 'Customer not found');
        }

        // Load data dengan eager loading
        $customer->load([
            'peminjaman.alat',
            'peminjaman.pengembalian',
            'peminjaman' => function($q) {
                $q->latest()->take(10);
            }
        ]);

        // Hitung statistik dari data yang diload
        $stats = $this->calculateCustomerStats($customer);

        return view('customers.show', compact('customer', 'stats'));
    }

    /**
     * Calculate customer statistics
     */
    private function calculateCustomerStats($customer)
    {
        $peminjaman = $customer->peminjaman;

        return [
            'total_peminjaman' => $peminjaman->count(),
            'active_loans' => $peminjaman->where('status', 'dipinjam')->count(),
            'completed_loans' => $peminjaman->where('status', 'dikembalikan')->count(),
            'total_denda' => $peminjaman->sum(function($p) {
                return $p->pengembalian ? $p->pengembalian->denda : 0;
            }),
            'total_items' => $peminjaman->sum('jumlah'),
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $customer)
    {
        // Pastikan ini adalah customer (role = peminjam)
        if ($customer->role !== 'peminjam') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'username' => 'nullable|string|max:50|unique:users,username,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($customer->avatar && file_exists(public_path($customer->avatar))) {
                unlink(public_path($customer->avatar));
            }
            
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $validated['avatar'] = 'uploads/avatars/' . $filename;
        }

        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer->id)
            ->with('success', 'Customer berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $customer)
    {
        // Pastikan ini adalah customer (role = peminjam)
        if ($customer->role !== 'peminjam') {
            abort(404);
        }

        if ($customer->peminjaman()->where('status', 'dipinjam')->exists()) {
            return redirect()
                ->route('customers.index')
                ->with('error', 'Customer masih memiliki peminjaman aktif.');
        }

        // Hapus avatar jika ada
        if ($customer->avatar && file_exists(public_path($customer->avatar))) {
            unlink(public_path($customer->avatar));
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    /**
     * Get customer data for select2 or other uses
     */
    public function getCustomerData(Request $request)
    {
        $customers = User::customers()
            ->withCount('peminjaman')
            ->withCount([
                'peminjaman as active_loans_count' => function ($q) {
                    $q->where('status', 'dipinjam');
                }
            ])
            ->latest()
            ->paginate(10);

        return response()->json($customers);
    }

    /**
     * Export customers
     */
    public function export()
    {
        $customers = User::customers()->get();

        return response()->json([
            'success' => true,
            'count' => $customers->count(),
            'message' => 'Customers data ready for export.'
        ]);
    }
}