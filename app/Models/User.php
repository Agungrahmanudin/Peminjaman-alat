<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
 protected $table = 'users';
  protected string $guard_name = 'web';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'company',
        'email',
        'password',
        'role',
        'phone',
        'location',
        'avatar',
        'cover',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        
    ];

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            // Jika avatar adalah URL eksternal (misal dari Google)
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            // Jika avatar adalah file lokal
            if (file_exists(public_path($this->avatar))) {
                return asset($this->avatar);
            }
        }
        return asset('assets/img/default-avatar.jpg');
    }

    /**
     * Get the user's cover URL.
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover && file_exists(public_path($this->cover))) {
            return asset($this->cover);
        }
        return null;
    }

    /**
     * Get the user's status label.
     */
   /**
 * Get the user's status label.
 */
/**
 * Get the user's status label.
 */
public function getStatusLabelAttribute()
{
    return $this->status ? 'Active' : 'Inactive';
}

/**
 * Get the user's status badge class.
 */
public function getStatusBadgeClassAttribute()
{
    return $this->status
        ? 'badge bg-success-light'
        : 'badge bg-danger-light';
}

    /**
     * Get the user's role display name.
     */
    public function getRoleDisplayNameAttribute()
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'petugas' => 'Petugas',
            'peminjam' => 'Peminjam',
            default => ucfirst($this->role),
        };
    }

    /**
     * Get the user's role badge color.
     */
    public function getRoleBadgeColorAttribute()
    {
        return match ($this->role) {
            'admin' => 'danger',
            'petugas' => 'warning',
            'peminjam' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeOfRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include customers (users with role peminjam).
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'peminjam');
    }

    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include petugas.
     */
    public function scopePetugas($query)
    {
        return $query->where('role', 'petugas');
    }

    /**
     * RELATIONS
     */

    /**
     * Get the peminjaman for the user.
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }

    /**
     * Get the pengembalian for the user.
     */
    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class, 'user_id');
    }

    /**
     * Get active peminjaman (belum dikembalikan).
     */
    public function peminjamanAktif()
    {
        return $this->hasMany(Peminjaman::class, 'user_id')
                    ->whereIn('status', ['dipinjam', 'terlambat']);
    }

    /**
     * Get completed peminjaman (sudah dikembalikan).
     */
    public function peminjamanSelesai()
    {
        return $this->hasMany(Peminjaman::class, 'user_id')
                    ->where('status', 'dikembalikan');
    }

    /**
     * Check if user has active loans.
     */
    public function hasActiveLoans()
    {
        return $this->peminjaman()
                    ->whereIn('status', ['dipinjam', 'terlambat'])
                    ->exists();
    }

    /**
     * Get total denda for user.
     */
    public function getTotalDendaAttribute()
    {
        return $this->pengembalian()
                    ->where('status_denda', '!=', 'lunas')
                    ->sum('denda');
    }

    /**
     * Get total denda yang sudah dibayar.
     */
    public function getTotalDendaDibayarAttribute()
    {
        return $this->pengembalian()
                    ->sum('denda_dibayar');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is petugas.
     */
    public function isPetugas()
    {
        return $this->role === 'petugas';
    }

    /**
     * Check if user is peminjam.
     */
    public function isPeminjam()
    {
        return $this->role === 'peminjam';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Hapus file avatar saat user dihapus
        static::deleting(function ($user) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            if ($user->cover && file_exists(public_path($user->cover))) {
                unlink(public_path($user->cover));
            }
        });
    }
}