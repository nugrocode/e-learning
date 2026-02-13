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
    <style>
        /* CSS UNTUK MENYEMBUNYIKAN SCROLLBAR TAPI TETAP BISA SCROLL */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        #sidebar-wrapper .sidebar-heading, .top-navbar {
            height: 70px !important; min-height: 70px !important; max-height: 70px !important;
            display: flex; align-items: center;
        }

        /* Warna Sidebar Gelap */
        #sidebar-wrapper { background-color: #2d3748; }
        #sidebar-wrapper .list-group-item {
            border-left: 3px solid transparent; color: #cbd5e0; background-color: transparent;
        }
        #sidebar-wrapper .list-group-item.active-menu {
            background: rgba(255, 255, 255, 0.05); border-left-color: #FACC15; 
            color: #fff !important; font-weight: 600;
        }
        #sidebar-wrapper .list-group-item:hover { background-color: #4a5568; color: #fff; }
    </style>
    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">

        {{-- SIDEBAR WRAPPER (GELAP) --}}
        <div id="sidebar-wrapper" class="d-flex flex-column h-100 border-end border-gray-700">
            
            {{-- HEADER SIDEBAR --}}
            <div class="sidebar-heading px-4 border-bottom border-gray-600 flex-shrink-0">
                <div class="d-flex align-items-center gap-3 w-100">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8 md:w-9" onerror="this.src='https://ui-avatars.com/api/?name=UK&background=random'">
                    <div class="d-flex flex-column leading-tight overflow-hidden">
                        <span class="text-sm md:text-base font-bold text-white tracking-wide whitespace-nowrap">E-LEARNING</span>
                        <span class="text-[10px] md:text-xs text-yellow-400 font-normal whitespace-nowrap">Mahasiswa Panel</span>
                    </div>
                    <button id="sidebar-close" class="btn btn-link text-gray-400 hover:text-white ms-auto d-md-none p-0">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
            </div>

            {{-- MENU LIST MAHASISWA --}}
            {{-- Perhatikan class 'no-scrollbar' di sini --}}
            <div class="list-group list-group-flush mt-2 overflow-y-auto flex-grow-1 no-scrollbar pb-5">
                
                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-3">Menu Utama</p>
                
                <a href="{{ url('/dashboard') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('dashboard') ? 'active-menu' : '' }}">
                    <i class="bi bi-grid-fill text-lg"></i> Dashboard
                </a>

                <a href="{{ url('/jalur-belajar') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('jalur-belajar*') || Request::is('mata-kuliah*') ? 'active-menu' : '' }}">
                    <i class="bi bi-diagram-3-fill text-lg"></i> Jalur Belajar
                </a>

                <a href="{{ url('/kelas-saya') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('kelas-saya') || Request::is('belajar*') ? 'active-menu' : '' }}">
                    <i class="bi bi-journal-bookmark-fill text-lg"></i> Kelas Saya
                </a>

                <p class="px-4 text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Lainnya</p>

                <a href="{{ url('/diskusi') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('diskusi') ? 'active-menu' : '' }}">
                    <i class="bi bi-chat-dots-fill text-lg"></i> Diskusi & Tanya AI
                </a>

                <a href="{{ url('/bantuan') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 transition-colors duration-200 {{ Request::is('bantuan') ? 'active-menu' : '' }}">
                    <i class="bi bi-question-circle-fill text-lg"></i> Bantuan
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

            {{-- NAVBAR ATAS --}}
            <nav class="navbar navbar-expand-lg top-navbar px-3 md:px-4 shadow-sm border-bottom d-flex align-items-center bg-white">
                <div class="container-fluid px-0">
                    <button class="btn btn-link text-dark p-0 me-3" id="menu-toggle">
                        <i class="bi bi-list text-2xl"></i>
                    </button>

                    <h2 class="m-0 font-bold text-gray-700 text-base md:text-lg d-none d-md-block">
                        @yield('title')
                    </h2>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="text-end hidden md:block" style="line-height: 1.2;">
                            <p class="mb-0 text-sm font-bold text-gray-800">{{ session('nama') }}</p>
                            <p class="mb-0 text-xs text-gray-700 uppercase">{{ session('role') }}</p>
                        </div>
                        
                        {{-- DROPDOWN PROFIL --}}
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="relative">
                                    {{-- FOTO PROFIL NAVBAR (JUGA ANTI GEPENG) --}}
                                    <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/' . session('foto')) : asset('images/logo_ukit.png') }}"
                                         class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-white shadow-sm"
                                         style="aspect-ratio: 1/1; object-fit: cover;"
                                         onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-xl mt-2 p-2" aria-labelledby="dropdownUser">
                                <li>
                                    <a class="dropdown-item rounded-lg py-2 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-700" href="{{ url('/profil') }}">
                                        <i class="bi bi-person-gear me-2"></i> Edit Profil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item rounded-lg py-2 text-sm font-medium text-red-500 hover:bg-red-50" href="{{ url('/logout') }}">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            {{-- KONTEN UTAMA --}}
            {{-- [PERBAIKAN 2: HAPUS SCROLLBAR] --}}
            {{-- Ditambahkan class 'no-scrollbar' di div ini --}}
            <div class="container-fluid px-3 md:px-4 py-4 md:py-5 overflow-y-auto no-scrollbar" style="height: calc(100vh - 70px); background-color: #f7fafc;">
                @yield('content')
            </div>

        </div>
        
        {{-- Overlay Mobile --}}
        <div class="overlay" id="sidebar-close-overlay"></div>
    </div>

    {{-- SCRIPT --}}
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        var sidebarClose = document.getElementById("sidebar-close");
        var overlay = document.getElementById("sidebar-close-overlay");

        function toggleSidebar() {
            el.classList.toggle("toggled");
            if (window.innerWidth <= 768) {
                document.body.classList.toggle("sidebar-open");
            }
        }

        if(toggleButton) toggleButton.onclick = toggleSidebar;
        if(sidebarClose) sidebarClose.onclick = toggleSidebar;
        if(overlay) overlay.onclick = toggleSidebar;

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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>