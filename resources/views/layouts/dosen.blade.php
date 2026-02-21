<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dosen Panel') - E-Learning UKI Toraja</title>

    {{-- CDN Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* HILANGKAN SCROLLBAR TAPI TETAP BISA SCROLL */
        ::-webkit-scrollbar {
            display: none !important; 
        }
        * {
            -ms-overflow-style: none !important;  
            scrollbar-width: none !important;  
        }

        html, body {
            font-family: 'Poppins', sans-serif; 
            background-color: #f7fafc; 
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }
        
        /* SIDEBAR STYLES (FIX TRANSITION AGAR TIDAK GLITCH) */
        #sidebar-wrapper {
            position: fixed; top: 0; left: 0; height: 100vh; width: 280px;
            background-color: #2d3748; z-index: 1000; display: flex; flex-direction: column;
            transition: left 0.3s ease-in-out; /* Hapus 'all' */
        }
        .sidebar-menu-area { flex-grow: 1; overflow-y: auto; overflow-x: hidden; }
        
        /* NAVBAR STYLES (FIX TRANSITION AGAR TIDAK GLITCH) */
        .top-navbar {
            position: fixed; top: 0; left: 280px; right: 0; height: 70px;
            background-color: #ffc107 !important; z-index: 900;
            border-bottom: 1px solid rgba(0,0,0,0.05); 
            transition: left 0.3s ease-in-out, width 0.3s ease-in-out; /* Hapus 'all' */
        }
        
        /* CONTENT WRAPPER (FIX TRANSITION AGAR TIDAK GLITCH) */
        #page-content-wrapper {
            margin-left: 280px; margin-top: 70px; width: calc(100% - 280px);
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out; /* Hapus 'all' */
        }
        
        /* RESPONSIVE LOGIC */
        @media (min-width: 769px) {
            #wrapper.toggled #sidebar-wrapper { left: -280px; }
            #wrapper.toggled .top-navbar { left: 0; width: 100%; }
            #wrapper.toggled #page-content-wrapper { margin-left: 0; width: 100%; }
        }
        @media (max-width: 768px) {
            #sidebar-wrapper { left: -280px; }
            .top-navbar { left: 0; width: 100%; } 
            #page-content-wrapper { margin-left: 0; width: 100%; }
            #wrapper.toggled #sidebar-wrapper { left: 0; }
            #wrapper.toggled .sidebar-overlay {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 999; display: block !important;
            }
        }

        #sidebar-wrapper .list-group-item {
            border-left: 3px solid transparent; color: #cbd5e0; background-color: transparent; border-bottom: none;
        }
        #sidebar-wrapper .list-group-item.active-menu {
            background: rgba(255, 255, 255, 0.05); border-left-color: #FACC15; color: #fff !important; font-weight: 600;
        }
        #sidebar-wrapper .list-group-item:hover { background-color: #4a5568; color: #fff; }
    </style>
