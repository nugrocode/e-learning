<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Learning UKI Toraja')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        #sidebar-wrapper .sidebar-heading, .top-navbar {
            height: 70px !important; min-height: 70px !important; max-height: 70px !important;
            display: flex; align-items: center;
        }

        #sidebar-wrapper { background-color: #2d3748; }
        #sidebar-wrapper .list-group-item {
            border-left: 3px solid transparent; color: #cbd5e0; background-color: transparent;
        }
        #sidebar-wrapper .list-group-item.active-menu {
            background: rgba(255, 255, 255, 0.05); border-left-color: #FACC15; 
            color: #fff !important; font-weight: 600;
        }
        #sidebar-wrapper .list-group-item:hover { background-color: #4a5568; color: #fff; }
        
        .notification-badge {
            position: absolute; top: -2px; right: -2px; padding: 3px 6px;
            border-radius: 50%; background: red; color: white; font-size: 8px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">

        <div id="sidebar-wrapper" class="d-flex flex-column h-100 border-end border-gray-700">
            <div class="sidebar-heading px-4 border-bottom border-gray-600 flex-shrink-0">
                <div class="d-flex align-items-center gap-3 w-100">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="w-8 md:w-9" onerror="this.src='https://ui-avatars.com/api/?name=UK&background=random'">
                    <div class="d-flex flex-column leading-tight overflow-hidden text-truncate">
                        <span class="text-sm md:text-base font-bold text-white tracking-wide">E-LEARNING</span>
                        <span class="text-[10px] text-yellow-400 font-normal">Mahasiswa Panel</span>
                    </div>
                    <button id="sidebar-close" class="btn btn-link text-gray-400 hover:text-white ms-auto d-md-none p-0">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="list-group list-group-flush mt-2 overflow-y-auto flex-grow-1 no-scrollbar pb-5">
                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 mt-3">Menu Utama</p>
                <a href="{{ url('/dashboard') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('dashboard') ? 'active-menu' : '' }}">
                    <i class="bi bi-grid-fill text-lg"></i> Dashboard
                </a>
                <a href="{{ url('/jalur-belajar') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('jalur-belajar*') || Request::is('mata-kuliah*') ? 'active-menu' : '' }}">
                    <i class="bi bi-diagram-3-fill text-lg"></i> Jalur Belajar
                </a>
                <a href="{{ url('/kelas-saya') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('kelas-saya') || Request::is('belajar*') ? 'active-menu' : '' }}">
                    <i class="bi bi-journal-bookmark-fill text-lg"></i> Kelas Saya
                </a>
                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4">Lainnya</p>
                <a href="{{ url('/diskusi') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('diskusi') ? 'active-menu' : '' }}">
                    <i class="bi bi-chat-dots-fill text-lg"></i> Diskusi & Tanya AI
                </a>
                <a href="{{ url('/bantuan') }}" class="list-group-item border-0 d-flex align-items-center gap-3 px-4 py-3 {{ Request::is('bantuan') ? 'active-menu' : '' }}">
                    <i class="bi bi-question-circle-fill text-lg"></i> Bantuan
                </a>
            </div>

            <div class="p-4 border-top border-gray-600 flex-shrink-0 bg-[#2d3748]">
                <a href="{{ url('/logout') }}" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 text-sm">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg top-navbar px-3 shadow-sm border-bottom d-flex align-items-center bg-white sticky-top">
                <div class="container-fluid px-0 d-flex align-items-center">
                    <button class="btn btn-link text-dark p-0 me-2 me-md-3" id="menu-toggle">
                        <i class="bi bi-list text-2xl"></i>
                    </button>

                    <h2 class="m-0 font-bold text-gray-700 text-sm md:text-lg d-none d-md-block">
                        @yield('title')
                    </h2>

                    <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
                        
                        @php
                            $notifs = \App\Models\Notification::where('user_id', session('user_id'))->where('is_read', 0)->latest()->get();
                        @endphp

                        <div class="dropdown">
                            <a href="#" class="text-gray-500 position-relative p-2 rounded-circle hover:bg-gray-100 transition" id="dropNotif" data-bs-toggle="dropdown">
                                <i class="bi bi-bell text-xl"></i>
                                @if($notifs->count() > 0)
                                    <span class="notification-badge">{{ $notifs->count() }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-xl mt-3 p-2" style="width: 280px;">
                                <li class="px-3 py-2 border-bottom"><h6 class="m-0 font-bold text-xs uppercase text-gray-400">Notifikasi</h6></li>
                                <div class="overflow-auto no-scrollbar" style="max-height: 300px;">
                                    @forelse($notifs as $n)
                                        <li>
                                            <a class="dropdown-item p-3 rounded-lg border-bottom transition hover:bg-blue-50" href="{{ url('/notifikasi/'.$n->id) }}">
                                                <p class="mb-1 text-xs font-bold text-gray-800">{{ $n->message }}</p>
                                                <small class="text-[10px] text-gray-400">{{ $n->created_at->diffForHumans() }}</small>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="p-4 text-center"><i class="bi bi-bell-slash text-gray-300 text-2xl d-block mb-2"></i><span class="text-xs text-gray-400">Tidak ada notifikasi baru</span></li>
                                    @endforelse
                                </div>
                            </ul>
                        </div>

                        <div class="text-end" style="line-height: 1;">
                            <p class="mb-0 text-[10px] md:text-sm font-bold text-gray-800">{{ session('nama') }}</p>
                            <p class="mb-0 text-[8px] md:text-xs text-gray-500 uppercase">{{ session('role') }}</p>
                        </div>
                        
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none" id="dropdownUser" data-bs-toggle="dropdown">
                                <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/' . session('foto')) : asset('images/logo_ukit.png') }}"
                                     class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-white shadow-sm"
                                     style="aspect-ratio: 1/1;"
                                     onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-xl mt-2 p-2">
                                <li><a class="dropdown-item rounded-lg py-2 text-sm" href="{{ url('/profil') }}"><i class="bi bi-person-gear me-2"></i> Edit Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item rounded-lg py-2 text-sm text-red-500" href="{{ url('/logout') }}"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-3 md:px-4 py-4 md:py-5 overflow-y-auto no-scrollbar" style="height: calc(100vh - 70px); background-color: #f7fafc;">
                @yield('content')
            </div>
        </div>
        
        <div class="overlay" id="sidebar-close-overlay"></div>
    </div>

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