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

    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">

        <div id="sidebar-wrapper">
            <div class="sidebar-heading d-flex justify-content-between align-items-center px-4 py-3 border-b border-gray-600">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" width="35">
                    <div class="d-flex flex-column leading-tight">
                        <span class="text-sm font-bold text-white tracking-wide">E-Learning</span>
                        <span class="text-xs text-gray-400 font-normal">UKI Toraja</span>
                    </div>
                </div>
                <button id="sidebar-close" class="btn btn-link text-gray-400 hover:text-white p-0 transition transform hover:-translate-x-1">
                    <i class="bi bi-chevron-double-left text-xl"></i>
                </button>
            </div>

            <div class="list-group list-group-flush mt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Menu Utama</p>

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
                    <i class="bi bi-chat-left-text-fill text-lg"></i> Diskusi
                </a>
            </div>

            <div class="mt-auto p-4">
                <a href="{{ url('/bantuan') }}" class="btn btn-outline-secondary w-100 text-white border-gray-600 hover:bg-gray-700 flex items-center justify-center gap-2">
                    <i class="bi bi-headset"></i> Bantuan
                </a>
                <a href="{{ url('/logout') }}" class="btn btn-danger w-100 mt-2 flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg top-navbar px-4 py-3">
                <div class="container-fluid">
                    <button class="btn btn-dark btn-sm me-3" id="menu-toggle">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="text-right hidden md:block">
                            <p class="mb-0 text-sm font-bold text-gray-800">Halo, {{ session('nama') }}</p>
                            <p class="mb-0 text-xs text-gray-700 uppercase">{{ session('role') }}</p>
                        </div>
                        <div class="relative">
                            <img src="{{ asset('images/' . (session('foto') ?? 'default.png')) }}"
                                 class="w-10 h-10 rounded-full object-cover border border-gray-400"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ session('nama') }}'">
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-5">
                @yield('content')
            </div>

        </div>
    </div>

    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        var sidebarClose = document.getElementById("sidebar-close");

        function toggleSidebar() { el.classList.toggle("toggled"); }

        if(toggleButton){ toggleButton.onclick = function () { toggleSidebar(); }; }
        if(sidebarClose){ sidebarClose.onclick = function () { toggleSidebar(); }; }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
