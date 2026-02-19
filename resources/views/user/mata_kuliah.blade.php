@extends('layouts.app')

@section('title', 'Mata Kuliah - ' . $concentration->nama_konsentrasi)

@section('content')
    {{-- Navigasi Kembali --}}
    <div class="mb-4 animate-fade-in-up">
        <a href="{{ url('/jalur-belajar') }}"
            class="text-decoration-none text-gray-500 hover:text-blue-900 transition flex items-center gap-2 text-sm font-semibold">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Konsentrasi
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0 rounded-lg text-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-lg"></i>
                <div><strong>Berhasil!</strong> {{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-end mb-4 gap-3 border-b border-gray-200 pb-3 animate-fade-in-up">
        <div>
            <h2 class="font-bold text-2xl md:text-3xl text-gray-800 mb-1">Daftar Mata Kuliah</h2>
            <p class="text-gray-500 m-0 text-sm md:text-base">
                Program Studi: <span class="font-bold text-blue-900 bg-blue-50 px-2 py-1 rounded">{{ $concentration->nama_konsentrasi }}</span>
            </p>
        </div>
    </div>

    {{-- Grid Mata Kuliah --}}
    <div class="row g-3">
        @forelse($courses as $mk)
            @php
                $progress_color = $mk->persen == 100 ? 'bg-green-500' : 'bg-yellow-400';
                $colors = ['bg-blue-600', 'bg-purple-600', 'bg-indigo-600', 'bg-teal-600', 'bg-slate-700'];
                $bg_random = $colors[$mk->id % count($colors)];
                
                $is_locked = ($mk->status_akses != 'open');
                $opacity_class = $is_locked ? 'grayscale opacity-70' : ''; 
            @endphp

            <div class="col-12 col-md-6 col-xl-4">
                {{-- CARD WRAPPER --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column {{ $is_locked ? 'bg-gray-50' : 'hover:shadow-md transition-shadow duration-300' }}">
                    
                    {{-- 1. HEADER GAMBAR (FIXED PATH) --}}
                    <div class="h-36 w-full relative overflow-hidden {{ $opacity_class }}">
                        @if(isset($mk->gambar) && $mk->gambar)
                            {{-- PERBAIKAN DI SINI: Menggunakan storage/thumbnails/ --}}
                            <img src="{{ asset('storage/thumbnails/' . $mk->gambar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full {{ $bg_random }} d-flex align-items-center justify-content-center relative">
                                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 10px 10px;"></div>
                                <div class="text-center text-white z-10 p-3">
                                    <i class="bi bi-code-square text-3xl mb-1 d-block opacity-80"></i>
                                    <span class="font-bold text-sm tracking-wider">{{ substr($mk->nama_mk, 0, 15) }}...</span>
                                </div>
                            </div>
                        @endif

                        {{-- Badge Materi --}}
                        <div class="absolute top-2 right-2">
                            <span class="bg-white/95 backdrop-blur-sm text-gray-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                                <i class="bi bi-file-earmark-text-fill text-blue-500"></i> {{ $mk->total_materi }} Materi
                            </span>
                        </div>

                        {{-- Overlay Gembok --}}
                        @if($is_locked)
                            <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white z-20 backdrop-blur-[1px]">
                                <i class="bi bi-lock-fill text-3xl mb-1"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider">Terkunci</span>
                            </div>
                        @endif
                    </div>

                    {{-- 2. KONTEN --}}
                    <div class="p-3 d-flex flex-column flex-grow-1 {{ $opacity_class }}">
                        
                        {{-- Judul MK --}}
                        <h5 class="font-bold text-base text-gray-800 mb-1 leading-tight line-clamp-1" title="{{ $mk->nama_mk }}">
                            {{ $mk->nama_mk }}
                        </h5>

                        {{-- Deskripsi --}}
                        <p class="text-xs text-gray-500 mb-3 line-clamp-2 leading-relaxed">
                            {{ $mk->deskripsi ?? 'Pelajari dasar hingga mahir dalam mata kuliah ini.' }}
                        </p>

                        {{-- BOX NAMA DOSEN --}}
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-2 mb-3 d-flex align-items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] flex-shrink-0">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="mb-0 text-[10px] text-gray-400 uppercase font-bold leading-none">Dosen Pengampu</p>
                                <p class="mb-0 text-xs font-semibold text-gray-700 text-truncate leading-tight mt-0.5">
                                    {{ $mk->dosen->nama_lengkap ?? 'Belum Ditentukan' }}
                                </p>
                            </div>
                        </div>

                        {{-- FOOTER (Progress & Button) --}}
                        <div class="mt-auto">
                            @if($is_locked)
                                <div class="bg-gray-100 rounded p-2 text-center border border-gray-200">
                                    <p class="text-[10px] text-gray-500 m-0 d-flex justify-content-center align-items-center gap-1">
                                        <i class="bi bi-lock"></i> {{ $mk->pesan_kunci }}
                                    </p>
                                </div>
                            @else
                                {{-- Progress Info --}}
                                <div class="d-flex justify-content-between text-[10px] text-gray-500 mb-1 font-bold uppercase">
                                    <span>Progres Belajar</span>
                                    <span class="{{ $mk->persen == 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $mk->persen }}%</span>
                                </div>
                                
                                {{-- Progress Bar --}}
                                <div class="progress h-1.5 bg-gray-100 rounded-full overflow-hidden mb-3">
                                    <div class="progress-bar {{ $progress_color }}" role="progressbar" style="width: {{ $mk->persen }}%"></div>
                                </div>

                                {{-- Button --}}
                                <a href="{{ url('/belajar/' . $mk->id . '/' . $mk->next_urutan) }}"
                                    class="btn w-100 rounded-lg py-2 font-bold text-xs shadow-sm transition-all duration-200 d-flex align-items-center justify-content-center gap-2
                                    {{ $mk->persen == 100 
                                        ? 'bg-green-600 text-white hover:bg-green-700 border-0' 
                                        : 'bg-gray-900 text-white hover:bg-blue-900 border-0' }}">
                                    
                                    @if ($mk->persen == 0)
                                        Mulai Belajar <i class="bi bi-play-fill text-sm"></i>
                                    @elseif($mk->persen == 100)
                                        Ulangi Materi <i class="bi bi-arrow-repeat"></i>
                                    @else
                                        Lanjut Belajar <i class="bi bi-journal-arrow-up"></i>
                                    @endif
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="d-inline-block p-4 rounded-full bg-gray-50 mb-3 border border-dashed border-gray-200">
                    <i class="bi bi-journal-x text-4xl text-gray-300"></i>
                </div>
                <h6 class="font-bold text-gray-600">Belum ada Mata Kuliah</h6>
                <p class="text-gray-400 text-xs">Data kelas belum tersedia.</p>
            </div>
        @endforelse
    </div>
@endsection