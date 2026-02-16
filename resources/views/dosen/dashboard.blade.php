@extends('layouts.dosen')

@section('title', 'Dashboard Dosen')

@section('content')
    {{-- BAGIAN 1: HEADER & SAMBUTAN --}}
    <div class="row mb-4 animate-fade-in-up">
        <div class="col-12">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 relative overflow-hidden">
                
                {{-- Teks Sambutan --}}
                <div class="z-10 relative">
                    <h1 class="font-bold text-2xl text-gray-800 mb-1">
                        Halo, {{ session('nama') }}! <span class="text-2xl">👋</span>
                    </h1>
                    <p class="text-gray-500 text-sm mb-0">
                        Siap mengajar hari ini? Ada <strong class="text-blue-600">{{ $total_kelas }} Kelas</strong> yang menunggu aktivitas Anda.
                    </p>
                </div>

                {{-- Quick Actions (Tombol Cepat) --}}
                <div class="d-flex gap-2 z-10 relative">
                    <a href="{{ url('/dosen/kelas') }}" class="btn btn-outline-secondary btn-sm rounded-lg fw-bold px-3 shadow-sm bg-white">
                        <i class="bi bi-journal-bookmark me-1"></i> Lihat Kelas
                    </a>
                    <a href="{{ url('/dosen/materi') }}" class="btn btn-primary btn-sm rounded-lg fw-bold px-3 shadow-sm bg-blue-600 border-0">
                        <i class="bi bi-plus-lg me-1"></i> Susun Materi Baru
                    </a>
                </div>

                {{-- Dekorasi Background Abstrak --}}
                <div class="absolute right-0 top-0 h-full w-48 bg-gradient-to-l from-blue-50 to-transparent opacity-50 rounded-r-xl"></div>
                <div class="absolute -right-6 -bottom-8 text-9xl text-blue-100 opacity-20 transform rotate-12">
                    <i class="bi bi-person-workspace"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: STATISTIK UTAMA (Cards) --}}
    <div class="row g-4 mb-5">
        
        {{-- Card 1: Total Mahasiswa --}}
        <div class="col-12 col-md-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100 hover:shadow-md transition-all duration-300 group">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="bi bi-people-fill text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Total Mahasiswa</span>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h3 class="font-bold text-3xl text-gray-800 m-0">{{ $total_mhs }}</h3>
                    <span class="text-[10px] text-green-500 bg-green-50 px-2 py-1 rounded-full font-bold">
                        <i class="bi bi-arrow-up-short"></i> Aktif
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 2: Tugas Belum Dinilai (PENTING) --}}
        <div class="col-12 col-md-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100 hover:shadow-md transition-all duration-300 group cursor-pointer" onclick="window.location='{{ url('/dosen/tugas') }}'">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-colors">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tugas Masuk</span>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <h3 class="font-bold text-3xl text-gray-800 m-0">{{ $total_tugas }}</h3>
                    @if($total_tugas > 0)
                        <span class="text-[10px] text-orange-600 bg-orange-50 px-2 py-1 rounded-full font-bold animate-pulse">
                            Perlu Dinilai
                        </span>
                    @else
                        <span class="text-[10px] text-gray-400 bg-gray-50 px-2 py-1 rounded-full font-bold">
                            Semua Beres
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3: Total Materi --}}
        <div class="col-12 col-md-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100 hover:shadow-md transition-all duration-300 group">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <i class="bi bi-collection-play-fill text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Bank Materi</span>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    {{-- Asumsi $total_materi dihitung di controller --}}
                    <h3 class="font-bold text-3xl text-gray-800 m-0">{{ $total_materi ?? 0 }}</h3>
                    <span class="text-[10px] text-purple-500 bg-purple-50 px-2 py-1 rounded-full font-bold">
                        Modul & Video
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 3: OVERVIEW KELAS (Grid Modern) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-1">
        <div>
            <h5 class="font-bold text-gray-800 m-0">Kelas Ajar Anda</h5>
            <p class="text-xs text-gray-500 m-0 mt-1">Kelola materi dan pantau mahasiswa per kelas.</p>
        </div>
        <a href="{{ url('/dosen/kelas') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 text-decoration-none transition">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row g-4">
        @forelse($kelas_list->take(3) as $k) {{-- Tampilkan 3 kelas terbaru saja agar dashboard rapi --}}
            @php
                $progress = min(($k->materials_count / 14) * 100, 100); // Asumsi 14 pertemuan
                $colors = ['blue', 'indigo', 'teal', 'purple'];
                $color = $colors[$k->id % 4]; // Rotasi warna
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-100 d-flex flex-column hover:-translate-y-1 transition-transform duration-300 relative group">
                    
                    {{-- Garis Indikator Warna di Kiri --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-{{ $color }}-500"></div>

                    <div class="p-4 flex-grow-1">
                        {{-- Header Kelas --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-{{ $color }}-50 text-{{ $color }}-600 border border-{{ $color }}-100 rounded-lg px-2 py-1 text-[10px]">
                                {{ $k->concentrations->first()->nama_konsentrasi ?? 'Umum' }}
                            </span>
                            <div class="dropdown">
                                <button class="btn btn-link text-gray-400 p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 text-sm">
                                    <li><a class="dropdown-item py-2" href="{{ url('/dosen/kelas/'.$k->id) }}"><i class="bi bi-gear me-2"></i> Kelola Kelas</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ url('/dosen/mahasiswa?kelas='.$k->id) }}"><i class="bi bi-people me-2"></i> Lihat Mahasiswa</a></li>
                                </ul>
                            </div>
                        </div>

                        {{-- Judul Kelas --}}
                        <h5 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-{{ $color }}-600 transition-colors">
                            <a href="{{ url('/dosen/kelas/'.$k->id) }}" class="text-decoration-none text-reset stretched-link">
                                {{ $k->nama_mk }}
                            </a>
                        </h5>
                        <p class="text-xs text-gray-500 line-clamp-2 mb-4">
                            {{ $k->deskripsi ?? 'Tidak ada deskripsi singkat.' }}
                        </p>

                        {{-- Statistik Mini --}}
                        <div class="d-flex gap-3 border-t border-dashed pt-3">
                            <div class="d-flex align-items-center gap-1 text-xs text-gray-500">
                                <i class="bi bi-file-earmark-text text-{{ $color }}-500"></i>
                                <span class="font-bold text-gray-700">{{ $k->materials_count }}</span> Materi
                            </div>
                            <div class="d-flex align-items-center gap-1 text-xs text-gray-500">
                                <i class="bi bi-person text-{{ $color }}-500"></i>
                                <span class="font-bold text-gray-700">{{ $k->students_count ?? 0 }}</span> Mhs
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar Bawah --}}
                    <div class="bg-gray-50 px-4 py-2 border-top border-gray-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-[10px] text-gray-400 fw-bold">KELENGKAPAN MATERI</span>
                            <span class="text-[10px] text-{{ $color }}-600 fw-bold">{{ round($progress) }}%</span>
                        </div>
                        <div class="progress h-1.5 bg-gray-200 rounded-full w-100">
                            <div class="progress-bar bg-{{ $color }}-500 rounded-full" role="progressbar" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                    <i class="bi bi-journal-x text-4xl text-gray-300 mb-3 d-block"></i>
                    <h6 class="text-gray-500 font-bold">Belum Ada Kelas Aktif</h6>
                    <p class="text-xs text-gray-400 mb-0">Hubungi Admin Akademik untuk plotting jadwal Anda.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Script Animasi Simpel --}}
    @push('styles')
    <style>
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endpush
@endsection