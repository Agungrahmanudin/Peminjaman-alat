@extends('layouts.master')

@section('title', 'Account Settings')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Account Settings</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <style>
        /* ==== Tab Navigation (sama dengan profile) ==== */
        .profile-tabs {
            border-bottom: none !important;
            display: flex !important;
            flex-wrap: wrap;
            gap: 16px !important;
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
        /* Tab 1: Notification -> biru */
        .profile-tabs #notif-tab,
        .profile-tabs #notif-tab i {
            color: #4338ca !important;
        }
        .profile-tabs #notif-tab {
            border-color: #c7d2fe !important;
        }
        .profile-tabs #notif-tab:hover {
            background-color: #eef2ff !important;
        }
        .profile-tabs #notif-tab.active,
        .profile-tabs #notif-tab.active i {
            background-color: #4338ca !important;
            border-color: #4338ca !important;
            color: #fff !important;
            box-shadow: 0 6px 14px rgba(79, 70, 229, 0.35);
        }
        /* Tab 2: Privacy -> teal */
        .profile-tabs #privacy-tab,
        .profile-tabs #privacy-tab i {
            color: #0369a1 !important;
        }
        .profile-tabs #privacy-tab {
            border-color: #bae6fd !important;
        }
        .profile-tabs #privacy-tab:hover {
            background-color: #f0f9ff !important;
        }
        .profile-tabs #privacy-tab.active,
        .profile-tabs #privacy-tab.active i {
            background-color: #0369a1 !important;
            border-color: #0369a1 !important;
            color: #fff !important;
            box-shadow: 0 6px 14px rgba(14, 165, 233, 0.35);
        }
        /* Tab 3: Language -> orange */
        .profile-tabs #language-tab,
        .profile-tabs #language-tab i {
            color: #c2410c !important;
        }
        .profile-tabs #language-tab {
            border-color: #fed7aa !important;
        }
        .profile-tabs #language-tab:hover {
            background-color: #fff7ed !important;
        }
        .profile-tabs #language-tab.active,
        .profile-tabs #language-tab.active i {
            background-color: #c2410c !important;
            border-color: #c2410c !important;
            color: #fff !important;
            box-shadow: 0 6px 14px rgba(249, 115, 22, 0.35);
        }
    </style>

    <!-- Tab Navigation -->
    <ul class="nav profile-tabs mb-4" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="notif-tab" data-bs-toggle="tab"
                data-bs-target="#tab-notif" type="button" role="tab" aria-controls="tab-notif"
                aria-selected="true">
                <i class="fas fa-bell"></i><span>Notification</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="privacy-tab" data-bs-toggle="tab"
                data-bs-target="#tab-privacy" type="button" role="tab" aria-controls="tab-privacy"
                aria-selected="false">
                <i class="fas fa-user-shield"></i><span>Privacy</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="language-tab" data-bs-toggle="tab"
                data-bs-target="#tab-language" type="button" role="tab" aria-controls="tab-language"
                aria-selected="false">
                <i class="fas fa-globe"></i><span>Language & Region</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="settingsTabContent">
        <!-- Notification Preferences -->
        <div class="tab-pane fade show active" id="tab-notif" role="tabpanel" aria-labelledby="notif-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>Notification Preferences
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update-notifications') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label d-block">Email Notifications</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" checked>
                                <label class="form-check-label" for="email_notifications">Receive email notifications for account activity</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Push Notifications</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="push_notifications" name="push_notifications" checked>
                                <label class="form-check-label" for="push_notifications">Receive push notifications on your device</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">SMS Notifications</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications">
                                <label class="form-check-label" for="sms_notifications">Receive SMS for security alerts</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Privacy Settings -->
        <div class="tab-pane fade" id="tab-privacy" role="tabpanel" aria-labelledby="privacy-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-shield me-2"></i>Privacy Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update-privacy') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Profile Visibility</label>
                            <select class="form-select" name="profile_visibility">
                                <option value="public">Public – everyone can see your profile</option>
                                <option value="members">Only members</option>
                                <option value="private">Private – only you</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Show Activity Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activity_status" name="activity_status" checked>
                                <label class="form-check-label" for="activity_status">Allow others to see when you are online</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Allow Search Engines</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="search_engines" name="search_engines">
                                <label class="form-check-label" for="search_engines">Allow search engines to index your profile</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Privacy Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Language & Region -->
        <div class="tab-pane fade" id="tab-language" role="tabpanel" aria-labelledby="language-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-globe me-2"></i>Language & Region
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update-language') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="language" class="form-label">Language</label>
                            <select class="form-select" id="language" name="language">
                                <option value="en">English</option>
                                <option value="id">Bahasa Indonesia</option>
                                <option value="es">Español</option>
                                <option value="fr">Français</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                <option value="UTC">UTC</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links & Danger Zone (tetap di bawah) -->
    <div class="row mt-4">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Links</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ url('/profile') }}#tab-auth" class="list-group-item list-group-item-action">
                        <i class="fas fa-lock me-2"></i>Keamanan &amp; Kata Sandi
                    </a>
                    <a href="{{ url('/profile') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i>Edit Profil
                    </a>
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Untuk menghapus akun atau mengubah email/password, silakan kunjungi halaman Profile Anda.</p>
                    <a href="{{ url('/profile') }}#tab-auth" class="btn btn-outline-danger">
                        <i class="fas fa-arrow-right me-2"></i>Buka Authentication
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ingat tab aktif
    const tabButtons = document.querySelectorAll('#settingsTab button[data-bs-toggle="tab"]');
    const lastTab = localStorage.getItem('activeSettingsTab');
    if (lastTab) {
        const triggerEl = document.querySelector('#settingsTab button[data-bs-target="' + lastTab + '"]');
        if (triggerEl) {
            new bootstrap.Tab(triggerEl).show();
        }
    }
    tabButtons.forEach(function(btn) {
        btn.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem('activeSettingsTab', e.target.getAttribute('data-bs-target'));
        });
    });
});
</script>
@endsection