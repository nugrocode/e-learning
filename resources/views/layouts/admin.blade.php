<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Administrator</title>
    
    {{-- Bootstrap 5 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Inter & Global CSS --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f3f4f6; 
            overflow: hidden; /* Mencegah scrollbar ganda di body */
        }

        /* --- 1. PERBAIKAN SCROLLBAR (TIPIS & TIDAK MERUSAK LAYOUT) --- */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Scrollbar Sidebar (Gelap) */
        .sidebar-nav::-webkit-scrollbar-thumb { background: #374151; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: #4b5563; }

        /* --- 2. PERBAIKAN TOMBOL AKSI BERTUMPUK (GLOBAL FIX) --- */
        .table td:last-child {
            white-space: nowrap !important;
            width: 1%; 
        }
        .table td:last-child .btn, 
        .table td:last-child form {
            display: inline-block;
            margin-right: 4px; 
        }

        /* --- 3. MENU STYLING --- */
        .nav-link-custom {
            position: relative;
            color: #9ca3af;
            transition: all 0.2s;
            border-radius: 6px;
            margin-bottom: 4px;
        }
        .nav-link-custom:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #fff;
        }
        .nav-link-custom.active {
            background-color: #fbbf24;
            color: #111827 !important;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(251, 191, 36, 0.2);
        }
        
        /* Sidebar Toggle Animation */
        #sidebar-wrapper { width: 260px; transition: margin 0.25s ease-out; }
        body.toggled #sidebar-wrapper { margin-left: -260px; }
        
        @media (max-width: 768px) { 
            #sidebar-wrapper { margin-left: -260px; } 
            body.toggled #sidebar-wrapper { margin-left: 0; } 
        }
    </style>
</head>
<body>

    <div class="d-flex h-screen w-full overflow-hidden">

        {{-- ================= SIDEBAR ================= --}}
        <aside id="sidebar-wrapper" class="bg-[#111827] text-white flex-shrink-0 d-flex flex-column border-end border-gray-800 z-20">
            
            {{-- LOGO AREA --}}
            <div class="h-16 d-flex align-items-center px-4 bg-[#0f172a] border-b border-gray-800 flex-shrink-0">
                <div class="d-flex align-items-center gap-3">
                    {{-- [FIX LOGO] Pastikan nama file di public/images adalah logo.png --}}
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="h-8 w-auto object-contain" 
                         onerror="this.src='https://placehold.co/40x40/fbbf24/000?text=U'">
                    <div class="leading-tight whitespace-nowrap">
                        <h6 class="m-0 fw-bold text-[14px] tracking-wide text-white">ADMINISTRATOR</h6>
                        <span class="text-[10px] text-yellow-500 fw-bold tracking-wider">CONTROL PANEL</span>
                    </div>
                </div>
            </div>

            {{-- MENU LIST --}}
            <div class="flex-grow-1 overflow-y-auto sidebar-nav p-3">
                <nav class="nav flex-column">
                    <a href="{{ url('/admin/dashboard') }}" class="nav-link-custom d-flex align-items-center gap-3 px-3 py-2 text-sm {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill text-lg"></i> <span>Dashboard</span>
                    </a>

                    <div class="text-[10px] fw-bold text-gray-500 uppercase tracking-wider mt-4 mb-2 px-3">Pengguna</div>
                    <a href="{{ url('/admin/users') }}" class="nav-link-custom d-flex align-items-center gap-3 px-3 py-2 text-sm {{ Request::is('admin/users*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill text-lg"></i> <span>Manajemen User</span>
                    </a>

                    <div class="text-[10px] fw-bold text-gray-500 uppercase tracking-wider mt-4 mb-2 px-3">Master Data</div>
                    <a href="{{ url('/admin/konsentrasi') }}" class="nav-link-custom d-flex align-items-center gap-3 px-3 py-2 text-sm {{ Request::is('admin/konsentrasi*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3-fill text-lg"></i> <span>Prodi / Konsentrasi</span>
                    </a>
                    <a href="{{ url('/admin/bank-mata-kuliah') }}" class="nav-link-custom d-flex align-items-center gap-3 px-3 py-2 text-sm {{ Request::is('admin/bank-mata-kuliah*') ? 'active' : '' }}">
                        <i class="bi bi-hdd-stack-fill text-lg"></i> <span>Bank Mata Kuliah</span>
                    </a>

                    <div class="text-[10px] fw-bold text-gray-500 uppercase tracking-wider mt-4 mb-2 px-3">Akademik</div>
                    <a href="{{ url('/admin/mata-kuliah') }}" class="nav-link-custom d-flex align-items-center gap-3 px-3 py-2 text-sm {{ Request::is('admin/mata-kuliah*') ? 'active' : '' }}">
                        <i class="bi bi-collection-fill text-lg"></i> <span>Distribusi Kurikulum</span>
                    </a>
                    <a href="{{ url('/admin/pengumuman') }}" class="nav-link-custom d-flex align-items-center gap-3 px-3 py-2 text-sm {{ Request::is('admin/pengumuman*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone-fill text-lg"></i> <span>Pengumuman</span>
                    </a>
                </nav>
            </div>

            {{-- FOOTER --}}
            <div class="p-3 bg-[#0f172a] border-t border-gray-800 flex-shrink-0">
                <a href="{{ url('/logout') }}" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 text-xs fw-bold shadow-sm">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </aside>

        {{-- ================= MAIN CONTENT ================= --}}
        <div class="flex-grow-1 d-flex flex-column h-screen overflow-hidden bg-gray-50 relative w-full">
            
            {{-- NAVBAR --}}
            <header class="h-16 bg-yellow-400 shadow-sm d-flex align-items-center justify-content-between px-4 z-10 flex-shrink-0">
                <button class="btn btn-transparent p-0 border-0" id="sidebarToggle">
                    <i class="bi bi-list text-2xl text-slate-900"></i>
                </button>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-md-block lh-sm">
                        <div class="fw-bold text-slate-900 text-sm">{{ session('nama') ?? 'Administrator' }}</div>
                        <div class="text-[10px] text-slate-800 uppercase tracking-wider font-bold">Super Admin</div>
                    </div>
                    
                    {{-- [FIX FOTO PROFIL] Tampilkan Foto jika ada, Inisial jika tidak ada --}}
                    <div class="w-9 h-9 rounded-circle bg-white border border-yellow-500 overflow-hidden shadow-sm d-flex align-items-center justify-content-center">
                        @if(session('foto'))
                            <img src="{{ asset('images/' . session('foto')) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-yellow-600 fw-bold">{{ substr(session('nama') ?? 'A', 0, 1) }}</span>
                        @endif
                    </div>

                </div>
            </header>

            {{-- CONTENT SCROLLABLE --}}
            <main class="flex-grow-1 overflow-y-auto custom-scroll p-4">
                <div class="container-fluid p-0" style="min-width: 0;">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    {{-- Script Toggle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.body.classList.toggle('toggled');
        });
    </script>
</body>
</html>