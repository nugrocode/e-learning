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
        /* 1. MANTRA PENGHILANG SCROLLBAR (TETAP BISA SCROLL) */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* 2. BODY LOCK (Supaya tidak ada double scroll) */
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f7fafc;
            overflow: hidden; /* Scroll dikendalikan oleh wrapper konten */
        }

        /* 3. SIDEBAR FIXED (FLEX COLUMN) */
        #sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background-color: #2d3748;
            z-index: 1000;
            display: flex;       
            flex-direction: column; 
            transition: all 0.3s ease-in-out;
        }

        /* 4. BAGIAN TENGAH SIDEBAR (SCROLLABLE) */
        .sidebar-menu-area {
            flex-grow: 1;      
            overflow-y: auto;  
        }

        /* 5. NAVBAR FIXED (KANAN) */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 280px;      /* Mulai setelah sidebar */
            right: 0;         
            height: 70px;
            background-color: #ffc107 !important;
            z-index: 900;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease-in-out;
        }

        /* 6. KONTEN AREA (SCROLLABLE UTAMA) */
        #page-content-wrapper {
            margin-left: 280px; 
            margin-top: 70px;   /* Jarak dari navbar */
            width: calc(100% - 280px);
            height: calc(100vh - 70px); /* Sisa tinggi layar */
            overflow-y: auto;   /* Scroll konten aktif di sini */
        }

        /* STYLE LAINNYA */
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
        #sidebar-wrapper .list-group-item:hover { background-color: #4a5568; color: #fff; }

        .sidebar-divider { 
            font-size: 10px; color: #718096; padding: 20px 20px 5px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; 
        }

        /* RESPONSIVE (HP) */
        @media (max-width: 768px) {
            #sidebar-wrapper { left: -280px; } /* Sembunyi ke kiri */
            .top-navbar { left: 0; }           /* Navbar full width */
            #page-content-wrapper { margin-left: 0; width: 100%; }
            
            /* Saat Tombol Burger Diklik (Sidebar Muncul) */
            #wrapper.toggled #sidebar-wrapper { left: 0; }
            
            /* Overlay Gelap */
            #wrapper.toggled .sidebar-overlay {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 999;
                display: block !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div id="wrapper">

        {{-- SIDEBAR --}}
        <div id="sidebar-wrapper">
            
            {{-- HEADER SIDEBAR --}}
            <div class="sidebar-heading px-4 border-bottom border-gray-600 flex-shrink-0 d-flex align-items-center" style="height: 70px;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8" onerror="this.src='https://ui-avatars.com/api/?name=UK&background=random'">
                    <div class="d-flex flex-column leading-tight">
                        <span class="text-sm font-bold text-white uppercase tracking-wider">Dosen Panel</span>
                        <span class="text-[10px] text-yellow-400 font-normal">Sistem Akademik</span>
                    </div>

                    {{-- TOMBOL TUTUP (X) KHUSUS HP --}}
                    <button id="sidebar-close" class="btn btn-link text-gray-400 ms-auto d-md-none p-0">
                        <i class="bi bi-x-lg text-lg hover:text-white transition"></i>
                    </button>
                </div>
            </div>

            {{-- MENU LIST (BISA SCROLL - NO SCROLLBAR) --}}
            <div class="sidebar-menu-area no-scrollbar list-group list-group-flush mt-2 pb-5">
                
                <p class="sidebar-divider">Utama</p>
                <a href="{{ url('/dosen/dashboard') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/dashboard') ? 'active-menu' : '' }}">
                    <i class="bi bi-grid-fill fs-5"></i> Dashboard
                </a>

                <p class="sidebar-divider">Akademik</p>
                <a href="{{ url('/dosen/kelas') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/kelas*') ? 'active-menu' : '' }}">
                    <i class="bi bi-journal-bookmark-fill fs-5"></i> Kelas Ajar Saya
                </a>
                <a href="{{ url('/dosen/mahasiswa') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/mahasiswa*') ? 'active-menu' : '' }}">
                    <i class="bi bi-people-fill fs-5"></i> Data Mahasiswa
                </a>

                <p class="sidebar-divider">Manajemen Materi</p>
                <a href="{{ url('/dosen/materi') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/materi*') ? 'active-menu' : '' }}">
                    <i class="bi bi-file-earmark-plus-fill fs-5"></i> Susun Materi
                </a>
                <a href="{{ url('/dosen/ai-builder') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/ai-builder*') ? 'active-menu' : '' }}">
                    <i class="bi bi-stars fs-5 text-yellow-400"></i> AI Smart Builder
                </a>

                <p class="sidebar-divider">Evaluasi</p>
                <a href="{{ url('/dosen/kuis') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/kuis*') ? 'active-menu' : '' }}">
                    <i class="bi bi-patch-question-fill fs-5"></i> Kuis & Bank Soal
                </a>
                <a href="{{ url('/dosen/tugas') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/tugas*') ? 'active-menu' : '' }}">
                    <i class="bi bi-link-45deg fs-4"></i> Penugasan (GitHub/Drive)
                </a>

                <p class="sidebar-divider">Interaksi</p>
                <a href="{{ url('/dosen/diskusi') }}" class="list-group-item d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dosen/diskusi*') ? 'active-menu' : '' }}">
                    <i class="bi bi-chat-dots-fill fs-5"></i> Tanya Jawab Materi
                </a>
                
                <div class="mb-5"></div> {{-- Spacer --}}
            </div>

            {{-- FOOTER KELUAR (DIAM DI BAWAH) --}}
            <div class="p-4 border-top border-gray-600 flex-shrink-0 bg-[#2d3748]">
                <a href="{{ url('/logout') }}" class="btn btn-danger btn-sm w-100 py-2 shadow-sm flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        {{-- NAVBAR (FIXED TOP) --}}
        <nav class="navbar navbar-expand-lg top-navbar px-4 shadow-sm">
            <div class="container-fluid px-0">
                <button class="btn btn-dark btn-sm d-flex align-items-center justify-content-center shadow-sm me-3" id="menu-toggle" style="width: 38px; height: 38px;">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <div class="text-end hidden md:block" style="line-height: 1.1;">
                        <p class="mb-1 text-sm font-bold text-gray-900 uppercase tracking-tighter">{{ session('nama') }}</p>
                        <p class="mb-0 text-[10px] text-gray-800 font-semibold opacity-75">DOSEN PENGAMPU</p>
                    </div>
                    {{-- FOTO PROFIL FIX --}}
                    <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/' . session('foto')) : asset('images/logo_ukit.png') }}"
                         class="rounded-full border-2 border-white shadow-sm bg-white"
                         style="width: 42px; height: 42px; object-fit: cover; aspect-ratio: 1/1;"
                         onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                </div>
            </div>
        </nav>

        {{-- PAGE CONTENT (AREA SCROLLABLE UTAMA) --}}
        {{-- Class no-scrollbar memastikan bar hilang, tapi tetap bisa discroll --}}
        <div id="page-content-wrapper" class="no-scrollbar">
            <div class="container-fluid px-4 py-4">
                @yield('content')
            </div>
        </div>

        {{-- OVERLAY MOBILE --}}
        <div class="sidebar-overlay d-none" id="overlay"></div>

    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const wrapper = document.getElementById("wrapper");
        const toggleBtn = document.getElementById("menu-toggle");
        const closeBtn = document.getElementById("sidebar-close");
        const overlay = document.getElementById("overlay");

        function toggleSidebar() {
            wrapper.classList.toggle("toggled");
        }

        // Buka Sidebar
        if(toggleBtn) {
            toggleBtn.onclick = function(e) {
                e.preventDefault();
                toggleSidebar();
            };
        }

        // Tutup Sidebar (Tombol X di HP)
        if(closeBtn) {
            closeBtn.onclick = function(e) {
                e.preventDefault();
                wrapper.classList.remove("toggled");
            };
        }

        // Tutup Sidebar (Klik Overlay)
        if(overlay) {
            overlay.onclick = function() {
                wrapper.classList.remove("toggled");
            };
        }
    </script>
    @stack('scripts')
</body>
</html>