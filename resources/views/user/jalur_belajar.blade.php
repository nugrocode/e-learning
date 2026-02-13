@extends('layouts.app')

@section('title', 'Jalur Belajar')

@section('content')
    {{-- HEADER --}}
    <div class="mb-5 border-b border-gray-300 pb-3 animate-fade-in-up">
        <h2 class="font-bold text-xl md:text-3xl text-gray-800">Pilih Konsentrasi</h2>
        <p class="text-gray-600 text-sm md:text-base">Pilih jalur minatmu untuk melihat Mata Kuliah yang tersedia.</p>
    </div>

    {{-- GRID KONSENTRASI --}}
    <div class="row g-4">
        @forelse($concentrations as $item)
            <div class="col-12 col-md-6 col-lg-4">
                {{-- CARD WRAPPER --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column hover:shadow-md transition-all duration-300 group relative">
                    
                    {{-- 1. BAGIAN GAMBAR (THUMBNAIL) --}}
                    <a href="{{ url('/mata-kuliah/' . $item->id) }}" class="relative h-48 bg-gray-100 overflow-hidden block">
                        {{-- 
                            LOGIKA GAMBAR TERBARU:
                            Mengambil dari folder 'storage/thumbnails/'
                        --}}
                        @if($item->gambar)
                            <img src="{{ asset('storage/thumbnails/' . $item->gambar) }}" 
                                 alt="{{ $item->nama_konsentrasi }}" 
                                 class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
                                 onerror="this.src='{{ asset('images/logo_ukit.png') }}'"> {{-- Fallback jika gambar error --}}
                        @else
                            {{-- Placeholder jika tidak ada gambar --}}
                            <div class="w-full h-full d-flex align-items-center justify-content-center bg-gray-200 text-gray-400">
                                <i class="bi bi-image text-4xl"></i>
                            </div>
                        @endif

                        {{-- Overlay Gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-80"></div>
                        
                        {{-- Badge Total MK --}}
                        <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-blue-900 shadow-sm flex items-center gap-1">
                            <i class="bi bi-book-fill text-blue-500"></i> {{ $item->total_mk ?? 0 }} Mata Kuliah
                        </div>
                    </a>

                    {{-- 2. BAGIAN KONTEN --}}
                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        {{-- Judul --}}
                        <h5 class="font-bold text-lg text-gray-800 mb-2 group-hover:text-blue-700 transition">
                            <a href="{{ url('/mata-kuliah/' . $item->id) }}" class="text-decoration-none text-inherit stretched-link">
                                {{ $item->nama_konsentrasi }}
                            </a>
                        </h5>

                        {{-- Deskripsi --}}
                        <p class="text-sm text-gray-500 line-clamp-3 mb-4 flex-grow-1 leading-relaxed">
                            {{ $item->deskripsi ?? 'Pelajari materi ini untuk meningkatkan skill teknis Anda secara terstruktur.' }}
                        </p>

                        {{-- Tombol Aksi (Footer Card) --}}
                        <div class="pt-3 border-t mt-auto">
                            <div class="btn btn-light w-100 font-bold text-blue-900 text-sm hover:bg-blue-50 transition border-blue-100 flex items-center justify-center gap-2">
                                Mulai Belajar <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            {{-- EMPTY STATE --}}
            <div class="col-12 text-center py-5">
                <div class="d-inline-block p-5 rounded-full bg-gray-50 mb-4 border border-dashed border-gray-200">
                    <i class="bi bi-diagram-3 text-5xl text-gray-300"></i>
                </div>
                <h5 class="font-bold text-gray-600">Belum ada Konsentrasi</h5>
                <p class="text-gray-400 text-sm">Silakan hubungi admin untuk menambahkan prodi baru.</p>
            </div>
        @endforelse
    </div>
@endsection