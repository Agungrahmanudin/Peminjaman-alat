@extends('layouts.master')

@section('title', 'User Details - ' . $user->name)

@section('content')
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">User Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Profile Info -->
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-img mb-3">
                        <img src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                             class="rounded-circle border"
                             width="120"
                             height="120"
                             style="object-fit: cover; border-width: 3px !important;">
                    </div>

                    <h4 class="mb-1">{{ $user->name }}</h4>

                    @if($user->username)
                    <p class="text-muted mb-2">
                        <i class="fas fa-user-tag me-1"></i>
                        {{ $user->username }}
                    </p>
                    @endif

                    <div class="mb-3">
                        <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'staff' ? 'bg-warning' : 'bg-info') }} mb-1">
                            {{ $user->role_name }}
                        </span>
                        <br>
                        <span class="{{ $user->status_badge_class }}">
                            {{ $user->status_label }}
                        </span>
                    </div>

                    <!-- Contact Buttons -->
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if($user->email)
                        <a href="mailto:{{ $user->email }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope"></i>
                        </a>
                        @endif

                        @if($user->phone)
                        <a href="tel:{{ $user->phone }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="p-2">
                                <h5 class="mb-0">{{ $user->peminjaman_count ?? 0 }}</h5>
                                <p class="text-muted mb-0 small">Peminjaman</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2">
                                <h5 class="mb-0">{{ $user->activities_count ?? 0 }}</h5>
                                <p class="text-muted mb-0 small">Activities</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fas fa-calendar text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted">Joined</small>
                                <div>{{ $user->created_at->format('d M Y') }}</div>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fas fa-clock text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted">Last Updated</small>
                                <div>{{ $user->updated_at->format('d M Y') }}</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-muted me-2" style="width: 20px;"></i>
                            <div>
                                <small class="text-muted">Email Verified</small>
                                <div>
                                    @if($user->email_verified_at)
                                        <span class="text-success">Verified</span>
                                        <small class="d-block text-muted">{{ $user->email_verified_at->format('d M Y') }}</small>
                                    @else
                                        <span class="text-danger">Not Verified</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column: Details -->
        <div class="col-md-8">
            <!-- Personal Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Full Name</label>
                                <div class="p-2 bg-light rounded">{{ $user->name }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Username</label>
                                <div class="p-2 bg-light rounded">
                                    {{ $user->username ? '@' . $user->username : '<span class="text-muted">Not set</span>' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Email Address</label>
                                <div class="p-2 bg-light rounded">
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Phone Number</label>
                                <div class="p-2 bg-light rounded">
                                    @if($user->phone)
                                        <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                            {{ $user->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Location</label>
                                <div class="p-2 bg-light rounded">
                                    {{ $user->location ?: '<span class="text-muted">Not set</span>' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Company</label>
                                <div class="p-2 bg-light rounded">
                                    {{ $user->company ?: '<span class="text-muted">Not set</span>' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Profile Cover</label>
                                <div class="p-2 bg-light rounded">
                                    @if($user->cover)
                                        <a href="{{ asset('storage/' . $user->cover) }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-image me-1"></i> View Cover
                                        </a>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Account Status</label>
                                <div class="p-2 bg-light rounded">
                                    <span class="{{ $user->status_badge_class }} px-2 py-1">
                                        {{ $user->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Peminjaman (jika ada) -->
            @if($user->peminjaman_count > 0)
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Recent Peminjaman</h5>
                    <span class="badge bg-primary">{{ $user->peminjaman_count }} total</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->peminjaman()->latest()->take(5)->get() as $peminjaman)
                                <tr>
                                    <td><strong>#{{ $peminjaman->id }}</strong></td>
                                    <td>{{ $peminjaman->created_at->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($peminjaman->status) {
                                                'dipinjam' => 'badge bg-warning',
                                                'dikembalikan' => 'badge bg-success',
                                                default => 'badge bg-secondary'
                                            };
                                        @endphp
                                        <span class="{{ $statusClass }} px-2 py-1">
                                            {{ ucfirst($peminjaman->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('peminjaman.show', $peminjaman->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($user->peminjaman_count > 5)
                    <div class="text-center mt-2">
                        <a href="#" class="btn btn-sm btn-outline-secondary">
                            View All ({{ $user->peminjaman_count }})
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-img img {
    object-fit: cover;
    border: 3px solid #f0f0f0 !important;
}

.badge.bg-success-light {
    background-color: rgba(40, 199, 111, 0.15) !important;
    color: #28c76f !important;
    border: 1px solid rgba(40, 199, 111, 0.3) !important;
}

.badge.bg-danger-light {
    background-color: rgba(234, 84, 85, 0.15) !important;
    color: #ea5455 !important;
    border: 1px solid rgba(234, 84, 85, 0.3) !important;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.p-2.bg-light.rounded {
    min-height: 38px;
    display: flex;
    align-items: center;
    color: #495057;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.card-title {
    color: #343a40;
    font-weight: 600;
}

.btn-outline-primary, .btn-outline-success {
    border-width: 1px;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

.list-unstyled li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.list-unstyled li:last-child {
    border-bottom: none;
}

.table-sm th,
.table-sm td {
    padding: 0.5rem;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.card-header {
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.card-footer {
    background-color: transparent;
    border-top: 1px solid #e9ecef;
}
</style>
@endpush
