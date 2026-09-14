@extends('layouts.master')

@section('title', 'Edit User')

@section('content')
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Edit User</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Edit: {{ $user->name }}</li>
                    </ul>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error!</strong> Terjadi kesalahan:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- ===== KOLOM KIRI: Info Akun ===== --}}
                                <div class="col-12 col-md-6">
                                    <h5 class="mb-3">Informasi Akun</h5>

                                    <div class="form-group mb-3">
                                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name"
                                               value="{{ old('name', $user->name) }}"
                                               placeholder="Masukkan nama lengkap" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="username">Username <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('username') is-invalid @enderror"
                                               id="username" name="username"
                                               value="{{ old('username', $user->username) }}"
                                               placeholder="Masukkan username unik" required>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email"
                                               value="{{ old('email', $user->email) }}"
                                               placeholder="contoh@email.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="phone">No. Telepon</label>
                                        <input type="text"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone"
                                               value="{{ old('phone', $user->phone) }}"
                                               placeholder="08xxxxxxxxxx">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="location">Alamat</label>
                                        <textarea class="form-control @error('location') is-invalid @enderror"
                                                  id="location" name="location" rows="3"
                                                  placeholder="Masukkan alamat lengkap">{{ old('location', $user->location) }}</textarea>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password">Password Baru</label>
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password"
                                               placeholder="Kosongkan jika tidak ingin mengubah">
                                        <small class="text-muted">Isi hanya jika ingin mengubah password.</small>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Ketik ulang password baru">
                                    </div>
                                </div>

                                {{-- ===== KOLOM KANAN: Role, Status, Avatar ===== --}}
                                <div class="col-12 col-md-6">
                                    <h5 class="mb-3">Role & Status</h5>

                                    {{-- Role: hanya bisa diubah Admin --}}
                                    @if(auth()->user()->role === 'admin')
                                        <div class="form-group mb-3">
                                            <label for="role">Role <span class="text-danger">*</span></label>
                                            <select class="form-control @error('role') is-invalid @enderror"
                                                    id="role" name="role" required>
                                                <option value="admin"    {{ old('role', $user->role) == 'admin'    ? 'selected' : '' }}>Admin</option>
                                                <option value="petugas"  {{ old('role', $user->role) == 'petugas'  ? 'selected' : '' }}>Petugas</option>
                                                <option value="peminjam" {{ old('role', $user->role) == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        {{-- Petugas: tampilkan role saat ini, tidak bisa diubah --}}
                                        <div class="form-group mb-3">
                                            <label>Role</label>
                                            <div class="form-control bg-light text-muted">
                                                <i class="fas fa-lock me-1"></i>
                                                {{ ucfirst($user->role) }}
                                                <small class="d-block text-muted mt-1" style="font-size:0.78rem;">
                                                    Hanya Admin yang dapat mengubah role.
                                                </small>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group mb-3">
                                        <label>Status</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="status"
                                                   name="status"
                                                   value="1"
                                                   {{ old('status', $user->status) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                Aktif (dapat login)
                                            </label>
                                        </div>
                                        @error('status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <h5 class="mb-3 mt-4">Foto Profil</h5>

                                    {{-- Preview foto saat ini --}}
                                    <div class="mb-3 text-center">
                                        <img src="{{ $user->avatar_url }}"
                                             id="avatarPreview"
                                             alt="{{ $user->name }}"
                                             class="img-thumbnail rounded-circle"
                                             style="width:120px;height:120px;object-fit:cover;border:2px solid #dee2e6;">
                                        <div class="small text-muted mt-1">Foto saat ini</div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="avatar">Ganti Foto</label>
                                        <input type="file"
                                               class="form-control @error('avatar') is-invalid @enderror"
                                               id="avatar" name="avatar"
                                               accept="image/*"
                                               onchange="previewAvatar(this)">
                                        <small class="text-muted">Format: jpg, jpeg, png. Maks: 2MB. Kosongkan jika tidak ingin mengubah.</small>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <hr>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update User
                                        </button>
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewAvatar(input) {
        var preview = document.getElementById('avatarPreview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush