@extends('layouts.dosen')

@section('title', 'Kelas Ajar Saya')

@section('content')
    <div class="mb-4">
        <h2 class="font-bold text-xl text-gray-800">Kelas Ajar Saya</h2>
        <p class="text-gray-500 text-sm">Pilih kelas untuk mengelola materi dan memantau mahasiswa.</p>
    </div>

    <div class="row g-3">
        @foreach($courses as $course)
            <div class="col-12 col-md-6 col-lg-4">
                {{-- CARD COMPACT (Tanpa Efek Hover Melayang) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden d-flex flex-column h-100">
                    
                    {{-- 1. Thumbnail Pendek --}}
                    <div class="relative h-24 overflow-hidden">
                        <img src="{{ $course->gambar ? asset('storage/thumbnails/'.$course->gambar) : asset('images/default_course.jpg') }}" 
                             class="w-full h-full object-cover" alt="{{ $course->nama_mk }}">
                        <div class="absolute top-2 left-2">
                            @foreach($course->concentrations as $con)
                                <span class="bg-blue-600/80 backdrop-blur-sm text-white text-[9px] px-2 py-0.5 rounded uppercase font-bold">
                                    {{ $con->nama_konsentrasi }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Konten Padat & Informatif --}}
                    <div class="p-3 flex-grow-1">
                        <h6 class="font-bold text-gray-800 mb-1 truncate" title="{{ $course->nama_mk }}">
                            {{ $course->nama_mk }}
                        </h6>
                        <p class="text-gray-400 text-[11px] mb-3 line-clamp-1 italic">
                            "{{ $course->deskripsi ?? 'Tidak ada deskripsi MK' }}"
                        </p>

                        {{-- Stats Row (Grid Sederhana) --}}
                        <div class="row g-0 border-t border-b border-gray-50 py-2 mb-3">
                            <div class="col-6 border-end text-center">
                                <span class="d-block font-bold text-indigo-600 text-sm">{{ $course->students_count ?? 0 }}</span>
                                <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Mahasiswa</span>
                            </div>
                            <div class="col-6 text-center">
                                <span class="d-block font-bold text-gray-700 text-sm">{{ $course->materials_count ?? 0 }}</span>
                                <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Materi</span>
                            </div>
                        </div>

                        {{-- Progress Bar Tipis --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-[10px] mb-1 font-bold">
                                <span class="text-gray-400">STATUS KONTEN</span>
                                <span class="text-blue-600">100%</span>
                            </div>
                            <div class="progress h-1 bg-gray-100 rounded-full">
                                <div class="progress-bar bg-blue-500" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Action Button (Tetap di Bawah) --}}
                    <div class="p-3 pt-0">
                        <a href="{{ url('/dosen/kelas/'.$course->id) }}" 
                           class="btn btn-dark w-100 py-2 rounded-lg font-bold text-xs shadow-sm flex items-center justify-center gap-2">
                            Masuk Kelas <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection