<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Learning UKI Toraja')</title>

    {{-- CDN Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">

        {{-- ========================================= --}}
        {{-- SIDEBAR WRAPPER                           --}}
        {{-- ========================================= --}}
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <div class="d-flex align-items-center gap-2 md:gap-3">
                    {{-- Logo: Ukuran otomatis menyesuaikan HP/Laptop --}}
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8 md:w-9" onerror="this.src='https://ui-avatars.com/api/?name=U+K&background=random'">

                    <div class="d-flex flex-column leading-tight">
                        {{-- Teks: Ukuran otomatis (Responsive Text) --}}
                        <span class="text-sm md:text-base font-bold text-white tracking-wide whitespace-nowrap">E-Learning</span>
                        <span class="text-[10px] md:text-xs text-gray-400 font-normal whitespace-nowrap">UKI Toraja</span>
                    </div>
                </div>

                {{-- Tombol Tutup Sidebar (Hanya Muncul di Mobile) --}}
                <button id="sidebar-close" class="btn btn-link text-gray-400 hover:text-white p-0 d-md-none">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <div class="list-group list-group-flush mt-4">
                <p class="px-4 text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Menu Utama</p>

                <a href="{{ url('/dashboard') }}" class="list-group-item list-group-item-action {{ Request::is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill text-lg"></i> Beranda
                </a>

                <a href="{{ url('/jalur-belajar') }}" class="list-group-item list-group-item-action {{ Request::is('jalur-belajar*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill text-lg"></i> Jalur Belajar
                </a>

                <a href="{{ url('/kelas-saya') }}" class="list-group-item list-group-item-action {{ Request::is('kelas-saya*') ? 'active' : '' }}">
                    <i class="bi bi-book-half text-lg"></i> Kelas Saya
                </a>

                <a href="{{ url('/diskusi') }}" class="list-group-item list-group-item-action {{ Request::is('diskusi*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text-fill text-lg"></i> Diskusi Dengan AI
                </a>
            </div>

            <div class="mt-auto p-4">
                <a href="{{ url('/bantuan') }}" class="btn btn-outline-secondary w-100 text-white border-gray-600 hover:bg-gray-700 flex items-center justify-center gap-2 mb-2 text-sm">
                    <i class="bi bi-headset"></i> Bantuan
                </a>
                <a href="{{ url('/logout') }}" class="btn btn-danger w-100 flex items-center justify-center gap-2 text-sm">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- PAGE CONTENT                              --}}
        {{-- ========================================= --}}
        <div id="page-content-wrapper">

            {{-- NAVBAR --}}
            <nav class="navbar navbar-expand-lg top-navbar px-3 md:px-4 py-3 shadow-sm border-bottom">
                <div class="container-fluid px-0">

                    {{-- Tombol Toggle Hamburger --}}
                    <button class="btn btn-link text-dark p-0 me-3" id="menu-toggle">
                        <i class="bi bi-list text-2xl"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-3 md:gap-4">

                        {{-- 1. NOTIFIKASI --}}
                        @php
                            $userId = session('user_id');
                            $myNotifs = \App\Models\Notification::with('sender', 'material')
                                        ->where('user_id', $userId)
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)->get();
                            $unreadCount = \App\Models\Notification::where('user_id', $userId)->where('is_read', 0)->count();
                        @endphp

                        <div class="dropdown">
                            <a href="#" class="text-dark position-relative" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill text-xl hover:text-gray-700 transition"></i>
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem;">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-xl mt-3 p-0" aria-labelledby="notifDropdown" style="width: 280px; md:width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="p-3 border-b bg-gray-50 fw-bold text-sm text-gray-700 sticky-top bg-white">Notifikasi</li>
                                @forelse($myNotifs as $notif)
                                    <li>
                                        <a class="dropdown-item p-3 border-b hover:bg-gray-50 {{ $notif->is_read == 0 ? 'bg-blue-50' : '' }}" href="{{ url('/notifikasi/' . $notif->id) }}">
                                            <div class="d-flex gap-2 align-items-start">
                                                <i class="bi bi-chat-dots-fill text-blue-600 mt-1 flex-shrink-0"></i>
                                                <div style="line-height: 1.2;">
                                                    <p class="text-xs text-gray-800 mb-1" style="white-space: normal;">
                                                        <strong>{{ $notif->sender->nama_lengkap ?? 'User' }}</strong> membalas komentar.
                                                    </p>
                                                    <small class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="p-4 text-center text-xs text-gray-500">Tidak ada notifikasi baru.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- 2. PROFIL USER --}}
                        <div class="d-flex align-items-center gap-3">
                            {{-- Nama & Role (Disembunyikan di HP agar hemat tempat) --}}
                            <div class="text-right hidden md:block" style="line-height: 1.2;">
                                <p class="mb-0 text-sm font-bold text-gray-800">{{ session('nama') }}</p>
                                <p class="mb-0 text-xs text-gray-700 uppercase">{{ session('role') }}</p>
                            </div>

                            {{-- Foto Profil --}}
                            <a href="{{ url('/profil') }}" class="relative group" title="Edit Profil">
                                <img src="{{ asset('images/' . (session('foto') ?? 'default.png')) }}"
                                     class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border border-white shadow-sm transition group-hover:opacity-80"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ session('nama') }}'">
                            </a>
                        </div>

                    </div>
                </div>
            </nav>

            {{-- KONTEN UTAMA --}}
            <div class="container-fluid px-3 md:px-4 py-4 md:py-5">
                @yield('content')
            </div>

        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SCRIPT TOGGLE SIDEBAR (OVERLAY LOGIC)     --}}
    {{-- ========================================= --}}
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        var sidebarClose = document.getElementById("sidebar-close");

        function toggleSidebar() {
            el.classList.toggle("toggled");

            // Tambahkan class 'sidebar-open' ke body untuk mencegah scroll di background saat menu HP terbuka
            if (window.innerWidth <= 768) {
                document.body.classList.toggle("sidebar-open");
            }
        }

        // Event Listeners Tombol
        if(toggleButton) toggleButton.onclick = toggleSidebar;
        if(sidebarClose) sidebarClose.onclick = toggleSidebar;

        // Logic: Tutup Sidebar saat klik area gelap (Overlay) di HP
        el.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && el.classList.contains('toggled')) {
                const sidebar = document.getElementById('sidebar-wrapper');
                const menuBtn = document.getElementById('menu-toggle');

                // Jika yang diklik BUKAN sidebar dan BUKAN tombol menu, maka tutup sidebar
                if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                    toggleSidebar();
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
