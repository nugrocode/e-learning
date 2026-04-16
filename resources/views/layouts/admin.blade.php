<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - E-Learning UKI Toraja</title>

    {{-- Favicon Logo --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo_ukit.png') }}">

    {{-- CDN Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        /* 1. HILANGKAN SCROLLBAR (TETAP BISA DI-SCROLL) */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        /* 2. FIX TINGGI HEADER AGAR SIMETRIS */
        #sidebar-wrapper .sidebar-heading,
        .top-navbar {
            height: 70px !important;
            min-height: 70px !important;
            max-height: 70px !important;
            display: flex;
            align-items: center;
        }

        /* 3. STYLE SIDEBAR ASLI (GELAP) */
        #sidebar-wrapper {
            background-color: #2d3748; 
        }
        #sidebar-wrapper .list-group-item {
            border-left: 3px solid transparent;
            color: #cbd5e0;
        }
        /* State Aktif (Kuning Emas) */
        #sidebar-wrapper .list-group-item.active-menu {
            background: rgba(255, 255, 255, 0.05);
            border-left-color: #FACC15; 
            color: #fff !important;
            font-weight: 600;
        }
        #sidebar-wrapper .list-group-item:hover {
            background-color: #4a5568;
            color: #fff;
        }
    </style>

    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">

        {{-- SIDEBAR WRAPPER --}}
        <div id="sidebar-wrapper" class="d-flex flex-column h-100 border-end border-gray-700">
            
            {{-- HEADER SIDEBAR --}}
            <div class="sidebar-heading px-4 border-bottom border-gray-600 flex-shrink-0">
                <div class="d-flex align-items-center gap-3 w-100">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8 md:w-9" onerror="this.src='https://ui-avatars.com/api/?name=UK&background=random'">
                    
                    <div class="d-flex flex-column leading-tight overflow-hidden">
                        <span class="text-sm md:text-base font-bold text-white tracking-wide whitespace-nowrap">Administrator</span>
                        <span class="text-[10px] md:text-xs text-yellow-400 font-normal whitespace-nowrap">Control Panel</span>
                    </div>

                    {{-- Tombol Tutup Sidebar (Hanya di HP) --}}
                    <button id="sidebar-close" class="btn btn-link text-gray-400 hover:text-white ms-auto d-md-none p-0">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
            </div>

            {{-- MENU LIST (SCROLLABLE TANPA BAR) --}}
            <div class="list-group list-group-flush mt-2 overflow-y-auto flex-grow-1 no-scrollbar pb-5" id="scrollable-sidebar-menu">
                
                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-3">Utama</p>
                <a href="{{ url('/admin/dashboard') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('admin/dashboard') ? 'active-menu' : '' }}">
                    <i class="bi bi-grid-fill text-lg"></i> Dashboard
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Pengguna</p>
                <a href="{{ url('/admin/users') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('admin/users*') ? 'active-menu' : '' }}">
                    <i class="bi bi-people-fill text-lg"></i> Manajemen User
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Master Data</p>
                <a href="{{ url('/admin/konsentrasi') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('admin/konsentrasi*') ? 'active-menu' : '' }}">
                    <i class="bi bi-diagram-3-fill text-lg"></i> Konsentrasi
                </a>
                <a href="{{ url('/admin/bank-mk') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('admin/bank-mk*') ? 'active-menu' : '' }}">
                    <i class="bi bi-hdd-stack-fill text-lg"></i> Bank Mata Kuliah
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Akademik</p>
                <a href="{{ url('/admin/kurikulum') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('admin/kurikulum*') ? 'active-menu' : '' }}">
                    <i class="bi bi-collection-fill text-lg"></i> Distribusi Mata kuliah
                </a>
                <a href="{{ url('/admin/pengumuman') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('admin/pengumuman*') ? 'active-menu' : '' }}">
                    <i class="bi bi-megaphone-fill text-lg"></i> Pengumuman
                </a>
            </div>

            {{-- FOOTER SIDEBAR --}}
            <div class="p-4 border-top border-gray-600 flex-shrink-0 bg-[#2d3748]">
                <a href="{{ url('/logout') }}" class="btn btn-danger w-100 flex items-center justify-center gap-2 text-sm shadow-md">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        {{-- PAGE CONTENT --}}
        <div id="page-content-wrapper">

            {{-- NAVBAR --}}
            <nav class="navbar navbar-expand-lg top-navbar px-3 md:px-4 shadow-sm border-bottom d-flex align-items-center bg-white">
                <div class="container-fluid px-0">

                    <button class="btn btn-link text-dark p-0 me-3" id="menu-toggle">
                        <i class="bi bi-list text-2xl"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-2 md:gap-3">
                        {{-- DIUBAH: Dibuat responsif seperti mahasiswa --}}
                        <div class="text-end" style="line-height: 1.2;">
                            <p class="mb-0 text-[10px] md:text-sm font-bold text-gray-800 truncate max-w-[120px] md:max-w-none">{{ session('nama') ?? 'Administrator' }}</p>
                            <p class="mb-0 text-[8px] md:text-[10px] text-gray-700 uppercase font-semibold">Super Admin</p>
                        </div>
                        <div class="relative">
                            <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/' . session('foto')) : asset('images/logo_ukit.png') }}"
                                 class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-white shadow-sm"
                                 style="aspect-ratio: 1/1;"
                                 onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                        </div>
                    </div>
                </div>
            </nav>

            {{-- MAIN CONTENT (NO SCROLLBAR) --}}
            <div class="container-fluid px-3 md:px-4 py-4 md:py-5 overflow-y-auto no-scrollbar" style="height: calc(100vh - 70px); background-color: #f7fafc;">
                @yield('content')
            </div>

        </div>
    </div>

    {{-- Script Toggle Sidebar & Keep Scroll Position --}}
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        var sidebarClose = document.getElementById("sidebar-close");
        var sidebarMenu = document.getElementById("scrollable-sidebar-menu");

        function toggleSidebar() {
            el.classList.toggle("toggled");
            if (window.innerWidth <= 768) {
                document.body.classList.toggle("sidebar-open");
            }
        }

        if(toggleButton) toggleButton.onclick = function(e) {
            e.preventDefault();
            toggleSidebar();
        };

        if(sidebarClose) sidebarClose.onclick = function(e) {
            e.preventDefault();
            toggleSidebar();
        };

        // Tutup Sidebar saat klik overlay (Mobile)
        el.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && el.classList.contains('toggled')) {
                const sidebar = document.getElementById('sidebar-wrapper');
                const menuBtn = document.getElementById('menu-toggle');
                if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                    toggleSidebar();
                }
            }
        });

        // =========================================================================
        // SCRIPT UNTUK MENYIMPAN POSISI SCROLL SIDEBAR MENGGUNAKAN SESSION STORAGE
        // =========================================================================
        if (sidebarMenu) {
            const savedScrollPosition = sessionStorage.getItem("adminSidebarScroll");
            if (savedScrollPosition) {
                sidebarMenu.scrollTop = savedScrollPosition;
            }

            sidebarMenu.addEventListener("scroll", function() {
                sessionStorage.setItem("adminSidebarScroll", sidebarMenu.scrollTop);
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>