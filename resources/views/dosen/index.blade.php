@extends('layouts.dosen')

@section('title', 'Daftar Kelas Ajar')

@section('content')
    {{-- 1. HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3 animate-fade-in-up">
        <div>
            <h2 class="font-bold text-2xl md:text-3xl text-gray-800 mb-1">Kelas Ajar Saya</h2>
            <p class="text-gray-500 text-sm m-0">Pilih kelas di bawah ini untuk mengelola materi, kuis, dan mahasiswa.</p>
        </div>
        
        {{-- Statistik Ringkas --}}
        <div class="bg-white px-4 py-2 rounded-lg border shadow-sm d-flex align-items-center gap-3">
            <div class="text-end">
                <span class="block text-2xl font-bold text-blue-900 leading-none">{{ $courses->count() }}</span>
                <span class="text-[10px] text-gray-400 uppercase font-bold">Total Kelas</span>
            </div>
            <div class="h-8 w-1 bg-gray-200 rounded-full"></div>
            <i class="bi bi-collection-fill text-2xl text-blue-200"></i>
        </div>
    </div>

    {{-- 2. GRID DAFTAR KELAS --}}
    <div class="row g-4">
        @forelse($courses as $k)
            @php
                // Warna acak untuk variasi visual
                $bgs = ['bg-blue-600', 'bg-purple-600', 'bg-teal-600', 'bg-indigo-600'];
                $bg_color = $bgs[$k->id % count($bgs)];
                $total_materi = $k->materials->count();
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                {{-- CARD KELAS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column hover:-translate-y-1 transition-transform duration-300">
                    
                    {{-- Header Gambar / Placeholder --}}
                    <div class="h-32 w-full relative overflow-hidden">
                        @if($k->gambar)
                            <img src="{{ asset('images/' . $k->gambar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full {{ $bg_color }} d-flex align-items-center justify-content-center relative">
                                <i class="bi bi-easel2-fill text-4xl text-white opacity-50"></i>
                                {{-- Pola hiasan --}}
                                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 8px 8px;"></div>
                            </div>
                        @endif

                        {{-- Badge Konsentrasi --}}
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/90 backdrop-blur-sm text-gray-800 text-[10px] font-bold px-2 py-1 rounded shadow-sm border">
                                {{ $k->concentrations->first()->nama_konsentrasi ?? 'Umum' }}
                            </span>
                        </div>
                    </div>

                    {{-- Konten Card --}}
                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        
                        {{-- Judul --}}
                        <h5 class="font-bold text-lg text-gray-800 mb-2 line-clamp-1" title="{{ $k->nama_mk }}">
                            {{ $k->nama_mk }}
                        </h5>

                        {{-- Deskripsi Singkat --}}
                        <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed">
                            {{ $k->deskripsi ?? 'Belum ada deskripsi untuk mata kuliah ini.' }}
                        </p>

                        {{-- Info Baris Bawah --}}
                        <div class="mt-auto border-t pt-3 d-flex justify-content-between align-items-center">
                            
                            {{-- Info Materi --}}
                            <div class="d-flex align-items-center gap-2 text-xs text-gray-500">
                                <i class="bi bi-file-earmark-text text-blue-500"></i> 
                                <span class="font-semibold">{{ $total_materi }} Materi</span>
                            </div>

                            {{-- Tombol Aksi --}}
                            <a href="{{ url('/dosen/kelas/' . $k->id) }}" class="btn btn-sm btn-dark rounded-pill px-3 font-bold text-xs shadow-sm hover:bg-gray-800 transition">
                                Kelola <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            {{-- EMPTY STATE --}}
            <div class="col-12 text-center py-5">
                <div class="d-inline-block p-5 rounded-full bg-gray-50 mb-3 border border-dashed border-gray-300">
                    <i class="bi bi-journal-x text-5xl text-gray-300"></i>
                </div>
                <h5 class="font-bold text-gray-600">Belum Ada Kelas</h5>
                <p class="text-gray-400 text-sm">Anda belum ditugaskan untuk mengajar kelas manapun.</p>
                <button class="btn btn-outline-primary btn-sm rounded-pill mt-2">
                    <i class="bi bi-whatsapp me-1"></i> Hubungi Admin
                </button>
            </div>
        @endforelse
    </div>
@endsection