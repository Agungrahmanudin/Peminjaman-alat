<div class="header header-one">
    <!-- Logo di sebelah kiri - semuanya mengarah ke dashboard -->
    <div class="header-left header-left-one">
        <span class="user-img">
                    <img src="{{ Auth::user()->avatar_url }}" 
                         alt="{{ Auth::user()->name }}"
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; margin-top: 5px;">
                    <span class="status online"></span>
                </span>
    </div>
    
    <!-- Toggle Button -->
    <a href="javascript:void(0);" id="toggle_btn">
        <i class="fas fa-bars"></i>
    </a>
    
    <!-- Search Form -->
    <div class="top-nav-search">
        <form>
            <input type="text" class="form-control" placeholder="Search here">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    
    <!-- Mobile Button -->
    <a class="mobile_btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>
    
    <!-- User Menu (di sebelah kanan) -->
    <ul class="nav nav-tabs user-menu">
        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <i data-feather="bell"></i> 
                @if(isset($navNotifCount) && $navNotifCount > 0)
                    <span class="badge rounded-pill">{{ $navNotifCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifikasi</span>
                    <a href="{{ route('peminjaman.index') }}" class="clear-noti">Lihat Semua</a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list">
                        @forelse($navNotifications ?? [] as $notif)
                        <li class="notification-message">
                            <a href="{{ $notif['url'] }}">
                                <div class="media d-flex align-items-start">
                                    <span class="avatar avatar-sm flex-shrink-0 me-2">
                                        <span class="avatar-title rounded-circle bg-{{ $notif['color'] }}-light text-{{ $notif['color'] }}">
                                            <i data-feather="{{ $notif['icon'] }}" style="width:14px;height:14px;"></i>
                                        </span>
                                    </span>
                                    <div class="media-body">
                                        <p class="noti-details mb-0">{!! $notif['message'] !!}</p>
                                        <p class="noti-time mb-0">
                                            <span class="notification-time">{{ $notif['time'] }}</span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @empty
                        <li class="notification-message text-center py-3">
                            <p class="text-muted mb-0">Tidak ada notifikasi</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="{{ route('peminjaman.index') }}">Lihat Semua Peminjaman</a>
                </div>
            </div>
        </li>

        <!-- User Menu -->
        <li class="nav-item dropdown has-arrow main-drop">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <span class="user-img">
                    <img src="{{ Auth::user()->avatar_url }}" 
                         alt="{{ Auth::user()->name }}"
                         style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">
                    <span class="status online"></span>
                </span>
                <span>{{ Auth::user()->name }}</span>
                <span class="badge bg-{{ Auth::user()->role_badge_color }} ms-1">
                    {{ Auth::user()->role_display_name }}
                </span>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile') }}">
                    <i data-feather="user" class="me-1" style="pointer-events:none;"></i> Profile
                </a>
                <a class="dropdown-item" href="{{ route('settings') }}">
                    <i data-feather="settings" class="me-1" style="pointer-events:none;"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="dropdown-item w-100 text-start border-0 bg-transparent">
                        <i data-feather="log-out" class="me-1" style="pointer-events:none;"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</div>

@push('scripts')
<script>
    // Inisialisasi feather icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush