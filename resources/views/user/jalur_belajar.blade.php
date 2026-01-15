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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-yellow-400 transition-all duration-300 h-100 flex flex-col group overflow-hidden">

                    {{-- 1. BAGIAN GAMBAR (THUMBNAIL) --}}
                    <a href="{{ url('/mata-kuliah/' . $item->id) }}" class="block relative h-48 overflow-hidden bg-gray-100">
                        {{-- Logika Gambar: Database -> Fallback Random --}}
                        <img src="{{ $item->gambar ? asset('uploads/concentrations/' . $item->gambar) : 'https://source.unsplash.com/600x400/?coding,technology&sig=' . $item->id }}"
                             alt="{{ $item->nama_konsentrasi }}"
                             class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105"
                             onerror="this.src='https://via.placeholder.com/600x400?text=UKI+Toraja'">

                        {{-- Overlay halus saat hover --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                    </a>

                    {{-- 2. BAGIAN KONTEN --}}
                    <div class="p-4 md:p-5 flex flex-col flex-grow-1">

                        {{-- Judul --}}
                        <h3 class="font-bold text-lg md:text-xl text-gray-800 mb-2 leading-tight group-hover:text-blue-900 transition-colors">
                            <a href="{{ url('/mata-kuliah/' . $item->id) }}" class="text-decoration-none text-inherit">
                                {{ $item->nama_konsentrasi }}
                            </a>
                        </h3>

                        {{-- Metadata Kecil (Opsional: Jumlah SKS/Modul jika nanti ada) --}}
                        <div class="d-flex align-items-center gap-3 text-xs md:text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1">
                                <i class="bi bi-collection-play-fill text-yellow-500"></i> Kurikulum Tersedia
                            </span>
                        </div>

                        {{-- Deskripsi --}}
                        <p class="text-sm text-gray-500 line-clamp-3 mb-0 flex-grow-1 leading-relaxed">
                            {{ $item->deskripsi ?? 'Pelajari materi ini untuk meningkatkan skill teknis Anda secara terstruktur.' }}
                        </p>

                        {{-- 3. FOOTER LINK (Kanan Bawah) --}}
                        <div class="mt-4 text-end border-t border-gray-100 pt-3">
                            <a href="{{ url('/mata-kuliah/' . $item->id) }}" class="text-decoration-none font-bold text-blue-900 text-sm md:text-base hover:text-yellow-600 transition-colors inline-flex items-center gap-2">
                                Lihat Kelas <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            {{-- EMPTY STATE --}}
            <div class="col-12 text-center py-5">
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm d-inline-block border border-dashed border-gray-300">
                    <i class="bi bi-grid-fill text-4xl md:text-5xl text-gray-300 mb-3 d-block"></i>
                    <h5 class="text-gray-700 font-bold text-base md:text-lg">Belum Ada Kelas</h5>
                    <p class="text-xs md:text-sm text-gray-400 mb-0">Silakan hubungi admin prodi.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection
