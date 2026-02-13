<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Dosen') - E-Learning UKI Toraja</title>

    {{-- 1. CDN LIBRARIES (Sama dengan User/Admin) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- 2. CUSTOM CSS (Menggunakan style.css yang sama agar konsisten) --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        /* CSS Tambahan Khusus Layout ini (No Scrollbar & Fix Height) */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Paksa Tinggi Navbar & Header Sidebar sama (70px) agar lurus */
        #sidebar-wrapper .sidebar-heading, .top-navbar {
            height: 70px !important; 
            min-height: 70px !important; 
            max-height: 70px !important;
            display: flex; align-items: center;
        }

        #sidebar-wrapper { background-color: #2d3748; } /* Warna Dark Grey */
        #sidebar-wrapper .list-group-item { border-left: 3px solid transparent; color: #cbd5e0; }
        
        /* State Aktif Menu (Kuning Emas) */
        #sidebar-wrapper .list-group-item.active-menu {
            background: rgba(255, 255, 255, 0.05); 
            border-left-color: #FACC15; 
            color: #fff !important; 
            font-weight: 600;
        }
        #sidebar-wrapper .list-group-item:hover { background-color: #4a5568; color: #fff; }
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">

        {{-- ================= SIDEBAR ================= --}}
        <div id="sidebar-wrapper" class="d-flex flex-column h-100 border-end border-gray-700">
            
            {{-- A. HEADER SIDEBAR --}}
            <div class="sidebar-heading px-4 border-bottom border-gray-600 flex-shrink-0">
                <div class="d-flex align-items-center gap-3 w-100">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8 md:w-9">
                    <div class="d-flex flex-column leading-tight overflow-hidden">
                        <span class="text-sm md:text-base font-bold text-white tracking-wide whitespace-nowrap">Dosen Panel</span>
                        <span class="text-[10px] md:text-xs text-yellow-400 font-normal whitespace-nowrap">Manajemen Kelas</span>
                    </div>
                    {{-- Tombol Tutup (Mobile Only) --}}
                    <button id="sidebar-close" class="btn btn-link text-gray-400 hover:text-white ms-auto d-md-none p-0">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
            </div>

            {{-- B. MENU LIST (Menu Khusus Dosen) --}}
            <div class="list-group list-group-flush mt-2 overflow-y-auto flex-grow-1 no-scrollbar pb-5">
                
                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-3">Utama</p>
                
                {{-- Menu 1: Dashboard --}}
                <a href="{{ url('/dosen/dashboard') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('dosen/dashboard') ? 'active-menu' : '' }}">
                    <i class="bi bi-speedometer2 text-lg"></i> Dashboard
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Akademik</p>

                {{-- Menu 2: Kelas Ajar (Inti Fitur) --}}
                <a href="{{ url('/dosen/kelas') }}" 
                   class="list-group-item bg-transparent border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 
                   {{ Request::is('dosen/kelas*') ? 'active-menu' : '' }}">
                    <i class="bi bi-easel-fill text-lg"></i> Kelas Ajar Saya
                </a>

            </div>

            {{-- C. FOOTER SIDEBAR --}}
            <div class="p-4 border-top border-gray-600 flex-shrink-0 bg-[#2d3748]">
                <a href="{{ url('/logout') }}" class="btn btn-danger w-100 flex items-center justify-center gap-2 text-sm shadow-md">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        {{-- ================= KONTEN UTAMA ================= --}}
        <div id="page-content-wrapper">

            {{-- A. NAVBAR --}}
            <nav class="navbar navbar-expand-lg top-navbar px-3 md:px-4 shadow-sm border-bottom d-flex align-items-center bg-white fixed-header-height">
                <div class="container-fluid px-0">
                    <button class="btn btn-link text-dark p-0 me-3" id="menu-toggle">
                        <i class="bi bi-list text-2xl"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="text-end hidden md:block" style="line-height: 1.2;">
                            {{-- Mengambil Nama Dosen dari Session --}}
                            <p class="mb-0 text-sm font-bold text-gray-800">{{ session('nama') }}</p>
                            <p class="mb-0 text-xs text-gray-700 uppercase">Dosen Pengajar</p>
                        </div>
                        <div class="relative">
                            <img src="{{ asset('images/' . (session('foto') ?? 'default.png')) }}" 
                                 class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-white shadow-sm"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ session('nama') }}'">
                        </div>
                    </div>
                </div>
            </nav>

            {{-- B. AREA KONTEN (Yield) --}}
            <div class="container-fluid px-3 md:px-4 py-4 md:py-5 overflow-y-auto" style="height: calc(100vh - 70px);">
                @yield('content')
            </div>

        </div>
    </div>

    {{-- 3. JAVASCRIPT TOGGLE (Sama dengan User) --}}
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        var sidebarClose = document.getElementById("sidebar-close");

        function toggleSidebar() {
            el.classList.toggle("toggled");
            if (window.innerWidth <= 768) { document.body.classList.toggle("sidebar-open"); }
        }

        if(toggleButton) toggleButton.onclick = toggleSidebar;
        if(sidebarClose) sidebarClose.onclick = toggleSidebar;

        // Tutup Sidebar saat klik area luar (Mobile)
        el.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && el.classList.contains('toggled')) {
                const sidebar = document.getElementById('sidebar-wrapper');
                const menuBtn = document.getElementById('menu-toggle');
                if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) { toggleSidebar(); }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>