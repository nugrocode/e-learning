@extends('layouts.app')

@section('title', 'Mata Kuliah - ' . $concentration->nama_konsentrasi)

@section('content')
    {{-- Navigasi Kembali --}}
    <div class="mb-4 animate-fade-in-up">
        <a href="{{ url('/jalur-belajar') }}"
            class="text-decoration-none text-gray-500 hover:text-gray-800 flex items-center gap-2 text-sm md:text-base">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Konsentrasi
        </a>
    </div>

    {{-- Alert System --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-xl me-2"></i>
                <div class="text-sm md:text-base">
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-xl me-2"></i>
                <div class="text-sm md:text-base">
                    <strong>Terjadi Kesalahan!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header Halaman (Responsif) --}}
    <div class="mb-5 border-b border-gray-300 pb-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 animate-fade-in-up">
        <div>
            {{-- Judul: Besar di Laptop, Sedang di HP --}}
            <h2 class="font-bold text-xl md:text-2xl text-gray-800 m-0">Mata Kuliah</h2>
            <p class="text-gray-600 m-0 text-sm md:text-base">Konsentrasi: <span class="font-semibold text-blue-900">{{ $concentration->nama_konsentrasi }}</span></p>
        </div>
    </div>

    {{-- Grid Mata Kuliah --}}
    <div class="row g-4">
        @forelse($courses as $mk)
            @php
                $progress_color = $mk->persen == 100 ? 'bg-green-500' : 'bg-yellow-400';
                $jumlah_materi = \App\Models\Material::where('course_id', $mk->id)->count();
            @endphp

            <div class="col-12 col-md-6 col-xl-4">
                {{-- Card Hover Effect --}}
                <div class="card-hover p-3 md:p-4 h-100 shadow-sm border rounded-xl bg-white position-relative"
                    style="border-left: 5px solid #2d3748;">

                    {{-- [DIHAPUS] Badge Urutan sudah dihilangkan dari sini --}}

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-blue-100 text-blue-800 rounded-pill px-2 py-1 text-[10px] md:text-xs font-bold">
                                Wajib
                            </span>
                            <small class="text-gray-400 font-medium me-5 text-[10px] md:text-xs">
                                {{ $jumlah_materi }} Materi
                            </small>
                        </div>

                        {{-- Judul Card Responsif --}}
                        <h5 class="font-bold text-base md:text-lg text-gray-800 mb-2 leading-tight pe-4">
                            {{ $mk->nama_mk }}
                        </h5>

                        <p class="text-xs md:text-sm text-gray-500 mb-0 line-clamp-2">
                            {{ $mk->deskripsi ?? 'Pelajari konsep dasar dan lanjutan dari mata kuliah ini secara terstruktur.' }}
                        </p>
                    </div>

                    <div class="mt-auto">
                        <div class="d-flex justify-content-between text-[10px] md:text-xs text-gray-500 mb-1">
                            <span>Progres Belajar</span>
                            <span class="font-bold text-gray-700">{{ $mk->persen }}%</span>
                        </div>

                        <div class="progress progress-height bg-gray-100 mb-4" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar {{ $progress_color }}" role="progressbar" style="width: {{ $mk->persen }}%"></div>
                        </div>

                        {{-- Tombol Responsif --}}
                        <a href="{{ url('/belajar/' . $mk->id . '/' . $mk->next_urutan) }}"
                            class="btn {{ $mk->persen == 100 ? 'btn-success' : 'btn-dark' }} w-100 rounded-lg py-2 md:py-2.5 text-xs md:text-sm font-bold hover:opacity-90 transition shadow-sm d-flex align-items-center justify-content-center gap-2">

                            @if ($mk->persen == 0)
                                Mulai Belajar <i class="bi bi-play-circle-fill"></i>
                            @elseif($mk->persen == 100)
                                Ulangi Materi <i class="bi bi-arrow-repeat"></i>
                            @else
                                Lanjutkan Belajar <i class="bi bi-box-arrow-in-right"></i>
                            @endif

                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="bg-white p-4 md:p-5 rounded-xl shadow-sm d-inline-block border border-dashed mx-3">
                    <i class="bi bi-journal-x text-4xl md:text-5xl text-gray-300"></i>
                    <h5 class="mt-3 text-gray-600 font-semibold text-sm md:text-base">Belum ada Mata Kuliah</h5>
                    <p class="text-xs md:text-sm text-gray-400 mb-0">Silakan hubungi admin prodi untuk menambahkan data.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection
