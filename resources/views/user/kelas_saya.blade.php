@extends('layouts.app')

@section('title', 'Kelas Saya')

@section('content')
    {{-- Header Halaman --}}
    <div class="mb-5 border-b border-gray-300 pb-3 animate-fade-in-up">
        <h2 class="font-bold text-2xl md:text-3xl text-gray-800">Kelas Saya</h2>
        <p class="text-gray-600 text-sm md:text-base">Lanjutkan pembelajaran di kelas yang sedang Anda ikuti.</p>
    </div>

    {{-- Grid Kelas --}}
    <div class="row g-4">
        @forelse($courses as $mk)
            @php
                $progress_color = ($mk->persen == 100) ? 'bg-green-500' : 'bg-yellow-400';
                $jumlah_materi = \App\Models\Material::where('course_id', $mk->id)->count();
                
                // Variasi Warna Placeholder Gambar
                $colors = ['bg-blue-600', 'bg-purple-600', 'bg-indigo-600', 'bg-teal-600', 'bg-slate-700'];
                $bg_random = $colors[$mk->id % count($colors)];
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                {{-- Card Wrapper --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column hover:shadow-lg transition-shadow duration-300">

                    {{-- 1. Bagian Gambar (Header) --}}
                    <div class="h-40 w-full relative overflow-hidden">
                        @if(isset($mk->gambar) && $mk->gambar)
                            <img src="{{ asset('images/' . $mk->gambar) }}" class="w-full h-full object-cover">
                        @else
                            {{-- Placeholder Keren: Gradasi + Icon --}}
                            <div class="w-full h-full {{ $bg_random }} d-flex align-items-center justify-content-center relative">
                                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 10px 10px;"></div>
                                <div class="text-center text-white z-10 p-3">
                                    <i class="bi bi-laptop text-4xl mb-2 d-block opacity-80"></i>
                                    <span class="font-bold text-lg tracking-wider">{{ substr($mk->nama_mk, 0, 15) }}...</span>
                                </div>
                            </div>
                        @endif

                        {{-- Badge Jumlah Materi --}}
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/90 backdrop-blur-sm text-gray-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                                <i class="bi bi-collection-play-fill text-blue-500"></i> {{ $jumlah_materi }} Materi
                            </span>
                        </div>
                    </div>

                    {{-- 2. Bagian Konten --}}
                    <div class="p-4 flex-grow-1 d-flex flex-column">

                        {{-- Judul --}}
                        <h5 class="font-bold text-lg text-gray-800 mb-2 leading-tight">
                            {{ $mk->nama_mk }}
                        </h5>

                        {{-- Deskripsi --}}
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2 leading-relaxed">
                            {{ $mk->deskripsi ?? 'Pelajari materi ini untuk meningkatkan keahlian Anda.' }}
                        </p>

                        {{-- Progress Bar & Tombol --}}
                        <div class="mt-auto pt-3 border-t border-gray-100">
                            
                            {{-- Info Progress --}}
                            <div class="d-flex justify-content-between text-[10px] text-gray-500 mb-1 uppercase font-semibold">
                                <span>Progres Belajar</span>
                                <span class="{{ $mk->persen == 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $mk->persen }}%</span>
                            </div>
                            
                            {{-- Bar --}}
                            <div class="progress h-2 bg-gray-100 rounded-full overflow-hidden mb-4">
                                <div class="progress-bar {{ $progress_color }}" role="progressbar" style="width: {{ $mk->persen }}%"></div>
                            </div>

                            {{-- Tombol Aksi --}}
                            <a href="{{ url('/belajar/' . $mk->id . '/' . $mk->next_urutan) }}" 
                               class="btn w-100 rounded-lg py-2 font-bold text-sm shadow-sm transition-all duration-300 d-flex align-items-center justify-content-center gap-2
                               {{ $mk->persen == 100 
                                    ? 'bg-green-600 text-white hover:bg-green-700 border-0' 
                                    : 'bg-gray-800 text-white hover:bg-blue-900 border-0' }}">
                                
                                @if($mk->persen == 100)
                                    Ulangi Materi <i class="bi bi-arrow-repeat"></i>
                                @else
                                    Lanjutkan Belajar <i class="bi bi-play-fill text-lg"></i>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            {{-- Empty State (Jika belum ambil kelas) --}}
            <div class="col-12 py-5 text-center">
                <div class="d-inline-block p-5 rounded-full bg-gray-50 mb-4 border border-dashed border-gray-200">
                    <i class="bi bi-journal-bookmark text-5xl text-gray-300"></i>
                </div>
                <h5 class="font-bold text-gray-600">Belum Ada Kelas</h5>
                <p class="text-gray-400 text-sm mb-4">Anda belum memulai pembelajaran apapun. Yuk mulai sekarang!</p>
                <a href="{{ url('/jalur-belajar') }}" class="btn btn-primary bg-blue-900 border-0 text-white px-4 py-2 rounded-lg font-bold shadow-sm hover:bg-blue-800 transition">
                    Mulai Belajar <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        @endforelse
    </div>
@endsection