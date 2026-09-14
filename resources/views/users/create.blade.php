@extends('layouts.master')

@section('title', 'Create User')

@section('content')
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Tambah User</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Tambah User</li>
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
                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                {{-- ===== KOLOM KIRI: Info Akun ===== --}}
                                <div class="col-12 col-md-6">
                                    <h5 class="mb-3">Informasi Akun</h5>

                                    <div class="form-group mb-3">
                                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name"
                                               value="{{ old('name') }}"
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
                                               value="{{ old('username') }}"
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
                                               value="{{ old('email') }}"
                                               placeholder="contoh@email.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password"
                                               placeholder="Minimal 6 karakter" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Ketik ulang password" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="phone">No. Telepon</label>
                                        <input type="text"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone"
                                               value="{{ old('phone') }}"
                                               placeholder="08xxxxxxxxxx">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="location">Alamat</label>
                                        <textarea class="form-control @error('location') is-invalid @enderror"
                                                  id="location" name="location" rows="3"
                                                  placeholder="Masukkan alamat lengkap">{{ old('location') }}</textarea>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ===== KOLOM KANAN: Role, Status, Avatar ===== --}}
                                <div class="col-12 col-md-6">
                                    <h5 class="mb-3">Role & Status</h5>

                                    {{-- Role: hanya tampil & bisa diubah oleh Admin --}}
                                    @if(auth()->user()->role === 'admin')
                                        <div class="form-group mb-3">
                                            <label for="role">Role <span class="text-danger">*</span></label>
                                            <select class="form-control @error('role') is-invalid @enderror"
                                                    id="role" name="role" required>
                                                <option value="">Pilih Role</option>
                                                <option value="admin"    {{ old('role') == 'admin'    ? 'selected' : '' }}>Admin</option>
                                                <option value="petugas"  {{ old('role') == 'petugas'  ? 'selected' : '' }}>Petugas</option>
                                                <option value="peminjam" {{ old('role') == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        {{-- Petugas: role dikunci sebagai Peminjam --}}
                                        <div class="form-group mb-3">
                                            <label>Role</label>
                                            <div class="form-control bg-light text-muted">
                                                <i class="fas fa-lock me-1"></i> Peminjam
                                                <small class="d-block text-muted mt-1" style="font-size:0.78rem;">
                                                    Hanya Admin yang dapat mengubah role.
                                                </small>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group mb-3">
                                        <label for="status">Status</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="status"
                                                   name="status"
                                                   value="1"
                                                   {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                Aktif (dapat login)
                                            </label>
                                        </div>
                                        @error('status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <h5 class="mb-3 mt-4">Foto Profil</h5>

                                    <div class="form-group mb-3">
                                        <label for="avatar">Upload Foto</label>
                                        <input type="file"
                                               class="form-control @error('avatar') is-invalid @enderror"
                                               id="avatar" name="avatar"
                                               accept="image/*"
                                               onchange="previewAvatar(this)">
                                        <small class="text-muted">Format: jpg, jpeg, png. Maks: 2MB</small>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mt-3 text-center" id="avatarPreviewContainer" style="display: none;">
                                        <label class="d-block mb-2">Preview Foto:</label>
                                        <img src="" id="avatarPreview" alt="Preview Avatar"
                                             class="img-thumbnail rounded-circle"
                                             style="width:150px;height:150px;object-fit:cover;border:2px solid #007bff;">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <hr>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan User
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
        var previewContainer = document.getElementById('avatarPreviewContainer');
        var preview = document.getElementById('avatarPreview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            previewContainer.style.display = 'none';
            preview.src = '';
        }
    }
</script>
@endpush