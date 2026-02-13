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

    <style>
        /* 1. CSS PENGHILANG VISUAL SCROLLBAR (TETAP BISA SCROLL) */
        .no-scrollbar::-webkit-scrollbar { display: none !important; }
        .no-scrollbar { 
            -ms-overflow-style: none !important; 
            scrollbar-width: none !important; 
        }

        /* 2. LAYOUT DASAR */
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f7fafc;
            overflow: hidden; /* Mencegah double scrollbar pada browser */
            height: 100vh;
        }

        /* 3. SIMETRI HEADER (70px) */
        #sidebar-wrapper .sidebar-heading, 
        .top-navbar {
            height: 70px !important; 
            min-height: 70px !important; 
            max-height: 70px !important;
            display: flex; 
            align-items: center;
        }

        /* 4. SIDEBAR DOSEN (DARK MODERN) */
        #sidebar-wrapper { 
            background-color: #2d3748; 
            width: 280px;
            transition: margin 0.25s ease-out;
            flex-shrink: 0;
            z-index: 1000;
        }
        #sidebar-wrapper .list-group-item {
            border-left: 3px solid transparent; 
            color: #cbd5e0;
            background-color: transparent;
            border-bottom: none;
        }
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

        /* 5. NAVBAR KUNING */
        .top-navbar { background-color: #ffc107 !important; border-bottom: 1px solid rgba(0,0,0,0.05); }
        
        #page-content-wrapper { 
            flex-grow: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-divider { 
            font-size: 10px; 
            color: #718096; 
            padding: 20px 20px 5px; 
            text-transform: uppercase; 
            font-weight: bold; 
            letter-spacing: 1px; 
        }

        /* RESPONSIVE TOGGLE */
        #wrapper.toggled #sidebar-wrapper { margin-left: -280px; }
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -280px; position: fixed; height: 100vh; }
            #wrapper.toggled #sidebar-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">

        {{-- SIDEBAR WRAPPER --}}
        <div id="sidebar-wrapper" class="d-flex flex-column h-100 border-end border-gray-700">
            
            <div class="sidebar-heading px-4 border-bottom border-gray-600 flex-shrink-0">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8">
                    <div class="d-flex flex-column leading-tight">
                        <span class="text-sm font-bold text-white uppercase tracking-wider">Dosen Panel</span>
                        <span class="text-[10px] text-yellow-400 font-normal">Sistem Akademik</span>
                    </div>
                </div>
            </div>

            {{-- MENU LIST (SCROLLABLE NO BAR) --}}
            <div class="list-group list-group-flush mt-2 overflow-y-auto flex-grow-1 no-scrollbar pb-5">
                
                <p class="sidebar-divider">Utama</p>
                <a href="{{ url('/dosen/dashboard') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/dashboard') ? 'active-menu' : '' }}">
                    <i class="bi bi-grid-fill fs-5"></i> Dashboard
                </a>

                <p class="sidebar-divider">Akademik & Kelas</p>
                <a href="{{ url('/dosen/kelas') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/kelas*') ? 'active-menu' : '' }}">
                    <i class="bi bi-journal-bookmark-fill fs-5"></i> Kelas Ajar Saya
                </a>
                <a href="{{ url('/dosen/mahasiswa') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/mahasiswa*') ? 'active-menu' : '' }}">
                    <i class="bi bi-people-fill fs-5"></i> Data Mahasiswa
                </a>

                <p class="sidebar-divider">Manajemen Materi</p>
                <a href="{{ url('/dosen/materi') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/materi*') ? 'active-menu' : '' }}">
                    <i class="bi bi-file-earmark-plus-fill fs-5"></i> Susun Materi
                </a>
                {{-- MENU AI SMART BUILDER --}}
                <a href="{{ url('/dosen/ai-builder') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/ai-builder*') ? 'active-menu' : '' }}">
                    <i class="bi bi-stars fs-5 text-yellow-400"></i> AI Smart Builder
                </a>

                <p class="sidebar-divider">Evaluasi & Tugas</p>
                <a href="{{ url('/dosen/kuis') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/kuis*') ? 'active-menu' : '' }}">
                    <i class="bi bi-patch-question-fill fs-5"></i> Kuis & Bank Soal
                </a>
                <a href="{{ url('/dosen/tugas') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/tugas*') ? 'active-menu' : '' }}">
                    <i class="bi bi-link-45deg fs-4"></i> Penugasan (GitHub/Drive)
                </a>

                <p class="sidebar-divider">Interaksi</p>
                <a href="{{ url('/dosen/diskusi') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 transition-all {{ Request::is('dosen/diskusi*') ? 'active-menu' : '' }}">
                    <i class="bi bi-chat-dots-fill fs-5"></i> Tanya Jawab Materi
                </a>
            </div>

            <div class="p-4 border-top border-gray-600 flex-shrink-0 bg-[#2d3748]">
                <a href="{{ url('/logout') }}" class="btn btn-danger btn-sm w-100 py-2 shadow-sm flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        {{-- PAGE CONTENT --}}
        <div id="page-content-wrapper">
            
            {{-- NAVBAR KUNING --}}
            <nav class="navbar navbar-expand-lg top-navbar px-4">
                <div class="container-fluid px-0">
                    <button class="btn btn-dark btn-sm d-flex align-items-center justify-content-center shadow-sm" id="menu-toggle" style="width: 38px; height: 38px;">
                        <i class="bi bi-list fs-4"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="text-end hidden md:block" style="line-height: 1.1;">
                            <p class="mb-1 text-sm font-bold text-gray-900 uppercase tracking-tighter">{{ session('nama') }}</p>
                            <p class="mb-0 text-[10px] text-gray-800 font-semibold opacity-75">DOSEN PENGAMPU</p>
                        </div>
                        {{-- FOTO PROFIL (FIX STORAGE PATH) --}}
                        <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/' . session('foto')) : asset('images/logo_ukit.png') }}"
                             class="rounded-full border-2 border-white shadow-sm bg-white"
                             style="width: 42px; height: 42px; object-fit: cover; aspect-ratio: 1/1;"
                             onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                    </div>
                </div>
            </nav>

            {{-- KONTEN UTAMA (AREA SCROLLABLE TANPA BAR) --}}
            <div class="container-fluid px-4 py-4 overflow-y-auto no-scrollbar" style="height: calc(100vh - 70px);">
                @yield('content')
            </div>

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("menu-toggle").onclick = function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        };
    </script>
    @stack('scripts')
</body>
</html>