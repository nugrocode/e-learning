@extends('layouts.app')

@section('title', 'Mata Kuliah - ' . $concentration->nama_konsentrasi)

@section('content')
    {{-- Navigasi Kembali --}}
    <div class="mb-5 animate-fade-in-up">
        <a href="{{ url('/jalur-belajar') }}"
            class="text-decoration-none text-gray-500 hover:text-blue-900 transition flex items-center gap-2 text-sm font-semibold">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Konsentrasi
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0 rounded-lg" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-xl"></i>
                <div><strong>Berhasil!</strong> {{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-end mb-5 gap-3 border-b border-gray-200 pb-4 animate-fade-in-up">
        <div>
            <h2 class="font-bold text-2xl md:text-3xl text-gray-800 mb-1">Daftar Mata Kuliah</h2>
            <p class="text-gray-500 m-0 text-sm md:text-base">
                Program Studi: <span class="font-bold text-blue-900 bg-blue-50 px-2 py-1 rounded">{{ $concentration->nama_konsentrasi }}</span>
            </p>
        </div>
    </div>

    {{-- Grid Mata Kuliah --}}
    <div class="row g-4">
        @forelse($courses as $mk)
            @php
                $progress_color = $mk->persen == 100 ? 'bg-green-500' : 'bg-yellow-400';
                $colors = ['bg-blue-600', 'bg-purple-600', 'bg-indigo-600', 'bg-teal-600', 'bg-slate-700'];
                $bg_random = $colors[$mk->id % count($colors)];
                
                // Cek Status Akses dari Controller
                $is_locked = ($mk->status_akses != 'open');
                $opacity_class = $is_locked ? 'opacity-60 grayscale' : ''; // Efek abu-abu jika dikunci
            @endphp

            <div class="col-12 col-md-6 col-xl-4">
                {{-- CARD UTAMA --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column transition-shadow duration-300 {{ $is_locked ? 'bg-gray-50' : 'hover:shadow-lg' }}">
                    
                    {{-- 1. BAGIAN GAMBAR (Header) --}}
                    <div class="h-40 w-full relative overflow-hidden {{ $opacity_class }}">
                        @if(isset($mk->gambar) && $mk->gambar)
                            <img src="{{ asset('images/' . $mk->gambar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full {{ $bg_random }} d-flex align-items-center justify-content-center relative">
                                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 10px 10px;"></div>
                                <div class="text-center text-white z-10 p-3">
                                    <i class="bi bi-code-square text-4xl mb-2 d-block opacity-80"></i>
                                    <span class="font-bold text-lg tracking-wider">{{ substr($mk->nama_mk, 0, 15) }}...</span>
                                </div>
                            </div>
                        @endif

                        {{-- Badge Jumlah Materi --}}
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/90 backdrop-blur-sm text-gray-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                                <i class="bi bi-file-earmark-text-fill text-blue-500"></i> {{ $mk->total_materi }} Materi
                            </span>
                        </div>

                        {{-- OVERLAY GEMBOK JIKA TERKUNCI --}}
                        @if($is_locked)
                            <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-white z-20">
                                <i class="bi bi-lock-fill text-4xl mb-1"></i>
                                <span class="text-xs font-bold uppercase tracking-wider">Terkunci</span>
                            </div>
                        @endif
                    </div>

                    {{-- 2. BAGIAN KONTEN --}}
                    <div class="p-4 flex-grow-1 d-flex flex-column {{ $opacity_class }}">
                        
                        <h5 class="font-bold text-lg text-gray-800 mb-2 leading-tight">
                            {{ $mk->nama_mk }}
                        </h5>

                        <p class="text-sm text-gray-500 mb-4 line-clamp-2 leading-relaxed">
                            {{ $mk->deskripsi ?? 'Pelajari dasar hingga mahir dalam mata kuliah ini.' }}
                        </p>

                        <div class="mt-auto pt-3 border-t border-gray-100">
                            
                            {{-- TAMPILAN JIKA TERKUNCI --}}
                            @if($is_locked)
                                <div class="alert alert-secondary py-2 px-3 text-xs mb-0 d-flex align-items-center gap-2 border-0 bg-gray-200 text-gray-600">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <span>{{ $mk->pesan_kunci }}</span>
                                </div>
                            
                            {{-- TAMPILAN JIKA TERBUKA --}}
                            @else
                                <div class="d-flex justify-content-between text-[10px] text-gray-500 mb-1 uppercase font-semibold">
                                    <span>Progres</span>
                                    <span class="{{ $mk->persen == 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $mk->persen }}%</span>
                                </div>
                                <div class="progress h-2 bg-gray-100 rounded-full overflow-hidden mb-4">
                                    <div class="progress-bar {{ $progress_color }}" role="progressbar" style="width: {{ $mk->persen }}%"></div>
                                </div>

                                <a href="{{ url('/belajar/' . $mk->id . '/' . $mk->next_urutan) }}"
                                    class="btn w-100 rounded-lg py-2 font-bold text-sm shadow-sm transition-all duration-300 d-flex align-items-center justify-content-center gap-2
                                    {{ $mk->persen == 100 
                                        ? 'bg-green-600 text-white hover:bg-green-700 border-0' 
                                        : 'bg-gray-800 text-white hover:bg-blue-900 border-0' }}">
                                    
                                    @if ($mk->persen == 0)
                                        Mulai Belajar <i class="bi bi-play-fill text-lg"></i>
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
                <div class="d-inline-block p-5 rounded-full bg-gray-50 mb-4 border border-dashed border-gray-200">
                    <i class="bi bi-journal-x text-5xl text-gray-300"></i>
                </div>
                <h5 class="font-bold text-gray-600">Belum ada Mata Kuliah</h5>
                <p class="text-gray-400 text-sm">Data kelas belum tersedia untuk konsentrasi ini.</p>
            </div>
        @endforelse
    </div>
@endsection