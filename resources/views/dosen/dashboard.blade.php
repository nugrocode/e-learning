@extends('layouts.dosen')

@section('title', 'Dashboard Dosen')

@section('content')
    {{-- 1. WELCOME BANNER --}}
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 rounded-xl p-6 text-white shadow-lg mb-5 relative overflow-hidden animate-fade-in-up">
        <div class="relative z-10">
            <h2 class="font-bold text-2xl md:text-3xl mb-1">Selamat Datang, {{ session('nama') }}! 👋</h2>
            <p class="text-blue-100 text-sm md:text-base opacity-90">
                Panel Kontrol Akademik: Pantau perkembangan kelas dan nilai mahasiswa Anda di sini.
            </p>
        </div>
        {{-- Hiasan Background --}}
        <i class="bi bi-person-video3 absolute -right-6 -bottom-6 text-9xl text-white opacity-10 transform rotate-12"></i>
    </div>

    {{-- 2. STATISTIK CARDS --}}
    <div class="row g-4 mb-5">
        
        {{-- Card 1: Total Kelas --}}
        <div class="col-12 col-md-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-100 d-flex align-items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="bi bi-easel2-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800 m-0 leading-none">{{ $total_kelas }}</h3>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide m-0 mt-1">Kelas Diampu</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Mahasiswa (Aktif) --}}
        <div class="col-12 col-md-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-100 d-flex align-items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800 m-0 leading-none">{{ $total_mhs }}</h3>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide m-0 mt-1">Mahasiswa Aktif</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Tugas Masuk --}}
        <div class="col-12 col-md-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-100 d-flex align-items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="bi bi-file-earmark-check-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-2xl text-gray-800 m-0 leading-none">{{ $total_tugas }}</h3>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide m-0 mt-1">Total Tugas Masuk</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. DAFTAR KELAS (GRID VIEW) --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="font-bold text-gray-800 text-lg m-0 border-l-4 border-yellow-400 ps-3">Kelas Ajar Anda</h5>
        <a href="{{ url('/dosen/kelas') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 text-xs font-bold">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        @forelse($kelas_list as $k)
            @php
                // Logika Warna Card Acak agar tidak bosan
                $colors = ['border-l-blue-500', 'border-l-purple-500', 'border-l-teal-500', 'border-l-indigo-500'];
                $border_color = $colors[$k->id % count($colors)];
                
                // Hitung Kelengkapan Materi (Misal target 1 semester 14 pertemuan)
                $persen_materi = min(($k->materials_count / 14) * 100, 100);
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ url('/dosen/kelas/'.$k->id) }}" class="text-decoration-none group">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300 h-100 d-flex flex-column border-l-4 {{ $border_color }}">
                        
                        {{-- Header Card --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            {{-- Badge Konsentrasi --}}
                            <span class="badge bg-gray-100 text-gray-600 rounded-lg text-[10px] border border-gray-200 px-2 py-1">
                                <i class="bi bi-diagram-3-fill me-1"></i> 
                                {{ $k->concentrations->first()->nama_konsentrasi ?? 'Mata Kuliah Umum' }}
                            </span>
                            
                            {{-- Icon Arrow --}}
                            <div class="w-6 h-6 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-blue-600 group-hover:text-white transition">
                                <i class="bi bi-chevron-right text-xs"></i>
                            </div>
                        </div>

                        {{-- Judul & Deskripsi --}}
                        <h6 class="font-bold text-gray-800 text-base mb-1 line-clamp-1 group-hover:text-blue-700 transition">
                            {{ $k->nama_mk }}
                        </h6>
                        <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed flex-grow-1">
                            {{ $k->deskripsi ?? 'Belum ada deskripsi untuk mata kuliah ini.' }}
                        </p>
                        
                        {{-- Footer: Mini Statistik --}}
                        <div class="mt-auto pt-3 border-t border-gray-50 bg-gray-50/50 -mx-4 -mb-4 p-3 rounded-b-xl">
                            <div class="d-flex justify-content-between text-[10px] text-gray-500 mb-1 font-semibold uppercase">
                                <span>Konten Materi</span>
                                <span>{{ $k->materials_count }} Item</span>
                            </div>
                            <div class="progress h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="progress-bar bg-blue-600 rounded-full" role="progressbar" style="width: {{ $persen_materi }}%"></div>
                            </div>
                            <div class="mt-2 text-[10px] text-gray-400 text-end">
                                Klik untuk kelola <i class="bi bi-hand-index-thumb"></i>
                            </div>
                        </div>

                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="d-inline-block p-4 rounded-full bg-gray-50 mb-3 border border-dashed border-gray-200">
                    <i class="bi bi-journal-x text-4xl text-gray-300"></i>
                </div>
                <h6 class="font-bold text-gray-600">Belum ada Kelas</h6>
                <p class="text-gray-400 text-sm">Anda belum ditugaskan mengajar kelas manapun.</p>
            </div>
        @endforelse
    </div>
@endsection