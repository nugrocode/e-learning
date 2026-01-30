@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    {{-- WELCOME BANNER --}}
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl p-6 text-white shadow-lg mb-5 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="font-bold text-2xl md:text-3xl mb-1">Halo, {{ session('nama') }}! 👋</h2>
            <p class="text-gray-300 text-sm md:text-base">Selamat datang di Panel Administrator E-Learning.</p>
        </div>
        {{-- Hiasan Background --}}
        <i class="bi bi-grid-1x2-fill absolute -right-6 -bottom-6 text-9xl text-white opacity-10"></i>
    </div>

    {{-- STATISTIK CARDS --}}
    <div class="row g-4 mb-5">
        
        {{-- Card 1: Mahasiswa --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-100 d-flex align-items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800 m-0">{{ $total_mhs }}</h3>
                    <p class="text-xs text-gray-500 uppercase font-semibold m-0">Mahasiswa</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Dosen --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-100 d-flex align-items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-2xl">
                    <i class="bi bi-person-video3"></i>
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800 m-0">{{ $total_dosen }}</h3>
                    <p class="text-xs text-gray-500 uppercase font-semibold m-0">Dosen</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Konsentrasi --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-100 d-flex align-items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 text-2xl">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800 m-0">{{ $total_konsentrasi }}</h3>
                    <p class="text-xs text-gray-500 uppercase font-semibold m-0">Konsentrasi</p>
                </div>
            </div>
        </div>

        {{-- Card 4: Mata Kuliah --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-100 d-flex align-items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-2xl">
                    <i class="bi bi-book-half"></i>
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800 m-0">{{ $total_mk }}</h3>
                    <p class="text-xs text-gray-500 uppercase font-semibold m-0">Mata Kuliah</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL USER TERBARU (PREVIEW) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b bg-gray-50 d-flex justify-content-between align-items-center">
            <h5 class="font-bold text-gray-800 text-sm md:text-base m-0">Pengguna Terbaru</h5>
            <a href="{{ url('/admin/users') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 text-xs">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-100 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th>Role</th>
                        <th>NIM/NIDN</th>
                        <th>Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($recent_users as $user)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset('images/' . ($user->foto_profil ?? 'default.png')) }}" 
                                         class="w-8 h-8 rounded-full object-cover border"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}'">
                                    <span class="font-bold text-gray-700">{{ $user->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'bg-red-500' : ($user->role == 'dosen' ? 'bg-purple-500' : 'bg-blue-500') }} rounded-pill px-2">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="text-gray-500">{{ $user->nim_nidn }}</td>
                            <td class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-400">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection