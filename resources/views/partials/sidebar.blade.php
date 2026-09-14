<style>
    * { box-sizing: border-box; }
    #sidebar-menu { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
    #sidebar-menu ul { padding: 0; margin: 0; list-style: none; padding-bottom: 150px; }
    #sidebar-menu ul li { margin-bottom: 4px; position: relative; }
    #sidebar-menu ul li:not(.menu-title) { padding: 0 8px; }
    #sidebar-menu ul li a { display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; transition: all 0.2s ease-in-out; color: #4b5563; text-decoration: none; font-size: 14px; position: relative; }
    #sidebar-menu ul li a i { margin-right: 12px; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
    #sidebar-menu ul li a .feather { width: 18px; height: 18px; stroke-width: 2; }
    #sidebar-menu ul li a:hover { background-color: #f3f4f6; color: #1f2937; transform: translateX(2px); }
    #sidebar-menu ul li.active > a { background-color: #e0f2fe; color: #0369a1; font-weight: 500; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    #sidebar-menu ul li.active > a::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 3px; height: 60%; background-color: #2563eb; border-radius: 0 3px 3px 0; }
    .sidebar-footer-gradient { position: absolute; bottom: 0; left: 0; right: 0; height: 150px; background: linear-gradient(to top, rgba(255,255,255,1) 0%, rgba(249,250,251,0.9) 20%, rgba(243,244,246,0.7) 40%, rgba(229,231,235,0.4) 60%, rgba(209,213,219,0.2) 80%, transparent 100%); pointer-events: none; z-index: 1; }
    .slimscroll { height: calc(100vh - 70px) !important; overflow-y: auto !important; overflow-x: hidden; }
    .slimscroll::-webkit-scrollbar { width: 6px; }
    .slimscroll::-webkit-scrollbar-track { background: transparent; }
    .slimscroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .slimscroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    @media (max-width: 768px) {
        #sidebar-menu ul li a { padding: 12px 8px; font-size: 13px; }
        #sidebar-menu ul li a i { margin-right: 8px; }
        .sidebar-footer-gradient { height: 80px; }
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
    #sidebar-menu ul li { animation: fadeIn 0.3s ease-out forwards; opacity: 0; }
    #sidebar-menu ul li:nth-child(1) { animation-delay: 0.05s; }
    #sidebar-menu ul li:nth-child(2) { animation-delay: 0.1s; }
    #sidebar-menu ul li:nth-child(3) { animation-delay: 0.15s; }
    #sidebar-menu ul li:nth-child(4) { animation-delay: 0.2s; }
    #sidebar-menu ul li:nth-child(5) { animation-delay: 0.25s; }
    #sidebar-menu ul li:nth-child(6) { animation-delay: 0.3s; }
    #sidebar-menu ul li:nth-child(7) { animation-delay: 0.35s; }
    #sidebar-menu ul li:nth-child(8) { animation-delay: 0.4s; }
    #sidebar-menu ul li:nth-child(9) { animation-delay: 0.45s; }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>

                <li class="menu-title"><span>Main</span></li>

                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i data-feather="home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- Customers & Users: hanya admin & petugas --}}
                @if(auth()->user()->role !== 'peminjam')
                    <li class="{{ request()->routeIs('customers*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}">
                            <i data-feather="users"></i>
                            <span>Customers</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">
                            <i data-feather="user"></i>
                            <span>Users</span>
                        </a>
                    </li>
                @endif

                {{-- Alat: semua role bisa lihat, peminjam hanya read --}}
                <li class="{{ request()->routeIs('alat*') ? 'active' : '' }}">
                    <a href="{{ route('alat.index') }}">
                        <i data-feather="tool"></i>
                        <span>Alat</span>
                        @if(auth()->user()->role === 'peminjam')
                            <span class="ms-auto badge bg-light text-secondary border" style="font-size:9px; padding:2px 5px;">view</span>
                        @endif
                    </a>
                </li>

                <li class="menu-title"><span>Transaksi</span></li>

                <li class="{{ request()->routeIs('peminjaman*') ? 'active' : '' }}">
                    <a href="{{ route('peminjaman.index') }}">
                        <i data-feather="clipboard"></i>
                        <span>Peminjaman</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('pengembalian*') ? 'active' : '' }}">
                    <a href="{{ route('pengembalian.index') }}">
                        <i data-feather="rotate-ccw"></i>
                        <span>Pengembalian</span>
                    </a>
                </li>

                {{-- Laporan: hanya admin & petugas --}}
                @if(auth()->user()->role !== 'peminjam')
                    <li class="{{ request()->routeIs('reports*') ? 'active' : '' }}">
                        <a href="{{ route('reports.index') }}">
                            <i data-feather="bar-chart-2"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                @endif

                <li class="menu-title"><span>Akun</span></li>

                <li class="{{ request()->routeIs('profile*') ? 'active' : '' }}">
                    <a href="{{ route('profile') }}">
                        <i data-feather="user"></i>
                        <span>Profile</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('settings*') ? 'active' : '' }}">
                    <a href="{{ route('settings') }}">
                        <i data-feather="settings"></i>
                        <span>Settings</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="sidebar-footer-gradient"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>