</head>
<body>

    <div id="wrapper">
        {{-- SIDEBAR --}}
        <div id="sidebar-wrapper">
            <div class="sidebar-heading px-4 border-bottom border-gray-600 flex-shrink-0 d-flex align-items-center" style="height: 70px;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8" onerror="this.src='https://ui-avatars.com/api/?name=UK&background=random'">
                    <div class="d-flex flex-column leading-tight">
                        <span class="text-sm font-bold text-white uppercase tracking-wider">Dosen Panel</span>
                        <span class="text-[10px] text-yellow-400 font-normal">Sistem Akademik</span>
                    </div>
                    <button id="sidebar-close" class="btn btn-link text-gray-400 ms-auto d-md-none p-0">
                        <i class="bi bi-x-lg text-lg hover:text-white transition"></i>
                    </button>
                </div>
            </div>

            <div class="sidebar-menu-area list-group list-group-flush mt-2 pb-5" id="scrollable-sidebar-menu">
                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-3">Utama</p>
                <a href="{{ url('/dosen/dashboard') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('dosen/dashboard') ? 'active-menu' : '' }}">
                    <i class="bi bi-grid-fill fs-5"></i> Dashboard
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Akademik</p>
                <a href="{{ url('/dosen/mahasiswa') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('dosen/mahasiswa*') ? 'active-menu' : '' }}">
                    <i class="bi bi-people-fill fs-5"></i> Data Mahasiswa
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Manajemen Materi</p>
                <a href="{{ url('/dosen/materi') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('dosen/materi*') ? 'active-menu' : '' }}">
                    <i class="bi bi-collection-play-fill fs-5"></i> Susun Materi
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Evaluasi</p>
                <a href="{{ url('/dosen/kuis') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('dosen/kuis*') ? 'active-menu' : '' }}">
                    <i class="bi bi-patch-question-fill fs-5"></i> Kuis & Bank Soal
                </a>
                <a href="{{ url('/dosen/tugas') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('dosen/tugas*') ? 'active-menu' : '' }}">
                    <i class="bi bi-link-45deg fs-4"></i> Penugasan
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Interaksi</p>
                <a href="{{ url('/dosen/diskusi') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('dosen/diskusi*') ? 'active-menu' : '' }}">
                    <i class="bi bi-chat-dots-fill fs-5"></i> Tanya Jawab
                </a>
            </div>

            <div class="p-4 border-top border-gray-600 flex-shrink-0 bg-[#2d3748]">
                <a href="{{ url('/logout') }}" class="btn btn-danger btn-sm w-100 py-2 shadow-sm flex items-center justify-center gap-2 transition-colors duration-200">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        {{-- SCRIPT RESTORE SCROLL: Ditaruh di luar sidebar area agar tereksekusi tanpa mengganggu layout HTML --}}
        <script>
            (function() {
                var menu = document.getElementById('scrollable-sidebar-menu');
                var savedScroll = sessionStorage.getItem('dosenSidebarScroll');
                if (menu && savedScroll) {
                    menu.scrollTop = parseInt(savedScroll, 10);
                }
            })();
        </script>

        {{-- NAVBAR ATAS --}}
        <nav class="navbar navbar-expand-lg top-navbar px-3 px-md-4 shadow-sm">
            <div class="container-fluid px-0">
                <button class="btn btn-link text-dark p-0 border-0 me-2 d-flex align-items-center" id="menu-toggle">
                    <i class="bi bi-list" style="font-size: 1.8rem;"></i>
                </button>

                <div class="ms-auto d-flex align-items-center gap-2 md:gap-3">
                    <div class="text-end" style="line-height: 1.1;">
                        <p class="mb-0 text-xs md:text-sm font-bold text-gray-900 uppercase tracking-tighter truncate max-w-[120px] md:max-w-none">
                            {{ session('nama', 'Dosen Pengampu') }}
                        </p>
                        <p class="mb-0 text-[9px] md:text-[10px] text-gray-800 font-semibold opacity-75">DOSEN</p>
                    </div>
                    
                    <a href="{{ url('/dosen/profil') }}" title="Pengaturan Akun & Profil">
                        <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/' . session('foto')) : asset('images/logo_ukit.png') }}"
                             class="rounded-full border-2 border-white shadow-sm bg-white hover:opacity-80 transition object-cover"
                             style="width: 36px; height: 36px; object-fit: cover;"
                             onerror="this.src='https://ui-avatars.com/api/?name=Dosen&background=random'">
                    </a>
                </div>
            </div>
        </nav>

        {{-- CONTENT AREA --}}
        <div id="page-content-wrapper">
            <div class="container-fluid px-3 md:px-4 py-4 overflow-y-auto" style="height: calc(100vh - 70px);">
                @yield('content')
            </div>
        </div>

        <div class="sidebar-overlay d-none" id="overlay"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const wrapper = document.getElementById("wrapper");
        const toggleBtn = document.getElementById("menu-toggle");
        const closeBtn = document.getElementById("sidebar-close");
        const overlay = document.getElementById("overlay");

        function toggleSidebar() { wrapper.classList.toggle("toggled"); }
        if(toggleBtn) toggleBtn.onclick = function(e) { e.preventDefault(); toggleSidebar(); };
        if(closeBtn) closeBtn.onclick = function(e) { e.preventDefault(); wrapper.classList.remove("toggled"); };
        if(overlay) overlay.onclick = function() { wrapper.classList.remove("toggled"); };

        // LOGIKA PENYIMPANAN SCROLL BARU YANG LEBIH TEPAT
        window.addEventListener('beforeunload', function() {
            var menu = document.getElementById('scrollable-sidebar-menu');
            if (menu) {
                sessionStorage.setItem('dosenSidebarScroll', menu.scrollTop);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>