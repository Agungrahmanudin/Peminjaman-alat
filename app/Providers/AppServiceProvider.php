<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inject notifikasi ke semua partial navbar
        View::composer('partials.navbar', function ($view) {
            if (!Auth::check()) return;

            $user = Auth::user();
            $notifications = collect();

            if (in_array($user->role, ['admin', 'petugas'])) {
                // Peminjaman menunggu approval
                $menunggu = Peminjaman::with('user')
                    ->where('status_approval', 'menunggu')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($p) => [
                        'icon'    => 'clock',
                        'color'   => 'warning',
                        'message' => '<strong>' . e($p->user->name ?? '-') . '</strong> mengajukan peminjaman baru',
                        'time'    => $p->created_at->diffForHumans(),
                        'url'     => route('peminjaman.show', $p->id),
                    ]);

                // Pengajuan perpanjangan menunggu persetujuan
                $perpanjangan = Peminjaman::with('user')
                    ->where('status_perpanjangan', 'menunggu')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($p) => [
                        'icon'    => 'calendar',
                        'color'   => 'info',
                        'message' => '<strong>' . e($p->user->name ?? '-') . '</strong> mengajukan perpanjangan hingga ' . ($p->tanggal_perpanjangan_diminta?->format('d/m/Y') ?? '-'),
                        'time'    => $p->updated_at->diffForHumans(),
                        'url'     => route('peminjaman.show', $p->id),
                    ]);

                // Peminjaman terlambat dikembalikan
                $terlambat = Peminjaman::with('user')
                    ->where('status', 'terlambat')
                    ->latest()
                    ->take(3)
                    ->get()
                    ->map(fn($p) => [
                        'icon'    => 'alert-triangle',
                        'color'   => 'danger',
                        'message' => '<strong>' . e($p->user->name ?? '-') . '</strong> terlambat mengembalikan alat',
                        'time'    => $p->tanggal_kembali_rencana?->diffForHumans() ?? '-',
                        'url'     => route('peminjaman.show', $p->id),
                    ]);

                $notifications = $menunggu->concat($perpanjangan)->concat($terlambat)->sortByDesc('time')->take(8)->values();

            } else {
                // Notifikasi untuk peminjam: status peminjaman mereka
                $myPeminjaman = Peminjaman::where('user_id', $user->id)
                    ->whereIn('status_approval', ['disetujui', 'ditolak'])
                    ->latest('updated_at')
                    ->take(5)
                    ->get()
                    ->map(fn($p) => [
                        'icon'    => $p->status_approval === 'disetujui' ? 'check-circle' : 'x-circle',
                        'color'   => $p->status_approval === 'disetujui' ? 'success' : 'danger',
                        'message' => 'Peminjaman Anda <strong>' . ($p->status_approval === 'disetujui' ? 'disetujui' : 'ditolak') . '</strong>',
                        'time'    => $p->approved_at?->diffForHumans() ?? $p->updated_at->diffForHumans(),
                        'url'     => route('peminjaman.show', $p->id),
                    ]);

                $notifications = $myPeminjaman->values();
            }

            $view->with('navNotifications', $notifications)
                 ->with('navNotifCount', $notifications->count());
        });
    }
}
