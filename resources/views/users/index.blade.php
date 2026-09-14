@extends('layouts.master')

@section('title', 'Users')

@section('content')

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Users</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Users</li>
                    </ul>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">

                    <!-- ================= HEADER TABLE ================= -->
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <h4 class="card-title mb-0">Daftar Pengguna Terdaftar</h4>

                        <div class="d-flex align-items-center gap-2">

                            <!-- SEARCH -->
                            <form action="{{ route('users.index') }}" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control form-control-sm me-2"
                                    placeholder="Cari nama, email, username..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>

                            <!-- ADD USER -->
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Tambah User
                            </a>

                            <!-- FILTER -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index') }}">
                                            Semua User
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">Filter by Role</h6></li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index', ['role' => 'admin']) }}">
                                            Admin
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index', ['role' => 'petugas']) }}">
                                            Petugas
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index', ['role' => 'peminjam']) }}">
                                            Peminjam
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">Filter by Status</h6></li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index', ['status' => 'active']) }}">
                                            Aktif
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index', ['status' => 'inactive']) }}">
                                            Tidak Aktif
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                    <!-- ================= END HEADER ================= -->

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Kontak</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Terdaftar</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                            </td>

                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                        class="avatar avatar-sm me-2">
                                                        <img class="avatar-img rounded-circle"
                                                            src="{{ $user->avatar_url }}"
                                                            style="width:40px;height:40px;object-fit:cover;">
                                                    </a>

                                                    <a href="{{ route('users.show', $user->id) }}">
                                                        {{ $user->name }}
                                                        @if ($user->username)
                                                            <small class="text-muted d-block">
                                                                {{ $user->username }}
                                                            </small>
                                                        @endif
                                                    </a>
                                                </h2>
                                            </td>

                                            <td class="small text-muted">
                                                <div>{{ $user->email }}</div>
                                                @if ($user->phone)
                                                    <div>{{ $user->phone }}</div>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge bg-{{ $user->role_badge_color }} p-2">
                                                    {{ $user->role_display_name }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="{{ $user->status_badge_class }}">
                                                    {{ $user->status_label }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ $user->created_at->format('d M Y') }}
                                            </td>

                                            {{-- ===== ACTION BUTTONS (LANGSUNG, TANPA DROPDOWN) ===== --}}
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-1">

                                                    {{-- Tombol View --}}
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                       class="btn btn-sm btn-info"
                                                       title="Lihat Detail">
                                                        <i class="far fa-eye"></i>
                                                    </a>

                                                    {{-- Tombol Edit --}}
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                       class="btn btn-sm btn-warning"
                                                       title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    {{-- Tombol Activate / Deactivate --}}
                                                    <form action="{{ route('users.status', $user->id) }}"
                                                          method="POST"
                                                          style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status"
                                                               value="{{ $user->status ? 0 : 1 }}">
                                                        <button type="submit"
                                                                class="btn btn-sm {{ $user->status ? 'btn-secondary' : 'btn-success' }}"
                                                                title="{{ $user->status ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </form>

                                                    {{-- Tombol Delete --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger"
                                                            title="Hapus User"
                                                            onclick="confirmDelete({{ $user->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>

                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <h4>Tidak ada user</h4>
                                                    <p class="text-muted">Belum ada user yang terdaftar.</p>
                                                    <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">
                                                        <i class="fas fa-plus me-2"></i>Tambah User Baru
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($users->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
function confirmDelete(userId) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')) {
        var form = document.getElementById('delete-form');
        form.action = '{{ url("users") }}/' + userId;
        form.submit();
    }
}
</script>
@endpush