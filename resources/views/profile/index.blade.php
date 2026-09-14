@extends('layouts.master')

@section('title', 'Profile')

@section('content')
    <div class="content container-fluid">
        <div class="row justify-content-lg-center">
            <div class="col-lg-11">
                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Profile</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Profile</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Loading Overlay -->
                <div id="loading-overlay"
                    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; text-align: center; padding-top: 20%;">
                    <div class="spinner-border text-white" style="width: 4rem; height: 4rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h4 class="text-white mt-3">Uploading...</h4>
                    <p class="text-white-50">Please wait while we upload your file</p>
                </div>

                <style>
                    /* ===== Tab Navigation: bentuk tombol solid seperti "Update Password" (btn btn-lg) ===== */
                    .profile-tabs {
                        border-bottom: none !important;
                        display: flex !important;
                        flex-wrap: wrap;
                        gap: 16px !important;
                        /* jarak antar tombol */
                        list-style: none;
                        padding-left: 0;
                        margin-bottom: 1.5rem;
                    }

                    .profile-tabs .nav-item {
                        margin: 0 !important;
                    }

                    .profile-tabs .nav-link {
                        border: 1.5px solid #e5e7eb !important;
                        border-radius: 0.5rem !important;
                        /* sudut sama seperti tombol btn-lg biasa, bukan pill */
                        padding: 0.7rem 1.5rem !important;
                        font-weight: 600 !important;
                        font-size: 15px !important;
                        line-height: 1 !important;
                        font-family: inherit !important;
                        background-color: #fff !important;
                        transition: all 0.2s ease;
                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
                        display: inline-flex !important;
                        align-items: center !important;
                        gap: 8px;
                    }

                    .profile-tabs .nav-link i {
                        margin-right: 0;
                        color: inherit !important;
                        font-size: 14px !important;
                        font-weight: 900 !important;
                        /* paksa render solid, bukan tipis/regular */
                        line-height: 1 !important;
                        display: inline-block;
                        width: 15px;
                        text-align: center;
                        flex-shrink: 0;
                    }

                    .profile-tabs .nav-link span {
                        color: inherit !important;
                        line-height: 1 !important;
                    }

                    /* Tab 1: Profile Details -> biru */
                    .profile-tabs #details-tab,
                    .profile-tabs #details-tab i {
                        color: #4338ca !important;
                    }

                    .profile-tabs #details-tab {
                        border-color: #c7d2fe !important;
                    }

                    .profile-tabs #details-tab:hover {
                        background-color: #eef2ff !important;
                    }

                    .profile-tabs #details-tab.active,
                    .profile-tabs #details-tab.active i {
                        background-color: #4338ca !important;
                        border-color: #4338ca !important;
                        color: #fff !important;
                        box-shadow: 0 6px 14px rgba(79, 70, 229, 0.35);
                    }

                    /* Tab 2: Recent Activity -> teal/cyan */
                    .profile-tabs #activity-tab,
                    .profile-tabs #activity-tab i {
                        color: #0369a1 !important;
                    }

                    .profile-tabs #activity-tab {
                        border-color: #bae6fd !important;
                    }

                    .profile-tabs #activity-tab:hover {
                        background-color: #f0f9ff !important;
                    }

                    .profile-tabs #activity-tab.active,
                    .profile-tabs #activity-tab.active i {
                        background-color: #0369a1 !important;
                        border-color: #0369a1 !important;
                        color: #fff !important;
                        box-shadow: 0 6px 14px rgba(14, 165, 233, 0.35);
                    }

                    /* Tab 3: Authentication -> oranye */
                    .profile-tabs #auth-tab,
                    .profile-tabs #auth-tab i {
                        color: #c2410c !important;
                    }

                    .profile-tabs #auth-tab {
                        border-color: #fed7aa !important;
                    }

                    .profile-tabs #auth-tab:hover {
                        background-color: #fff7ed !important;
                    }

                    .profile-tabs #auth-tab.active,
                    .profile-tabs #auth-tab.active i {
                        background-color: #c2410c !important;
                        border-color: #c2410c !important;
                        color: #fff !important;
                        box-shadow: 0 6px 14px rgba(249, 115, 22, 0.35);
                    }

                    /* ===== Enlarged content ===== */
                    .profile-cover-lg .profile-cover-img {
                        height: 260px;
                        object-fit: cover;
                    }

                    .card-lg .card-header {
                        padding: 1.25rem 1.5rem;
                    }

                    .card-lg .card-body {
                        padding: 1.75rem;
                    }

                    .card-lg .card-title {
                        font-size: 1.25rem;
                    }

                    .profile-detail-list li {
                        font-size: 1.05rem;
                    }

                    .card-body-height-lg {
                        min-height: 320px;
                    }

                    .activity-feed-lg .feed-item {
                        padding-bottom: 1.5rem;
                    }

                    .activity-feed-lg .feed-text {
                        font-size: 1.05rem;
                    }
                </style>

                <!-- ===================== TAB NAVIGATION (di atas, di bawah breadcrumb) ===================== -->
                <ul class="nav profile-tabs mb-4" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab"
                            data-bs-target="#tab-details" type="button" role="tab" aria-controls="tab-details"
                            aria-selected="true">
                            <i class="fas fa-user"></i><span>Profile Details</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="activity-tab" data-bs-toggle="tab"
                            data-bs-target="#tab-activity" type="button" role="tab" aria-controls="tab-activity"
                            aria-selected="false">
                            <i class="fas fa-history"></i><span>Recent Activity</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="auth-tab" data-bs-toggle="tab" data-bs-target="#tab-auth"
                            type="button" role="tab" aria-controls="tab-auth" aria-selected="false">
                            <i class="fas fa-lock"></i><span>Authentication</span>
                        </button>
                    </li>
                </ul>
                <!-- ===================== END TAB NAVIGATION ===================== -->

                <div class="tab-content" id="profileTabContent">

                    <!-- ===================== TAB 1: PROFILE DETAILS ===================== -->
                    <div class="tab-pane fade show active" id="tab-details" role="tabpanel"
                        aria-labelledby="details-tab">

                        <div class="profile-cover profile-cover-lg">
                            <div class="profile-cover-wrap">
                                <img class="profile-cover-img"
                                    src="{{ $user->cover ? asset($user->cover) : asset('assets/img/profiles/avatar-02.jpg') }}"
                                    alt="Profile Cover"
                                    onerror="this.src='{{ asset('assets/img/profiles/avatar-02.jpg') }}'">

                                <div class="cover-content">
                                    <div class="custom-file-btn">
                                        <form action="{{ route('profile.cover') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="cover" id="simple_cover" accept="image/*"
                                                style="display: none;" onchange="this.form.submit()">
                                            <label for="simple_cover" class="btn btn-white">
                                                <i class="fas fa-camera"></i> Update Cover
                                            </label>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mb-5">
                            <!-- Avatar Upload Form -->
                            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div style="position: relative; display: inline-block;">
                                    <img src="{{ $user->avatar ? asset($user->avatar) : asset('assets/img/profiles/avatar-02.jpg') }}"
                                        style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 14px rgba(0,0,0,0.12);">
                                    <input type="file" name="avatar" id="simple_avatar" accept="image/*"
                                        style="display: none;" onchange="this.form.submit()">
                                    <label for="simple_avatar"
                                        style="position: absolute; bottom: 6px; right: 6px; background: #007bff; color: white; padding: 8px 12px; border-radius: 20px; cursor: pointer; font-size: 13px;">
                                        <i class="fas fa-camera"></i> Ganti
                                    </label>
                                </div>
                            </form>

                            <h2 class="mt-3 mb-1">{{ $user->name }}
                                @if ($user->email_verified_at)
                                    <i class="fas fa-certificate text-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Verified"></i>
                                @endif
                            </h2>
                            <ul class="list-inline fs-6">
                                @if ($user->company)
                                    <li class="list-inline-item">
                                        <i class="far fa-building"></i> <span>{{ $user->company }}</span>
                                    </li>
                                @endif
                                @if ($user->location)
                                    <li class="list-inline-item">
                                        <i class="fas fa-map-marker-alt"></i> {{ $user->location }}
                                    </li>
                                @endif
                                <li class="list-inline-item">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>Joined {{ $user->created_at->format('F Y') }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <div class="card card-body card-lg mb-4">
                                    <h4 class="mb-3">Complete your profile</h4>

                                    @php
                                        $profileFields = [
                                            'name' => $user->name,
                                            'username' => $user->username,
                                            'email' => $user->email,
                                            'company' => $user->company,
                                            'phone' => $user->phone,
                                            'location' => $user->location,
                                            'avatar' => $user->avatar,
                                            'cover' => $user->cover,
                                        ];

                                        $completedCount = 0;
                                        $totalFields = count($profileFields);

                                        foreach ($profileFields as $field) {
                                            if (!empty($field)) {
                                                $completedCount++;
                                            }
                                        }

                                        $completionPercentage =
                                            $totalFields > 0 ? round(($completedCount / $totalFields) * 100) : 0;
                                    @endphp

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="progress flex-grow-1" style="height: 12px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: {{ $completionPercentage }}%"
                                                aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ms-4 fs-5 fw-semibold">{{ $completionPercentage }}%</span>
                                    </div>

                                    @if ($completionPercentage < 100)
                                        <div class="mt-4">
                                            <button type="button" class="btn btn-outline-primary btn-lg w-100"
                                                data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                                <i class="fas fa-edit me-1"></i>Complete Your Profile
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="card card-lg">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">Profile Details</h4>
                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editProfileModal">
                                            <i class="fas fa-pen me-1"></i> Edit
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0 fs-5 profile-detail-list">
                                            <li class="py-1">
                                                <h6 class="text-muted text-uppercase small mb-2">About</h6>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Name:</strong> {{ $user->name }}
                                            </li>
                                            @if ($user->username)
                                                <li class="mb-2">
                                                    <strong>Username:</strong> {{ $user->username }}
                                                </li>
                                            @endif
                                            @if ($user->company)
                                                <li class="mb-2">
                                                    <strong>Company:</strong> {{ $user->company }}
                                                </li>
                                            @endif

                                            <li class="pt-3 pb-1">
                                                <h6 class="text-muted text-uppercase small mb-2">Contacts</h6>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Email:</strong>
                                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                                @if ($user->email_verified_at)
                                                    <span class="badge bg-success ms-1">Verified</span>
                                                @else
                                                    <span class="badge bg-warning ms-1">Pending</span>
                                                @endif
                                            </li>
                                            @if ($user->phone)
                                                <li class="mb-2">
                                                    <strong>Phone:</strong> {{ $user->phone }}
                                                </li>
                                            @endif

                                            @if ($user->location)
                                                <li class="pt-3 pb-1">
                                                    <h6 class="text-muted text-uppercase small mb-2">Address</h6>
                                                </li>
                                                <li>
                                                    {{ $user->location }}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="card card-lg h-100">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">
                                            <i class="fas fa-info-circle me-2"></i>Overview
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted fs-5 mb-0">
                                            Gunakan tab <strong>Recent Activity</strong> untuk melihat riwayat
                                            aktivitas akunmu, dan tab <strong>Authentication</strong> untuk mengubah
                                            password, email, atau menghapus akun.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ===================== END TAB 1 ===================== -->

                    <!-- ===================== TAB 2: RECENT ACTIVITY ===================== -->
                    <div class="tab-pane fade" id="tab-activity" role="tabpanel" aria-labelledby="activity-tab">
                        <div class="card card-lg">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Recent Activity</h4>
                            </div>
                            <div class="card-body card-body-height-lg">
                                @if ($activities->count() > 0)
                                    <ul class="activity-feed activity-feed-lg">
                                        @foreach ($activities as $activity)
                                            <li class="feed-item">
                                                <div class="feed-date fs-6">
                                                    {{ $activity->created_at->format('M d') }}
                                                </div>
                                                <span class="feed-text fs-5">
                                                    {{ $activity->activity }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                        <p class="text-muted fs-5">No recent activity</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- ===================== END TAB 2 ===================== -->

                    <!-- ===================== TAB 3: AUTHENTICATION ===================== -->
                    <div class="tab-pane fade" id="tab-auth" role="tabpanel" aria-labelledby="auth-tab">

                        <!-- Change Password -->
                        <div class="card card-lg mb-4">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('settings.update-password') }}">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label fs-5">Current
                                                    Password</label>
                                                <input type="password"
                                                    class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                                                    id="current_password" name="current_password" required>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label fs-5">New Password</label>
                                                <input type="password"
                                                    class="form-control form-control-lg @error('new_password') is-invalid @enderror"
                                                    id="new_password" name="new_password" required>
                                                @error('new_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="new_password_confirmation" class="form-label fs-5">Confirm
                                                    New Password</label>
                                                <input type="password" class="form-control form-control-lg"
                                                    id="new_password_confirmation" name="new_password_confirmation"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Update Password
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Update Email -->
                        <div class="card card-lg mb-4">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-envelope me-2"></i>Update Email
                                </h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('settings.update-email') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label fs-5">New Email Address</label>
                                                <input type="email"
                                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                                    id="email" name="email" value="{{ old('email', $user->email) }}"
                                                    required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label fs-5">Confirm Password</label>
                                                <input type="password"
                                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                    id="password" name="password" required>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="fas fa-save me-2"></i>Update Email
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Delete Account -->
                        <div class="card card-lg mb-4">
                            <div class="card-header">
                                <h4 class="card-title mb-0 text-danger">
                                    <i class="fas fa-trash me-2"></i>Delete Account
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger fs-6">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Warning:</strong> This action cannot be undone. All your data will be
                                    permanently deleted.
                                </div>
                                <form method="POST" action="{{ route('settings.delete-account') }}"
                                    onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="delete_password" class="form-label fs-5">Enter your
                                                    password to confirm</label>
                                                <input type="password" class="form-control form-control-lg"
                                                    id="delete_password" name="password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-trash me-2"></i>Delete My Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- ===================== END TAB 3 ===================== -->

                </div>
                <!-- ===================== END TAB CONTENT ===================== -->

            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" id="editProfileForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="{{ old('username', $user->username) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="modal_email" name="email"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $user->phone) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="company" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company" name="company"
                                value="{{ old('company', $user->company) }}">
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <textarea class="form-control" id="location" name="location" rows="2">{{ old('location', $user->location) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password confirmation validation
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('new_password_confirmation');

            if (confirmPassword) {
                confirmPassword.addEventListener('keyup', function() {
                    if (newPassword.value !== this.value) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            }

            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Debug untuk form submit modal edit profile
            const editForm = document.querySelector('#editProfileModal form');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                });
            }

            // ==== Ingat tab yang terakhir aktif ====
            const tabButtons = document.querySelectorAll('#profileTab button[data-bs-toggle="tab"]');

            @if ($errors->has('current_password') || $errors->has('new_password') || $errors->has('password'))
                const authTabTrigger = document.getElementById('auth-tab');
                if (authTabTrigger) {
                    new bootstrap.Tab(authTabTrigger).show();
                }
            @else
                const lastTab = localStorage.getItem('activeProfileTab');
                if (lastTab) {
                    const triggerEl = document.querySelector('#profileTab button[data-bs-target="' + lastTab + '"]');
                    if (triggerEl) {
                        new bootstrap.Tab(triggerEl).show();
                    }
                }
            @endif

            tabButtons.forEach(function(btn) {
                btn.addEventListener('shown.bs.tab', function(e) {
                    localStorage.setItem('activeProfileTab', e.target.getAttribute('data-bs-target'));
                });
            });
        });
    </script>
@endsection